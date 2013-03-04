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
<?php use_helper('Javascript', 'Date') ?>


<div class="row new-item" id="article_<?php echo $article->getId() ?>">
  <span class="article-details-toggle">
    <?php echo link_to_function(__('detalles'), "$('article_details_".$article->getId()."').toggle(); $(this).up().toggleClassName('active');") ?>
  </span>

  <?php echo image_tag(
    'backend/move_handle.png',
    array(
      'alt'   => 'M',
      'title' => __('Mover'),
      'class' => 'handle'
    )) ?>
  <?php echo $article->getTitle() ?>
  

  <div id="article_details_<?php echo $article->getId() ?>" style="display: none" class="article-details">
    <div class="multimedia">
      <?php if ($article->hasMultimedia()): ?>
        <?php echo $article->getMultimedia()->getHTMLRepresentation('s') ?>
      <?php else: ?>
        <?php echo __('Sin imagen') ?>
      <?php endif ?>
    </div>
    <div class="information">
      <div><span class="label"><?php echo __('Tipo') ?>:</span> <?php echo $article->getTypeText() ?></div>
      <div>
        <span class="label"><?php echo __('Sección') ?>:</span>
        <?php if ($article->getSection()): ?>
          <?php echo $article->getSection()->getTitle() ?>
        <?php else: ?>
          <?php echo __('Sin asignar') ?>
        <?php endif ?>
      </div>
      <div><span class="label"><?php echo __('Autor') ?>:</span> <?php echo $article->getsfGuardUserRelatedByCreatedBy()->getName() ?></div>
      <div><span class="label"><?php echo __('Publicado') ?>:</span> <?php echo format_date($article->getPublishedAt(), 'f') ?></div>
      <div><span class="label"><?php echo __('Última modificación') ?>:</span> <?php echo format_date($article->getUpdatedAt(), 'f') ?></div>
    </div>
  </div>
</div>

<?php echo draggable_element('article_'.$article->getId(), array('revert' => true, 'scroll' => 'window')) ?>