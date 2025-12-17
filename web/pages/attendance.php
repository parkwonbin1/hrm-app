<?php
include "./config/db.php";

$emp = $_SESSION['emp_id'];
$today = date("Y-m-d");

// 오늘 근태 기록 조회
$sql = "SELECT * FROM attendance WHERE emp_id='$emp' AND work_date='$today'";
$res = $conn->query($sql);
$att = $res->fetch_assoc();

$has_record   = $res->num_rows > 0;
$clocked_in   = $has_record && !empty($att['clock_in_time']);
$clocked_out  = $has_record && !empty($att['clock_out_time']);
?>
<div class="content" style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 200px);">
    <div class="card" style="width: 100%; max-width: 400px; text-align: center; padding: 3rem 2rem;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.75rem;">근태 관리</h2>
        <div style="margin-bottom: 2rem;">
            <div id="currentTime" style="font-size: 3rem; font-weight: 700; color: var(--slate-900); line-height: 1;">0:00:00</div>
            <div style="margin-top: 0.5rem; color: var(--slate-500); font-size: 1rem;">
                <?= date("Y년 m월 d일 (D)") ?>
            </div>
        </div>
        <div style="margin-bottom: 2rem; font-size: 1.125rem; font-weight: 500; color: var(--slate-700);">
            <?php
                if (!$clocked_in) echo "출근 전 입니다.";
                else if (!$clocked_out) echo "근무 중입니다.";
                else echo "퇴근 완료되었습니다.";
            ?>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-primary" onclick="checkIn()" <?= $clocked_in ? 'disabled' : '' ?> style="flex: 1; padding: 1rem; font-size: 1.125rem; opacity: <?= $clocked_in ? '0.5' : '1' ?>; cursor: <?= $clocked_in ? 'not-allowed' : 'pointer' ?>;">출근</button>
            <button class="btn btn-secondary" onclick="checkOut()" <?= (!$clocked_in || $clocked_out) ? 'disabled' : '' ?> style="flex: 1; padding: 1rem; font-size: 1.125rem; opacity: <?= (!$clocked_in || $clocked_out) ? '0.5' : '1' ?>; cursor: <?= (!$clocked_in || $clocked_out) ? 'not-allowed' : 'pointer' ?>;">퇴근</button>
        </div>
    </div>
</div>
<script>
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString("ko-KR", { hour: "2-digit", minute: "2-digit", second:"2-digit" });
    document.getElementById("currentTime").innerText = time;
}
setInterval(updateClock, 1000);
updateClock();
function checkIn() { location.href = "pages/attendance_check.php?type=in"; }
function checkOut() { location.href = "pages/attendance_check.php?type=out"; }
</script>
