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
 * BaseSlotletRenderer
 *
 * Base class for Slotlet renderers. A slotlet renderer can be attached to a
 * Slotlet an be used for rendering it, decoupling the presentation of the
 * logic, avoiding duplication of logic.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
abstract class BaseSlotletRenderer
{
  protected
    $options = array(),
    $slotlet = null;

  /**
   * Return the user-oriented name for this renderer.
   *
   * @return string
   */
  abstract public function getName();

  /**
   * Return the user-oriented description for this renderer.
   *
   * @return string
   */
  abstract public function getDescription();

  /**
   * Perform the actual rendering process of the related slotlet.
   * This method should return the resulting HTML.
   *
   * @return string
   */
  abstract protected function doRender();

  /**
   * Return an array holding the names of the classes that this slotlet
   * renderer is able to render.
   *
   * @return array
   */
  abstract protected function renderableClasses();

  /**
   * Return a boolean value indicating whether this slotlet renderer
   * is able to render $slotlet.
   *
   * @param  ISlotlet $slotlet The slotlet to test.
   *
   * @return bool
   */
  public function renders(ISlotlet $slotlet)
  {
    return in_array(get_class($slotlet), $this->renderableClasses());
  }

  /**
   * Return the required Javascript files for using this renderer.
   *
   * @return array
   */
  public function getJavascripts()
  {
    return array();
  }

  /**
   * Return the required Cascading StyleSheet files for using this renderer.
   *
   * @return array
   */
  public function getStylesheets()
  {
    return array();
  }

  /**
   * Render $slotlet using an array of optional configuration parameters.
   *
   * @see    doRender()
   *
   * @param  ISlotlet $slotlet The slotlet to render.
   * @param  array    $options The array of configuration parameters.
   *
   * @return string
   */
  public function render(ISlotlet $slotlet, $options = array())
  {
    $this->slotlet = $slotlet;
    $this->options = array_merge($this->getDefaultOptions(), $options);

    return $this->doRender();
  }

  /**
   * Get the related Slotlet (The one that's being rendered).
   *
   * @return ISlotlet
   */
  public function getSlotlet()
  {
    return $this->slotlet;
  }

  /**
   * Get the value of the option $name, and if it hasn't been defined yet,
   * return $default.
   *
   * @param  string $name    The option name.
   * @param  mixed  $default The default value to return when the option is not defined.
   *
   * @return mixed  The value for option $name, or $default.
   */
  public function getOption($name, $default = null)
  {
    if (array_key_exists($name, $this->options))
    {
      return $this->options[$name];
    }

    return $default;
  }

  /**
   * Get the default options for this renderer.
   * Override this method if default options are to be specified for subclasses.
   *
   * @return array
   */
  public function getDefaultOptions()
  {
    return array();
  }

  public function __toString()
  {
    return sprintf('%s (%s)', $this->getName(), $this->getDescription());
  }
}