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
 * choiqueSimpleFetcher
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class choiqueSimpleFetcher implements FetcherInterface
{
  public function fetch($url)
  {
    $browser = $this->getBrowser();

    if ($browser->get($url)->responseIsError())
    {
      throw new RuntimeException(sprintf('Unable to fetch %s. Status code %d: %s.', $url, $browser->getResponseCode(), $browser->getResponseMessage()));
    }

    return $browser->getResponseText();
  }

  /**
   * Get an instance of sfWebBrowser for fetching URLs.
   * 
   * @return sfWebBrowser
   */
  protected function getBrowser()
  {
    return new sfWebBrowser();
  }

}