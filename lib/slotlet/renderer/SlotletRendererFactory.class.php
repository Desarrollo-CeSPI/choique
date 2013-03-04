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
 * SlotletRendererFactory
 *
 * Factory for SlotletRenderer objects.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SlotletRendererFactory
{
  static public function getFor(ISlotlet $slotlet)
  {
    $applying_renderers = array();

    foreach (self::getAll() as $class_name => $renderer)
    {
      if ($renderer->renders($slotlet))
      {
        $applying_renderers[$class_name] = $renderer;
      }
    }

    return $applying_renderers;
  }

  static protected function find()
  {
    return sfFinder::type('file')
      ->name('*.php')
      ->maxdepth(2)
      ->ignore_version_control()
      ->relative()
      ->in(sfConfig::get('sf_root_dir').'/lib/slotlet/renderer')
    ;
  }

  /**
   * Get all the available slotlets.
   *
   * @return array
   */
  static public function getAll()
  {
    $renderers      = array();
    $renderer_files = self::find();

    foreach ($renderer_files as $renderer_file)
    {
      $class_name = preg_replace('/(\.class)?\.php$/', '', $renderer_file);

      if (!self::isValid($class_name))
      {
        continue;
      }

      $renderers[$class_name] = self::get($class_name);
    }

    asort($renderers);

    return $renderers;
  }

  /**
   * Get a new instance of $renderer_class.
   * If $renderer_class does not extend BaseSlotletRenderer, throw an Exception.
   *
   * @throw  LogicException If $renderer_class does not extend BaseSlotletRenderer.
   *
   * @param  string $renderer_class The class for the slotlet renderer. This must extend BaseSlotletRenderer.
   *
   * @return BaseSlotletRenderer The slotlet renderer instance.
   */
  static public function get($renderer_class)
  {
    if (!self::isValid($renderer_class))
    {
      throw new LogicException('The provided class is not a valid Slotlet Renderer as it does not extend BaseSlotletRenderer abstract class: '.$renderer_class);
    }

    return new $renderer_class();
  }

  /**
   * Return TRUE if $renderer_class is a valid Slotlet renderer class,
   * i.e. it extends BaseSlotletRenderer abstract class.
   *
   * @return bool
   */
  static public function isValid($renderer_class)
  {
    $reflection_class = new ReflectionClass($renderer_class);

    return $reflection_class->isSubclassOf('BaseSlotletRenderer');
  }

}