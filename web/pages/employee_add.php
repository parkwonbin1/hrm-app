<?php
include __DIR__ . "/../auth/role_admin.php";
include __DIR__ . "/../config/db.php";
include __DIR__ . "/../config/minio.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pw    = hash('sha256', $_POST['password']);
    $dept  = $_POST['department'];
    $job   = $_POST['job_title'];
    $pos   = $_POST['position'];
    $hire  = $_POST['hire_date'];
    $role  = $_POST['role'];
    $tech  = $_POST['tech_stack'];

    $profile_url = null;
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['profile_img']['tmp_name'];
        $origName = basename($_FILES['profile_img']['name']);
        $uniqueName = uniqid('profile_') . '_' . $origName;
        $s3->putObject([
            'Bucket' => $MINIO_BUCKET,
            'Key'    => $uniqueName,
            'SourceFile' => $tmpPath,
            'ACL'    => 'public-read',
        ]);
        $profile_url = $MINIO_PUBLIC . '/' . $uniqueName;
    }

    $sql = "INSERT INTO employees (name,email,password,department,job_title,position,hire_date,role,tech_stack,profile_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssss', $name, $email, $pw, $dept, $job, $pos, $hire, $role, $tech, $profile_url);
    if ($stmt->execute()) {
        echo "<script>alert('등록 완료');location.href='index.php?page=employees_list';</script>";
        exit;
    } else {
        echo "<script>alert('등록 실패');</script>";
    }
    $stmt->close();
}
?>

<div style="display:flex; justify-content:center; align-items:center; min-height:calc(100vh - 140px);">
    <div class="card" style="max-width:800px; width:100%; padding:2rem;">

        <div style="margin-bottom:2rem; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <h1 style="margin-bottom:0.5rem;">직원 추가</h1>
                <p class="text-muted">새로운 직원을 시스템에 등록합니다</p>
            </div>
            <a href="index.php?page=employees_list" class="btn btn-secondary">목록으로</a>
        </div>

        <form method="POST" enctype="multipart/form-data">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

                <div class="form-group">
                    <label class="form-label">이름 <span style="color:var(--danger-500)">*</span></label>
                    <input class="form-control" name="name" required placeholder="홍길동">
                </div>

                <div class="form-group">
                    <label class="form-label">이메일 <span style="color:var(--danger-500)">*</span></label>
                    <input class="form-control" name="email" type="email" required placeholder="name@company.com">
                </div>

                <div class="form-group">
                    <label class="form-label">비밀번호 <span style="color:var(--danger-500)">*</span></label>
                    <input class="form-control" name="password" type="password" required placeholder="초기 비밀번호 입력">
                </div>

                <div class="form-group">
                    <label class="form-label">프로필 사진</label>
                    <input type="file" class="form-control" name="profile_img" accept="image/*">
                </div>

            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-top:1.5rem;">

                <div class="form-group">
                    <label class="form-label">부서</label>
                    <input class="form-control" name="department" placeholder="예: 개발팀">
                </div>

                <div class="form-group">
                    <label class="form-label">직무</label>
                    <input class="form-control" name="job_title" placeholder="예: 백엔드 개발자">
                </div>

            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-top:1.5rem;">

                <div class="form-group">
                    <label class="form-label">직책</label>
                    <input class="form-control" name="position" placeholder="예: 대리">
                </div>

                <div class="form-group">
                    <label class="form-label">입사일</label>
                    <input type="date" class="form-control" name="hire_date">
                </div>

            </div>

            <div class="form-group" style="margin-top:1.5rem;">
                <label class="form-label">권한</label>
                <select class="form-control" name="role">
                    <option value="USER">USER</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
            </div>

            <div class="form-group" style="margin-top:1.5rem;">
                <label class="form-label">기술스택</label>
                <textarea class="form-control" name="tech_stack" rows="4" placeholder="쉼표로 구분"></textarea>
            </div>

            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--slate-100); display:flex; justify-content:flex-end; gap:1rem;">
                <a href="index.php?page=employees_list" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary" style="padding-left:2rem; padding-right:2rem;">직원 등록</button>
            </div>

        </form>

    </div>
</div>