<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php include_http_metas() ?>
<?php include_metas() ?>

<?php include_title() ?>
<?php
	$uri = $sf_request->getRelativeUrlRoot();
	echo tag('link', array('rel' => 'shortcut icon', 'href' => $uri.'/favicon.ico'))."\n";
	echo tag('link', array('rel' => 'search', 'type'=>'application/opensearchdescription+xml', 'href' => $uri.'/search.xml', 'title'=>'CMS Search'))."\n";
?>

</head>
<body>
  <?php echo $sf_content ?>
</body>
</html>
