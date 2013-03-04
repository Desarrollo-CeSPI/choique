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
 * sitemap actions.
 *
 * @package    cms
 * @subpackage sitemap
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class sitemapActions extends sfActions
{
  public function executeIndex()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_SITEMAP);
    }
    else
    {
      $this->setLayout('sitemap');
    }
    
    $this->getRequest()->getParameterHolder()->set('section_name', 'sitemap_section');

    if (CmsConfiguration::get('check_use_horizontal_as_base_sitemap_section', true))
    {
      $base_section = SectionPeer::retrieveHorizontalSection();
    }
    else
    {
      $base_section = SectionPeer::retrieveHomeSection();
    }

    if (CmsConfiguration::get('check_include_home_in_sitemap', false))
    {
      $sections_to_remove = array();
    }
    else
    {
      $sections_to_remove = array(SectionPeer::retrieveHomeSection());
    }
    
    $this->first_level = array_diff($base_section->getPublishedChildren(), $sections_to_remove);
  }
}