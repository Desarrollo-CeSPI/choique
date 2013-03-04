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
<?php use_helper('Javascript') ?>

<div align="center">
<?php if ($class != 1): ?>
  <?php echo select_tag('element', options_for_select($options, '', 'include_blank=true'), array('onChange' => "$('element_id').value = $(this).getValue();")) ?>
<?php else: ?>
  <select id="element" onchange="$('element_id').value = $(this).getValue();" name="element" size="3" style="width:50%;">
    <?php foreach ($options as $key => $value): ?>
      <?php $multimedia = MultimediaPeer::retrieveByPk($key) ?>
      <option value="<?php echo $key ?>" style="background-image: url('<?php echo Multimedia::relativeUriFor($multimedia->getSmallUri()) ?>');
                                                            background-repeat: no-repeat;
                                                            background-color: #ffffff;
                                                            color : #333333;
                                                            font-family : Arial, Verdana, Helvetica, Sans-Serife;
                                                            text-align:right;
                                                            height: 100px;
                                                            padding-left:35px;">
        <?php echo $value ?>
      </option>
    <?php endforeach ?>
  </select>
<?php endif ?>
</div>