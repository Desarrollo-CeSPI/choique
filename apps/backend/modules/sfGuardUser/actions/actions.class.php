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
require_once(dirname(__FILE__).'../../../../../../plugins/sfGuardPlugin/modules/sfGuardUser/lib/BasesfGuardUserActions.class.php');
/**
 * autoSfGuardUser actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage autoSfGuardUser
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 390 2007-12-18 15:59:00Z romain $
 */
class SfGuardUserActions extends BasesfGuardUserActions
{
  public function handlePost()
  {
    $this->updatesfGuardUserFromRequest();

    $this->sf_guard_user->save();

    $this->setFlash('notice', 'Your modifications have been saved');

    if ($this->getRequestParameter('save_and_add'))
    {
      return $this->redirect('sfGuardUser/create');
    }
    else if ($this->getRequestParameter('save_and_list'))
    {
      return $this->redirect('sfGuardUser/list');
    }
    else
    {
      return $this->redirect('sfGuardUser/edit?id='.$this->sf_guard_user->getId());
    }
  }
}