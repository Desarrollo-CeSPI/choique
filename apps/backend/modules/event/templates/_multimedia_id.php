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

<?php echo input_hidden_tag('event[multimedia_id]', $event->getMultimediaId()) ?>
<?php echo input_auto_complete_tag('multimedia_id_search',
                                   ($event->getMultimediaId()) ? $event->getMultimedia()->__toString() : '',
                                   'article/autocompleteMultimedia?_csrf_token='.csrf_token(),
                                   array('size' => '80'),
                                   array('use_style' => true,
                                         'after_update_element' => "function(inputField, selectedItem) {" .
                                                                   "$('event_multimedia_id').value = selectedItem.id; }",
                                         'indicator' => 'searching')) ?>

<?php echo link_to_function(__('Limpiar campo'), "$('event_multimedia_id').value = ''; $('multimedia_id_search').value = '';") ?>


<span id="searching" style="display: none;">
  <?php echo image_tag('common/indicator.gif') ?><?php echo __('buscando') ?>
</span>