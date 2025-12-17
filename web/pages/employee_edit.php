<?php
include "./auth/role_admin.php";
include "./config/db.php";

$id = $_GET['id'];

$sql = "SELECT * FROM employees WHERE emp_id='$id'";
$res = $conn->query($sql);
$emp = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD']=='POST') {

    $name  = $_POST['name'];
    $dept  = $_POST['department'];
    $job   = $_POST['job_title'];
    $pos   = $_POST['position'];
    $hire  = $_POST['hire_date'];
    $role  = $_POST['role'];
    $tech  = $_POST['tech_stack'];

    $sql = "
        UPDATE employees SET
            name='$name',
            department='$dept',
            job_title='$job',
            position='$pos',
            hire_date='$hire',
            role='$role',
            tech_stack='$tech'
        WHERE emp_id='$id'
    ";

    $conn->query($sql);
    echo "<script>alert('수정 완료');location.href='index.php?page=employee_view&id=$id';</script>";
}
?>


<h3>직원 수정</h3>

<form method="POST">
    <label>이름</label>
    <input class="form-control" name="name" value="<?= $emp['name'] ?>">

    <label>부서</label>
    <input class="form-control" name="department" value="<?= $emp['department'] ?>">

    <label>직무</label>
    <input class="form-control" name="job_title" value="<?= $emp['job_title'] ?>">

    <label>직책</label>
    <input class="form-control" name="position" value="<?= $emp['position'] ?>">

    <label>입사일</label>
    <input type="date" class="form-control" name="hire_date" value="<?= $emp['hire_date'] ?>">

    <label>권한</label>
    <select class="form-control" name="role">
        <option <?= $emp['role']=='USER'?'selected':'' ?>>USER</option>
        <option <?= $emp['role']=='ADMIN'?'selected':'' ?>>ADMIN</option>
    </select>

    <label>기술스택</label>
    <textarea class="form-control" name="tech_stack"><?= $emp['tech_stack'] ?></textarea>

    <button class="btn btn-primary mt-3">저장</button>
</form>

