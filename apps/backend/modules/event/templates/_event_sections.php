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

<script type="text/javascript">
  function select_event_section(field, item)
  {
    if (jQuery('#associated_event_sections option[value="'+item.id+'"]').length > 0)
    {
      var message_id = '#section_already_added';
    }
    else
    {
      var message_id = '#section_added';

      jQuery('#associated_event_sections').append('<option selected="selected" value="'+item.id+'" class="'+item.id+'">'+item.id+'</option>');

      var new_item = jQuery('<li id="sev_'+item.id+'" />').html(jQuery(item).html());
      new_item.prepend('<a href="#" onclick="delete_event_section('+item.id+'); return false;"><?php echo __('Quitar') ?></a>');
      jQuery('#selected_event_sections').append(new_item);
    }

    jQuery(message_id).fadeIn(500, function() {
      jQuery(this).fadeOut(3500);
    });

    jQuery(field).val('');
  }

  function delete_event_section(id)
  {
    jQuery('#sev_'+id).fadeOut(500, function() { jQuery(this).remove(); });
    jQuery('#associated_event_sections option[value="'+id+'"]').remove();
  }
</script>

<?php echo select_tag('associated_event_sections[]', options_for_select($selected, array_keys($selected)), array('multiple' => true, 'style' => 'display: none;')) ?>

<div>
  <?php echo input_auto_complete_tag(
    'event_section_search',
    '',
    'event/autocompleteSection?_csrf_token='.csrf_token(),
    array('size' => '80', 'placeholder' => __('Busque las secciones a relacionar aquí'), 'title' => __('Busque las secciones a relacionar aquí')),
    array(
      'use_style'            => true,
      'after_update_element' => 'select_event_section',
      'indicator'            => 'searching_section'
  )) ?>

  <span id="searching_section" style="display:none;">
    <?php echo image_tag('common/indicator.gif') ?> <?php echo __('Buscando...') ?>
  </span>

  <span id="section_added" style="display: none;">
    <?php echo __('La sección fue relacionada.') ?>
  </span>
  <span id="section_already_added" style="display: none;">
    <?php echo __('La sección ya está relacionada.') ?>
  </span>
</div>

<div class="selected-event-sections-title"><?php echo __('Secciones actualmente relacionadas') ?></div>

<ul id="selected_event_sections" class="selected-event-sections-list">
<?php foreach ($selected as $id => $value): ?>
  <li id="sev_<?php echo $id ?>">
    <?php echo link_to_function(__('Quitar'), 'delete_event_section('.$id.');') ?>
    <?php echo $value ?>
  </li>
<?php endforeach; ?>
</ul>