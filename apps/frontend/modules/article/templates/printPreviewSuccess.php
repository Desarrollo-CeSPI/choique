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
<?php use_helper('Javascript') ?>

<div id="print-content">
  <?php echo image_tag(choiqueFlavors::getImagePath('logo', 'gif'), Array('alt' => __('Logotipo'), 'id' => 'top-image')) ?>
  <div class="article-content">
    <div class="path">
      <?php if_javascript(); ?>
        <?php echo link_to_function(image_tag(choiqueFlavors::getImagePath('print', 'gif'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))),'print()', array('id' => 'top-print')) ?>
      <?php end_if_javascript(); ?>
     <!--<?php $sectionName = ($article->getSectionId()) ? $article->getSection()->getName() : Section::HOME ?>
      <?php echo Section::getPath($section) ?>-->
    </div>
    <noscript>
      <div id="contact_return_link">
        <?php echo link_to(__("Volver al artículo"), $article->getURLReference(), array('title' => __("Volver al artículo"), 'accesskey' => 'r')); ?>
      </div>
    </noscript>
    <?php echo $article->getFullHTMLRepresentation() ?>
  </div>

  <div class="footer">
    <div><?php echo url_for($article->getUrlReference(), true) ?></div>
    <div><?php echo CmsConfiguration::get('footer') ?></div>
  </div>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery.noConflict();

if ($('text_right'))
{
  $('text_right').select('a').each(function (e, i)
  {
    $(e).removeAttribute('href');
    $(e).removeAttribute('onclick');
  });
};
//]]>
</script>

<style type="text/css">
body
{
  background: #fff;
  padding: 1em;
}

#print-content
{
  text-align: left;
}

#top-print
{
  float: right;
}
</style>