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
 * editor components.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class editorComponents extends sfComponents
{
  public function executeNavigator()
  {
    $this->dirs  = PathHelper::find('dir', $this->getVar('base'));
    $this->files = PathHelper::find('file', $this->getVar('base'));

    if ($this->getVar('base') != PathHelper::getRoot())
    {
      $path = substr(realpath($this->getVar('base').'/..'), strlen(PathHelper::getRoot()));

      if (!$path)
      {
        $path = '';
      }

      $addition = array($path => '..');

      $this->dirs = array_merge($addition, $this->dirs);
    }
  }

  public function executeEditor()
  {
    sfConfig::set('sf_web_debug', false);
    try
    {
      $this->content = file_get_contents(PathHelper::getPath($this->getVar('file'), true));
    }
    catch (Exception $e)
    {
      $this->error = $e->getMessage();
    }
    $ph = $this->getResponse()->getParameterHolder();
    $ph->removeNamespace('helper/asset/auto/javascript');
    $ph->removeNamespace('helper/asset/auto/stylesheets');
  }
  
}