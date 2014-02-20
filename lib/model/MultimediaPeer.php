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
 * Subclass for performing query and update operations on the 'multimedia' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MultimediaPeer extends BaseMultimediaPeer
{
  /**
   * Retrieve all Multimedia objects ordered by their titles, optionally
   * filtered by a $criteria.
   *
   * @param  Criteria $criteria The optional filter.
   * @param  PDO      $con      The database connection.
   *
   * @return array The matching multimedias ordered by title.
   */
  public static function retrieveAll($criteria = null, PDO $con = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $criteria->addAscendingOrderByColumn(self::TITLE);

    return self::doSelect($criteria, $con);
  }

  static public function getBySmallURI($s_uri, $con = null)
  {
  $c = new Criteria();
  $c->add(self::SMALL_URI, $s_uri);

  return self::doSelectOne($c);
  }

}