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
<?php use_stylesheet('backend/editor') ?>

<div id="gallery_editor">
  <?php if(count($article_article_groups)): ?>
    <div id="article_article_group_div">
      <ul style="list-style:none;" id="sortable-list" class="elements-list">
      <?php foreach ($article_article_groups as $article_article_group): ?>
        <li id="multimedia_gallery_<?php echo $article_article_group->getId() ?>" class="element article">
            <div class="delete-icon">
              <?php echo link_to_remote(image_tag('backend/delete.gif', array('alt' => __('Eliminar de la lista'), 'title' => __('Eliminar de la lista'))),
                                        array('url'      => 'articlegroup/deleteArticleGroup?_csrf_token='.csrf_token().'&article_article_group_id='.$article_article_group->getId().'&id='.$article_group->getId(),
                                              'update'   => 'article_article_group',
                                              'with'     => "'article_group_id=".$article_group->getId()."&article_id=".$article_article_group->getArticleId()."'",
                                              'loading'  => "Element.show('searching')",
                                              'complete' => "Element.hide('searching'); $('message').innerHTML='".__("Modificaciones guardadas")."'",
                                              'script'   => true)) ?>
            </div>
            <div class="title">
              <?php echo $article_article_group->getArticle()->__toString() ?>
            </div>
            <?php if($article_article_group->getArticle()->getHeading()):?>
              <div class="heading">
                <?php echo $article_article_group->getArticle()->getHeading() ?>
              </div>
            <?php endif; ?>
        </li>
      <?php endforeach ?>
      </ul>
      <?php echo sortable_element('sortable-list', array('url'        => 'articlegroup/sortArticleArticleGroup?_csrf_token='.csrf_token(),
                                                         'overlap'    => 'horizontal',
                                                         'constraint' => '',
                                                         'script'     => true,
                                                         'loading'    => 'Element.show("searching")',
                                                         'complete'   => 'Element.hide("searching")')) ?>
    </div>
    <div style="clear: both; font-size: 1px; height: 10px;">&nbsp;</div>
  <?php else: ?>
    <?php echo __('La galería no tiene elementos aún.') ?>
  <?php endif ?>
</div>
