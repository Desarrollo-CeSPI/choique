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
<?php echo input_hidden_tag("row[$index][count]", count($row)) ?>

<ul class="row" id="row_<?php echo $index ?>">
  <li class="delete_row">
    <?php echo link_to_function(
      image_tag('backend/delete_item.png', array('alt' => 'X', 'title' => __('Eliminar esta fila'))),
      "if (confirm('".__('¿Está seguro?')."')) { $('row_${index}').remove(); }"
    ) ?>
  </li>

  <?php foreach ($row as $j => $news_space): ?>
    <?php include_partial(
      'template/editor_cell',
      array(
        'representations' => $representations,
        'row_number'      => $index,
        'cell_number'     => $j,
        'news_space'      => $news_space
      )) ?>
  <?php endforeach ?>
</ul>

<?php echo drop_receiving_element(
    "row_$index",
    array(
      'url'        => 'template/addCell',
      'accept'     => 'new-item',
      'update'     => "row_${index}",
      'script'     => true,
      'with'       => "'id=' + encodeURIComponent(element.id) + '&row=${index}&cell=' + $('row_${index}_count').getValue()+'&_csrf_token=".csrf_token()."'",
      'position'   => 'bottom',
      'before'     => "$('action_indicator').show();",
      'complete'   => "$('action_indicator').hide();",
      'hoverclass' => 'highlight'
    )) ?>

<?php echo sortable_element(
    "row_${index}",
    array(
      'url'        => 'template/changeOrder',
      'overlap'    => 'horizontal',
      'constraint' => 'horizontal',
      'only'       => 'item',
      'before'     => "$('action_indicator').show();",
      'complete'   => "$('action_indicator').hide();",
      'handles'    => "$$('#row_${index} .handle')",
      'update'     => 'hidden_area',
      'script'     => true,
      'with'       => "Sortable.serialize('row_${index}', { name: 'data' }) + '&row=${index}"."&_csrf_token=".csrf_token()."'"
    )) ?>