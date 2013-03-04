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
<div class="column_controls">
  <a href="#" onclick="jQuery(this).nextAll('.toggleable_set').toggle(); return false;"><?php echo image_tag('backend/switch.png', array('alt' => __('Controles'), 'title' => __('Mostrar u ocultar los controles de esta columna'))) ?></a>

  <div class="toggleable_set"<?php $hide and print ' style="display: none;"' ?>>
    <div class="actions_separator">&nbsp;</div>

    <a href="#" onclick="Layout.column.swap(this, 'first'); return false;"><?php echo image_tag('backend/control-stop-180.png', array('alt' => __('Mover primera'), 'title' => __('Mover esta columna al primer lugar en su fila'))) ?></a>
    <a href="#" onclick="Layout.column.swap(this, 'left'); return false;"><?php echo image_tag('backend/arrow-180-medium.png', array('alt' => __('Mover a izquierda'), 'title' => __('Mover esta columna a la izquierda'))) ?></a>
    <a href="#" onclick="Layout.column.swap(this, 'right'); return false;"><?php echo image_tag('backend/arrow-000-medium.png', array('alt' => __('Mover a derecha'), 'title' => __('Mover esta columna a la derecha'))) ?></a>
    <a href="#" onclick="Layout.column.swap(this, 'last'); return false;"><?php echo image_tag('backend/control-stop.png', array('alt' => __('Mover última'), 'title' => __('Mover esta columna al último lugar en su fila'))) ?></a>

    <div class="actions_separator">&nbsp;</div>

    <a href="#" onclick="Layout.column.addSlotlet(this); return false;"><?php echo image_tag('backend/block--plus.png', array('alt' => __('Añadir slotlet'), 'title' => __('Añadir un slotlet a esta columna'))) ?></a>
    <a href="#" onclick="Layout.column.edit(this); return false;"><?php echo image_tag('backend/layer--pencil.png', array('alt' => __('Editar'), 'title' => __('Editar las propiedades de esta columna'))) ?></a>

    <div class="actions_separator">&nbsp;</div>

    <a href="#" onclick="Layout.column.remove(this); return false;"><?php echo image_tag('backend/cross.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar esta columna y todo su contenido'))) ?></a>

    <div class="form" style="display: none;">
      <div>
        <?php echo __('Ancho de columna') ?>
        <input type="text" name="width" min="1" placeholder="<?php echo __('Automático') ?>" size="8" class="column_option" title="<?php echo __('Ingrese el ancho expresado en <tamaño><unidad>') ?>" value="<?php isset($column) and print $column->getOption('width') ?>" />
      </div>

      <div>
        <?php echo __('Clases CSS') ?>
        <input type="text" name="class" placeholder="-" size="24" class="column_option" value="<?php isset($column) and print $column->getOption('class') ?>" />
      </div>

      <div class="actions">
        <input type="button" value="<?php echo __('Ocultar') ?>" onclick="Layout.column.stopEdition(this); return false;" />
      </div>
    </div>
  </div>
</div>