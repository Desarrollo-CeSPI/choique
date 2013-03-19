<?php 
/*
 * Choique CMS - A Content Management System.
 * Copyright (C) 2012 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 * 
 * This file is part of Choique CMS.
 * 
 * Choique CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 * 
 * Choique CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Choique CMS.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>

<?php include_title() ?>
<link rel="shortcut icon" href="<?php echo image_path('frontend/favicon.ico') ?>" />
<!--  myThemeOfficeBase is used in pmJScookMenuPlugin -->
<!--  in IE some images couldn't be found -->
<!--  it should be redefined according to the theme selected for the menu -->
<script type="text/javascript">
//<![CDATA[
var myThemeOfficeBase = '<?php echo $sf_request->getRelativeUrlRoot() ?>/pmJSCookMenuPlugin/images/ThemeOffice/';
//]]>
</script>
</head>
<body>

<?php if (sfConfig::get('app_choique_testing', false)): ?>
<div class="testing-banner"> <?php echo sfConfig::get('app_choique_testing_text','Version de prueba')?></div>
<?php endif?>


<div id="wrapper">

<?php if ($sf_user->isAuthenticated()):  ?>
  <div id="header">
    <div id="logo">
      <?php echo image_tag('backend/logo.jpg', array('alt' => __('Choique CMS'), 'title' => __('Choique CMS'))) ?>
    </div>

    <div id="actions">
      <div>
        <?php echo sfConfig::get('app_choique_instance_name') ?> -
        <?php echo __('Versión %%version%%', array('%%version%%' => CmsConfiguration::getVersion())) ?>
      </div>
      <div><?php echo __('Ingresado como %%user%%', array('%%user%%' => $sf_user->getGuardUser()->getName() .' - '. link_to(' Cambiar Contraseña ', '@change_password'))) ?></div>
      <div>[<?php echo link_to(__('SALIR'), '/logout') ?>]</div>
    </div>
  </div>
  <?php include_partial('global/menu') ?>
<?php endif ?>

<?php include_partial('global/choique_settings') ?>

<?php echo $sf_data->getRaw('sf_content') ?>

</div>
</body>
</html>
