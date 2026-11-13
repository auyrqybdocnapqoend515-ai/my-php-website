<?php
declare(strict_types=1);
session_start();
$config = require __DIR__ . '/config.php';
$pdo = new PDO(
  "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
  $config['db_user'], $config['db_pass'],
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function admin_required(): void {
  if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
}
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_check(): void {
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(400); exit('طلب غير صالح. حدّث الصفحة وحاول مجدداً.');
  }
}
function clean_slug(string $s): string {
  $s = strtolower(trim($s));
  $s = preg_replace('/[^a-z0-9\-]+/', '-', $s);
  return trim(preg_replace('/-+/', '-', $s), '-') ?: 'page-' . bin2hex(random_bytes(4));
}
