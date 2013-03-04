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
 * Subclass for representing a row from the 'article_section' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ArticleSection extends BaseArticleSection
{
  public static function getArticleSection($section_id, $article_id)
  {
    $c = new Criteria();
    $crit0 = $c->getNewCriterion(ArticleSectionPeer::SECTION_ID, $section_id);
    $crit1 = $c->getNewCriterion(ArticleSectionPeer::ARTICLE_ID, $article_id);
    $crit0->addAnd($crit1);
    $c->add($crit0);
    
    return ArticleSectionPeer::doSelectOne($c);
  }
    
  public static function _get_article_section_object_list($object, $method, $options)
  {
    // get the lists of article_section objects
    $through_class = _get_option($options, 'through_class');
    $sf_user = _get_option($options, 'sf_user');
    $section = new Section();
    $objects = $section->getAllSectionsTree($sf_user);
    $objects_associated = sfPropelManyToMany::getRelatedObjects($object, $through_class);
    $ids = array_map(create_function('$o', 'return $o->getPrimaryKey();'), $objects_associated);

    return array($objects, $objects_associated, $ids);
  }
}