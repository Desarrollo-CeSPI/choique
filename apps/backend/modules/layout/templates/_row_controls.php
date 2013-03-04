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
<td class="row_controls">
  <a href="#" onclick="Layout.row.swap(this, 'top'); return false;"><?php echo image_tag('backend/control-stop-090.png', array('alt' => __('Mover primera'), 'title' => __('Mover esta fila al primer lugar'))) ?></a>
  <a href="#" onclick="Layout.row.swap(this, 'up'); return false;"><?php echo image_tag('backend/arrow-090-medium.png', array('alt' => __('Mover arriba'), 'title' => __('Mover esta fila hacia arriba'))) ?></a>
  <a href="#" onclick="Layout.row.swap(this, 'down'); return false;"><?php echo image_tag('backend/arrow-270-medium.png', array('alt' => __('Mover abajo'), 'title' => __('Mover esta fila hacia abajo'))) ?></a>
  <a href="#" onclick="Layout.row.swap(this, 'bottom'); return false;"><?php echo image_tag('backend/control-stop-270.png', array('alt' => __('Mover última'), 'title' => __('Mover esta fila al último lugar'))) ?></a>
  <div class="actions_separator_vertical">&nbsp;</div>
  <a href="#" onclick="Layout.column.add(this, 'first'); return false;"><?php echo image_tag('backend/prepend_column.png', array('alt' => __('Agregar columna a izquierda'), 'title' => __('Agregar una columna a la izquierda'))) ?></a>
  <a href="#" onclick="Layout.column.add(this, 'last'); return false;"><?php echo image_tag('backend/append_column.png', array('alt' => __('Agregar columna a derecha'), 'title' => __('Agregar una columna a la derecha'))) ?></a>
  <div class="actions_separator_vertical">&nbsp;</div>
  <a href="#" onclick="Layout.row.edit(this); return false;"><?php echo image_tag('backend/layer--pencil.png', array('alt' => 'Editar', 'title' => 'Editar las propiedades de esta fila')) ?></a>
  <div class="form" style="display: none;">
    <?php echo __('Clases CSS') ?>
    <input type="text" name="class" placeholder="-" size="24" class="row_option" value="<?php isset($row) and print $row->getOption('class') ?>" />

    <input type="button" value="<?php echo __('Ocultar') ?>" onclick="Layout.row.stopEdition(this); return false;" />
  </div>
  <div class="actions_separator_vertical">&nbsp;</div>
  <a href="#" onclick="Layout.row.remove(this); return false;"><?php echo image_tag('backend/cross.png', array('alt' => 'Eliminar', 'title' => 'Eliminar esta fila y todo su contenido')) ?></a>
</td>