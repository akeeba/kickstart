<?php
/**
 * Akeeba Restore
 * A JSON-powered JPA, JPS and ZIP archive extraction library
 *
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd.
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/**
 * ZIP archive extraction class - The all-in-one approach
 */
class AKUnarchiverZIP extends AKAbstractUnarchiver
{
	protected $archiveHeaderData = array();

	var $expectDataDescriptor = false;

	protected function readArchiveHeader()
	{
		debugMsg('Preparing to read archive header');
		// Initialize header data array
		$this->archiveHeaderData = new stdClass();

		// Open the first part
		debugMsg('Opening the first part');
		$this->nextFile();

		// Fail for unreadable files
		if( $this->fp === false ) {
			debugMsg('The first part is not readable');
			return false;
		}

		// Read a possible multipart signature
		$sigBinary = fread( $this->fp, 4 );
		$headerData = unpack('Vsig', $sigBinary);

		// Roll back if it's not a multipart archive
		if( $headerData['sig'] == 0x04034b50 ) {
			debugMsg('The archive is not multipart');
			fseek($this->fp, -4, SEEK_CUR);
		} else {
			debugMsg('The archive is multipart');
		}

		$multiPartSigs = array(
			0x08074b50,		// Multi-part ZIP
			0x30304b50,		// Multi-part ZIP (alternate)
			0x04034b50		// Single file
		);
		if( !in_array($headerData['sig'], $multiPartSigs) )
		{
			debugMsg('Invalid header signature '.dechex($headerData['sig']));
			$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));
			return false;
		}

		$this->currentPartOffset = @ftell($this->fp);
		debugMsg('Current part offset after reading header: '.$this->currentPartOffset);

		$this->dataReadLength = 0;

		return true;
	}

	/**
	 * Concrete classes must use this method to read the file header
	 * @return bool True if reading the file was successful, false if an error occured or we reached end of archive
	 */
	protected function readFileHeader()
	{
		// If the current part is over, proceed to the next part please
		if( $this->isEOF(true) ) {
			debugMsg('Opening next archive part');
			$this->nextFile();
		}

		$this->currentPartOffset = ftell($this->fp);

		if($this->expectDataDescriptor)
		{
			// The last file had bit 3 of the general purpose bit flag set. This means that we have a
			// 12 byte data descriptor we need to skip. To make things worse, there might also be a 4
			// byte optional data descriptor header (0x08074b50).
			$junk = @fread($this->fp, 4);
			$junk = unpack('Vsig', $junk);
			if($junk['sig'] == 0x08074b50) {
				// Yes, there was a signature
				$junk = @fread($this->fp, 12);
				debugMsg('Data descriptor (w/ header) skipped at '.(ftell($this->fp)-12));
			} else {
				// No, there was no signature, just read another 8 bytes
				$junk = @fread($this->fp, 8);
				debugMsg('Data descriptor (w/out header) skipped at '.(ftell($this->fp)-8));
			}

			// And check for EOF, too
			if( $this->isEOF(true) ) {
				debugMsg('EOF before reading header');

				$this->nextFile();
			}
		}

		// Get and decode Local File Header
		$headerBinary = fread($this->fp, 30);
		$headerData = unpack('Vsig/C2ver/vbitflag/vcompmethod/vlastmodtime/vlastmoddate/Vcrc/Vcompsize/Vuncomp/vfnamelen/veflen', $headerBinary);

		// Check signature
		if(!( $headerData['sig'] == 0x04034b50 ))
		{
			debugMsg('Not a file signature at '.(ftell($this->fp)-4));

			// The signature is not the one used for files. Is this a central directory record (i.e. we're done)?
			if($headerData['sig'] == 0x02014b50)
			{
				debugMsg('EOCD signature at '.(ftell($this->fp)-4));
				// End of ZIP file detected. We'll just skip to the end of file...
				while( $this->nextFile() ) {};
				@fseek($this->fp, 0, SEEK_END); // Go to EOF
				return false;
			}
			else
			{
				debugMsg( 'Invalid signature ' . dechex($headerData['sig']) . ' at '.ftell($this->fp) );
				$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));
				return false;
			}
		}

		// If bit 3 of the bitflag is set, expectDataDescriptor is true
		$this->expectDataDescriptor = ($headerData['bitflag'] & 4) == 4;

		$this->fileHeader = new stdClass();
		$this->fileHeader->timestamp = 0;

		// Read the last modified data and time
		$lastmodtime = $headerData['lastmodtime'];
		$lastmoddate = $headerData['lastmoddate'];

		if($lastmoddate && $lastmodtime)
		{
			// ----- Extract time
			$v_hour = ($lastmodtime & 0xF800) >> 11;
			$v_minute = ($lastmodtime & 0x07E0) >> 5;
			$v_seconde = ($lastmodtime & 0x001F)*2;

			// ----- Extract date
			$v_year = (($lastmoddate & 0xFE00) >> 9) + 1980;
			$v_month = ($lastmoddate & 0x01E0) >> 5;
			$v_day = $lastmoddate & 0x001F;

			// ----- Get UNIX date format
			$this->fileHeader->timestamp = @mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);
		}

		$isBannedFile = false;

		$this->fileHeader->compressed	= $headerData['compsize'];
		$this->fileHeader->uncompressed	= $headerData['uncomp'];
		$nameFieldLength				= $headerData['fnamelen'];
		$extraFieldLength				= $headerData['eflen'];

		// Read filename field
		$this->fileHeader->file			= fread( $this->fp, $nameFieldLength );

		// Handle file renaming
		$isRenamed = false;
		if(is_array($this->renameFiles) && (count($this->renameFiles) > 0) )
		{
			if(array_key_exists($this->fileHeader->file, $this->renameFiles))
			{
				$this->fileHeader->file = $this->renameFiles[$this->fileHeader->file];
				$isRenamed = true;
			}
		}

		// Handle directory renaming
		$isDirRenamed = false;
		if(is_array($this->renameDirs) && (count($this->renameDirs) > 0)) {
			if(array_key_exists(dirname($this->fileHeader->file), $this->renameDirs)) {
				$file = rtrim($this->renameDirs[dirname($this->fileHeader->file)],'/').'/'.basename($this->fileHeader->file);
				$isRenamed = true;
				$isDirRenamed = true;
			}
		}

		// Read extra field if present
		if($extraFieldLength > 0) {
			$extrafield = fread( $this->fp, $extraFieldLength );
		}

		debugMsg( '*'.ftell($this->fp).' IS START OF '.$this->fileHeader->file. ' ('.$this->fileHeader->compressed.' bytes)' );


		// Decide filetype -- Check for directories
		$this->fileHeader->type = 'file';
		if( strrpos($this->fileHeader->file, '/') == strlen($this->fileHeader->file) - 1 ) $this->fileHeader->type = 'dir';
		// Decide filetype -- Check for symbolic links
		if( ($headerData['ver1'] == 10) && ($headerData['ver2'] == 3) )$this->fileHeader->type = 'link';

		switch( $headerData['compmethod'] )
		{
			case 0:
				$this->fileHeader->compression = 'none';
				break;
			case 8:
				$this->fileHeader->compression = 'gzip';
				break;
		}

		// Find hard-coded banned files
		if( (basename($this->fileHeader->file) == ".") || (basename($this->fileHeader->file) == "..") )
		{
			$isBannedFile = true;
		}

		// Also try to find banned files passed in class configuration
		if((count($this->skipFiles) > 0) && (!$isRenamed))
		{
			if(in_array($this->fileHeader->file, $this->skipFiles))
			{
				$isBannedFile = true;
			}
		}

		// If we have a banned file, let's skip it
		if($isBannedFile)
		{
			// Advance the file pointer, skipping exactly the size of the compressed data
			$seekleft = $this->fileHeader->compressed;
			while($seekleft > 0)
			{
				// Ensure that we can seek past archive part boundaries
				$curSize = @filesize($this->archiveList[$this->currentPartNumber]);
				$curPos = @ftell($this->fp);
				$canSeek = $curSize - $curPos;
				if($canSeek > $seekleft) $canSeek = $seekleft;
				@fseek( $this->fp, $canSeek, SEEK_CUR );
				$seekleft -= $canSeek;
				if($seekleft) $this->nextFile();
			}

			$this->currentPartOffset = @ftell($this->fp);
			$this->runState = AK_STATE_DONE;
			return true;
		}

		// Remove the removePath, if any
		$this->fileHeader->file = $this->removePath($this->fileHeader->file);

		// Last chance to prepend a path to the filename
		if(!empty($this->addPath) && !$isDirRenamed)
		{
			$this->fileHeader->file = $this->addPath.$this->fileHeader->file;
		}

		// Get the translated path name
		if($this->fileHeader->type == 'file')
		{
			$this->fileHeader->realFile = $this->postProcEngine->processFilename( $this->fileHeader->file );
		}
		elseif($this->fileHeader->type == 'dir')
		{
			$this->fileHeader->timestamp = 0;

			$dir = $this->fileHeader->file;

			$this->postProcEngine->createDirRecursive( $this->fileHeader->file, 0755 );
			$this->postProcEngine->processFilename(null);
		}
		else
		{
			// Symlink; do not post-process
			$this->fileHeader->timestamp = 0;
			$this->postProcEngine->processFilename(null);
		}

		$this->createDirectory();

		// Header is read
		$this->runState = AK_STATE_HEADER;

		return true;
	}

	/**
	 * Concrete classes must use this method to process file data. It must set $runState to AK_STATE_DATAREAD when
	 * it's finished processing the file data.
	 * @return bool True if processing the file data was successful, false if an error occured
	 */
	protected function processFileData()
	{
		switch( $this->fileHeader->type )
		{
			case 'dir':
				return $this->processTypeDir();
				break;

			case 'link':
				return $this->processTypeLink();
				break;

			case 'file':
				switch($this->fileHeader->compression)
				{
					case 'none':
						return $this->processTypeFileUncompressed();
						break;

					case 'gzip':
					case 'bzip2':
						return $this->processTypeFileCompressedSimple();
						break;

				}
				break;

			default:
				debugMsg('Unknown file type '.$this->fileHeader->type);
				break;
		}
	}

	private function processTypeFileUncompressed()
	{
		// Uncompressed files are being processed in small chunks, to avoid timeouts
		if( ($this->dataReadLength == 0) && !AKFactory::get('kickstart.setup.dryrun','0') )
		{
			// Before processing file data, ensure permissions are adequate
			$this->setCorrectPermissions( $this->fileHeader->file );
		}

		// Open the output file
		if( !AKFactory::get('kickstart.setup.dryrun','0') )
		{
			$ignore = AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);
			if ($this->dataReadLength == 0) {
				$outfp = @fopen( $this->fileHeader->realFile, 'wb' );
			} else {
				$outfp = @fopen( $this->fileHeader->realFile, 'ab' );
			}

			// Can we write to the file?
			if( ($outfp === false) && (!$ignore) ) {
				// An error occured
				debugMsg('Could not write to output file');
				$this->setError( AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile) );
				return false;
			}
		}

		// Does the file have any data, at all?
		if( $this->fileHeader->compressed == 0 )
		{
			// No file data!
			if( !AKFactory::get('kickstart.setup.dryrun','0') && is_resource($outfp) ) @fclose($outfp);
			$this->runState = AK_STATE_DATAREAD;
			return true;
		}

		// Reference to the global timer
		$timer = AKFactory::getTimer();

		$toReadBytes = 0;
		$leftBytes = $this->fileHeader->compressed - $this->dataReadLength;

		// Loop while there's data to read and enough time to do it
		while( ($leftBytes > 0) && ($timer->getTimeLeft() > 0) )
		{
			$toReadBytes = ($leftBytes > $this->chunkSize) ? $this->chunkSize : $leftBytes;
			$data = $this->fread( $this->fp, $toReadBytes );
			$reallyReadBytes = akstringlen($data);
			$leftBytes -= $reallyReadBytes;
			$this->dataReadLength += $reallyReadBytes;
			if($reallyReadBytes < $toReadBytes)
			{
				// We read less than requested! Why? Did we hit local EOF?
				if( $this->isEOF(true) && !$this->isEOF(false) )
				{
					// Yeap. Let's go to the next file
					$this->nextFile();
				}
				else
				{
					// Nope. The archive is corrupt
					debugMsg('Not enough data in file. The archive is truncated or corrupt.');
					$this->setError( AKText::_('ERR_CORRUPT_ARCHIVE') );
					return false;
				}
			}
			if( !AKFactory::get('kickstart.setup.dryrun','0') )
				if(is_resource($outfp)) @fwrite( $outfp, $data );
		}

		// Close the file pointer
		if( !AKFactory::get('kickstart.setup.dryrun','0') )
			if(is_resource($outfp)) @fclose($outfp);

		// Was this a pre-timeout bail out?
		if( $leftBytes > 0 )
		{
			$this->runState = AK_STATE_DATA;
		}
		else
		{
			// Oh! We just finished!
			$this->runState = AK_STATE_DATAREAD;
			$this->dataReadLength = 0;
		}

		return true;
	}

	private function processTypeFileCompressedSimple()
	{
		if( !AKFactory::get('kickstart.setup.dryrun','0') )
		{
			// Before processing file data, ensure permissions are adequate
			$this->setCorrectPermissions( $this->fileHeader->file );

			// Open the output file
			$outfp = @fopen( $this->fileHeader->realFile, 'wb' );

			// Can we write to the file?
			$ignore = AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);
			if( ($outfp === false) && (!$ignore) ) {
				// An error occured
				debugMsg('Could not write to output file');
				$this->setError( AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile) );
				return false;
			}
		}

		// Does the file have any data, at all?
		if( $this->fileHeader->compressed == 0 )
		{
			// No file data!
			if( !AKFactory::get('kickstart.setup.dryrun','0') )
				if(is_resource($outfp)) @fclose($outfp);
			$this->runState = AK_STATE_DATAREAD;
			return true;
		}

		// Simple compressed files are processed as a whole; we can't do chunk processing
		$zipData = $this->fread( $this->fp, $this->fileHeader->compressed );
		while( akstringlen($zipData) < $this->fileHeader->compressed )
		{
			// End of local file before reading all data, but have more archive parts?
			if($this->isEOF(true) && !$this->isEOF(false))
			{
				// Yeap. Read from the next file
				$this->nextFile();
				$bytes_left = $this->fileHeader->compressed - akstringlen($zipData);
				$zipData .= $this->fread( $this->fp, $bytes_left );
			}
			else
			{
				debugMsg('End of local file before reading all data with no more parts left. The archive is corrupt or truncated.');
				$this->setError( AKText::_('ERR_CORRUPT_ARCHIVE') );
				return false;
			}
		}

		if($this->fileHeader->compression == 'gzip')
		{
			$unzipData = gzinflate( $zipData );
		}
		elseif($this->fileHeader->compression == 'bzip2')
		{
			$unzipData = bzdecompress( $zipData );
		}
		unset($zipData);

		// Write to the file.
		if( !AKFactory::get('kickstart.setup.dryrun','0') && is_resource($outfp) )
		{
			@fwrite( $outfp, $unzipData, $this->fileHeader->uncompressed );
			@fclose( $outfp );
		}
		unset($unzipData);

		$this->runState = AK_STATE_DATAREAD;
		return true;
	}

	/**
	 * Process the file data of a link entry
	 * @return bool
	 */
	private function processTypeLink()
	{
		$readBytes = 0;
		$toReadBytes = 0;
		$leftBytes = $this->fileHeader->compressed;
		$data = '';

		while( $leftBytes > 0)
		{
			$toReadBytes = ($leftBytes > $this->chunkSize) ? $this->chunkSize : $leftBytes;
			$mydata = $this->fread( $this->fp, $toReadBytes );
			$reallyReadBytes = akstringlen($mydata);
			$data .= $mydata;
			$leftBytes -= $reallyReadBytes;
			if($reallyReadBytes < $toReadBytes)
			{
				// We read less than requested! Why? Did we hit local EOF?
				if( $this->isEOF(true) && !$this->isEOF(false) )
				{
					// Yeap. Let's go to the next file
					$this->nextFile();
				}
				else
				{
					debugMsg('End of local file before reading all data with no more parts left. The archive is corrupt or truncated.');
					// Nope. The archive is corrupt
					$this->setError( AKText::_('ERR_CORRUPT_ARCHIVE') );
					return false;
				}
			}
		}

		$filename = isset($this->fileHeader->realFile) ? $this->fileHeader->realFile : $this->fileHeader->file;

		if( !AKFactory::get('kickstart.setup.dryrun','0') )
		{
			// Try to remove an existing file or directory by the same name
			if(file_exists($filename)) { @unlink($filename); @rmdir($filename); }
			// Remove any trailing slash
			if(substr($filename, -1) == '/') $filename = substr($filename, 0, -1);
			// Create the symlink - only possible within PHP context. There's no support built in the FTP protocol, so no postproc use is possible here :(
			@symlink($data, $filename);
		}

		$this->runState = AK_STATE_DATAREAD;

		return true; // No matter if the link was created!
	}

	/**
	 * Process the file data of a directory entry
	 * @return bool
	 */
	private function processTypeDir()
	{
		// Directory entries in the JPA do not have file data, therefore we're done processing the entry
		$this->runState = AK_STATE_DATAREAD;
		return true;
	}

	/**
	 * Creates the directory this file points to
	 */
	protected function createDirectory()
	{
		if( AKFactory::get('kickstart.setup.dryrun','0') ) return true;

		// Do we need to create a directory?
		if(empty($this->fileHeader->realFile)) $this->fileHeader->realFile = $this->fileHeader->file;
		$lastSlash = strrpos($this->fileHeader->realFile, '/');
		$dirName = substr( $this->fileHeader->realFile, 0, $lastSlash);
		$perms = $this->flagRestorePermissions ? $this->fileHeader->permissions : 0755;
		$ignore = AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($dirName);
		if( ($this->postProcEngine->createDirRecursive($dirName, $perms) == false) && (!$ignore) ) {
			$this->setError( AKText::sprintf('COULDNT_CREATE_DIR', $dirName) );
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function heuristicFileHeaderLocator()
	{
		$ret = false;
		$fullEOF = false;

		while(!$ret && !$fullEOF) {
			$this->currentPartOffset = @ftell($this->fp);
			if($this->isEOF(true)) {
				$this->nextFile();
			}

			if($this->isEOF(false)) {
				$fullEOF = true;
				continue;
			}

			// Read 512Kb
			$chunk = fread($this->fp, 524288);
			$size_read = mb_strlen($chunk, '8bit');
			//$pos = strpos($chunk, 'JPF');
			$pos = mb_strpos($chunk, 'JPF', 0, '8bit');
			if($pos !== false) {
				// We found it!
				$this->currentPartOffset += $pos + 3;
				@fseek($this->fp, $this->currentPartOffset, SEEK_SET);
				$ret = true;
			} else {
				// Not yet found :(
				$this->currentPartOffset = @ftell($this->fp);
			}
		}

		return $ret;
	}
}
