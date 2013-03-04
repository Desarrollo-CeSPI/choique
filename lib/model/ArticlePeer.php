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
 * Subclass for performing query and update operations on the 'article' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ArticlePeer extends BaseArticlePeer
{
  public static function retrievePublished()
  {
    $c = new Criteria();
    $c->add(self::IS_PUBLISHED, true);
    $c->addAscendingOrderByColumn(self::TITLE);

    return self::doSelect($c);
  }

  public static function retrieveForSitemap()
  {
    $c = new Criteria();
    $c->add(self::IS_PUBLISHED, true);
    $c->addAscendingOrderByColumn(self::NAME);

    return self::doSelect($c);
  }

  public static function retrieveForPopup()
  {
    $c = new Criteria();
    $c->add(self::OPEN_AS_POPUP,true);
    $c->add(self::IS_PUBLISHED, true);

    return self::doSelect($c);
  }

  public static function retrieveByCreationDateAndName($created_at, $name)
  {
    $date = new sfDate($created_at);

    $c = new Criteria();
    $c->add(ArticlePeer::CREATED_AT, "DATE(created_at) = '" . $date->format('Y-m-d') . "'", Criteria::CUSTOM);
    $c->add(ArticlePeer::NAME, $name);

    return ArticlePeer::doSelectOne($c);
  }

  /**
   * Retrieve articles for RSS Feed generation.
   * 
   * @return Article[] Matched articles
   */
  static public function doSelectForRssFeed()
  {
    $criteria = new Criteria();

    $criteria->addDescendingOrderByColumn(CmsConfiguration::get('check_use_published_at_for_rss_feeds', true) ? self::PUBLISHED_AT:self::UPDATED_AT);
    $criteria->setLimit(sfConfig::get('app_rss_feed_max_items', 15));

    $criteria->add(self::IS_PUBLISHED, true);
    $criteria->add(self::TYPE, array(Article::NEWS, Article::INSTITUTIONAL), Criteria::IN);

    return self::doSelect($criteria);
  }

  static public function getSortedNewsCriteriaBase($section_ids, $sort_by_priority = false, $include_institutional = false)
  {
    $c = new Criteria();
    $c->addJoin(ArticlePeer::ID, ArticleSectionPeer::ARTICLE_ID, Criteria::LEFT_JOIN);
    $c->add(ArticlePeer::IS_PUBLISHED, true);
    if ($include_institutional)
    {
      $c->add(ArticlePeer::TYPE, array(Article::NEWS, Article::INSTITUTIONAL), Criteria::IN);
    }
    else
    {
      $c->add(ArticlePeer::TYPE, Article::NEWS);
    }
    $c->add(ArticleSectionPeer::SECTION_ID, array_unique($section_ids), Criteria::IN);

    //Select the sort column according to $sort_by_priority's value
    $sort_column = ($sort_by_priority) ? ArticleSectionPeer::PRIORITY : ArticlePeer::PUBLISHED_AT;

    $c->addDescendingOrderByColumn($sort_column);

    //In Postgres the order column must be in the selected columns
    ArticlePeer::addSelectColumns($c);
    $c->addSelectColumn(ArticleSectionPeer::PRIORITY);

    $c->setDistinct();
    
    return $c;
  }
}