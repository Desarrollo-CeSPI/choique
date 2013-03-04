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
  <h1><?php echo __('Eliminar estilo visual')?></h1>

  <div id="sf_admin_content">
    <?php include_partial('flavors/create_messages', array('labels' => $labels)) ?>
    <div style="margin:10px">

    <?php echo form_tag('flavors/deleteFlavor', array('id'=>'sf_admin_edit_form')) ?>
      <fieldset id="sf_fieldset_flavors">
        <h2><?php echo __('Estilos actualmente instalados')?></h2>
        <div class="form-row">
          <?php echo label_for('flavor[select]', __($labels['flavor{select}']), 'class="required" ') ?>
          <div class="content<?php if ($sf_request->hasError('flavor{select}')): ?> form-error<?php endif; ?>">
            <?php if ($sf_request->hasError('flavor{select}')): ?>
              <?php echo form_error('flavor{select}', array('class' => 'form-error-msg')) ?>
            <?php endif; ?>
            <div class='flavors_list'>
              <ul>
              <?php foreach (choiqueFlavors::getInstance()->getAll(true, false) as $flavor): ?>
                <li>
                  <?php echo checkbox_tag('flavors[]',$flavor, array() )?>
                  <?php echo $flavor ?> 
                </li>
             <?php endforeach?>
              </ul>
            </div>
          </div>
          <div style="clear:both; height: 1px; font-size: 1px;">&nbsp;</div>
        </div>

      </fieldset>

      <ul class="sf_admin_actions">
        <li> <?php echo submit_tag(__('Eliminar estilo visual seleccionado'), 'class=sf_admin_action_save') ?> </li>
      </ul>
    </form>
  </div>
<div>