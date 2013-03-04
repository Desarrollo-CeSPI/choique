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

<fieldset id="sf_fieldset_none" class="">
  <div class="form-row">
    <?php echo label_for('article_section', __('Prioridades de las novedades')) ?>
    <div class="content">

      <div id="article_section" style="margin:10px;">
        <?php include_partial('section/article_section_list', array('section' => $section)) ?>
      </div>

      <?php echo input_hidden_tag('article_id', null) ?>
      <?php echo input_auto_complete_tag('article_id_search',
                                         '',
                                         'section/autocompleteArticle?t='.Article::NEWS.'&_csrf_token='.csrf_token(),
                                         array('size' => '80'),
                                         array('use_style' => true,
                                               'after_update_element' => "function(inputField, selectedItem) { $('article_id').value = selectedItem.id; }",
                                               'indicator' => 'searching')) ?>

      <?php echo submit_to_remote(__('agregar novedad'), __('agregar novedad'), array('url' => 'section/addArticleSection?_csrf_token='.csrf_token(),
                                                                                               'script' => true,
                                                                                               'with' => "'section_id=".$section->getId()."&article_id='+$(\"article_id\").value",
                                                                                               'update' => 'article_section')) ?>

      <span id="searching" style="display:none;">
        <?php echo image_tag('common/indicator.gif') ?><?php echo __('buscando') ?>
      </span>
    </div>
    <div style="clear:both;height:1px;">&nbsp;</div>
  </div>
</fieldset>