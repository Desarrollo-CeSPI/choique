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
 * choiqueFeedPeer
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class choiqueFeedPeer extends sfFeedPeer
{
  /**
   * Get an sfFeed from $articles.
   * 
   * @param  Article[] $articles   Articles
   * @param  string    $title      Title to show
   * @param  string    $uri_prefix URI Prefix
   *
   * @return sfFeed
   */
  static public function create($articles, $title, $uri_prefix)
  {
    $options = array(
      'format'      => sfConfig::get('app_rss_feed_format', 'atom1'),
      'title'       => $title,
      'link'        => $uri_prefix,
      'authorEmail' => CmsConfiguration::get('feeds_contact_email', 'webmaster@choique.cespi.unlp.edu.ar'),
      'authorName'  => CmsConfiguration::get('feeds_contact_name', 'Choique CMS'),
      'methods'     => array(
        'authorEmail' => 'getContact',
        'authorName'  => 'getAuthor',
        'pubdate'     => 'getTimestampForRssFeed',
        'uniqueId'    => 'getId',
        'description' => 'getHeading',
        'title'       => 'getTitle',
        'link'        => 'getURLReference',
        'guid'        => 'getLinkedTitle',
        'content'     => 'getRssFeedContent',
        'enclosure'   => 'getRssFeedEnclosure'
      )
    );

    return self::createFromObjects($articles, $options);
  }

  /**
   * Get an sfFeed from $events.
   *
   * @param  Event[]   $events     Events
   * @param  string    $title      Title to show
   * @param  string    $uri_prefix URI Prefix
   *
   * @return sfFeed
   */
  static public function createFromEvents($events, $title, $uri_prefix)
  {
    $options = array(
      'format'      => sfConfig::get('app_rss_feed_format', 'atom1'),
      'title'       => $title,
      'link'        => $uri_prefix,
      'authorEmail' => CmsConfiguration::get('feeds_contact_email', 'webmaster@choique.cespi.unlp.edu.ar'),
      'authorName'  => CmsConfiguration::get('feeds_contact_name', 'Choique CMS'),
      'methods'     => array(
        'authorEmail' => 'getContact',
        'authorName'  => 'getAuthor',
        'pubdate'     => 'getTimestampForRssFeed',
        'uniqueId'    => 'getId',
        'description' => 'getHeadingForRssFeed',
        'title'       => 'getGuidForRssFeed',
        'link'        => 'getLinkForRssFeed',
        'guid'        => 'getGuidForRssFeed',
        'content'     => 'getDescription',
        'enclosure'   => 'getRssFeedEnclosure'
      )
    );

    return self::createFromObjects($events, $options);
  }

  public static function createFromWeb($uri, $options = array())
  {
    $feed_contents = choiqueFetcherFactory::get()->fetch($uri);

    return self::createFromXml($feed_contents, $uri);
  }

}