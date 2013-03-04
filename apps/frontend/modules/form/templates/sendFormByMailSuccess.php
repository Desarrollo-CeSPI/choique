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
<h1>
Se ha completado el formulario: <?php echo $form->getTitle();?>
</h1>

<p>Los datos Ingresados son los siguientes:</p>
<table border="1">
  <tr>
    <th>Campo del Formulario</th>
    <th>Valor Introducido</th>
  </tr>
  <?php foreach ($form->getFields() as $field): ?>
    <?php $data = DataPeer::getDataByRowAndFieldId($row, $field->getId()) ?>
    <tr>
      <th style="text-align: left;"><?php echo ($field->getLabel())?$field->getLabel():__('Etiqueta') ?></th>
      <td><?php echo ($data ? $data : "&nbsp;") ?></td>
    </tr>
  <?php endforeach; ?>
</table>