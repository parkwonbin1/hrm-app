<?php
session_start();

// DB, 세션 로드
include __DIR__ . "/../config/db.php";
include __DIR__ . "/../auth/auth_check.php";

// 타입: in / out
$type = $_GET['type'] ?? null;

$emp_id = $_SESSION['emp_id'];
$today  = date("Y-m-d");

// 오늘 기록 조회
$sql = "SELECT * FROM attendance WHERE emp_id='$emp_id' AND work_date='$today'";
$res = $conn->query($sql);
$att = $res->fetch_assoc();

// ==========================================
// 출근 처리
// ==========================================
if ($type === "in") {

    // 오늘 출근 기록이 없는 경우에만 입력
    if (!$att) {
        $conn->query("
            INSERT INTO attendance(emp_id, work_date, clock_in_time)
            VALUES ('$emp_id', '$today', NOW())
        ");
    }

}
// ==========================================
// 퇴근 처리
// ==========================================
elseif ($type === "out") {

    // 출근 기록이 있고, 퇴근 시간이 아직 없을 때만 퇴근 처리
    if ($att && empty($att['clock_out_time'])) {
        $conn->query("
            UPDATE attendance 
            SET clock_out_time = NOW(),
                total_hours = TIMESTAMPDIFF(MINUTE, clock_in_time, NOW()) / 60
            WHERE emp_id='$emp_id' AND work_date='$today'
        ");
    }
}

// ==========================================
// 공통 리다이렉트 
// ==========================================
header("Location: ../index.php?page=attendance");
exit();
