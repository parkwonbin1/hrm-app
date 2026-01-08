<?php
// 환경변수에서 가져오고, 없으면 기본값(로컬 테스트용) 사용
$read_host  = getenv('DB_READ_HOST')  ?: "172.16.6.141";
$write_host = getenv('DB_WRITE_HOST') ?: "172.16.6.141";

$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "soldesk5.";
$db   = getenv('DB_NAME') ?: "hrm_db";
$db_port = getenv('DB_PORT') ?: 3306;

$conn_read = new mysqli($read_host, $user, $pass, $db, $db_port);
if ($conn_read->connect_error) {
    die("Read Connection failed: " . $conn_read->connect_error);
}

// 3. [쓰기 전용] 연결 생성 (INSERT/UPDATE용) -> 온프레미스 Master DB
$conn_write = new mysqli($write_host, $user, $pass, $db, $db_port);
if ($conn_write->connect_error) {
    die("Write Connection failed: " . $conn_write->connect_error);
}
?>
