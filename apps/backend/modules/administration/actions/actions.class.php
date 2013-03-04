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
 * administration actions.
 *
 * @package    new_cms
 * @subpackage administration
 * @author     Nahuel
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class administrationActions extends sfActions
{
  public function executeIndex()
  {
    $this->parameters = CmsConfiguration::getAllOptions();
  }

  public function executeSave()
  {
    $cms_configuration = $this->getRequestParameter('cms_configuration');
    foreach (CmsConfigurationPeer::doSelect(new Criteria()) as $param)
    {
      $value = $this->getRequestParameter('cms_configuration[' . $param->getConfigurationKey() . ']');
      CmsConfiguration::set($param->getConfigurationKey(), $value);
    }

    choiqueFlavors::getInstance()->clearCache('all');

    $this->setFlash("notice", "Se han guardado los cambios");

    return $this->redirect("administration/index");
  }
}