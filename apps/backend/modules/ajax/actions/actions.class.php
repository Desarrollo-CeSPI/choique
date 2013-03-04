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

class ajaxActions extends sfActions
{
	private function doSearchForClass($id, $queryString)
	{
		$id = intval($id);
		$c  = new Criteria();
		switch ($id)
    {
			case 0: //Article
				$crit0 = $c->getNewCriterion(ArticlePeer::IS_PUBLISHED, true);
				$crit1 = $c->getNewCriterion(ArticlePeer::BODY, $queryString, Criteria::LIKE);
				$crit2 = $c->getNewCriterion(ArticlePeer::TITLE, $queryString, Criteria::LIKE);
				$crit1->addOr($crit2);
				$crit0->addAnd($crit1);
				$c->add($crit0);
				$c->setIgnoreCase(true);
  	  	$ret = ArticlePeer::doSelect($c);
		  	break;
			case 1: //Multimedia
		    $crit0 = $c->getNewCriterion(MultimediaPeer::TITLE, $queryString, Criteria::LIKE);
	      $crit1 = $c->getNewCriterion(MultimediaPeer::NAME, $queryString, Criteria::LIKE);
		    $crit2 = $c->getNewCriterion(MultimediaPeer::DESCRIPTION, $queryString, Criteria::LIKE);
		    $crit1->addOr($crit2);
		    $crit0->addOr($crit1);
		    $c->add($crit0);
				$c->setIgnoreCase(true);
		    $ret = MultimediaPeer::doSelect($c);
		    break;
			case 2: //Gallery
	      $crit0 = $c->getNewCriterion(GalleryPeer::NAME, $queryString, Criteria::LIKE);
		    $crit1 = $c->getNewCriterion(GalleryPeer::DESCRIPTION, $queryString, Criteria::LIKE);
		    $crit0->addOr($crit1);
        $c->add(GalleryPeer::IS_PUBLISHED, true);
		    $c->add($crit0);
				$c->setIgnoreCase(true);
		    $ret = GalleryPeer::doSelect($c);
		    break;
			case 3: //Form
		    $crit0 = $c->getNewCriterion(FormPeer::TITLE, $queryString, Criteria::LIKE);
	      $crit1 = $c->getNewCriterion(FormPeer::NAME, $queryString, Criteria::LIKE);
		    $crit2 = $c->getNewCriterion(FormPeer::DESCRIPTION, $queryString, Criteria::LIKE);
		    $crit1->addOr($crit2);
		    $crit0->addOr($crit1);
		    $c->add($crit0);
				$c->setIgnoreCase(true);
		    $ret = FormPeer::doSelect($c);
		    break;
			case 4: //Document
		    $crit0 = $c->getNewCriterion(DocumentPeer::TITLE, $queryString, Criteria::LIKE);
	      $crit1 = $c->getNewCriterion(DocumentPeer::NAME, $queryString, Criteria::LIKE);
		    $crit0->addOr($crit1);
		    $c->add($crit0);
				$c->setIgnoreCase(true);
		    $ret = DocumentPeer::doSelect($c);
		    break;
      case 5: //RSS
        $crit0 = $c->getNewCriterion(RssChannelPeer::TITLE, $queryString, Criteria::LIKE);
        $crit0->addAnd($c->getNewCriterion(RssChannelPeer::IS_ACTIVE, true));
        $c->add($crit0);
        $c->setIgnoreCase(true);
        $ret = RssChannelPeer::doSelect($c);
        break;
      default:
        $ret = array();
      }

    return $ret;
	}
	
	public function executeGetArticleById()
	{
		sfConfig::set('sf_web_debug', false);
		$id 		       = $this->getRequestParameter('id');
		$this->type    = $this->getRequestParameter('type');
		$this->article = ArticlePeer::retrieveByPK($id);
	}

	public function executeGetArticleByName()
	{
		sfConfig::set('sf_web_debug', true);
		$name = $this->getRequestParameter('name');
		$date = sprintf('%d-%d-%d', $this->getRequestParameter('year'), $this->getRequestParameter('month'), $this->getRequestParameter('day'));
		$date = new sfDate($date);
		
		$c    = new Criteria();
		$c->add(ArticlePeer::PUBLISHED_AT, $date->format('Y-m-d'));
		$c->add(ArticlePeer::NAME, $name);
		$art  = ArticlePeer::doSelectOne($c);
		
		$this->forward404Unless($art);
		
		$this->redirect(sprintf('@ajax_article?type=0&id=%d', $art->getId()));
	}

	public function executePerformSearch()
	{
		$queryString = '%'.$this->getRequestParameter('query').'%';
		$this->items = $this->doSearchForClass($this->getRequestParameter('on'), $queryString);

    $options = array();
    foreach ($this->items as $item)
      $options[$item->getId()] = $item->getTitle();

		$this->options = $options;
    $this->class   = $this->getRequestParameter('on');
	}
	
	public function executeTest()
	{
	  return $this->renderText('It works!');
	}
}