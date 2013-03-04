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
 * Dynamic background manager.
 *
 * NOTE: Dynamic background image files *MUST* be
 * placed in web/images/dynbg/ directory.
 *
 * @author ncuesta <ncuesta@cespi.unlp.edu.ar>
 */
class DynamicBackground
{
  static protected 
    $attribute_name = 'choique.dynamic_background';

  static public function getImage(sfUser $user)
  {
    if (!$user->hasAttribute(self::$attribute_name))
    {
      $user->setAttribute(self::$attribute_name, self::getRandomImage());
    }

    return $user->getAttribute(self::$attribute_name);
  }

  static protected function getRandomImage()
  {
    $directory = sfConfig::get('sf_web_dir').'/images/frontend/dynbg';
    $images    = sfFinder::type('file')->prune('.svn')->name('*.jpg')->name('*.png')->name('*.gif')->in($directory);
    
    if (!empty($images))
    {
      shuffle($images);

      return 'frontend/dynbg/'.basename($images[0]);
    }

    return null;
  }
}