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
 * Subclass for performing query and update operations on the 'article_section' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ArticleSectionPeer extends BaseArticleSectionPeer
{
	static function doSort($order)
	{
	  $reverse_order=array_reverse($order);
	  $con = Propel::getConnection(self::DATABASE_NAME);
	  try 
	  {
	    $con->begin();
	   
	    foreach ($reverse_order as $priority => $id) 
	    {
	      $item = ArticleSectionPeer::retrieveByPk($id);
	      if ($item->getPriority() != $priority)
	      {
	        $item->setPriority($priority);
	        $item->save();
	      }
	    }
	 
	    $con->commit();
      
	    return true;    
	  }
	  catch (Exception $e)
	  {
	    $con->rollback();
      
	    return false;
	  }
	}

  public static function exists($section, $article)
  {
    $c = new Criteria();
    $c->add(self::ARTICLE_ID, $article->getId());
    $c->add(self::SECTION_ID, $section->getId());

    return self::doSelectOne($c);
  }
}