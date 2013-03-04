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
<?php use_stylesheet('backend/admin_theme_black') ?>
<?php use_helper('Object', 'Validation', 'ObjectAdmin', 'I18N', 'Date', 'AdvancedAdmin') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Descargar estilo visual')?></h1>

  <div id="sf_admin_content">
    <?php include_partial('flavors/create_messages', array('labels' => isset($labels)?$labels:array())) ?>

    <?php echo form_tag('flavors/downloadFlavor', array('id'=>'sf_admin_edit_form')) ?>
      <fieldset id="sf_fieldset_flavors">
        <h2><?php echo __('Seleccione el estilo visual a descargar')?></h2>
        <div class="form-row">
          <?php echo label_for('flavor', 'Estilo', 'class="required" ') ?>
          <div class="content">
            <?php echo select_tag('flavor', options_for_select(array('current'=>'Actual', 'default' => 'Por defecto'))) ?>
          </div>
          <div style="clear:both; height: 1px; font-size: 1px;">&nbsp;</div>
        </div>

      </fieldset>

      <ul class="sf_admin_actions">
        <li> <?php echo submit_tag(__('Descargar'), 'class=sf_admin_action_save') ?> </li>
      </ul>
    </form>
  </div>
<div>