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

<div id="external_reference">
  <?php echo input_tag('shortcut[external_reference_value]',
                       (($shortcut && ($shortcut->getReferenceType() == 0 || $shortcut->getReferenceType() == 4)) ? $shortcut->getReference() : null),
                       array('size' => 80)) ?>
</div>

<div id="article_reference">
  <?php echo select_tag('shortcut[article_reference_value]',
                        objects_for_select(ArticlePeer::retrievePublished(),
                                           'getId',
                                           '__toString',
                                           (($shortcut && $shortcut->getReferenceType() == 1) ? $shortcut->getReference() : null),
                                           'include_blank=true')) ?>
</div>

<div id="section_reference">
  <?php echo select_tag('shortcut[section_reference_value]',
                        objects_for_select(SectionPeer::getSectionsTree(),
                                           'getId',
                                           'getPaddedToString',
                                           (($shortcut && $shortcut->getReferenceType() == 2) ? $shortcut->getReference() : null),
                                           'include_blank=true')) ?>
</div>

<div id="none_reference">
  <?php echo input_hidden_tag('shortcut[none_reference_value]', "Ninguno") ?>
  <?php echo __("El tipo de enlace seleccionado no requiere ningún parametro") ?>
</div>

<?php echo javascript_tag("
function toggle_reference(option)
{
  switch(parseInt(option)) {
    case 0:
    case 4:
      $('external_reference').show();
      $('article_reference').hide();
      $('section_reference').hide();
      $('none_reference').hide();
      break;
    case 1:
      $('external_reference').hide();
      $('article_reference').show();
      $('section_reference').hide();
      $('none_reference').hide();
      break;
    case 2:
      $('external_reference').hide();
      $('article_reference').hide();
      $('section_reference').show();
      $('none_reference').hide();
      break;
    case 3:
    case 5:
    case 6:
      $('external_reference').hide();
      $('article_reference').hide();
      $('section_reference').hide();
      $('none_reference').show();
      break;
  }

  return false;
}

toggle_reference(" . $shortcut->getReferenceType() . ");") ?>
