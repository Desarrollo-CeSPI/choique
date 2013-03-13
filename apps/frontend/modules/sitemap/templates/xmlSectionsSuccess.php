<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><![CDATA[<?php echo url_for('@homepage', true) ?>]]></loc>
    <changefreq>daily</changefreq>
    <priority><![CDATA[1.0]]></priority>
  </url>

  <?php foreach ($sections as $section): ?>
    <url>
      <loc><![CDATA[<?php echo url_for($section->getRoute(), true) ?>]]></loc>
      <priority><![CDATA[<?php echo 0.9 - $section->getDepth() / 10 ?>]]></priority>
    </url>
  <?php endforeach; ?>
</urlset>
