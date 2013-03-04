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
 * Subclass for performing query and update operations on the 'temporary_layout' table.
 *
 *
 *
 * @package lib.model
 */
class TemporaryLayoutPeer extends BaseTemporaryLayoutPeer
{
  static public function create($configuration, PropelPDO $con = null)
  {
    $temporary_layout = new TemporaryLayout();
    $temporary_layout->setLayout($configuration);

    $temporary_layout->save($con);

    return $temporary_layout;
  }

  static public function retrieve($primary_key, PropelPDO $con = null)
  {
    $temporary_layout = self::retrieveByPK($primary_key, $con);

    if (null !== $temporary_layout)
    {
      $temporary_layout->delete($con);

      return $temporary_layout;
    }
  }

}