<?php
include "./config/db.php";
include "./auth/role_admin.php";

// =========================
// 페이지네이션 설정
// =========================
$limit = 10;
$page  = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// =========================
// 검색 파라미터 처리
// =========================
$field   = $_GET['field']   ?? 'all';
$keyword = $_GET['keyword'] ?? '';

// =========================
// 검색 WHERE 조건 생성
// =========================
$where = "";

if ($keyword !== "") {
    $safeKeyword = $conn_read->real_escape_string($keyword);

    if ($field === "all") {
        $where = "WHERE 
            name LIKE '%$safeKeyword%' OR
            department LIKE '%$safeKeyword%' OR
            job_title LIKE '%$safeKeyword%' OR
            position LIKE '%$safeKeyword%' OR
            email LIKE '%$safeKeyword%'";
    } else {
        $safeField = $conn_read->real_escape_string($field);
        $where = "WHERE $safeField LIKE '%$safeKeyword%'";
    }
}

// 총 데이터 수
$countRes = $conn_read->query("SELECT COUNT(*) AS total FROM employees $where");
$totalRows = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// 현재 페이지 데이터 
$sql = "SELECT * FROM employees $where ORDER BY emp_id DESC LIMIT $start, $limit";
$res = $conn_read->query($sql);
?>

<div class="content">
    <!-- 페이지 헤더 -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">👥 직원 관리</h1>
            <p class="text-muted">조직의 모든 직원 정보를 한눈에 확인하고 관리하세요</p>
        </div>
        <a href="?page=employee_add" class="btn btn-primary">
            + 직원 추가
        </a>
    </div>

    <!-- 상단 통계 바 -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: var(--radius); background: var(--primary-50); color: var(--primary-600); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                👤
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.5rem;"><?= number_format($totalRows) ?></h3>
                <p class="text-muted" style="margin: 0; font-size: 0.875rem;">총 직원 수</p>
            </div>
        </div>
    </div>

    <!-- 검색 영역 -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="min-width: 140px;">
                <select id="search-field" class="form-control">
                    <option value="all" <?= $field === 'all' ? 'selected' : '' ?>>전체</option>
                    <option value="name" <?= $field === 'name' ? 'selected' : '' ?>>이름</option>
                    <option value="department" <?= $field === 'department' ? 'selected' : '' ?>>부서</option>
                    <option value="job_title" <?= $field === 'job_title' ? 'selected' : '' ?>>직무</option>
                    <option value="position" <?= $field === 'position' ? 'selected' : '' ?>>직책</option>
                    <option value="email" <?= $field === 'email' ? 'selected' : '' ?>>이메일</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <input type="text" 
                       id="search-input" 
                       class="form-control" 
                       placeholder="검색어를 입력하세요..."
                       value="<?= htmlspecialchars($keyword) ?>"
                       onkeypress="if(event.key==='Enter') searchEmployees()">
            </div>

            <button onclick="searchEmployees()" class="btn btn-primary">
                🔍 검색
            </button>
        </div>
    </div>

    <!-- 직원 목록 테이블 -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>이름</th>
                        <th>부서</th>
                        <th>직무</th>
                        <th>직책</th>
                        <th>입사일</th>
                        <th>이메일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res->num_rows > 0): ?>
                        <?php while($row = $res->fetch_assoc()): ?>
                            <tr onclick="openEmployee(<?= $row['emp_id'] ?>)" style="cursor: pointer;">
                                <td style="font-weight: 600; color: var(--slate-900);"><?= htmlspecialchars($row['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['department'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['job_title'] ?? '') ?></td>
                                <td><span class="badge badge-primary"><?= htmlspecialchars($row['position'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($row['hire_date'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem; color: var(--slate-400);">
                                검색 결과가 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 페이지네이션 -->
    <?php if($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
        <?php if($page > 1): ?>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=1" class="btn btn-secondary" style="padding: 0.5rem;">≪</a>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=<?= $page - 1 ?>" class="btn btn-secondary" style="padding: 0.5rem;">‹</a>
        <?php endif; ?>

        <?php 
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        for($i = $startPage; $i <= $endPage; $i++): 
        ?>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=<?= $i ?>" 
               class="btn <?= ($page == $i ? 'btn-primary' : 'btn-secondary') ?>" style="min-width: 2.5rem;">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($endPage < $totalPages): ?>
            <span style="display: flex; align-items: center; padding: 0 0.5rem; color: var(--slate-400);">...</span>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=<?= $totalPages ?>" class="btn btn-secondary" style="min-width: 2.5rem;">
                <?= $totalPages ?>
            </a>
        <?php endif; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=<?= $page + 1 ?>" class="btn btn-secondary" style="padding: 0.5rem;">›</a>
            <a href="?page=employees_list&field=<?= $field ?>&keyword=<?= urlencode($keyword) ?>&p=<?= $totalPages ?>" class="btn btn-secondary" style="padding: 0.5rem;">≫</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="modal-area"></div>

<script>
    function openEmployee(id) {
        fetch("modal/employee_modal.php?view=" + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById("modal-area").innerHTML = html;
                document.body.style.overflow = "hidden";

                const scripts = document.querySelectorAll("#modal-area script");
                scripts.forEach(oldScript => {
                    const newScript = document.createElement("script");
                    newScript.text = oldScript.textContent;
                    document.body.appendChild(newScript).remove();
                });
            });
    }

    function closeEmployeeModal() {
        document.getElementById("modal-area").innerHTML = "";
        document.body.style.overflow = "auto";
        location.reload();
    }

    function searchEmployees() {
        const field = document.getElementById('search-field').value;
        const keyword = document.getElementById('search-input').value;

        location.href = `?page=employees_list&field=${field}&keyword=${encodeURIComponent(keyword)}&p=1`;
    }
</script>
