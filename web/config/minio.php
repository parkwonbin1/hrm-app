<?php
require __DIR__ . "/../vendor/autoload.php";
use Aws\S3\S3Client;

// =================================================================
// 1. [WRITE 전략] 업로드/삭제는 무조건 '온프레미스 MinIO'가 원본(Master)
//    클라우드(K3s)에서도 VPN을 통해 이 MinIO IP로 접속해서 업로드해야 함.
// =================================================================
$minio_host = getenv('MINIO_HOST') ?: "172.16.6.143"; // 온프레미스 내부 IP
$minio_port = getenv('MINIO_PORT') ?: "9000";
$bucket     = getenv('MINIO_BUCKET') ?: "hrm-bucket-soldesk";

$minio_key    = getenv('MINIO_ACCESS_KEY') ?: "admin";
$minio_secret = getenv('MINIO_SECRET_KEY') ?: "admin1234";

// 업로드/삭제용 클라이언트 (항상 MinIO)
$s3_writer = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1', // MinIO는 region 무관하지만 형식상 필요
    'endpoint' => "http://{$minio_host}:{$minio_port}",
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => $minio_key,
        'secret' => $minio_secret,
    ],
]);

// =================================================================
// 2. [READ 전략] 조회는 환경에 따라 '가까운 곳'에서 읽음
//    온프레미스 -> MinIO 주소 반환
//    AWS 클라우드 -> S3 주소 반환 (VPN 트래픽 절약 & 속도 향상)
// =================================================================

// 환경변수 STORAGE_MODE가 'AWS'면 S3 URL, 아니면 MinIO URL 사용
$storage_mode = getenv('STORAGE_MODE') ?: "ONPREM"; 

if ($storage_mode === 'AWS') {
    // [AWS 모드] S3 버킷의 퍼블릭 URL (CloudFront를 쓴다면 그 주소)
    // 예: https://hrm-profile-backup.s3.ap-northeast-2.amazonaws.com
    $base_url = "https://" . getenv('AWS_S3_BUCKET_URL');
} else {
    // [온프레미스 모드] MinIO의 외부 접속 URL (Ingress 주소)
    // 예: http://minio.hrm.com/hrm-profile
    $public_host = getenv('MINIO_PUBLIC_URL') ?: "http://{$minio_host}:{$minio_port}";
    $base_url = "{$public_host}/{$bucket}";
}

/**
 * 이미지 전체 URL을 만들어주는 도우미 함수
 * 예: get_image_url('user1.jpg') -> http://minio.../user1.jpg 또는 https://s3.../user1.jpg
 */
if (!function_exists('get_image_url')) {
    function get_image_url($filename) {
        global $base_url;
        if (empty($filename)) return "/assets/img/default_profile.png"; // 기본 이미지
        return "{$base_url}/{$filename}";
    }
}
?>
