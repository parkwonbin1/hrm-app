<?php
require __DIR__ . "/../vendor/autoload.php";
use Aws\S3\S3Client;

$minio_host = getenv('MINIO_HOST') ?: "172.16.6.143";
$minio_port = getenv('MINIO_PORT') ?: "9000";

$MINIO_ENDPOINT = "http://{$minio_host}:{$minio_port}";
// 외부 접속 URL (Ingress 주소로 나중에 교체됨)
$MINIO_PUBLIC   = getenv('MINIO_PUBLIC_URL') ?: "http://{$minio_host}:{$minio_port}/hrm-profile";
$MINIO_BUCKET   = getenv('MINIO_BUCKET') ?: "hrm-profile";

$MINIO_KEY      = getenv('MINIO_ACCESS_KEY') ?: "admin";
$MINIO_SECRET   = getenv('MINIO_SECRET_KEY') ?: "admin1234";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1',
    'endpoint' => $MINIO_ENDPOINT,
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => $MINIO_KEY,
        'secret' => $MINIO_SECRET,
    ],
]);
?>
