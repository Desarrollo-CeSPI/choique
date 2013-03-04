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
<?php if ($article): ?>
  <?php include_partial(
    'template/editor_cell',
    array(
      'representations' => $representations,
      'row_number'      => $row,
      'cell_number'     => $cell,
      'news_space'      => $news_space
    )) ?>

  <?php echo javascript_tag("$('row_${row}_count').value = parseInt($('row_${row}_count').getValue()) + 1;") ?>

  <?php echo sortable_element(
      "row_${row}",
      array(
        'url'        => 'template/changeOrder',
        'overlap'    => 'horizontal',
        'constraint' => false,
        'only'       => 'item',
        'before'     => "$('action_indicator').show();",
        'complete'   => "$('action_indicator').hide();",
        'handles'    => "$$('#row_${row} .handle')",
        'update'     => 'hidden_area',
        'script'     => true,
        'with'       => "Sortable.serialize('row_${row}', { name: 'data' }) + '&row=${row}'"
      )) ?>

<?php endif ?>