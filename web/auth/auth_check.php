<?php
if (!isset($_SESSION['emp_id'])) {
    // 출력 버퍼가 있으면 정리
    if (ob_get_level()) ob_end_clean();
    
    header("Location: auth/login.php");
    exit();
}
?>