<?php
declare(strict_types=1);
session_start();
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    exit('أنشئ config.php أولاً بنسخ config.example.php وتعبئة بيانات قاعدة البيانات.');
}
$config = require $configFile;
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'], $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));
    $count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($count > 0) exit('تم إعداد المدير مسبقاً. احذف setup.php الآن.');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (strlen($username) < 4 || strlen($password) < 12) {
            $error = 'اسم المستخدم 4 أحرف على الأقل وكلمة المرور 12 حرفاً على الأقل.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO admins (username,password_hash) VALUES (?,?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            exit('تم إنشاء حساب المدير. احذف setup.php فوراً ثم افتح /admin.php');
        }
    }
} catch (Throwable $e) { $error = 'تعذر الاتصال بقاعدة البيانات. راجع إعدادات config.php.'; }
?>
<!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>إعداد الموقع</title><link rel="stylesheet" href="assets/style.css">
<main class="box"><h1>إعداد لوحة التحكم</h1><p>أنشئ حساب المدير الأول. احذف ملف setup.php بعد النجاح.</p>
<?php if (!empty($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
<form method="post"><label>اسم المستخدم<input name="username" required minlength="4"></label><label>كلمة المرور<input type="password" name="password" required minlength="12"></label><button>إنشاء المدير</button></form></main></html>
