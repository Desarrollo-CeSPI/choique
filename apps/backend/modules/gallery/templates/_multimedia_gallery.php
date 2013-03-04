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

<?php echo input_hidden_tag('multimedia_id', ' ') ?>
<?php echo input_auto_complete_tag('multimedia_id_search',
                                   '',
                                   'gallery/autocompleteMultimedia?_csrf_token='.csrf_token(),
                                   array('size'                 => '80'),
                                   array('use_style'            => true,
                                         'indicator'            => 'searching',
                                         'after_update_element' => "function(inputField, selectedItem) { $('multimedia_id').value = selectedItem.id; if ($('multimedia_id').value.blank()) $('multimedia_id_search').value = ''; }")) ?>
<?php echo submit_to_remote('add_multimedia', __('Agregar a la lista'), array('url'       => 'gallery/addTmpMultimedia?_csrf_token='.csrf_token(),
                                                                              'script'    => true,
                                                                              'condition' => "!($('multimedia_id').value.blank())",
                                                                              'with'      => "'gallery_id=".$gallery->getId()."&multimedia_id='+$(\"multimedia_id\").value",
                                                                              'update'    => 'multimedia_gallery')) ?>

<div class="sf_admin_edit_help">
  <?php echo __('Tipee el nombre o título del contenido multimedial que desee agregar a la lista, y luego seleccionelo de las opciones que aparezcan. Una vez seleccionado, presione el botón "Agregar a la lista".') ?>
</div>

<div id="multimedia_gallery" style="margin-top: 10px">
  <?php include_partial('gallery/multimedia_gallery_list', array('gallery' => $gallery)) ?>
</div>

<span id="searching" style="display:none;">
  <?php echo image_tag('common/indicator.gif') ?><?php echo __('Buscando...') ?>
</span>