<?php
// 출력 버퍼가 없으면 생성
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "auth/auth_check.php";

$page = $_GET['page'] ?? "home";
$view = $_GET['view'] ?? null;

$titles = [
    "home"             => "홈",
    "employees_list"   => "직원 목록",
    "employee_add"     => "직원 추가",
    "employee_edit"    => "직원 수정",
    "my_profile"       => "내 정보",
    "attendance"       => "근태 관리",
];

$page_title = $titles[$page] ?? "HRM 시스템";
?>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <style>
/* Font Import */
@import url("https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/static/pretendard.css");

:root {
    /* Colors */
    --primary-50: #eef2ff;
    --primary-100: #e0e7ff;
    --primary-500: #6366f1;
    --primary-600: #4f46e5;
    --primary-700: #4338ca;

    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1e293b;
    --slate-900: #0f172a;

    --danger-50: #fef2f2;
    --danger-500: #ef4444;
    --danger-600: #dc2626;

    --success-50: #f0fdf4;
    --success-500: #22c55e;
    --success-600: #16a34a;

    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);

    /* Radius */
    --radius-sm: 0.375rem;
    --radius: 0.5rem;
    --radius-md: 0.75rem;
    --radius-lg: 1rem;
    --radius-full: 9999px;
}

/* Reset & Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Pretendard", -apple-system, BlinkMacSystemFont, system-ui, Roboto, sans-serif;
    background-color: var(--slate-50);
    color: var(--slate-800);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

a {
    text-decoration: none;
    color: inherit;
}

button {
    cursor: pointer;
    border: none;
    background: none;
    font-family: inherit;
}

/* Layout */
.content {
    max-width: 1200px;
    margin: 100px auto 40px;
    padding: 0 24px;
    min-height: calc(100vh - 140px);
}

/* Components */
.card {
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    padding: 24px;
    border: 1px solid var(--slate-100);
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    box-shadow: var(--shadow-md);
}

/* Typography */
h1,
h2,
h3,
h4,
h5,
h6 {
    color: var(--slate-900);
    font-weight: 700;
    letter-spacing: -0.025em;
}

h1 {
    font-size: 2.25rem;
    line-height: 2.5rem;
}

h2 {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

h3 {
    font-size: 1.5rem;
    line-height: 2rem;
}

h4 {
    font-size: 1.25rem;
    line-height: 1.75rem;
}

.text-sm {
    font-size: 0.875rem;
}

.text-muted {
    color: var(--slate-500);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-weight: 500;
    border-radius: var(--radius);
    transition: all 0.2s;
    gap: 0.5rem;
}

.btn-primary {
    background-color: var(--primary-600);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-700);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.btn-secondary {
    background-color: white;
    border: 1px solid var(--slate-200);
    color: var(--slate-700);
}

.btn-secondary:hover {
    background-color: var(--slate-50);
    border-color: var(--slate-300);
}

/* Tables */
.table-container {
    overflow-x: auto;
    border-radius: var(--radius-md);
    border: 1px solid var(--slate-200);
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 0.875rem;
}

.table th {
    background-color: var(--slate-50);
    color: var(--slate-600);
    font-weight: 600;
    text-align: left;
    padding: 12px 24px;
    border-bottom: 1px solid var(--slate-200);
}

.table td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--slate-100);
    color: var(--slate-700);
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover td {
    background-color: var(--slate-50);
}

/* Forms */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--slate-700);
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border-radius: var(--radius);
    border: 1px solid var(--slate-300);
    background-color: white;
    color: var(--slate-900);
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-primary {
    background: var(--primary-50);
    color: var(--primary-700);
}

.badge-success {
    background: var(--success-50);
    color: var(--success-600);
}

.badge-danger {
    background: var(--danger-50);
    color: var(--danger-600);
}

.badge-gray {
    background: var(--slate-100);
    color: var(--slate-600);
}



/* Top Navigation */
.topnav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: white;
    border-bottom: 1px solid var(--slate-200);
    z-index: 1000;
    backdrop-filter: blur(8px);
    background: rgba(255, 255, 255, 0.9);
}

.nav-container {
    max-width: 1200px;
    height: 100%;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 40px;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.logo-icon {
    font-size: 24px;
}

.logo-text {
    font-size: 20px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-500) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 8px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    color: var(--slate-500);
    font-size: 0.9375rem;
    font-weight: 500;
    border-radius: var(--radius);
    transition: all 0.2s;
}

.menu-item:hover {
    background-color: var(--slate-50);
    color: var(--slate-900);
}

.menu-item.active {
    background-color: var(--primary-50);
    color: var(--primary-700);
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 6px 12px;
    border-radius: var(--radius-full);
    border: 1px solid var(--slate-200);
    background: white;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.user-info {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--slate-900);
}

.user-role {
    font-size: 0.75rem;
    color: var(--slate-500);
}

.logout-btn {
    padding: 8px;
    color: var(--slate-400);
    border-radius: var(--radius);
    transition: all 0.2s;
}

.logout-btn:hover {
    background-color: var(--danger-50);
    color: var(--danger-600);
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    z-index: 9998;
    animation: fadeIn 0.2s ease-out;
}

.modal-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 2rem;
    width: 90%;
    max-width: 520px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 9999;
    animation: slideUp 0.3s ease-out;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--slate-100);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--slate-900);
}

.modal-close {
    font-size: 1.5rem;
    color: var(--slate-400);
    transition: color 0.2s;
    padding: 0.25rem;
    line-height: 1;
    cursor: pointer;
}

.modal-close:hover {
    color: var(--slate-600);
}

.modal-body {
    margin-bottom: 2rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid var(--slate-100);
}

/* Login Layout */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--slate-50);
    padding: 1rem;
}

.login-card {
    width: 100%;
    max-width: 420px;
    background: white;
    padding: 2.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-logo {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.login-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    margin-bottom: 0.5rem;
}

.login-subtitle {
    color: var(--slate-500);
    font-size: 0.875rem;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        transform: translate(-50%, -45%); 
        opacity: 0; 
    }
    to { 
        transform: translate(-50%, -50%); 
        opacity: 1; 
    }
}
    </style>
</head>

<body>

<?php include "layouts/topbar.php"; ?>

<div class="content">
<?php
    $file = "pages/$page.php";
    if (file_exists($file)) include $file;
?>
</div>

<?php
// 직원 모달
if ($page === 'employees_list' && $view) {
    include "modal/employee_modal.php";
}
?>

</body>
</html>