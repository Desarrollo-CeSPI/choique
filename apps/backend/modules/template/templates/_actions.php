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
<div id="selected_items" class="actions top">
  <?php echo image_tag('common/indicator.gif', array('alt' => __('Cargando'), 'id' => 'action_indicator', 'style' => 'display: none')) ?>

  <?php echo link_to_remote(
    image_tag('backend/prepend_row.png', array('alt' => '', 'title' => __('Agregar fila'))).' '.__('Nueva fila al principio'),
    array(
      'url'      => 'template/addRow',
      'update'   => 'upper_rows',
      'position' => 'bottom',
      'script'   => true,
      'with'     => "'row=' + (--row_lower_count) +'&_csrf_token=".csrf_token()."'",
      'before'   => "$('action_indicator').show()",
      'complete' => "$('action_indicator').hide()"
    ),
    array(
      'class'    => 'action'
    )) ?>

  <?php echo link_to_remote(
    image_tag('backend/append_row.png', array('alt' => '', 'title' => __('Agregar fila'))).' '.__('Nueva fila al final'),
    array(
      'url'      => 'template/addRow',
      'update'   => 'lower_rows',
      'position' => 'bottom',
      'script'   => true,
      'with'     => "'row=' + (++row_upper_count)+'&_csrf_token=".csrf_token()."'",
      'before'   => "$('action_indicator').show()",
      'complete' => "$('action_indicator').hide()"
    ),
    array(
      'class'    => 'action'
    )) ?>

  <?php echo link_to_function(
      image_tag('backend/magnifier.png', array('alt' => '', 'title' => __('Previsualizar'))).' '.__('Previsualizar'),
      "window.open('".choiqueUtil::generateUrl('frontend','/PREVIEW')."?' + $('template_edit_form').serialize(), '_blank');",
      array('class' => 'action')
    )?>
</div>