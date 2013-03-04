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
 * choiqueUtil
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class choiqueUtil
{
  static $controller = null;

  static public function generateUrl($application, $route, $generate = false)
  {
    if ($generate && sfContext::hasInstance())
    {
      sfConfig::set('sf_no_script_name', true);

      $controller = sfContext::getInstance()->getController();
      $route      = ltrim($controller->genUrl($route, false), '/');
    }

    if ($base_url = self::getApplicationUrl($application))
    {
      return sprintf('%s/%s', $base_url, $route);
    }

    throw new InvalidArgumentException('Unable to generate URL for application '.$application.' / route "'.$route.'". Check config/app.yml for url configurations');
  }

  static protected function getApplicationUrl($application)
  {
    $urls = sfConfig::get('app_choique_url', array());

    return array_key_exists($application, $urls) ? $urls[$application] : false;
  }

}