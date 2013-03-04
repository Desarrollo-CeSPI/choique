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
<div>
  <?php echo radiobutton_tag('shortcut[reference_type]',
                             '0',
                             $shortcut->getReferenceType( )== 0,
                             array("onclick" => 'toggle_reference(0);')) ?>

  <?php echo __('Externo') ?>
</div>

<div>
  <?php echo radiobutton_tag('shortcut[reference_type]',
                             '4',
                             $shortcut->getReferenceType() == 4,
                             array("onclick" => 'toggle_reference(4);')) ?>

  <?php echo __('Externo en Pop Up') ?>
</div>

<div>
  <?php echo radiobutton_tag('shortcut[reference_type]',
                             '1',
                             $shortcut->getReferenceType() == 1,
                             array("onclick" => 'toggle_reference(1);')) ?>

  <?php echo __('Artículo') ?>
</div>

<div>
  <?php echo radiobutton_tag('shortcut[reference_type]',
                             '2',
                             $shortcut->getReferenceType() == 2,
                             array("onclick" => 'toggle_reference(2);')) ?>

  <?php echo __('Sección') ?>
</div>

<div>
  <?php echo radiobutton_tag('shortcut[reference_type]',
                             '3',
                             $shortcut->getReferenceType() == 3,
                             array("onclick" => 'toggle_reference(3);')) ?>

  <?php echo __('Ninguno') ?>
</div>