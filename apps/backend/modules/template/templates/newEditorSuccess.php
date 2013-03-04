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
<?php use_helper('Object', 'Javascript', 'Text') ?>
<?php use_stylesheet('backend/new_template_editor.css') ?>
<?php use_stylesheet('backend/admin_theme.css') ?>

<div id="sf_admin_container">
  <?php include_partial('template/edit_messages') ?>

  <h1><?php echo __('Editar diseño de la portada "%%name%%"', array('%%name%%' => $template->getName())) ?></h1>
  <div id="sf_admin_container">

    <form action="<?php echo url_for('template/newSave') ?>" method="post" id="template_edit_form">

     <?php include_partial('template/help') ?>

      <ul class="sf_admin_actions">
        <li><?php echo button_to(__('Volver al listado'), 'template/list', array('class' => 'sf_admin_action_list')) ?></li>
        <li><?php echo submit_tag(__('Guardar cambios'), array('class' => 'sf_admin_action_save')) ?></li>
      </ul>

      <div id="moving_articles_box" class="articles-container">
        <?php include_partial('template/articles', array('used_articles' => $used_articles)) ?>
      </div>

      <div class="editor-container">

        <?php include_partial('template/actions') ?>

        <div id="template_editor" class="container">
          <?php echo input_hidden_tag('template[id]', $template->getId()) ?>

          <div id="upper_rows"></div>

          <?php foreach ($rows as $i => $row): ?>
            <?php include_partial(
              'template/editor_row',
              array(
                'index'           => $i,
                'representations' => Article::getAvailableRepresentations(),
                'row'             => $row
              )) ?>
          <?php endforeach ?>

          <div id="lower_rows"></div>
        </div>
      </div>

    </form>

    <div id="hidden_area" style="display: none;"></div>

  </div>
</div>

<?php echo javascript_tag("
var row_lower_count = 0; var row_upper_count = ".count($rows).";
var div_movil = '#moving_articles_box';
var menuYloc = null;
  jQuery(document).ready(function(){
      menuYloc = 10;
      jQuery(window).scroll(function () { 
        if (jQuery(document).scrollTop() >= jQuery('#selected_items').offset().top)
        {
          jQuery(div_movil).css('{position: absolute;}');
          offset = menuYloc+jQuery(document).scrollTop()+'px';
          jQuery(div_movil).animate({top:offset},{duration:500,queue:false});
        }
        else
        {
          jQuery(div_movil).css('{position: relative;}');
        }
      });
    }); "
) ?>