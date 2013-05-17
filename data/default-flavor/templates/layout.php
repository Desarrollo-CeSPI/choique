<!DOCTYPE html>
<html lang="es">
<head>

<?php include_http_metas() ?>
<?php include_metas() ?>

<title>
  <?php echo $sf_context->getResponse()->getTitle() ?>
  <?php isset($section) and print ' - '.$section->getTitle() ?>
  <?php isset($article) and print ' - '.$article->getTitle() ?>
</title>

<?php echo tag('link', array('rel'   => 'shortcut icon',
                             'href'  => image_path('frontend/favicon.ico'))) ?>
<?php echo tag('link', array('rel'   => 'search',
                             'type'  => 'application/opensearchdescription+xml',
                             'href'  => url_for('@search_xml', true),
                             'title' => sfConfig::get('app_search_xml_title', __('Buscar en Choique CMS')))) ?>
<?php echo tag('link', array('rel'   => 'alternate',
                             'type'  => 'application/rss+xml',
                             'href'  => url_for('@feed?section=', true),
                             'title' => 'Todas las noticias')) ?>
<?php echo tag('link', array('rel'   => 'alternate',
                             'type'  => 'application/rss+xml',
                             'href'  => url_for('@feed?section='.$sf_request->getParameter('section_name', ''), true),
                             'title' => 'Noticias de la sección actual')) ?>
<?php if (isset($template_object)): ?>
<?php echo tag('link', array('rel'   => 'alternate',
                             'type'  => 'application/rss+xml',
                             'href'  => url_for('@template_feed?template='.$template_object->getPublicName(), true),
                             'title' => 'Noticias de la portada actual')) ?>
<?php endif; ?>

<link rel="stylesheet" type="text/css" media="all" href="<?php echo url_for('@section_css?name='.$sf_request->getParameter('section_name', '')) ?>" />
</head>
<body class="<?php echo $sf_user && $sf_user->hasAttribute('mobile_mode') && $sf_user->getAttribute('mobile_mode')? 'mobile' : $sf_request->getParameter('section_name') ?>">
  <div id="wrapper">
    <?php include_layout_for_section($sf_request->getParameter('section_name'), $sf_content) ?>
  </div>
</body>
</html>
