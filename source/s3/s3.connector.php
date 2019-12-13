<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class AKS3Connector
{
    /**
     * Amazon S3 configuration object
     *
     * @var  AKS3Configuration
     */
    private $configuration = null;

    /**
     * Connector constructor.
     *
     * @param   AKS3Configuration   $configuration  The configuration object to use
     */
    public function __construct(AKS3Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get (download) an object
     *
     * @param   string  $bucket  Bucket name
     * @param   string  $uri     Object URI
     * @param   mixed   $saveTo  Filename or resource to write to
     * @param   int     $from    Start of the download range, null to download the entire object
     * @param   int     $to      End of the download range, null to download the entire object
     *
     * @return  void|string  No return if $saveTo is specified; data as string otherwise
     *
     * @throws  AKS3CannotOpenFileForWrite
     * @throws  AKS3CannotGetFile
     */
    public function getObject($bucket, $uri, $saveTo = false, $from = null, $to = null)
    {
        $request = new AKS3Request('GET', $bucket, $uri, $this->configuration);

        $fp = null;

        if (!is_resource($saveTo) && is_string($saveTo))
        {
            $fp = @fopen($saveTo, 'wb');

            if ($fp === false)
            {
                throw new AKS3CannotOpenFileForWrite($saveTo);
            }
        }

        if (is_resource($saveTo))
        {
            $fp = $saveTo;
        }

        if (is_resource($fp))
        {
            $request->setFp($fp);
        }

        // Set the range header
        if ((!empty($from) && !empty($to)) || (!is_null($from) && !empty($to)))
        {
            $request->setHeader('Range', "bytes=$from-$to");
        }

        $response = $request->getResponse();

        if (!$response->error->isError() && (($response->code !== 200) && ($response->code !== 206)))
        {
            $response->error = new AKS3Error(
                $response->code,
                "Unexpected HTTP status {$response->code}"
            ) ;
        }

        if ($response->error->isError())
        {
            throw new AKS3CannotGetFile(
                sprintf(__METHOD__ . "({$bucket}, {$uri}): [%s] %s\n\nDebug info:\n%s",
                    $response->error->getCode(), $response->error->getMessage(), print_r($response->body, true)),
                $response->error->getCode()
            );
        }

        if (!is_resource($fp))
        {
            return $response->body;
        }

        return null;
    }

    /**
     * Get the location (region) of a bucket. You need this to use the V4 API on that bucket!
     *
     * @param   string   $bucket  Bucket name
     *
     * @return  string
     */
    public function getBucketLocation($bucket)
    {
        $request = new AKS3Request('GET', $bucket, '', $this->configuration);
        $request->setParameter('location', null);

        $response = $request->getResponse();

        if (!$response->error->isError() && $response->code !== 200)
        {
            $response->error = new AKS3Error(
                $response->code,
                "Unexpected HTTP status {$response->code}"
            );
        }

        if ($response->error->isError())
        {
            throw new AKS3CannotGetBucket(
                sprintf(__METHOD__ . "(): [%s] %s", $response->error->getCode(), $response->error->getMessage()),
                $response->error->getCode()
            );
        }

        $result = 'us-east-1';

        if ($response->hasBody())
        {
            $result = (string) $response->body;
        }

        switch ($result)
        {
            // "EU" is an alias for 'eu-west-1', however the canonical location name you MUST use is 'eu-west-1'
            case 'EU':
            case 'eu':
                $result = 'eu-west-1';
                break;

            // If the bucket location is 'us-east-1' you get an empty string. @#$%^&*()!!
            case '':
                $result = 'us-east-1';
                break;
        }

        return $result;
    }

    /**
     * Get the contents of a bucket
     *
     * If maxKeys is null this method will loop through truncated result sets
     *
     * @param   string   $bucket                Bucket name
     * @param   string   $prefix                Prefix (directory)
     * @param   string   $marker                Marker (last file listed)
     * @param   string   $maxKeys               Maximum number of keys ("files" and "directories") to return
     * @param   string   $delimiter             Delimiter, typically "/"
     * @param   boolean  $returnCommonPrefixes  Set to true to return CommonPrefixes
     *
     * @return  array
     */
    public function getBucket($bucket, $prefix = null, $marker = null, $maxKeys = null, $delimiter = '/', $returnCommonPrefixes = false)
    {
        $request = new AKS3Request('GET', $bucket, '', $this->configuration);

        if (!empty($prefix))
        {
            $request->setParameter('prefix', $prefix);
        }

        if (!empty($marker))
        {
            $request->setParameter('marker', $marker);
        }

        if (!empty($maxKeys))
        {
            $request->setParameter('max-keys', $maxKeys);
        }

        if (!empty($delimiter))
        {
            $request->setParameter('delimiter', $delimiter);
        }

        $response = $request->getResponse();

        if (!$response->error->isError() && $response->code !== 200)
        {
            $response->error = new AKS3Error(
                $response->code,
                "Unexpected HTTP status {$response->code}"
            );
        }

        if ($response->error->isError())
        {
            throw new AKS3CannotGetBucket(
                sprintf(__METHOD__ . "(): [%s] %s", $response->error->getCode(), $response->error->getMessage()),
                $response->error->getCode()
            );
        }

        $results = array();

        $nextMarker = null;

        if ($response->hasBody() && isset($response->body->Contents))
        {
            foreach ($response->body->Contents as $c)
            {
                $results[(string)$c->Key] = array(
                    'name' => (string)$c->Key,
                    'time' => strtotime((string)$c->LastModified),
                    'size' => (int)$c->Size,
                    'hash' => substr((string)$c->ETag, 1, -1)
                );

                $nextMarker = (string)$c->Key;
            }
        }

        if ($returnCommonPrefixes && $response->hasBody() && isset($response->body->CommonPrefixes))
        {
            foreach ($response->body->CommonPrefixes as $c)
            {
                $results[(string)$c->Prefix] = array('prefix' => (string)$c->Prefix);
            }
        }

        if ($response->hasBody() && isset($response->body->IsTruncated) &&
            ((string)$response->body->IsTruncated == 'false')
        )
        {
            return $results;
        }

        if ($response->hasBody() && isset($response->body->NextMarker))
        {
            $nextMarker = (string)$response->body->NextMarker;
        }

        // Loop through truncated results if maxKeys isn't specified
        if ($maxKeys == null && $nextMarker !== null && ((string)$response->body->IsTruncated == 'true'))
        {
            do
            {
                $request = new AKS3Request('GET', $bucket, '', $this->configuration);

                if (!empty($prefix))
                {
                    $request->setParameter('prefix', $prefix);
                }

                $request->setParameter('marker', $nextMarker);

                if (!empty($delimiter))
                {
                    $request->setParameter('delimiter', $delimiter);
                }

                try
                {
                    $response = $request->getResponse();
                }
                catch (\Exception $e)
                {
                    break;
                }

                if ($response->hasBody() && isset($response->body->Contents))
                {
                    foreach ($response->body->Contents as $c)
                    {
                        $results[(string)$c->Key] = array(
                            'name' => (string)$c->Key,
                            'time' => strtotime((string)$c->LastModified),
                            'size' => (int)$c->Size,
                            'hash' => substr((string)$c->ETag, 1, -1)
                        );

                        $nextMarker = (string)$c->Key;
                    }
                }

                if ($returnCommonPrefixes && $response->hasBody() && isset($response->body->CommonPrefixes))
                {
                    foreach ($response->body->CommonPrefixes as $c)
                    {
                        $results[(string)$c->Prefix] = array('prefix' => (string)$c->Prefix);
                    }
                }

                if ($response->hasBody() && isset($response->body->NextMarker))
                {
                    $nextMarker = (string)$response->body->NextMarker;
                }
            }
            while (!$response->error->isError() && (string)$response->body->IsTruncated == 'true');
        }

        return $results;
    }

    /**
     * Get a list of buckets
     *
     * @param   boolean  $detailed  Returns detailed bucket list when true
     *
     * @return  array
     */
    public function listBuckets($detailed = false)
    {
        // When listing buckets with the AWSv4 signature method we MUST set the region to us-east-1. Don't ask...
        $configuration = clone $this->configuration;
        $configuration->setRegion('us-east-1');

        $request = new AKS3Request('GET', '', '', $configuration);
        $response = $request->getResponse();

        if (!$response->error->isError() && (($response->code !== 200)))
        {
            $response->error = new AKS3Error(
                $response->code,
                "Unexpected HTTP status {$response->code}"
            ) ;
        }

        if ($response->error->isError())
        {
            throw new AKS3CannotListBuckets(
                sprintf(__METHOD__ . "(): [%s] %s", $response->error->getCode(), $response->error->getMessage()),
                $response->error->getCode()
            );
        }

        $results = array();

        if (!isset($response->body->Buckets))
        {
            return $results;
        }

        if ($detailed)
        {
            if (isset($response->body->Owner, $response->body->Owner->ID, $response->body->Owner->DisplayName))
            {
                $results['owner'] = array(
                    'id' => (string)$response->body->Owner->ID,
                    'name' => (string)$response->body->Owner->DisplayName
                );
            }

            $results['buckets'] = array();

            foreach ($response->body->Buckets->Bucket as $b)
            {
                $results['buckets'][] = array(
                    'name' => (string)$b->Name,
                    'time' => strtotime((string)$b->CreationDate)
                );
            }
        }
        else
        {
            foreach ($response->body->Buckets->Bucket as $b)
            {
                $results[] = (string)$b->Name;
            }
        }

        return $results;
    }

    /**
     * Returns the configuration object
     *
     * @return  AKS3Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
