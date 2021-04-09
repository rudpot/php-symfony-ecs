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
        // // The same options that can be provided to a specific client constructor can also be supplied to the Aws\Sdk class.
        // // Use the us-west-2 region and latest version of each client.
        // $sharedConfig = [
        //     'region' => 'us-west-2',
        //     'version' => 'latest'
        // ];

        // // Create an SDK class used to share configuration across clients.
        // $sdk = new \Aws\Sdk($sharedConfig);

        // // Create an Amazon S3 client using the shared configuration data.
        // $s3client = $sdk->createS3();

        //Create a S3Client
        $s3client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'us-west-2'
        ]);

        $buckets = $s3client->listBuckets([]);
        // $objects = [];
        $objects = $s3client->listObjects(['Bucket' => "rudpot-sam-templates"]);
        

        return new Response(
            '<html><body>We did not blow up<br />'.json_encode($buckets['Buckets']).'<br />'.json_encode($objects['Contents']).'</body></html>'
        );
    }
}
