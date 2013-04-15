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
 * choiqueCurlFetcher
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class choiqueCurlFetcher implements FetcherInterface
{
  /**
   * Fetch the contents of $url and return them.
   * If an error occurs, an Exception must be thrown.
   *
   * @throws Exception
   *
   * @param  string $url The URL to fetch.
   *
   * @return string The contents of $url.
   */
  public function fetch($url)
  {
    if (!self::isCurlAvailable())
    {
      throw new RuntimeException(
        sprintf('cURL extension not loaded. Unable to fetch %s', $url)
      );
    }

    $curl_handle = curl_init($url);

    if (false === $curl_handle)
    {
      throw new RuntimeException(
        sprintf('An error occurred when trying to initialize cURL session for %s', $url)
      );
    }

    curl_setopt_array($curl_handle, array(
      CURLOPT_VERBOSE        => false,
      CURLOPT_CONNECTTIMEOUT => 3,
      CURLOPT_TIMEOUT        => 3,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false
    ));

    $contents = curl_exec($curl_handle);

    if (curl_errno($curl_handle))
    {
      throw new RuntimeException(
        sprintf('The following error occured when trying to fetch %s: %s', $url, curl_error($curl_handle))
      );
    }

    curl_close($curl_handle);

    return $contents;
  }

  /**
   * Answer whether cURL is available.
   *
   * @return bool
   */
  static public function isCurlAvailable()
  {
    return in_array('curl', get_loaded_extensions()) || function_exists('curl_init');
  }
  
}
