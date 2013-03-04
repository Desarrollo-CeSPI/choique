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
<ul id="available_slotlets">
  <?php foreach ($slotlets as $class => $attributes): ?>
    <li class="available_slotlet">
      <span class="class" style="display: none;"><?php echo $class ?></span>

      <span class="name"><?php echo __($attributes['name']) ?></span>
      <a href="#" class="toggle_description" onclick="jQuery(this).nextAll('.description:first').slideToggle(250); return false;"><?php echo __('Descripción') ?></a>
      <a class="add_slotlet" href="#" onclick="Layout.slotlet.add(this); return false;"><?php echo __('Agregar') ?></a>
      <span class="description" style="display: none;"><?php echo __($attributes['description']) ?></span>
    </li>
  <?php endforeach; ?>
</ul>