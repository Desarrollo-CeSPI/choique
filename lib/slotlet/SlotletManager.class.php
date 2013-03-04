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
 * SlotletManager
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SlotletManager
{
  static protected $_main_content;

  /**
   * Get all the available slotlets.
   *
   * @return array
   */
  static public function getAll()
  {
    $slotlets      = array();
    $slotlet_files = sfFinder::type('file')->name('*.php')->maxdepth(2)->ignore_version_control()->relative()->in(sfConfig::get('sf_root_dir').'/lib/slotlet/slotlets');

    foreach ($slotlet_files as $slotlet_file)
    {
      $class_name = preg_replace('/(\.class)?\.php$/', '', $slotlet_file);

      if (!self::isValid($class_name))
      {
        continue;
      }

      $slotlets[$class_name] = array('name' => call_user_func(array($class_name, 'getName')), 'description' => call_user_func(array($class_name, 'getDescription')));
    }

    asort($slotlets);

    return $slotlets;
  }

  /**
   * Get a new instance of $slotlet_class.
   * If $slotlet_class does not implement ISlotlet, throw an Exception.
   *
   * @throw  LogicException If $slotlet_class does not implement ISlotlet.
   *
   * @param  string $slotlet_class The class for the slotlet. This must implement ISlotlet.
   *
   * @return ISlotlet The slotlet instance.
   */
  static public function get($slotlet_class)
  {
    if (!self::isValid($slotlet_class))
    {
      throw new LogicException('The provided class is not a valid Slotlet as it does not implement ISlotlet interface: '.$slotlet_class);
    }

    return new $slotlet_class();
  }

  /**
   * Return TRUE if $slotlet_class is a valid Slotlet class, i.e. it implements
   * ISlotlet interface.
   *
   * @return bool Whether $slotlet_class is a valid class of Slotlets.
   */
  static public function isValid($slotlet_class)
  {
    $reflection_class = new ReflectionClass($slotlet_class);

    return $reflection_class->implementsInterface('ISlotlet');
  }

  /**
   * Render a slotlet of class $slotlet_class passing $options to it.
   * This method automatically adds required javascripts and stylesheets to the
   * web response.
   *
   * @param  string $slotlet_class The class of the slotlet (must implement ISlotlet).
   * @param  array  $options       Options for the slotlet (optional)
   *
   * @return string The rendered slotlet.
   */
  static public function render($slotlet_class, $options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Url', 'Tag', 'Asset'));

    $slotlet = self::get($slotlet_class);

    if (!isset($options['main_content']) || null === $options['main_content'])
    {
      $options['main_content'] = self::getMainContent();
    }

    $options = array_merge($slotlet->getDefaultOptions(), $options);

    $slotlet->options = $options;

    self::addStylesheets($slotlet->getStylesheets());
    self::addJavascripts($slotlet->getJavascripts());

    return $slotlet->render($options);
  }

  /**
   * Get a configuration form for a Slotlet of class $slotlet_class,
   * optionally providing the $values to be set on it.
   *
   * @param  string $slotlet_class The class of the slotlet.
   * @param  array  $values        Values for the form (optional).
   *
   * @return string The HTML form.
   */
  static public function getConfigurationForm($slotlet_class, $values = array())
  {
    sfLoader::loadHelpers(array('Form', 'Tag', 'I18N'));

    $slotlet = self::get($slotlet_class);
    $values  = array_merge($slotlet->getDefaultOptions(), $values);

    return $slotlet->getConfigurationForm($values);
  }

  /**
   * Get the response object.
   *
   * @param  sfContext $context The context (optional)
   *
   * @return sfWebResponse
   */
  static protected function getResponse(sfContext $context = null)
  {
    if (null === $context)
    {
      $context = sfContext::getInstance();
    }

    return $context->getResponse();
  }

  /**
   * Add $stylesheets to response.
   *
   * @param array $stylesheets
   */
  static public function addStylesheets($stylesheets)
  {
    $response = self::getResponse();

    foreach ($stylesheets as $stylesheet)
    {
      $response->addStylesheet($stylesheet);
    }
  }

  /**
   * Add $javascripts to response.
   *
   * @param array $javascripts
   */
  static public function addJavascripts($javascripts)
  {
    $response = self::getResponse();

    foreach ($javascripts as $javascript)
    {
      $response->addJavascript($javascript);
    }
  }

  /**
   * Set the main content to $main_content.
   *
   * @return mixed The main content.
   */
  static public function setMainContent($main_content)
  {
    self::$_main_content = $main_content;

    return $main_content;
  }

  /**
   * Get the main content object (if any has been set).
   *
   * @return mixed The main content object.
   */
  static public function getMainContent()
  {
    return self::$_main_content;
  }
  
}