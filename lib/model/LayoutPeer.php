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
 * Subclass for performing query and update operations on the 'layout' table.
 *
 *
 *
 * @package lib.model
 */
class LayoutPeer extends BaseLayoutPeer
{
  const DEFAULT_LAYOUT_NAME = 'Distribución por defecto';

  static protected $_active;

  /**
   * Get the default layout. If none has been defined, return an empty layout.
   *
   * @param PDO $con Database connection (optional)
   *
   * @return Layout
   */
  static public function retrieveDefault($con = null)
  {
    $criteria = new Criteria();

    $criteria->add(self::IS_DEFAULT, true);

    $default_layout = self::doSelectOne($criteria, $con);

    if (null === $default_layout)
    {
      $default_layout = self::createDefaultLayout();
    }

    return $default_layout;
  }

  static public function retrieveByVirtualSectionId($virtual_section_id, $con = null)
  {
    $criteria = new Criteria();

    $criteria->add(self::VIRTUAL_SECTION_ID, $virtual_section_id);

    return self::doSelectOne($criteria, $con);
  }

  /**
   * Create a new object to be used a default (empty) layout.
   *
   * This is a fallback method to be used when no default layout has been set.
   *
   * @return Layout
   */
  static private function createDefaultLayout()
  {
    $layout = new Layout();

    $layout->setIsDefault(true);
    $layout->setName(self::DEFAULT_LAYOUT_NAME);

    return $layout;
  }

  /**
   * Get the active Layout (or the default one).
   *
   * @param  PDO $con Database connection (optional)
   *
   * @return Layout
   */
  static public function active($con = null)
  {
    return self::$_active;
  }

  /**
   * Set the active Layout to be $layout for this request.
   *
   * @param Layout $layout The Layout to set as active.
   *
   * @return Layout $layout
   */
  static public function setActive(Layout $layout)
  {
    return self::$_active = $layout;
  }

  static public function getNameForDuplicate($original_name, $suffix = ' copia')
  {
    $criteria = new Criteria();

    $criteria->add(self::NAME, $original_name.'%', Criteria::LIKE);
    $criteria->addDescendingOrderByColumn(self::NAME);

    $match = self::doSelectOne($criteria);

    if (null === $match)
    {
      $match = $original_name;
    }
    else
    {
      $match = $match->getName();
    }

    return $match.$suffix;
  }

  static public function mobileExists() 
  {
    //No chequeo si tiene VS_MOBILE_CONTENT porque podría querer hacer solo la home mobile...
    return LayoutPeer::retrieveByVirtualSectionId(VirtualSection::VS_MOBILE_HOME) != NULL;
  }

}
