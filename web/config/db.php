<?php
// 환경변수에서 가져오고, 없으면 기본값(로컬 테스트용) 사용
$host = getenv('DB_HOST') ?: "172.16.6.141";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "soldesk5.";
$db   = getenv('DB_NAME') ?: "hrm_db";

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

mysqli_real_connect($conn, $host, $user, $pass, $db, 3306, null, 0);

if (mysqli_connect_errno()) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
?>
