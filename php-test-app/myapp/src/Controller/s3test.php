<?php
// src/Controller/s3test.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

require '/app/vendor/autoload.php';

class s3test
{
     /**
      * @Route("/aws/s3test")
      */
    public function number(): Response
    {
        // Create a S3Client using global path to escape symfony namespace
        $s3client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'us-west-2'
        ]);

        $buckets = $s3client->listBuckets([]);
        $bucketstring = '';
        foreach ($buckets['Buckets'] as $bucket) {
            $bucketstring .= $bucket['Name'] . "<br />\n";
        }

        return new Response(
            '<html><body><h1>Buckets in your account</h1><br />'.$bucketstring.'<br /></body></html>'
        );
    }
}
