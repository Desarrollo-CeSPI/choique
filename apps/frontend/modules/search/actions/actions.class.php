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
 * search actions.
 *
 * @package    cms
 * @subpackage search
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class searchActions extends sfActions
{
  /**
   * Executes index action
   *
   */
  public function executeIndex()
  {
    sfLoader::loadHelpers(array('I18N'));
    
    $this->shortname=sfConfig::get('app_search_xml_shortname', __('Buscar en Choique CMS'));
    $this->contactmail=CmsConfiguration::get('contact_mail');
    $this->getResponse()->setContentType('text/xml');
    $this->setLayout(false);
  }
}