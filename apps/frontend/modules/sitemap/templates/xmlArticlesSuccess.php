<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($articles as $article): ?>
  <url>
    <loc><![CDATA[<?php echo url_for($article->getURLReference(), true) ?>]]></loc>
    <lastmod><![CDATA[<?php echo $article->getUpdatedAt('c') ?>]]></lastmod>
  </url>
<?php endforeach; ?>
</urlset>