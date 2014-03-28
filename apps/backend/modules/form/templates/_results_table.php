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
<?php use_helper('I18N') ?>

<?php if ($form->getIsPoll()): ?>
  <?php echo tag('div', array('class' => 'rectangle'), true) ?>
  <?php echo content_tag('div', content_tag('h5', $form->getDescription()), array('class' => 'form-description')) ?>
  <?php echo tag('/div', array(), true) ?>
  <?php echo tag('table', array('id' => "results"), true) ?>
  <?php if ($rows = $form->getRows()): ?>
    <?php foreach ($form->getFields() as $field): ?>
      <?php echo tag('tr', array(), true) ?>
      <?php echo content_tag('td', ($field->getLabel())?$field->getLabel():__('Etiqueta')) ?>
      <?php echo content_tag('td', sprintf("%.2f%s", $field->getPercentage(), '%')) ?>
      <?php echo tag('/tr', array(), true) ?>
    <?php endforeach ?>
  <?php endif ?>
  <?php echo tag('/table', array(), true) ?>
<?php else: ?>
  <div class="rectangle">
    <div class="form-description">
      <h3><?php echo __('Resultados del formulario').' "'.$form->getTitle().'"' ?></h3>
      <?php echo content_tag('div', content_tag('h5', $form->getDescription()), array('class' => 'form-description')) ?>
    </div>
  </div>
  <table id="results">
    <?php if ($rows = $form->getRows()): ?>
      <?php foreach ($form->getFields() as $field): ?>
        <th><?php echo ($field->getLabel())?$field->getLabel():__('Etiqueta') ?></th>
      <?php endforeach ?>
      <?php for ($i = 1; $i <= $rows; $i++): ?>
        <tr>
          <?php foreach ($form->getFields() as $field): ?>
            <td>
              <?php if ($data = DataPeer::getDataByRowAndFieldId($i, $field->getId())): ?>
                <?php echo esc_entities($data->getData()) ?>
              <?php endif ?>
            </td>
          <?php endforeach ?>
        </tr>
      <?php endfor ?>
    <?php else: ?>
      <?php echo __('No se arrojaron resultados.') ?>
    <?php endif ?>
  </table>
<?php endif ?>
