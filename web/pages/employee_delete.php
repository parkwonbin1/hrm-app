<?php
include "../config/db.php";
include "../config/minio.php";

$id = $_GET['id'] ?? null;

$res = $conn_write->query("SELECT * FROM employees WHERE emp_id='$id'");
$emp = $res->fetch_assoc();

// 이미지 삭제
if (!empty($emp['profile_image_url'])) {
    $key = basename($emp['profile_image_url']);
    $s3_writer->deleteObject([
        'Bucket' => $MINIO_BUCKET,
        'Key'    => $key
    ]);
}

// DB 삭제
$conn_write->query("DELETE FROM employees WHERE emp_id='$id'");

echo "DELETED";
?>

