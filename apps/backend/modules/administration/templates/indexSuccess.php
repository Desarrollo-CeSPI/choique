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
<div id="sf_admin_container">
  <h1><?php echo __('Administración del Sistema') ?></h1>

  <?php if ($sf_flash->has("notice")): ?>
    <div class="save-ok">
      <h2>
        <?php echo __($sf_flash->get("notice")) ?>
      </h2>
    </div>
  <?php endif ?>

  <?php echo form_tag('administration/save') ?>

  <div class="tabber">
      <?php foreach ($parameters as $title => $configs): ?>
        <div class="tabbertab">
        <h2><?php echo __($title) ?></h2>
        <?php foreach ($configs as $key => $config): ?>
            <?php echo $config->getFormRow() ?>
        <?php endforeach ?>
        </div>
      <?php endforeach ?>
  </div>
    <ul class="sf_admin_actions">
      <li>
        <?php echo submit_tag(__('Guardar cambios'), 'class=sf_admin_action_save') ?>
      </li>
    </ul>
  </form>

</div>