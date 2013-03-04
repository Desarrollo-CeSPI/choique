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
 * Subclass for performing query and update operations on the 'template' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TemplatePeer extends BaseTemplatePeer
{
  /**
   * Get a template by its public_name attribute.
   * 
   * @param  string $public_name Template's public name
   *
   * @return Template
   */
  static public function retrieveByPublicName($public_name)
  {
    $criteria = new Criteria();

    $criteria->add(self::PUBLIC_NAME, $public_name);

    return self::doSelectOne($criteria);
  }
}