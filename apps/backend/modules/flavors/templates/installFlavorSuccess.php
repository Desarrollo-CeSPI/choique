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
  <h1><?php echo __('Instalar nuevo estilo visual')?></h1>

  <div id="sf_admin_content">
    <?php include_partial('flavors/create_messages', array('labels' => $labels)) ?>
    <div style="margin:5px">
        <p>Tareas a realizar para la carga de un nuevo estilo visual</p>
        <ul style="margin-left:25px;margin-bottom:20px;">
          <li>Descargue desde <?php echo link_to('aquí', 'flavors/downloadFlavor')?> la estructura basica de un estilo</li>
          <li>Descomprima la estructura y realice las modificaciones que desea en los archivos correspondientes</li>
          <li>Vuelva a comprimir la estructura en un archivo ZIP </li>
        </ul>
    </div>

    <?php echo form_tag('flavors/installFlavor','multipart=true', array('id'=>'sf_admin_edit_form')) ?>
      <fieldset id="sf_fieldset_flavors">
        <h2><?php echo __('Archivos del nuevo estilo')?></h2>

        <div class="form-row">
          <?php echo label_for('flavor[name]', __($labels['flavor{name}']), 'class="required" ') ?>
          <div class="content<?php if ($sf_request->hasError('flavor{name}')): ?> form-error<?php endif; ?>">
            <?php if ($sf_request->hasError('flavor{name}')): ?>
              <?php echo form_error('flavor{name}', array('class' => 'form-error-msg')) ?>
            <?php endif; ?>
            <?php $value = input_tag('flavor_name'); echo $value ? $value : '&nbsp;' ?>
          </div>
          <?php echo observe_field('flavor_name', array(
                        'script'    =>  true,
                        'url'       =>  'flavors/checkFlavorExist?_csrf_token='.csrf_token(),
                        'update'    =>  'in-use',
                        'with'      =>  "'name=' + value"
          ))?>
          <div id="in-use"></div>
          <div style="clear:both; height: 1px; font-size: 1px;">&nbsp;</div>
        </div>

        <div class="form-row">
          <?php echo label_for('flavor[file]', __($labels['flavor{file}']), 'class="required" ') ?>
          <div class="content<?php if ($sf_request->hasError('flavor{file}')): ?> form-error<?php endif; ?>">
            <?php if ($sf_request->hasError('flavor{file}')): ?>
              <?php echo form_error('flavor{file}', array('class' => 'form-error-msg')) ?>
            <?php endif; ?>
            <?php $value = input_file_tag('flavor_file'); echo $value ? $value : '&nbsp;' ?>
          </div>
          <div class="sf_admin_edit_help"><?php echo __('Solo se admiten archivos comprimidos en formato ZIP con la estructura de directorios del nuevo estilo visual')?></div>
          <div style="clear:both; height: 1px; font-size: 1px;">&nbsp;</div>
        </div>
      </fieldset>

      <ul class="sf_admin_actions">
        <li> <?php echo submit_tag(__('Subir nuevo estilo'), 'class=sf_admin_action_save') ?> </li>
      </ul>
    </form>
  </div>
<div>