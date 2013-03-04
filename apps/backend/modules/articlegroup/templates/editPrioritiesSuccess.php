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
<?php use_stylesheet('backend/admin_theme_black') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Editar prioridades para los artículos de la galería "%%name%%"', array('%%name%%' => $article_group->getName())) ?></h1>
  <div id="sf_admin_content">
    <span id="searching" style="display:none;">
      <?php echo image_tag('common/indicator.gif') ?><?php echo __('Actualizando') ?>
    </span>
    <fieldset>
      <div class="form-row">
        <?php echo label_for('article_group_priorities', __("Orden:"), "class=required") ?>
        <div class="content" id="article_article_group">
          <?php include_partial('articlegroup/article_gallery_priorities', array('article_group' => $article_group, 'article_article_groups' => $article_article_groups)); ?>
        </div>
      </div>
    </fieldset>
    <div id="message">
    </div>
  </div>
  <ul class="sf_admin_actions">
    <li>
      <?php echo button_to(__('list'),
                           'articlegroup/list?id='.$article_group->getId(),
                           array ('class' => 'sf_admin_action_list')) ?>
    </li>
    <li>
      <?php echo button_to(__('edit'),
                           'articlegroup/edit?id='.$article_group->getId(),
                           array ('class' => 'sf_admin_action_edit')) ?>
    </li>
  </ul>
</div>