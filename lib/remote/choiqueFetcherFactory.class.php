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
 * choiqueFetcherFactory
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class choiqueFetcherFactory
{
  static private $class = null;

  /**
   * Get a fetcher.
   * The best possible fetcher will be guessed.
   *
   * @return FetcherInterface
   */
  static public function get()
  {
    if (null === self::$class)
    {
      self::$class = self::guessClass();
    }

    $k = self::$class;

    return new $k();
  }

  static private function guessClass()
  {
    if (choiqueCurlFetcher::isCurlAvailable())
    {
      return 'choiqueCurlFetcher';
    }
    else
    {
      return 'choiqueSimpleFetcher';
    }
  }

}