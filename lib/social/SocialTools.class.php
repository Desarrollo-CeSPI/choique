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
 * SocialTools
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SocialTools
{
  static public function optionsForSelect()
  {
    return array(
      'addthis'   => 'AddThis',
      'sharethis' => 'ShareThis'
    );
  }

  /**
   * Get a Social Tool instance by its unique name (as in optionsForSelect()).
   * 
   * @throws RuntimeException
   *
   * @param  string $name The unique name of the Social Tool.
   *
   * @return SocialToolInterface
   */
  static public function get($name)
  {
    $available = self::optionsForSelect();

    if (array_key_exists($name, $available))
    {
      $class = sprintf('%sSocialTool', $available[$name]);

      return new $class();
    }
    else
    {
      throw new RuntimeException(sprintf('Unable to find social tool class for "%s".', $name));
    }
  }
}