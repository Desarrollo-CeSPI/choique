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
 * articlegroup actions.
 *
 * @package    choique
 * @subpackage articlegroup
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class articlegroupActions extends autoarticlegroupActions
{
  public function executeCreateRelated()
  {
    $this->getUser()->setAttribute('convenience_creation', true);
    $this->forward($this->getModuleName(), 'create');
  }

  public function executeCloseWindow()
  {
    $this->getUser()->setAttribute('convenience_creation', false);
    $this->setLayout('cleanLayout');
  }

  public function executeList()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->forward($this->getModuleName(), 'closeWindow');
    }

    return parent::executeList();
  }

  public function executeDeleteArticleGroup()
  {
    $article_article_group = ArticleArticleGroupPeer::retrieveByPK($this->getRequestParameter('article_article_group_id'));
    $this->article_group = ArticleGroupPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article_group);
    if (!empty($article_article_group))
    {
      $article_article_group->delete();
    }
    $this->article_article_groups = $this->article_group->getArticleArticleGroups();
  }


  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->article_group = $this->getArticleGroupOrCreate();
    if ($this->article_group->canDelete())
    {
      try
      {
        $this->article_group->delete();
        $this->setFlash('notice', 'La galeria de artículos seleccionada fue borrada exitosamente');
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete','La galería '.$this->article_group->__toString().' no se puede borrar debido a que esta referenciado en un artículo.');
        return $this->forward('articlegroup', 'list');
      }
    }
    else
    {
      $this->getRequest()->setError('delete','La galería '.$this->article_group->__toString().' no se puede borrar debido a que esta referenciado en un artículo.');
    }

    return $this->forward('articlegroup','list');
  }

  public function executeCreate()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->setLayout('cleanLayout');
    }

    $ret = parent::executeCreate();
    if ($this->getRequest()->getMethod() == sfRequest::GET)
    {
      if ($this->getUser()->hasAttribute('article'))
      {
        $this->getUser()->getAttributeHolder()->remove('article');
      }
      $this->getUser()->setAttribute('article', array());

      if ($this->getUser()->hasAttribute('deleted_article'))
      {
        $this->getUser()->getAttributeHolder()->remove('deleted_article');
      }
      $this->getUser()->setAttribute('deleted_article', array());
    }

    return $ret;
  }

  public function executeEdit()
  {
    $ret = parent::executeEdit();

    if ($this->getRequest()->getMethod() == sfRequest::GET)
    {
      if ($this->getUser()->hasAttribute('article'))
      {
        $this->getUser()->getAttributeHolder()->remove('article');
      }
      $this->getUser()->setAttribute('article', $this->article_group->getArticleArticleGroups());

      if ($this->getUser()->hasAttribute('deleted_article'))
      {
        $this->getUser()->getAttributeHolder()->remove('deleted_article');
      }
      $this->getUser()->setAttribute('deleted_article', array());
    }

    return $ret;
  }

  public function saveArticleGroup($article_group)
  {
    $article_group->save();

    foreach ($this->getUser()->getAttribute('article') as $article)
    {
      $article->setArticleGroup($this->article_group);
      $article->save();
    }

    foreach ($this->getUser()->getAttribute('deleted_article') as $article_article_group_id)
    {
      $article_article_group = ArticleArticleGroupPeer::retrieveByPK($article_article_group_id);
      $article_article_group->delete();
    }
  }

  public function executeUnpublish()
  {
    $this->article_group = ArticleGroupPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article_group);
    $this->article_group->setUnpublished(true);
    $this->article_group->save();
    $this->setFlash('notice', 'Your modifications have been saved');

    $this->redirect('articlegroup/index');
  }

  public function executePublish()
  {
    $this->article_group = ArticleGroupPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article_group);
    $this->article_group->setIsPublished(true);
    $this->article_group->save();
    $this->setFlash('notice', 'Your modifications have been saved');

    $this->redirect('articlegroup/index');
  }

  public function executeAutocompleteArticle()
  {
    $query = '%'.$this->getRequestParameter('article_id_search').'%';

    $c = new Criteria();
    $crit = $c->getNewCriterion(ArticlePeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(ArticlePeer::DESCRIPTION, $query, Criteria::LIKE));
    $c->add($crit);
    $c->add(ArticlePeer::IS_PUBLISHED, true);
    $c->setLimit(12);

    $this->articles = ArticlePeer::doSelect($c);
  }

  public function executeAddTmpArticle()
  {
    $this->article_group = $this->getArticleGroupOrCreate('article_group_id');
    $article = $this->getUser()->getAttribute('article');
    $this->getUser()->getAttributeHolder()->remove('article');
    $article_article_group = new ArticleArticleGroup();
    $article_article_group->setArticleId($this->getRequestParameter('article_id'));
    $article[] = $article_article_group;
    $this->getUser()->setAttribute('article', $article);
  }

  public function executeDeleteArticle()
  {
    $mg = ArticleArticleGroupPeer::retrieveByPk($this->getRequestParameter('article_article_group_id'));
    $this->article_group = $mg->getArticleGroup();

    $darticle = $this->getUser()->getAttribute('deleted_article');
    $this->getUser()->getAttributeHolder()->remove('deleted_article');

    $array = array();
    foreach ($darticle as $dm)
    {
      if ($dm->getId() != $mg->getId())
      {
        $array[] = $dm;
      }
    }

    $this->getUser()->setAttribute('deleted_article', $array);
  }

  public function executeDeleteTmpArticle()
  {
    $this->article_group = $this->getArticleGroupOrCreate('article_group_id');
    $article = $this->getUser()->getAttribute('article', array());
    $this->getUser()->getAttributeHolder()->remove('article');
    $deleted_article = $this->getUser()->getAttribute('deleted_article', array());
    $this->getUser()->getAttributeHolder()->remove('deleted_article');

    $article_id = $this->getRequestParameter('article_id');

    $array = array();
    $array_d = $deleted_article;
    foreach ($article as $m)
    {
      if ($m->getArticleId() != $article_id)
      {
        $array[] = $m;
      }
      else
      {
        if (!$m->isNew())
        {
          $array_d[] = $m->getId();
        }
      }
    }

    $this->getUser()->setAttribute('article', $array);
    $this->getUser()->setAttribute('deleted_article', array_unique($array_d));
  }

  public function executeEditPriorities()
  {
  	$article_group = $this->getArticleGroupOrCreate();
  	$this->article_group = $article_group;
  	$this->article_article_groups = $article_group->getArticleArticleGroupsByPriority();
  }

  public function executeSortArticleArticleGroup()
  {
    $sorts = array_reverse($this->getRequestParameter('sortable-list', array()));

    foreach ($sorts as $i => $id)
    {
      $article_article_group = ArticleArticleGroupPeer::retrieveByPk($id);
      
      $article_article_group->setPriority($i);
      $article_article_group->save();
    }

    return sfView::NONE;
  }

  protected function updateArticleGroupFromRequest()
  {
    parent::updateArticleGroupFromRequest();   
    
    switch ($this->getActionName()) {
      case 'create':
        $this->article_group->setCreatedBy($this->getUser()->getGuardUser()->getId());
      break;
      case 'edit':
        $this->article_group->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }
  }
}