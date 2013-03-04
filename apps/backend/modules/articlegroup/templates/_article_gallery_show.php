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
<?php use_helper('I18N', 'Javascript') ?>
<?php use_stylesheet('backend/editor') ?>

<div id="article_article_group" style="margin-top: 10px">
  <div id="gallery_editor">
    <?php if (count($articles = $article_group->getArticlesByPriority())): ?>

      <div style="clear: both; font-size: 1px; height: 1px;">&nbsp;</div>

      <div class="elements-list">
        <?php foreach ($articles as $article): ?>
          <div class="element article">
            <div class="title">
              <?php echo $article->__toString() ?>
            </div>
            <?php if($article->getHeading()):?>
              <div class="heading">
                <?php echo $article->getHeading() ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach ?>
      </div>
      <div style="clear: both; font-size: 1px; height: 10px;">&nbsp;</div>

    <?php else: ?>

      <?php echo __('La galería no tiene elementos.') ?>
    <?php endif ?>
  </div>
</div>