<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   2008-2017 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/**
 * Base class for request signing objects.
 */
abstract class AKS3Signature
{
    /**
     * The request we will be signing
     *
     * @var  AKS3Request
     */
    protected $request = null;

    /**
     * Signature constructor.
     *
     * @param   AKS3Request  $request  The request we will be signing
     */
    public function __construct(AKS3Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the authorization header for the request
     *
     * @return  string
     */
    abstract public function getAuthorizationHeader();

    /**
     * Pre-process the request headers before we convert them to cURL-compatible format. Used by signature engines to
     * add custom headers, e.g. x-amz-content-sha256
     *
     * @param   array  $headers     The associative array of headers to process
     * @param   array  $amzHeaders  The associative array of amz-* headers to process
     *
     * @return  void
     */
    abstract public function preProcessHeaders(&$headers, &$amzHeaders);

    /**
     * Get a pre-signed URL for the request. Typically used to pre-sign GET requests to objects, i.e. give shareable
     * pre-authorized URLs for downloading files from S3.
     *
     * @param   integer  $lifetime    Lifetime in seconds
     * @param   boolean  $https       Use HTTPS ($hostBucket should be false for SSL verification)?
     *
     * @return  string  The presigned URL
     */
    abstract public function getAuthenticatedURL($lifetime = null, $https = false);

    /**
     * Get a signature object for the request
     *
     * @param   AKS3Request  $request  The request which needs signing
     * @param   string   $method   The signature method, "v2" or "v4"
     *
     * @return  AKS3Signature
     */
    public static function getSignatureObject(AKS3Request $request, $method = 'v2')
    {
        $className = 'AKS3Signature' . ucfirst($method);

        return new $className($request);
    }
}