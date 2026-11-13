<?php
require __DIR__.'/common.php'; admin_required();
$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $action = $_POST['action'] ?? '';
  $id = (int)($_POST['id'] ?? 0);
  if ($action === 'toggle') {
    $stmt=$pdo->prepare("UPDATE pages SET status=IF(status='published','paused','published') WHERE id=?"); $stmt->execute([$id]);
    $notice='تم تحديث حالة الصفحة.';
  } elseif ($action === 'delete') {
    $stmt=$pdo->prepare('DELETE FROM pages WHERE id=?'); $stmt->execute([$id]); $notice='تم حذف الصفحة.';
  }
}
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
  $stmt=$pdo->prepare('SELECT * FROM pages WHERE title LIKE ? OR slug LIKE ? ORDER BY updated_at DESC');
  $like='%'.$q.'%'; $stmt->execute([$like,$like]); $pages=$stmt->fetchAll();
} else $pages=$pdo->query('SELECT * FROM pages ORDER BY updated_at DESC')->fetchAll();
$total=(int)$pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$live=(int)$pdo->query("SELECT COUNT(*) FROM pages WHERE status='published'")->fetchColumn();
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>لوحة التحكم</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><strong><?=e($config['site_name'])?></strong><nav><a href="index.php">عرض الموقع</a><a href="logout.php">تسجيل الخروج</a></nav></header>
<main class="wrap"><h1>لوحة التحكم</h1><?php if($notice) echo '<p class="success">'.e($notice).'</p>'; ?>
<section class="stats"><div><b><?=$total?></b><span>إجمالي الصفحات</span></div><div><b><?=$live?></b><span>صفحات منشورة</span></div><div><b><?=$total-$live?></b><span>صفحات متوقفة</span></div></section>
<div class="toolbar"><form method="get" class="search"><input name="q" value="<?=e($q)?>" placeholder="ابحث بالعنوان أو الرابط"><button>بحث</button></form><a class="button" href="edit.php">+ إنشاء صفحة</a></div>
<div class="table-wrap"><table><thead><tr><th>العنوان</th><th>الرابط</th><th>الحالة</th><th>آخر تعديل</th><th>الإجراءات</th></tr></thead><tbody>
<?php foreach($pages as $p): ?><tr><td><?=e($p['title'])?></td><td><a target="_blank" href="page.php?slug=<?=urlencode($p['slug'])?>">/p/<?=e($p['slug'])?></a></td><td><span class="badge <?=$p['status']?>"><?=$p['status']==='published'?'منشورة':'متوقفة'?></span></td><td><?=e($p['updated_at'])?></td><td class="actions"><a href="edit.php?id=<?=$p['id']?>">تعديل</a><a target="_blank" href="page.php?slug=<?=urlencode($p['slug'])?>">مشاهدة</a><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$p['id']?>"><input type="hidden" name="action" value="toggle"><button class="small"><?=$p['status']==='published'?'إيقاف':'تشغيل'?></button></form><form method="post" onsubmit="return confirm('هل تريد حذف الصفحة؟')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$p['id']?>"><input type="hidden" name="action" value="delete"><button class="small danger">حذف</button></form></td></tr><?php endforeach; ?>
<?php if(!$pages): ?><tr><td colspan="5">لا توجد صفحات مطابقة.</td></tr><?php endif; ?></tbody></table></div></main></body></html>
