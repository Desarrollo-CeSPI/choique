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
 * articlesection actions.
 *
 * @package    cms
 * @subpackage articlesection
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class articlesectionActions extends autoarticlesectionActions
{
  public function executeList()
  {
    $this->article = $this->getUser()->getAttribute('article');

    $this->processSort();

    $this->processFilters();

    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/article_section/filters');

    // pager
    $this->pager = new sfPropelPager('ArticleSection', 25);
    $c = new Criteria();

    $c->add(ArticleSectionPeer::ARTICLE_ID, $this->article->getId());

    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }

  public function executeCreate()
  {
    $this->article = $this->getUser()->getAttribute('article');

    $this->article_section = new ArticleSection();
    $this->article_section->setArticleId($this->article->getId());

    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      return $this->handlePost();
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }

  public function executeBack()
  {
    $this->article = $this->getUser()->getAttribute('article');
    $id = $this->article->getId();
    $this->getUser()->getAttributeHolder()->remove('article');

    $this->redirect('news/edit?id='.$id);
  }
  
	public function executeIncreasePriority()
	{
		$article_section = ArticleSectionPeer::retrieveByPK($this->getRequestParameter('id'));
		$article_section->setPriority($article_section->getPriority()+1);
		$article_section->save();
		$this->redirect('articlesection/list');
	}
	
	public function executeDecreasePriority()
	{
		$article_section = ArticleSectionPeer::retrieveByPK($this->getRequestParameter('id'));
		$priority = $article_section->getPriority();
		if ($priority > 0)
    {
			$priority--;
		}
		$article_section->setPriority($priority);
		$article_section->save();
		$this->redirect('articlesection/list');
	}
}