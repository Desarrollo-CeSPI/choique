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
<td class="slotlet_controls">
  <a href="#" onclick="Layout.slotlet.swap(this, 'top'); return false;"><?php echo image_tag('backend/control-stop-090.png', array('alt' => __('Mover primero'), 'title' => __('Mover este slotlet al primer lugar en su columna'))) ?></a>
  <a href="#" onclick="Layout.slotlet.swap(this, 'up'); return false;"><?php echo image_tag('backend/arrow-090-medium.png', array('alt' => __('Mover arriba'), 'title' => __('Mover este slotlet hacia arriba'))) ?></a>
  <a href="#" onclick="Layout.slotlet.swap(this, 'down'); return false;"><?php echo image_tag('backend/arrow-270-medium.png', array('alt' => __('Mover abajo'), 'title' => __('Mover este slotlet hacia abajo'))) ?></a>
  <a href="#" onclick="Layout.slotlet.swap(this, 'bottom'); return false;"><?php echo image_tag('backend/control-stop-270.png', array('alt' => __('Mover último'), 'title' => __('Mover este slotlet al último lugar en su columna'))) ?></a>
  <div class="actions_separator_vertical">&nbsp;</div>
  <a href="#" onclick="Layout.slotlet.remove(this); return false;"><?php echo image_tag('backend/cross.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar este slotlet'))) ?></a>
</td>
