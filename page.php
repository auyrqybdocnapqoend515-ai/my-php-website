<?php
require __DIR__.'/common.php';
$slug=clean_slug($_GET['slug']??'');
$s=$pdo->prepare('SELECT * FROM pages WHERE slug=?');$s->execute([$slug]);$page=$s->fetch();
if(!$page){http_response_code(404);$title='الصفحة غير موجودة';$body='لم يتم العثور على الصفحة المطلوبة.';}
elseif($page['status']!=='published'){http_response_code(503);$title='الصفحة غير متاحة حالياً';$body='هذه الصفحة متوقفة مؤقتاً. يرجى المحاولة لاحقاً.';}
else {$title=$page['title'];$body=$page['body'];}
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?></title><?php if(!empty($page['description']) && $page['status']==='published'):?><meta name="description" content="<?=e($page['description'])?>"><?php endif;?><link rel="stylesheet" href="assets/style.css"></head><body><main class="public-page"><a class="brand" href="index.php"><?=e($config['site_name'])?></a><article><h1><?=e($title)?></h1><div class="content"><?=nl2br(e($body))?></div></article><footer>محتوى منشور بواسطة صاحب الموقع. هذا موقع شخصي مستقل وغير تابع لأي جهة حكومية.</footer></main></body></html>
