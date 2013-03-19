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
 * feed actions.
 *
 * @package    cms
 * @subpackage feed
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class feedActions extends sfActions
{
  /**
   *  Generates an xml feed for the contents of the CMS.
   *  Request parameters (all of them are optional):
   *    * section String The name of the section whose
   *                     contents are to be included in the
   *                     feed. If no section parameter is
   *                     provided, contents from every
   *                     section will be included.
   *
   *  Uses sfFeed2 plugin
   */
  public function executeIndex()
  {
    $title   = $this->getTitle();
    $section = $this->getSection($this->getRequestParameter('section', 'NONE'));

    if ($section)
    {
      $title   .= ': '.$section->getTitle();

      $articles = $section->getSortedNews(false, true, sfConfig::get('app_rss_feed_max_items', 15), true, true);
    }
    else
    {
      $articles = ArticlePeer::doSelectForRssFeed();
    }

    $this->feed = choiqueFeedPeer::create($articles, $title, choiqueUtil::generateUrl('frontend',''));
  }

  // Generates an RSS feed from a template (using its articles)
  public function executeTemplate()
  {
    $template = TemplatePeer::retrieveByPublicName($this->getRequestParameter('template'));

    $this->forward404Unless($template);

    $this->feed = choiqueFeedPeer::create($template->getArticles(), $this->getTitle(), choiqueUtil::generateUrl('frontend',''));
    
    $this->setTemplate('index');
  }

  // Generates an RSS feed from events calendar
  public function executeCalendar()
  {
    $events     = EventPeer::retrieveStartingBetween(time(), strtotime('+1 month'), true);
    $this->feed = choiqueFeedPeer::createFromEvents($events, $this->getTitle('Calendario de eventos'), choiqueUtil::generateUrl('frontend',''));
  }

  // Generates an RSS feed from news slotlet
  public function executeNews()
  {
    $section = SectionPeer::retrieveByName($this->getRequestParameter('section'));

    $this->forward404Unless($section);

    $articles = $section->getSortedNews(
      CmsConfiguration::get('check_include_ancestor_sections', true),
      CmsConfiguration::get('check_include_children_sections', true),
      null,
      CmsConfiguration::get('check_sort_news_by_priority', true)
    );

    $this->feed = choiqueFeedPeer::create($articles, $this->getTitle(), choiqueUtil::generateUrl('frontend',''));

    $this->setTemplate('index');
  }

  /**
   * Get the title meta from the web response.
   * If no title has been set in the response
   * then $default is returned.
   *
   * @param  string $default The default title
   *
   * @return string The title meta content
   */
  protected function getTitle($default = 'Noticias RSS')
  {
    $title = $this->getResponse()->getTitle();

    return ('' != trim($title)) ? $title : $default;
  }

  /**
   * Get a Section by its name. 'NONE' equals a NULL value.
   * 
   * @param  string $section_name The name of the section
   *
   * @return mixed Section if found or FALSE if not
   */
  protected function getSection($section_name)
  {
    if ('' != trim($section_name) && 'NONE' != $section_name)
    {
      return SectionPeer::retrieveByName($section_name);
    }

    return false;
  }
}
