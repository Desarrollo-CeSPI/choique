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
<?php use_helper('I18N', 'Javascript', 'UJS') ?>

<div class="poll-error-response<?php echo isset($no_javascript)?'-accessible':'';?>">
  <div class="poll-error">
    <?php echo $sf_flash->get('error') ?>
  </div>
  <div class="poll-error-link">
    <?php echo UJS_link_to_function(__('reintentar'), 'window.history.go(0)'); ?>
    <noscript>
      <a href="<?php echo $sf_request->getReferer(); ?>"><?php echo __('reintentar'); ?></a>
    </noscript>
  </div>
</div>