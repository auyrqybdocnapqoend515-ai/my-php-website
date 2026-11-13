<?php
require __DIR__.'/common.php';
if (!empty($_SESSION['admin_id'])) { header('Location: admin.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare('SELECT * FROM admins WHERE username=?');
  $stmt->execute([trim($_POST['username'] ?? '')]);
  $admin = $stmt->fetch();
  if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
    session_regenerate_id(true); $_SESSION['admin_id'] = $admin['id']; $_SESSION['username'] = $admin['username'];
    header('Location: admin.php'); exit;
  }
  $error = 'بيانات الدخول غير صحيحة.';
}
?><!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>دخول المدير</title><link rel="stylesheet" href="assets/style.css">
<main class="box"><h1>دخول المدير</h1><?php if($error) echo '<p class="error">'.e($error).'</p>'; ?><form method="post"><label>اسم المستخدم<input name="username" required autocomplete="username"></label><label>كلمة المرور<input type="password" name="password" required autocomplete="current-password"></label><button>دخول</button></form></main></html>
