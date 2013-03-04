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
 * ISlotlet interface
 *
 * Interface that *must* be implemented by Slotlet classes.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
interface ISlotlet
{
  /**
   * Get the name of this slotlet.
   *
   * @return string The name of this slotlet.
   */
  static public function getName();

  /**
   * Get the description of this slotlet.
   *
   * @return string The description of this slotlet.
   */
  static public function getDescription();

  /**
   * Get the stylesheets needed by this slotlet.
   *
   * @return array The stylesheets needed by this slotlet.
   */
  public function getStylesheets();

  /**
   * Get the javascript files needed by this slotlet.
   *
   * @return array The javascript files needed by this slotlet.
   */
  public function getJavascripts();

  /**
   * Get the default options for this slotlet.
   *
   * @return array The default options for this slotlet.
   */
  public function getDefaultOptions();

  /**
   * Render the slotlet and return the HTML snippet that such process
   * generates. May receive $options as an array of configuration options for
   * the slotlet.
   *
   * @return string The rendered slotlet.
   */
  public function render($options = array());

  /**
   * Get an HTML configuration form for this slotlet class.
   *
   * @param array $values An optional set of values to set.
   *
   * @return string The HTML form.
   */
  public function getConfigurationForm($values = array());
}