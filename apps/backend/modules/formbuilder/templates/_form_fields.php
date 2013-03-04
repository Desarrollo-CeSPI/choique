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

<?php $fields = FieldPeer::retrieveSortedByForm($form->getId()) ?>
<ul style="list-style:none;" id="sortable-form">
  <?php foreach ($fields as $field): ?>
    <li id="field_<?php echo $field->getId() ?>">
        <div class="form-field-actions">
          <?php echo link_to_remote(image_tag('backend/shape_square_edit.png',
                                              array('alt'   => __('Editar campo'),
                                                    'title' => __('Editar campo'))),
                                    array('url'    => 'formbuilder/showField?field_id='.$field->getId().'&_csrf_token='.csrf_token(),
                                          'update' => 'field-editor',
                                          'script' => true)) ?>
          <?php echo link_to_remote(image_tag('backend/bullet_delete.png',
                                              array('alt'   => __('Eliminar campo'),
                                                    'title' => __('Eliminar campo'))),
                                    array('url'      => 'formbuilder/deleteField?form_id='.$form->getId() . '&field_id=' . $field->getId().'&_csrf_token='.csrf_token(),
                                          'update'   => 'form-fields',
                                          'loading'  => 'Element.show("loading")',
                                          'complete' => 'Element.hide("loading")',
                                          'script'   => true)) ?>
        </div>
      <div id="<?php echo $field->getId() ?>" class="instance-field">
        <?php echo $field->getHTMLRepresentation() ?>
      </div>
    </li>
  <?php endforeach ?>
</ul>

<?php echo sortable_element('sortable-form', array('url' => 'formbuilder/sortFields',
                                                   'script' => true,
                                                   'loading' => 'Element.show("loading")',
                                                   'complete' => 'Element.hide("loading")')) ?>

<div class="rectangle">
  <?php echo __('Para agregar un campo arrástrelo hasta aquí.') ?>
</div>