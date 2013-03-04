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
 * VirtualSection
 *
 * Reference class for managing the virtual sections inside Choique CMS.
 * This 'virtual sections' are the pages which have no section but still
 * need a Layout around them.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class VirtualSection
{
  const
    VS_CONTACT    = 1,
    VS_SITEMAP    = 2,
    VS_SEARCH     = 3,
    VS_RSS        = 4,
    VS_WS         = 5,
    VS_ALL_NEWS   = 6,
    VS_ALL_EVENTS = 7,
    VS_PREVIEW    = 8;

  static protected $instance;

  static protected $current;
  
  protected $virtual_sections = array(
    self::VS_CONTACT    => 'Contacto',
    self::VS_SITEMAP    => 'Mapa del sitio',
    self::VS_SEARCH     => 'Resultados de búsqueda',
    self::VS_RSS        => 'Vista completa de canal RSS',
    self::VS_ALL_NEWS   => 'Todas las noticias',
    self::VS_WS         => 'Resultados de búsqueda sobre Web Services',
    self::VS_ALL_EVENTS => 'Vista completa de eventos',
    self::VS_PREVIEW    => 'Previsualización'
  );

  /**
   * Singleton pattern implementation.
   * Return the singleton instace for this class.
   * 
   * @return VirtualSection
   */
  static public function getInstance()
  {
    if (null === self::$instance)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Get all the available virtual sections.
   *
   * @param  boolean $only_keys     TRUE if only the ids are to be returned.
   * @param  boolean $include_blank TRUE if an empty value is to be prepended
   *                                to the resulting elements.
   *
   * @return array
   */
  public function getAll($only_keys = false, $include_blank = false)
  {
    $values = $only_keys ? array_keys($this->virtual_sections) : $this->virtual_sections;

    if ($include_blank)
    {
      $values = array_merge(array('' => ''), $values);
    }
    
    return $values;
  }

  /**
   * Get a virtual section by its id.
   *
   * @param  int $id The id of the desired virtual section.
   *
   * @return string
   */
  public function getById($id)
  {
    if ($this->isValidId($id))
    {
      return $this->virtual_sections[$id];
    }
  }

  /**
   * Return TRUE if $id is a valid virtual section id.
   * 
   * @param  int $id The id of the virtual section.
   *
   * @return boolean
   */
  public function isValidId($id)
  {
    return array_key_exists($id, $this->virtual_sections);
  }

  /**
   * Set $id to be the current virtual section. This value is used by the
   * LayoutHelper to determine if the request is to a virtual section
   * rather than an actual Section object.
   *
   * @param integer $id The current id to set.
   */
  static public function setCurrentId($id)
  {
    self::$current = $id;
  }

  /**
   * Get the id tof the current virtual section, set via setCurrentId().
   * This is used by the LayoutHelper to determine if the request is to
   * a virtual section rather than an actual Section object.
   *
   * @return integer The current id to set or null if none has been set.
   */
  static public function getCurrentId()
  {
    return self::$current;
  }
  
}