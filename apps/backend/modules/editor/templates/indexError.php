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
<?php include_partial('editor/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Editor de estilos visuales - editando estilo "%flavor%"', array('%flavor%' => choiqueFlavors::getInstance()->current())) ?></h1>

  <div id="sf_admin_header">
    <?php include_partial('editor/flashes') ?>
  </div>

  <div id="sf_admin_content" class="css-editor-container">
  </div>
</div>