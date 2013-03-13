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
 * Subclass for performing query and update operations on the 'section' table.
 *
 *
 *
 * @package lib.model
 */
class SectionPeer extends BaseSectionPeer
{
  /**
   * @return Section
   */
  public static function retrieveByName($name)
  {
    $c = new Criteria();
    $c->add(self::NAME, $name);

    return self::doSelectOne($c);
  }

  static public function retrieveFirstLevelSectionByName($name)
  {
    $section = self::retrieveByName($name);

    if (null === $section)
    {
      return;
    }

    return $section->getFirstLevelSection();
  }

	public static function retrieveValid()
	{
		$c = new criteria();
		$c->add(self::NAME,Section::HORIZONTAL,Criteria::NOT_EQUAL);
		$c->add(self::IS_PUBLISHED,true);
		$c->addAscendingOrderByColumn(self::TITLE);

		return self::doSelect($c);
	}

  public static function retrieveAllRoots()
  {
  	$c = new Criteria();
  	$c->add(self::SECTION_ID,null,Criteria::ISNULL);

  	return self::doSelect($c);
  }

	public static function getSectionsTree($user = null)
	{
	  $sections = array();
	  foreach (self::retrieveAllRoots() as $root)
    {
	  	$ids = array();
	    $root->getSectionTree($ids, $user);
      $sorted_sections = array_fill_keys($ids,null);
	    $my_sections = self::retrieveByPKs($ids);
      foreach ($my_sections as $section)
      {
        $sorted_sections[$section->getId()] = $section;
      }

	    $sections = array_merge($sections, $sorted_sections);
    }

	  return $sections;
	}

  /**
   *  Used in the frontend app, to retrieve all the
   *  published sections in a sorted fashion. This is
   *  useful in the search form of article/showAll.
   **/
	public static function getPublishedSectionsTree()
	{
	  $sections = array();
	  foreach (self::retrieveAllRoots() as $root)
    {
	  	$ids = array();
	    $root->getPublishedSectionsTree($ids);
      $sorted_sections = array_fill_keys($ids, null);
	    $my_sections = self::retrieveByPKs($ids);
      foreach ($my_sections as $section)
      {
        $sorted_sections[$section->getId()] = $section;
      }
	    $sections = array_merge($sections, $sorted_sections);
    }

	  return $sections;
	}

  /**
   *  Retrieve the HORIZONTAL section. If such Section doesn't exist,
   *  create it and return it.
   *
   *  @return Section (Horizontal root section).
   **/
  public static function retrieveHorizontalSection()
  {
    $hs = self::retrieveByName(Section::HORIZONTAL);
    if (!$hs)
    {
      $hs = new Section();
      $hs->setName(Section::HORIZONTAL);
      $hs->setTitle(Section::HORIZONTAL);
      $hs->setIsPublished(false);
      $hs->save();
    }

    return $hs;
  }

  /**
   *  Retrieve the HOME section. If such Section doesn't exist,
   *  create it and return it.
   *
   *  @return Section (Home section).
   **/
  public static function retrieveHomeSection()
  {
    if (!CmsConfiguration::has('custom_section_home') || CmsConfiguration::get('custom_section_home')=== null )
    {
      $home_section = self::getOrCreateDefaultHomeSection();

      // Here use CmsConfiguration::get() in order to have it correctly labeled
      CmsConfiguration::get('custom_section_home', $home_section->getId());

      return $home_section;
    }
    else
    {
      return SectionPeer::retrieveByPK(CmsConfiguration::get('custom_section_home'));
    }
  }

  /**
   * Return the default HOME Section. If it doesn't exist,
   * try to create it and return it.
   *
   * @return Section Default HOME Section
   */
  private static function getOrCreateDefaultHomeSection()
  {
    $default_home_section = self::retrieveByName(Section::HOME);

    if (is_null($default_home_section))
    {
      $default_home_section = new Section();
      $default_home_section->setName(Section::HOME);
      $default_home_section->setTitle(Section::HOME);
      $default_home_section->setIsPublished(true);
      $default_home_section->save();
    }

    return $default_home_section;
  }

  public static function retrievePublished($c = null)
  {
    if ($c === null)
    {
      $c = new Criteria();
    }

    $c->add(SectionPeer::IS_PUBLISHED, true);

    return self::doSelect($c);
  }

  public static function retrieveForSitemap()
  {
    $c = new Criteria();
    $c->add(SectionPeer::IS_PUBLISHED, true);
    $c->addAscendingOrderByColumn(self::NAME);

    return self::doSelect($c);
  }

  public static function doSelectExcludingDescendants(Criteria $criteria, $section_id, $for_filter=false)
  {
    if (!is_null($section_id) && $section = SectionPeer::retrieveByPK($section_id))
    {
      //Exclude the descendants of the section whose id = $section_id
      $excluded_ids = array();
      foreach ($section->getLineage() as $descendant)
      {
        $excluded_ids[] = $descendant->getId();
      }


      $criteria->addAnd(SectionPeer::ID, $excluded_ids, Criteria::NOT_IN);
    }

    if (!$for_filter )
    {
      $user = ($context = sfContext::getInstance())? $context->getUser(): null;
      if ( !($user !== null && ( $user->hasCredential('designer') || $user->hasCredential('designer_admin'))) && $user->getGuardUser()->getSection() !== null)
      {
        $user_section = $user->getGuardUser()->getSection();
        $ids = array();
        foreach ($user_section->getLineage() as $s)
          $ids[] = $s->getId();
        $criteria->addAnd(SectionPeer::ID, $ids, Criteria::IN);
      }
    }
    return self::doSelect($criteria);
  }

  public static function getOptionsForSelect()
  {
    $c = new Criteria();
    $c->setIgnoreCase(true);
    $c->addAscendingOrderByColumn(SectionPeer::TITLE);
    $c->add(SectionPeer::IS_PUBLISHED, true);
    $results = self::doSelect($c);
    $sections = array();
    foreach($results as $section){
      $sections[$section->getId()] = $section;
    }
    return $sections;
  }

  public static function doSelectSorted($criteria = null, PDO $con = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $criteria->addAscendingOrderByColumn(self::TITLE);

    return self::doSelect($criteria, $con);
  }

  static public function retrieveFirstLevelByColor($color, $con = null)
  {
    $criteria = new Criteria();

    $criteria->add(self::COLOR, $color);
    $criteria->add(self::SECTION_ID, self::retrieveHorizontalSection()->getId());

    return self::doSelectOne($criteria, $con);
  }

  static public function retrieveOrdered($only_published = false, PropelPDO $con = null)
  {
    $criteria = new Criteria();

    $criteria->addAscendingOrderByColumn(self::TITLE);

    if ($only_published)
    {
      $criteria->add(self::IS_PUBLISHED, true);
    }

    return self::doSelect($criteria, $con);
  }

}