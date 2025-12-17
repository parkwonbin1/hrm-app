<?php
$role = $_SESSION['role'];
?>
<div class="topnav">
    <div class="nav-container">
        <div class="nav-left">
            <a href="index.php?page=home" class="logo">
                <span class="logo-icon">🏢</span>
                <span class="logo-text">HRM System</span>
            </a>

            <nav class="nav-menu">
                <a href="index.php?page=home" class="menu-item <?= $page=='home'?'active':'' ?>">
                    <span class="menu-icon">🏠</span>
                    <span class="menu-text">홈</span>
                </a>

                <?php if($role=='ADMIN'): ?>
                    <a href="index.php?page=employees_list" class="menu-item <?= $page=='employees_list'?'active':'' ?>">
                        <span class="menu-icon">👥</span>
                        <span class="menu-text">직원 관리</span>
                    </a>
                <?php endif; ?>

                <a href="index.php?page=attendance" class="menu-item <?= $page=='attendance'?'active':'' ?>">
                    <span class="menu-icon">📅</span>
                    <span class="menu-text">근태 관리</span>
                </a>

                <a href="index.php?page=my_profile" class="menu-item <?= $page=='my_profile'?'active':'' ?>">
                    <span class="menu-icon">👤</span>
                    <span class="menu-text">내 정보</span>
                </a>
            </nav>
        </div>

        <div class="nav-right">
            <div class="user-profile">
                <div class="user-avatar">
                    <?= mb_substr($_SESSION['name'], 0, 1) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['name'] ?></span>
                    <span class="user-role"><?= $role == 'ADMIN' ? '관리자' : '직원' ?></span>
                </div>
            </div>
            <a href="auth/logout.php" class="logout-btn" title="로그아웃">
                <span class="logout-icon">🚪</span>
            </a>
        </div>
    </div>
</div>