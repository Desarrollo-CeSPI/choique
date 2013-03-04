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
  <?php echo content_tag('div', content_tag('h1', $form->getDescription(), array('class' => 'form-description-title')), array('class' => 'form-description')) ?>
  <?php echo tag('/div', array(), true) ?>
  <?php echo tag('div', array('id' => "results"), true) ?>
  <?php if ($rows = $form->getRows()): ?>
    <?php foreach ($form->getFields() as $field): ?>
      <?php echo tag('div', array('class' => "results-row"), true) ?>
      <?php echo content_tag('div', ($field->getLabel())?$field->getLabel():__('Etiqueta'), array('class' => 'results-first-td')) ?>
      <?php echo content_tag('div', sprintf("%.2f%s", $field->getPercentage(), '%'), array('class' => 'results-second-td')) ?>
      <?php echo tag('/div', array(), true) ?>
    <?php endforeach ?>
  <?php endif ?>
  <?php echo tag('/div', array(), true) ?>
<?php else: ?>
  <div class="rectangle">
    <div class="form-description">
      <h1 class="form-description-title"><?php echo __('Resultados del formulario').' "'.$form->getTitle().'"' ?><h1>
      <?php echo content_tag('div', content_tag('h2', $form->getDescription(), array('class' => 'form-description-subtitle')), array('class' => 'form-description')) ?>
    </div>
  </div>
  <div id="results">
    <?php if ($rows = $form->getRows()): ?>
      <?php foreach ($form->getFields() as $field): ?>
        <div class="results-header"><?php echo ($field->getLabel())?$field->getLabel():__('Etiqueta') ?></div>
      <?php endforeach ?>
      <?php for ($i = 1; $i <= $rows; $i++): ?>
        <div class="result-row">
          <?php foreach ($form->getFields() as $field): ?>
            <div class="results-td-field"><?php echo DataPeer::getDataByRowAndFieldId($i, $field->getId()) ?></div>
          <?php endforeach ?>
        </div>
      <?php endfor ?>
    <?php else: ?>
      <?php echo __('No se arrojaron resultados.') ?>
    <?php endif ?>
  </div>
<?php endif ?>