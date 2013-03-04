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

  <?php echo input_hidden_tag('sf_guard_user[section_id]', $sf_guard_user->getSectionId()) ?>
  <?php echo input_auto_complete_tag('section_autocomplete_query',
                                     ($sf_guard_user->getSectionId()) ? $sf_guard_user->getSection()->getTitle() : null,
                                     'section/autocomplete?_csrf_token='.csrf_token(),
                                     array('size'      => 90,
                                           'onBlur'    => "if ($('section_autocomplete_query').value.blank()) $('sf_guard_user_section_id').value = '';"),
                                     array('use_style' => true,
                                           'indicator' => 'search_indicator',
                                           'min_chars' => 3,
                                           'after_update_element' => "function (inputField, selectedItem) { $('sf_guard_user_section_id').value = selectedItem.id; $('clean_section_id').checked = false; if ($('sf_guard_user_section_id').value.blank()) $('section_autocomplete_query').value = ''; }")) ?>
  <?php echo checkbox_tag('clean_section_id',
                          true,
                          false,
                          array('onChange' => "if ($(this).checked) { $('sf_guard_user_section_id').value = ''; $('section_autocomplete_query').value =''; }")) ?>
  <?php echo __("Limpiar el campo") ?>
<div id="search_indicator" style="display: none">
  <?php echo image_tag('common/indicator.gif') . ' ' . __('Buscando...') ?>
</div>
<div class="sf_admin_edit_help">
  <?php echo __("Comience a tipear el nombre de la sección y luego seleccionela de la lista desplegable") ?>
</div>
<?php echo javascript_tag(sprintf("jQuery('#section_autocomplete_query, #clean_section_id').%s", $sf_guard_user->getPermissionNames()==0  || ! ($sf_guard_user->hasPermission('reporter')||$sf_guard_user->hasPermission('reporter_admin'))?"attr('disabled',true)":"removeAttr('disabled')")) ?>