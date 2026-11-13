<?php
require __DIR__.'/common.php'; admin_required();
$id=(int)($_GET['id'] ?? 0); $page=['title'=>'','slug'=>'','description'=>'','body'=>'','status'=>'published'];
if($id){$s=$pdo->prepare('SELECT * FROM pages WHERE id=?');$s->execute([$id]);$page=$s->fetch();if(!$page)exit('الصفحة غير موجودة.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf_check();
 $title=trim($_POST['title']??''); $slug=clean_slug($_POST['slug']??''); $desc=trim($_POST['description']??'');
 $body=trim($_POST['body']??''); $status=($_POST['status']??'published')==='paused'?'paused':'published';
 if($title==='') $error='العنوان مطلوب.';
 else {
  try {
   if($id){$s=$pdo->prepare('UPDATE pages SET title=?,slug=?,description=?,body=?,status=? WHERE id=?');$s->execute([$title,$slug,$desc,$body,$status,$id]);}
   else {$s=$pdo->prepare('INSERT INTO pages(title,slug,description,body,status) VALUES(?,?,?,?,?)');$s->execute([$title,$slug,$desc,$body,$status]);$id=(int)$pdo->lastInsertId();}
   header('Location: edit.php?id='.$id.'&saved=1');exit;
  } catch(PDOException $e){$error='تعذر الحفظ. ربما الرابط مستخدم من صفحة أخرى.';}
 }
 $page=compact('title','slug','description','body','status');
}
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $id?'تعديل صفحة':'إنشاء صفحة'?></title><link rel="stylesheet" href="assets/style.css"></head><body><header class="top"><strong>إدارة الصفحات</strong><nav><a href="admin.php">العودة للوحة التحكم</a></nav></header><main class="box wide"><h1><?=$id?'تعديل الصفحة':'إنشاء صفحة جديدة'?></h1><?php if(isset($_GET['saved']))echo '<p class="success">تم الحفظ. رابط الصفحة لم يتغير إذا لم تعدّل خانة الرابط.</p>'; if($error)echo '<p class="error">'.e($error).'</p>';?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>عنوان الصفحة<input name="title" required maxlength="200" value="<?=e($page['title'])?>"></label><label>معرّف الرابط (Slug)<input name="slug" required value="<?=e($page['slug'])?>" placeholder="my-page"><small>اتركه كما هو للحفاظ على الرابط الثابت.</small></label><label>وصف محركات البحث<textarea name="description" maxlength="320" rows="2"><?=e($page['description'])?></textarea></label><label>محتوى الصفحة<textarea name="body" rows="12" required><?=e($page['body'])?></textarea></label><label>الحالة<select name="status"><option value="published" <?=$page['status']==='published'?'selected':''?>>منشورة</option><option value="paused" <?=$page['status']==='paused'?'selected':''?>>متوقفة</option></select></label><button>حفظ الصفحة</button></form></main></body></html>
