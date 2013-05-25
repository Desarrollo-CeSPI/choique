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
 * article actions.
 *
 * @package    new_cms
 * @subpackage article
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class articleActions extends autoarticleActions
{
  public function getCredential()
  {
    return $this->getUser()->requiredCredentialsFor($this, parent::getCredential());
  }

  public function updateArticleFromRequest()
  {
    $article = $this->getRequestParameter('article');

    $this->article->setType($this->getRequestParameter('type'));
    $this->article->setTextFromEditor($this->getRequestParameter('body'));

    if ($this->article->getType() != Article::NEWS)
    {
      $this->getRequest()->setParameter('associated_article_section', null);
    }

    if($this->article->isNew())
      $this->article->setCreatedBy($this->getUser()->getGuardUser()->getId());
    else
      $this->article->setUpdatedBy($this->getUser()->getGuardUser()->getId());

    parent::updateArticleFromRequest();

    if (isset($article['reference_type']))
    {
      $reference_type = $article['reference_type'];
      switch ($reference_type)
      {
        case Article::REFERENCE_TYPE_NONE:
          $this->article->setReference(null);
          break;
        case Article::REFERENCE_TYPE_EXTERNAL:
          $this->article->setReference(trim($article['external_reference_value']));
          break;
        case Article::REFERENCE_TYPE_SECTION:
          $this->article->setReference(trim($article['section_reference_value']));
          break;
        case Article::REFERENCE_TYPE_ARTICLE:
          $this->article->setReference(trim($article['article_reference_value']));
          break;
      }
    }
  }

  protected function saveArticle($article)
  {
    $priorities = array();
    foreach($article->getArticleSections() as $as)
    {
      $priorities[$as->getSectionId()]=$as->getPriority();
    }

    parent::saveArticle($article);

    $c = new Criteria();
    $c->add(ArticleSectionPeer::ARTICLE_ID, $article->getPrimaryKey());
    ArticleSectionPeer::doDelete($c);

    $ids = $this->getRequestParameter('associated_article_section');
    if (is_array($ids))
    {
      foreach ($ids as $id)
      {
        $ArticleSection = new ArticleSection();
        $ArticleSection->setArticleId($article->getPrimaryKey());
        $ArticleSection->setSectionId($id);
        if (array_key_exists($id, $priorities)) {
          $ArticleSection->setPriority($priorities[$id]);
        }
        $ArticleSection->save();
      }
    }
  }

  public function handlePost()
  {
    $this->updateArticleFromRequest();

    $this->saveArticle($this->article);

    $this->setFlash('notice', 'Sus modificaciones fueron guardadas');

    if ($this->getRequestParameter('save_and_add'))
    {
      return $this->redirect('article/create');
    }
    else if ($this->getRequestParameter('save_and_list'))
    {
      return $this->redirect('article/list');
    }
    else
    {
      return $this->redirect('article/edit?id='.$this->article->getId());
    }
  }

  public function executeCopy()
  {
    $article = ArticlePeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($article);
    $clone = $article->duplicate();
    $clone->setUnpublished(true);
    $clone->save();
    $this->setFlash('notice', 'Se hace creado una copia del articulo "'.$clone->getName().'". Puede aquí editar sus datos.');
    $this->redirect('article/edit?id='.$clone->getId());
  }

  public function executeUnpublish()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article);
    $this->article->setUnpublished(true);
    $this->article->save();
    $this->setFlash('notice', 'Se han guardado los cambios');

    $this->redirect('article/index');
  }

  public function executePublish()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article);
    $this->article->setIsPublished(true);
    $this->article->setPublishedAt(date('Y-m-d H:i'));
    $this->article->save();
    $this->setFlash('notice', 'Se han guardado los cambios');

    $this->redirect('article/index');
  }

  public function executeArchive()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->article);
    $this->article->setIsArchived(true);
    $this->article->save();
    $this->setFlash('notice', 'Se han guardado los cambios');

    $this->redirect('article/index');
  }

  function executeAutocomplete()
  {
    $this->response = $this->getResponse();
    $this->response->addJavascript('/sf/prototype/js/prototype');
    $this->response->addJavascript('/sf/prototype/js/effects');
    $this->response->addJavascript('/sf/prototype/js/controls');
    $this->getResponse()->addStylesheet('/sf/prototype/css/input_auto_complete_tag');

    $find = $this->getRequestParameter('article_name');
    if (is_null($find))
    {
      $find = $this->getRequestParameter('article_autocomplete_query');
    }

    $c = new Criteria();
    $criterion = $c->getNewCriterion(ArticlePeer::NAME, "%$find%", Criteria::LIKE);
    $criterion->addOr($c->getNewCriterion(ArticlePeer::TITLE, "%$find%", Criteria::LIKE));
    $c->add($criterion);
    $c->add(ArticlePeer::IS_PUBLISHED, true);
    $c->setLimit(12);
    $c->setIgnoreCase(true);
    $this->articles = ArticlePeer::doSelect($c);
  }

  protected function getLabels()
  {
    return array_merge(
        parent::getLabels(),
        array(
          'body'                              => 'Cuerpo:',
          'article{external_reference_value}' => 'Referencia:',
          'article{no_reference_value}'       => 'Referencia:',
          'article{article_reference_value}'  => 'Referencia:',
          'article{section_reference_value}'  => 'Referencia:'
      ));
  }

  public function executeAutocompleteMultimedia()
  {
    $query = '%'.$this->getRequestParameter('multimedia_id_search').'%';

    $c = new Criteria();
    $crit = $c->getNewCriterion(MultimediaPeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(MultimediaPeer::DESCRIPTION, $query, Criteria::LIKE));
    $crit->addOr($c->getNewCriterion(MultimediaPeer::COMMENT, $query, Criteria::LIKE));
    $c->add($crit);

    $this->multimedias = MultimediaPeer::doSelect($c);
  }


  public function executeDelete()
  {
    $this->article = $this->getArticleOrCreate();
    if ($this->article->canDelete())
    {
      try
      {
        $this->article->delete();

      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete', 'El articulo ' . $this->article->__toString() .' no se puede borrar, debido a que esta referenciado por otros elementos ');
      }
      $this->setFlash('notice', 'The selected element has been successfully deleted');
    }
    else
    {
      $this->getRequest()->setError('delete', 'El articulo ' . $this->article->__toString() .' no se puede borrar, debido a que esta referenciado por otros elementos ');
    }
    return $this->forward('article','list');
  }

  public function executePreview()
  {
    $id = $this->getRequestParameter('id');

    $this->redirect(choiqueUtil::generateUrl('frontend', 'ARTICLE_PREVIEW?id='.$id, false));
  }

}
