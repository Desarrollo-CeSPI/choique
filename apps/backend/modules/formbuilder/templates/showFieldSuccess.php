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
<?php use_helper('Form', 'Javascript', 'I18N') ?>

<div id="edition-form">
  <h2><?php echo __('Editar campo') ?></h2>
  <?php echo form_remote_tag(array('url'  => 'formbuilder/editField',
                                 'script' => true,
                                 'complete' => "Element.hide($('edition-form'))",
                                 'update' => 'form-fields')) ?>
    <?php echo input_hidden_tag('field_id', $efield->getId()) ?>

    <div class="field-editor-row">
      <?php echo label_for('label', __('Etiqueta')) ?>
      <?php echo input_tag('label', $efield->getLabel()) ?>
    </div>

    <div class="field-editor-row">
      <?php if ($efield->getType() == Field::SELECT || $efield->getType() == Field::TEXTAREA): ?>

        <?php echo label_for('default_value', __('Valor por defecto')) ?>

        <?php echo textarea_tag('default_value', $efield->getDefaultValue()) ?>
        <?php if ($efield->getType() == Field::SELECT): ?>
          <div class="help"><?php echo __('Agregue las distintas opciones en distintos renglones') ?></div>
        <?php endif ?>

      <?php elseif ($efield->getType() == Field::INPUT_TYPE_RADIO): ?>

        <?php echo label_for('default_value', __('Grupo')) ?>
        <?php echo input_tag('default_value', $efield->getDefaultValue()) ?>

      <?php else: ?>

        <?php echo label_for('default_value', __('Valor por defecto')) ?>
        <?php echo input_tag('default_value', $efield->getDefaultValue()) ?>

      <?php endif ?>
    </div>

    <div class="field-editor-row">
      <?php echo label_for('is_required', __('Obligatorio?')) ?>
      <?php echo checkbox_tag('is_required', '', $efield->getIsRequired()) ?>
    </div>

    <div class="field-editor-row-actions">
      <?php echo reset_tag(__('limpiar')) ?>
      <?php echo submit_tag(__('guardar')) ?>
    </div>
  </form>
</div>