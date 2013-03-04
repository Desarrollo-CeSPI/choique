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
  <?php if (count($articles = $sf_user->getAttribute('article'))): ?>

    <?php echo link_to(image_tag('/sf/sf_admin/images/edit.png', array('alt' => '')) . ' ' . __('Modificar el orden de los elementos'),
                       'articlegroup/editPriorities?id=' . $article_group->getId(),
                       array('class'   => 'modify-order-button',
                             'confirm' => __('¿Está seguro que desea continuar? Las modificaciones no guardadas se perderán'))) ?>
    <div style="clear: both; font-size: 1px; height: 1px;">&nbsp;</div>

    <div class="elements-list">
      <?php foreach ($articles as $article): ?>
        <div class="element article">
          <div class="delete-icon">
            <?php echo link_to_remote(image_tag('backend/delete.gif', array('alt' => __('Eliminar de la lista'), 'title' => __('Eliminar de la lista'))),
                                      array('url'    => 'articlegroup/deleteTmpArticle',
                                            'update' => 'article_article_group',
                                            'with'   => "'article_group_id=".$article_group->getId()."&article_id=".$article->getArticleId()."&_csrf_token=".csrf_token()."'",
                                            'script' => true)) ?>
          </div>
          <div class="title">
            <?php echo $article->getArticle()->__toString() ?>
          </div>
          <?php if($article->getArticle()->getHeading()):?>
            <div class="heading">
              <?php echo $article->getArticle()->getHeading() ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach ?>
    </div>
    <div style="clear: both; font-size: 1px; height: 10px;">&nbsp;</div>

  <?php else: ?>

    <?php echo __('La galería no tiene elementos aún. Puede comenzar a agregarlos utilizando el campo de arriba.') ?>
  <?php endif ?>
</div>