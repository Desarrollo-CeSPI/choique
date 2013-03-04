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
<?php use_helper('I18N', 'Javascript') ?>

<div class="rectangle">
  <?php echo link_to(__('volver'), 'form/edit?id='.$form->getId()) ?>
</div>

<div id="all-fields">
  <?php foreach (Field::getFieldTypes() as $field): ?>
    <span class="field-block">
      <?php echo Field::getRepresentativeImage($field) ?>
      <?php echo draggable_element($field.'_demo', array('revert' => true)) ?>
    </span>
  <?php endforeach ?>
</div>

<div id="loading" class="rectangle" style="display:none;">
  <?php echo __('Actualizando formulario') ?>
</div>

<div class="form">
  <div class="form-title">
    <?php echo $form->getTitle() ?>
  </div>
  <div class="form-description">
    <h5><?php echo $form->getDescription() ?></h5>
  </div>
  
  <div id="field-editor"></div>

  <div id="form-fields">
    <?php include_partial('formbuilder/form_fields', array('form' => $form)) ?>
  </div>
</div>

<?php echo drop_receiving_element('form-fields', array('url'      => 'formbuilder/addField',
                                                       'with'     => "'form_id=".$form->getId()."&field_type='+encodeURIComponent(element.id)+'&_csrf_token=".csrf_token()."'",
                                                       'accept'   => 'field',
                                                       'update'   => 'form-fields',
                                                       'script'   => true,
                                                       'loading'  => 'Element.show("loading")',
                                                       'complete' => 'Element.hide("loading")')) ?>