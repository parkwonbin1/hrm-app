<?php
include "../config/db.php";
include "../config/minio.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid Access");
}

$emp_id = $_POST['emp_id'];

// 기존 정보 불러오기
$res = $conn->query("SELECT * FROM employees WHERE emp_id='$emp_id'");
$emp = $res->fetch_assoc();

$name  = $_POST['name'];
$dept  = $_POST['department'];
$job   = $_POST['job_title'];
$pos   = $_POST['position'];
$hire  = $_POST['hire_date'];
$role  = $_POST['role'];
$tech  = $_POST['tech_stack'];

$profile_url = $emp['profile_image_url']; // 기존 이미지 유지

// 삭제 체크한 경우
if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
    $profile_url = null;
}

// 새 파일 업로드된 경우
if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['profile_img']['tmp_name'];
    $orig = basename($_FILES['profile_img']['name']);
    $unique = uniqid("profile_") . "_" . $orig;

    // MinIO 업로드
    $s3->putObject([
        'Bucket' => $MINIO_BUCKET,
        'Key' => $unique,
        'SourceFile' => $tmp,
        'ACL' => 'public-read'
    ]);

    $profile_url = $MINIO_PUBLIC . "/" . $unique;
}

// 최종 UPDATE
$stmt = $conn->prepare("
    UPDATE employees SET
        name=?, department=?, job_title=?, position=?,
        hire_date=?, role=?, tech_stack=?, profile_image_url=?
    WHERE emp_id=?
");

$stmt->bind_param("ssssssssi",
    $name, $dept, $job, $pos,
    $hire, $role, $tech, $profile_url,
    $emp_id
);

$stmt->execute();
$stmt->close();

echo "OK";
?>
