<?php
include "./config/db.php";

// 세션 시작 (이미 시작됐으면 무시)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$me = $_SESSION['emp_id'] ?? null;
if (!$me) {
    exit("잘못된 접근");
}

$sql = "SELECT * FROM employees WHERE emp_id='$me'";
$res = $conn->query($sql);
$emp = $res->fetch_assoc();
?>
<div class="content" style="display:flex; justify-content:center; align-items:center; min-height:calc(100vh - 140px);">
    <div class="card" style="max-width:800px; width:100%; padding:2rem;">
        <!-- 프로필 헤더 -->
        <div style="display:flex; align-items:center; gap:1.5rem; padding-bottom:2rem; border-bottom:1px solid var(--slate-100); margin-bottom:2rem;">
            <div style="width:120px; height:120px; border-radius:50%; overflow:hidden; border:4px solid var(--slate-50); box-shadow:var(--shadow);">
                <img src="<?= $emp['profile_image_url'] ?: 'https://via.placeholder.com/120' ?>" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div>
                <h2 style="margin-bottom:0.25rem;">
                    <?= htmlspecialchars($emp['name']) ?>
                </h2>
                <p class="text-muted" style="font-size:1.125rem; margin:0;">
                    <?= htmlspecialchars($emp['job_title'] ?: $emp['role']) ?>
                </p>
            </div>
        </div>

        <!-- 인사정보 -->
        <h3 style="font-size:1.25rem; margin-bottom:1rem;">인사정보</h3>
        <div style="background:var(--slate-50); border-radius:var(--radius); padding:1.5rem; margin-bottom:2rem;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:1.5rem;">
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">부서</div>
                    <div style="font-weight:500;"><?= htmlspecialchars($emp['department']) ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">직책</div>
                    <div style="font-weight:500;"><?= htmlspecialchars($emp['position']) ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">입사일</div>
                    <div style="font-weight:500;"><?= htmlspecialchars($emp['hire_date']) ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">역할</div>
                    <span class="badge <?= $emp['role'] === 'ADMIN' ? 'badge-primary' : 'badge-gray' ?>">
                        <?= htmlspecialchars($emp['role']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- 기본정보 -->
        <h3 style="font-size:1.25rem; margin-bottom:1rem;">기본정보</h3>
        <div style="background:var(--slate-50); border-radius:var(--radius); padding:1.5rem;">
            <div style="display:grid; gap:1.5rem;">
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">이메일</div>
                    <div style="font-weight:500;"><?= htmlspecialchars($emp['email']) ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm" style="margin-bottom:0.25rem;">기술스택</div>
                    <div style="font-weight:500; line-height:1.6;"><?= nl2br(htmlspecialchars($emp['tech_stack'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
