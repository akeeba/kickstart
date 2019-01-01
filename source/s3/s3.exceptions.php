<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     kickstart
 */

/**
 * Configuration error
 */
abstract class AKS3ConfigurationError extends \RuntimeException
{

}

/**
 * Invalid Amazon S3 access key
 */
class AKS3InvalidAccessKey extends AKS3ConfigurationError
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The Amazon S3 Access Key provided is invalid';
        }

        parent::__construct($message, $code, $previous);
    }

}

/**
 * Invalid Amazon S3 secret key
 */
class AKS3InvalidSecretKey extends AKS3ConfigurationError
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The Amazon S3 Secret Key provided is invalid';
        }

        parent::__construct($message, $code, $previous);
    }

}

/**
 * Invalid Amazon S3 signature method
 */
class AKS3InvalidSignatureMethod extends AKS3ConfigurationError
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The Amazon S3 signature method provided is invalid. Only v2 and v4 signatures are supported.';
        }

        parent::__construct($message, $code, $previous);
    }

}

/**
 * Invalid Amazon S3 region
 */
class AKS3InvalidRegion extends AKS3ConfigurationError
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The Amazon S3 region provided is invalid.';
        }

        parent::__construct($message, $code, $previous);
    }

}

/**
 * Invalid Amazon S3 endpoint
 */
class AKS3InvalidEndpoint extends AKS3ConfigurationError
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The custom S3 endpoint provided is invalid. Do NOT include the protocol (http:// or https://). Valid examples are s3.example.com and www.example.com/s3Api';
        }

        parent::__construct($message, $code, $previous);
    }

}

class AKS3CannotOpenFileForWrite extends \RuntimeException
{
    public function __construct($file = "", $code = 0, Exception $previous = null)
    {
        $message = "Cannot open $file for writing";

        parent::__construct($message, $code, $previous);
    }

}

class AKS3CannotGetFile extends \RuntimeException
{
}

class AKS3CannotGetBucket extends \RuntimeException
{
}

class AKS3CannotListBuckets extends \RuntimeException
{
}

class AKS3InvalidFilePointer extends \InvalidArgumentException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message))
        {
            $message = 'The specified file pointer is not a valid stream resource';
        }

        parent::__construct($message, $code, $previous);
    }

}

class AKS3CannotOpenFileForRead extends \RuntimeException
{
    public function __construct($file = "", $code = 0, Exception $previous = null)
    {
        $message = "Cannot open $file for reading";

        parent::__construct($message, $code, $previous);
    }

}

/**
 * Invalid magic property name
 */
class AKS3PropertyNotFound extends \LogicException
{
}
