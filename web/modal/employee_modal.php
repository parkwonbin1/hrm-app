<?php
include __DIR__ . "/../config/db.php";
include __DIR__ . "/../config/minio.php";

$id = $_GET['view'] ?? null;
if (!$id) exit("잘못된 접근");

// 직원 정보 조회
$empRes = $conn->query("SELECT * FROM employees WHERE emp_id='$id'");
$emp = $empRes->fetch_assoc();

// 최신 출근/퇴근 기록 조회 (최근 1건)
$attRes = $conn->query("SELECT * FROM attendance WHERE emp_id='$id' ORDER BY work_date DESC LIMIT 1");
$att = $attRes->fetch_assoc();
?>

<!-- 오버레이 -->
<div class="modal-overlay" onclick="closeEmployeeModal()"></div>

<!-- 상세 모달 -->
<div class="modal-container" id="viewModal">
    <div class="modal-header">
        <h3 class="modal-title"><?= htmlspecialchars($emp['name'] ?? '') ?> 상세정보</h3>
        <button class="modal-close" onclick="closeEmployeeModal()">✕</button>
    </div>
    <div class="modal-body">
        <div style="display: flex; gap: 1.5rem; margin-bottom: 2rem;">
            <div style="flex-shrink: 0;">
                <img src="<?= $emp['profile_image_url'] ?: 'https://via.placeholder.com/120' ?>"
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--slate-50); box-shadow: var(--shadow);">
            </div>
            <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <div class="text-muted text-sm">부서</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($emp['department'] ?? '') ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">직무</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($emp['job_title'] ?? '') ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">직책</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($emp['position'] ?? '') ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">입사일</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($emp['hire_date'] ?? '') ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">이메일</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($emp['email'] ?? '') ?></div>
                </div>
            </div>
        </div>
        <div style="background: var(--slate-50); padding: 1.25rem; border-radius: var(--radius); margin-bottom: 1rem;">
            <div class="text-muted text-sm" style="margin-bottom: 0.25rem;">권한</div>
            <span class="badge <?= $emp['role'] === 'ADMIN' ? 'badge-primary' : 'badge-gray' ?>">
                <?= htmlspecialchars($emp['role'] ?? '') ?>
            </span>
        </div>
        <div style="background: var(--slate-50); padding: 1.25rem; border-radius: var(--radius); margin-bottom: 1rem;">
            <div class="text-muted text-sm" style="margin-bottom: 0.25rem;">기술스택</div>
            <div style="font-size: 0.9375rem; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($emp['tech_stack'] ?? '')) ?>
            </div>
        </div>
        <?php if ($att): ?>
        <div style="background: var(--slate-50); padding: 1.25rem; border-radius: var(--radius);">
            <div class="text-muted text-sm" style="margin-bottom: 0.25rem;">최근 출근/퇴근</div>
            <div style="font-weight: 500;">날짜: <?= htmlspecialchars($att['work_date']) ?></div>
            <div style="font-weight: 500;">출근: <?= htmlspecialchars($att['clock_in_time'] ?? '미기록') ?></div>
            <div style="font-weight: 500;">퇴근: <?= htmlspecialchars($att['clock_out_time'] ?? '미기록') ?></div>
        </div>
        <?php else: ?>
        <div style="background: var(--slate-50); padding: 1.25rem; border-radius: var(--radius);">
            <div class="text-muted text-sm" style="margin-bottom: 0.25rem;">출근 기록이 없습니다.</div>
        </div>
        <?php endif; ?>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" onclick="openEditModal()">수정</button>
        <button class="btn btn-secondary" style="color: var(--danger-600); border-color: var(--danger-200);" onclick="deleteEmployee(<?= $emp['emp_id'] ?>)">삭제</button>
        <button class="btn btn-secondary" onclick="closeEmployeeModal()">닫기</button>
    </div>
</div>

<!-- 수정 모달 -->
<div class="modal-container hidden" id="editModal" style="display: none;">
    <div class="modal-header">
        <h3 class="modal-title">직원 정보 수정</h3>
        <button class="modal-close" onclick="closeEditModal()">✕</button>
    </div>
    <form id="updateForm" enctype="multipart/form-data">
        <input type="hidden" name="emp_id" value="<?= $emp['emp_id'] ?>">
        <div class="modal-body" style="max-height: 60vh; overflow-y: auto; padding-right: 0.5rem;">
            <div class="form-group">
                <label class="form-label">이름</label>
                <input class="form-control" name="name" value="<?= $emp['name'] ?>">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">부서</label>
                    <input class="form-control" name="department" value="<?= $emp['department'] ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">직무</label>
                    <input class="form-control" name="job_title" value="<?= $emp['job_title'] ?>">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">직책</label>
                    <input class="form-control" name="position" value="<?= $emp['position'] ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">입사일</label>
                    <input type="date" class="form-control" name="hire_date" value="<?= $emp['hire_date'] ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">권한</label>
                <select class="form-control" name="role">
                    <option <?= $emp['role']=='USER'?'selected':'' ?> value="USER">USER</option>
                    <option <?= $emp['role']=='ADMIN'?'selected':'' ?> value="ADMIN">ADMIN</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">기술스택</label>
                <textarea class="form-control" name="tech_stack" rows="3"><?= htmlspecialchars($emp['tech_stack'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">프로필 사진 변경</label>
                <input type="file" class="form-control" name="profile_img">
                <?php if ($emp['profile_image_url']): ?>
                <div style="margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--slate-600);">
                        <input type="checkbox" name="delete_image" value="1"> 사진 삭제
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitUpdate()">저장</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">취소</button>
        </div>
    </form>
</div>

<script>
function openEditModal(){
    document.getElementById('viewModal').style.display = 'none';
    document.getElementById('editModal').style.display = 'block';
}
function closeEditModal(){
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('viewModal').style.display = 'block';
}
function submitUpdate(){
    let fd = new FormData(document.getElementById('updateForm'));
    let emp_id = fd.get('emp_id');
    fetch('pages/employee_update_process.php', {method:'POST', body:fd})
        .then(r=>r.text())
        .then(d=>{
            alert('저장 완료!');
            document.getElementById('viewModal')?.remove();
            document.getElementById('editModal')?.remove();
            document.querySelector('.modal-overlay')?.remove();
            fetch('modal/employee_modal.php?view=' + emp_id)
                .then(r=>r.text())
                .then(html=>{document.body.insertAdjacentHTML('beforeend', html); document.body.style.overflow='hidden';});
        });
}
function deleteEmployee(id){
    if(!confirm('정말 삭제하시겠습니까?')) return;
    fetch('pages/employee_delete.php?id=' + id)
        .then(r=>r.text())
        .then(d=>{alert('삭제 완료!'); location.href='index.php?page=employees_list';});
}
function closeEmployeeModal(){
    location.href = "index.php?page=employees_list";
}
</script>
