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
<?php

/**
 * Section Links slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SectionLinksSlotlet extends SectionDocumentsSlotlet
{
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'class' => 'links_slotlet',
      'title' => __('Enlaces')
    ));
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_links.css');
  }

  protected function getObjects($section)
  {
    if (null !== $section)
    {
      return $section->getLinks();
    }

    return array();
  }

  protected function renderObject($object)
  {
    return $object->getName();
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra los enlaces asociados a la sección actual.';
  }

  public static function getName()
  {
    return 'Enlaces';
  }
  
}