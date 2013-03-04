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
 * First level navigation menu slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class HoveringFirstLevelNavigationMenuSlotlet extends SecondLevelNavigationMenuSlotlet
{
  public function getDefaultOptions()
  {
    return array(
      'class'        => 'second_level_slotlet',
      'section_name' => sfContext::getInstance()->getRequest()->getParameter('section_name')
    );
  }

  protected function getSections($current_name)
  {
    return Section::getSections(Section::HORIZONTAL);
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el primer nivel de navegación por secciones con menú desplegable.';
  }

  public static function getName()
  {
    return 'Menú primer nivel desplegable';
  }

}