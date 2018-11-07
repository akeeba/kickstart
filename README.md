# Akeeba Kickstart

The single file JPA, JPS and ZIP archive extractor for your site. Includes Akeeba Restore and Joomla! Start.

## Prerequisites

In order to build the scripts in this distribution you need to have the following tools:

- A command line environment. bash under Linux / Mac OS X works best. On Windows you will need to run most tools using an elevated privileges (administrator) command prompt.

- The PHP CLI binary in your path

- Command line Subversion and Git binaries(*)

- PEAR and Phing installed, with the Net_FTP and VersionControl_SVN PEAR packages installed

- libxml and libxslt tools if you intend to build the documentation PDF files

You will also need the following path structure on your system

- buildfiles	Akeeba Build Tools (https://github.com/akeeba/buildfiles)

You will need to use the exact folder names specified here.

## Initialising the repository

All of the following commands are to be run from the MAIN directory. Lines
starting with $ indicate a Mac OS X / Linux / other *NIX system commands. Lines
starting with > indicate Windows commands. The starting character ($ or >) MUST
NOT be typed!

1. You will first need to do the initial link with Akeeba Build Tools, running
   the following command (Mac OS X, Linux, other *NIX systems):

		$ php ../buildfiles/tools/link.php `pwd`

   or, on Windows:

		> php ../buildfiles/tools/link.php %CD%

2. After the initial linking takes place, go inside the build directory:

		$ cd build

   and run the link phing task:

		$ phing link

## Useful Phing tasks

All of the following commands are to be run from the MAIN/build directory.
Lines starting with $ indicate a Mac OS X / Linux / other *NIX system commands.
Lines starting with > indicate Windows commands. The starting character ($ or >)
MUST NOT be typed!

1. Relinking internal files

   This is required after every major upgrade in the component and/or when new
   plugins and modules are installed. It will create symlinks from the
   various external repositories to the MAIN directory.

		$ phing link
		> phing link

1. Creating a dev release installation package

   This creates the distributable ZIP packages of Kickstart inside the MAIN/release directory. Moreover, the directory `source/output` will contain the file `restore.php` which can be used either standalone or as an include to another application which needs to perform staggered extraction of JPA, JPS and ZIP packages.

		$ phing git
		> phing git

1. Build the documentation in PDF format

		$ phing documentation
		> phing documentation
