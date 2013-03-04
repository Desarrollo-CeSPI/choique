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
 * Adds a paging mechanism similar to sfPropelPager to the results.  This is
 * meant to be as similar to sfPager and sfPropelPager as possible.
 *
 * TODO: Find a more efficient way to do paging!  Right now, it has to return
 * the entire result set and do an array_slice() on it.
 *
 */
class sfChoiquePager
{
  protected $results = array();
  protected $page = 1, $perPage = 5;

  public function __construct($results, $maxPerPage = 5)
  {
    $this->perPage = $maxPerPage;

    if (!is_array($results))
    {
      throw new Exception('Pager constructor expects the results to be an array, ' . gettype($results) . ' given');
    }

    $this->results = $results;
  }

  /**
   * Hook for sfMixer
   */
  /*
  public function __call($a, $b)
  {
    return sfMixer::callMixins();
  }

  public function getSearch()
  {
    return $this->search;
  }
  */
  public function getLinks($nb_links = 5)
  {
    $links = array();
    $tmp   = $this->getPage() - floor($nb_links / 2);
    $check = $this->getLastPage() - $nb_links + 1;
    $limit = ($check > 0) ? $check : 1;
    $begin = ($tmp > 0) ? (($tmp > $limit) ? $limit : $tmp) : 1;

    $i = $begin;
    while (($i < $begin + $nb_links) && ($i <= $this->getLastPage()))
    {
      $links[] = $i++;
    }

    return $links;
  }

  public function haveToPaginate()
  {
    return (($this->getPage() != 0) && ($this->getNbResults() > $this->getMaxPerPage()));
  }

  public function getMaxPerPage()
  {
    return $this->perPage;
  }

  public function setMaxPerPage($per)
  {
    $this->perPage = $per;
  }

  public function setPage($page)
  {
    $this->page = $page;
  }

  public function getPage()
  {
    return $this->page;
  }

  public function getResults()
  {
    $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
    $limit = $this->getMaxPerPage();

    if ($limit == 0)
    {
      $results = $this->results;
    }
    else
    {
      $results = array_slice($this->results, $offset, $limit);
    }

    return $results;
  }

  public function getNbResults()
  {
    return count($this->results);
  }

  public function getFirstPage()
  {
    return 1;
  }

  public function getLastPage()
  {
    return ceil($this->getNbResults() / $this->getMaxPerPage());
  }

  public function getNextPage()
  {
    return min($this->getPage() + 1, $this->getLastPage());
  }

  public function getPreviousPage()
  {
    return max($this->getPage() - 1, $this->getFirstPage());
  }

  public function getFirstIndice()
  {
    if ($this->getPage() == 0)
    {
      return 1;
    }
    else
    {
      return ($this->getPage() - 1) * $this->getMaxPerPage() + 1;
    }
  }

  public function getLastIndice()
  {
    if ($this->getPage() == 0)
    {
      return $this->getNbResults();
    }
    else
    {
      if (($this->getPage() * $this->getMaxPerPage()) >= $this->getNbResults())
      {
        return $this->getNbResults();
      }
      else
      {
        return ($this->getPage() * $this->getMaxPerPage());
      }
    }
  }
}