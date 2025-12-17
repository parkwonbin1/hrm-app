<?php
if ($_SESSION['role'] !== 'ADMIN') {
    echo "관리자만 접근할 수 있습니다.";
    exit();
}
?>

