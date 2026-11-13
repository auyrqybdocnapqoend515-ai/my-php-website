<?php
require __DIR__.'/common.php';
header('Content-Type: application/xml; charset=utf-8');
$base=rtrim($config['site_url'],'/');
$pages=$pdo->query("SELECT slug,updated_at FROM pages WHERE status='published'")->fetchAll();
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc><?=e($base.'/')?></loc></url><?php foreach($pages as $p):?><url><loc><?=e($base.'/page.php?slug='.rawurlencode($p['slug']))?></loc><lastmod><?=date('c',strtotime($p['updated_at']))?></lastmod></url><?php endforeach;?></urlset>
