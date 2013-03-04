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

<?php $js = sprintf("function (input, selected) { $('article_name').value = ''; %s }",
        remote_function(array('url'      => 'template/addArticle',
                              'update'   => 'articles_list',
                              'script'   => true,
                              'before'   => "$('articles_indicator').show();",
                              'complete' => "$('articles_indicator').hide(); used_articles.push(parseInt(selected.id));",
                              'condition' => "used_articles.indexOf(parseInt(selected.id)) == -1",
                              'with'     => "'&id=' + selected.id +'&_csrf_token=".csrf_token()."'",
                              'position' => 'bottom'))) ?>

<div class="actions top">
  <?php echo image_tag('common/indicator.gif', array('alt' => __('Cargando'), 'id' => 'articles_indicator', 'style' => 'display: none')) ?>
  <?php echo image_tag('common/indicator.gif', array('alt' => __('Cargando'), 'id' => 'autocomplete_indicator', 'style' => 'display: none')) ?>
  <span class="title">
    <?php echo __('Artículos disponibles') ?>
  </span>

  <span class="action search">
    <label for="article_name"><?php echo __('Buscar artículo') ?></label>
    <?php echo input_auto_complete_tag('article_name',
        '',
        'article/autocomplete?_csrf_token='.csrf_token(),
        array(),
        array(
          'after_update_element' => $js,
          'use_style'            => true,
          'indicator'            => 'autocomplete_indicator',
        )) ?>
  </span>

</div>

<?php $js = "var used_articles = new Array();
 $('#articles_list').scroll(function() {
  $('#log').append('<div>Handler for .scroll() called.</div>');
});
" ?>

<?php $used_articles_ids = array() ?>
<div class="container" id="articles_list" style="overflow: scroll; height: 500px;">
      <?php foreach ($used_articles as $article): ?>    
        <?php if (!in_array($article->getId(), $used_articles_ids)): ?>                  
          <?php include_partial('template/article', array('article' => $article)) ?>
          <?php $used_articles_ids[] = $article->getId() ?>
          <?php $js .= "used_articles.push(".$article->getId().");" ?>                    
        <?php endif ?>
      <?php endforeach ?>      
</div>

<?php echo javascript_tag($js) ?>