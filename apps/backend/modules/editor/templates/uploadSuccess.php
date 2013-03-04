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
<?php if ($sf_request->hasErrors()): ?>
<div class="editor-error">
  <a href="#" class="close-btn" onclick="jQuery(this).closest('.editor-error').slideUp(250); return false;">
    <?php echo __('Ocultar') ?>
  </a>

  <?php foreach ($sf_request->getErrorNames() as $name): ?>
    <div><?php echo __($sf_request->getError($name)) ?></div>
  <?php endforeach; ?>
</div>
  
<?php endif; ?>

<?php include_partial('editor/upload', array('base' => $base_path)) ?>