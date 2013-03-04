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

class SlotletRenderer
{
  private static $instance;
  
  public static function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new SlotletRenderer();
    }

    return self::$instance;
  }

  public function getSlotlets($type)
  {
    $layout_id = sfConfig::get('layout_id', 1);

    $c = new Criteria();
    $c->add(SlotletLayoutSlotletPeer::SLOTLET_LAYOUT_ID,$layout_id);
    $c->add(SlotletLayoutSlotletPeer::SLOTLET_TYPE,$type);
    $c->addAscendingOrderByColumn(SlotletLayoutSlotletPeer::PRIORITY);

    $slotlets = array();
    foreach (SlotletLayoutSlotletPeer::doSelect($c) as $slotletls)
    {
      $slotlets[] = $slotletls->getSlotlet();
    }

    return $slotlets;
  }
  
  public static function getSlotletsClases() 
  {
    $classes       = array();
    $classes_files = sfFinder::type('file')->name('*.php')->maxdepth(2)->ignore_version_control()->relative()->in(SF_ROOT_DIR.'/lib');
    $classes_files = array_merge($classes_files, sfFinder::type('file')->name('*.php')->maxdepth(0)->ignore_version_control()->relative()->in(SF_ROOT_DIR.'/lib/model'));

    foreach ($classes_files as $class_file)
    {
      $file_name = SF_ROOT_DIR.'/lib/'.$class_file;
      if (!file_exists($file_name))
      {
        $file_name = SF_ROOT_DIR.'/lib/model/'.$class_file;
      }
      if (preg_match('/implements\s(.*)SlotletInterface/',file_get_contents($file_name)))
      {
        $class_name   = substr($class_file, strrpos($class_file, '/'), -4);
        $class_name   = preg_replace('/^\//', '', $class_name);
        $class_name   = preg_replace('/\.class/', '', $class_name);
        $slotlet_name = call_user_func(array($class_name, 'getSlotletName'));

        $classes[$class_name] = $slotlet_name;
      }
    }
    asort($classes, SORT_STRING);
    
    return $classes;
  }
  
  public static function getSlotletType($class)
  {
    return call_user_func(array($class, 'getSloletTypes'));
  }
  
  private function readFromConfig()
  {
  }
  
  private function uncacheConfigFile()
  {
    sfConfigCache::getInstance()->checkConfig($sf_root.'/apps/frontend/config/app.yml');
  }
  
  private function saveConfigFile()
  {
  }
}