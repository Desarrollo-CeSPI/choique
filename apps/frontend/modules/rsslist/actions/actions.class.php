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
 * rsslist actions.
 *
 * @package    cms
 * @subpackage rsslist
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class rsslistActions extends sfActions
{
  public function executeShowAll()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_RSS);
    }

    $request = $this->getRequest();
    $section = $request->getParameter('section');

    if ($section == 'NONE')
    {
      $section = SectionPeer::retrieveHomeSection()->getName();
    }

    $request->getParameterHolder()->set('section_name', $section);
	
    $this->rss_channel = RssChannelPeer::retrieveByPK($request->getParameter('id'));
    try
    {
      $feed = sfFeedPeer::createFromWeb($this->rss_channel->getLink());

      $this->feed_items=$feed->getItems();
    }
    catch (Exception $e)
    {
      $this->feed_items = array();
    }
  }
  
}