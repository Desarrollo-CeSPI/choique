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
 * default actions.
 *
 * @package    new_cms
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class defaultActions extends sfActions
{
  public function executeIndex()
  {
  	$this->getUser()->setCulture('es_AR');
  	$id = $this->getRequestParameter('id');
    if ($id)
      $this->article = ArticlePeer::retrieveByPK($id);
    else
      $this->article = new Article();

    if (!$this->getUser()->hasAttribute('calendarMonth'))
      $this->getUser()->setAttribute('calendarMonth', intval(date('m')));
    if (!$this->getUser()->hasAttribute('calendarYear'))
      $this->getUser()->setAttribute('calendarYear', intval(date('Y')));

    $this->forward('section','templateByName');
  }
  
  public function executeError404()
  {
  	return sfView::SUCCESS;
  }

  public function executeDisabled()
  {
    $this->setLayout('clean_layout');
  }

  public function executeSetMobileMode()
  {
    $can_be_mobile = LayoutPeer::mobileExists();
      
    $this->getResponse()->setCookie('mobile_mode_set', $can_be_mobile);
    $this->getUser()->setAttribute('mobile_mode', $can_be_mobile);
    $this->redirect('@homepage');
  }

  public function executeUnsetMobileMode()
  {
    $this->getResponse()->setCookie('mobile_mode_set', true);
    $this->getUser()->setAttribute('mobile_mode', false);
    $this->redirect('@homepage');
  }
}
