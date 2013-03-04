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
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class articleActions extends sfActions
{
  public function executeTemplateByArticle()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('articleId'));

    $this->forward404Unless($this->article && $this->article->canBeShown());

    SlotletManager::setMainContent($this->article);

    $this->setTemplate('getArticleById');
  }

  public function handleErrorSendByEmail()
  {
    $this->setTemplate('sendByEmailForm');

    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id"));
    $this->mailto  = $this->getRequestParameter("mailto");
    $this->from    = $this->getRequestParameter("from");
  }

  public function executeSendByEmail()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id"));
    $this->mailto  = $this->getRequestParameter("mailto");
    $this->from    = $this->getRequestParameter("from");

    if (!is_null($this->article) && !is_null($this->mailto) && !is_null($this->from) && $this->article->canBeShown())
    {
      try
      {
        $this->sendEmail('article', 'doSendEmail');
      }
      catch (Exception $e)
      {
        $this->error = $e->getMessage();

        return 'ConnError';
      }
    }
    else
    {
      $this->response->addStyleSheet('formbuilder.css');
      $this->setTemplate('sendByEmailForm');
    }
  }

  public function executeSendByEmailUJS()
  {
    $this->executeSendByEmail();
  }

  public function handleErrorSendByEmailUJS()
  {
    sfLoader::loadHelpers(array('Url'));

    $this->setTemplate('sendByEmailUJSForm');

    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id"));
    $this->mailto  = $this->getRequestParameter("mailto");
    $this->from    = $this->getRequestParameter("from");
    $this->body    = $this->getPresentationFor('article', 'sendArticleByEmail');
  }

  //Actually send the email
  public function executeDoSendEmail()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id", ""));
    $this->mailto  = $this->getRequestParameter("mailto", null);
    $this->from    = $this->getRequestParameter("from", null);

    $mail = new Mailer();
    $mail->setCharset('utf-8');
    $mail->setContentType('text/html');

    // definition of the required parameters
    $mail->setSender(CmsConfiguration::get('contact_mail'),$this->from);
    $mail->setFrom(CmsConfiguration::get('contact_mail'),$this->from);
    $mail->addReplyTo(CmsConfiguration::get('contact_mail'));

    $mail->addAddress($this->mailto);

    $mail->setSubject($this->article->getTitle());

    $this->body = $this->getPresentationFor('article','sendArticleByEmail');
    $this->mail = $mail;
  }

  public function executeSendArticleByEmail()
  {
    $this->executeGetArticleById();
  }

  public function executePrintPreviewBackend()
  {
    $this->getResponse()->setTitle('Esta es una previsualizacion del articulo');
    $this->setTemplate('getArticleById');
    $this->includes_navigation();
    $this->mailto=$this->getRequestParameter("mailto", null);
    $this->from=$this->getRequestParameter("from", null);

    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id"));

    $section = $this->article->getSection();
    if (!$section)
      $section = SectionPeer::retrieveHomeSection();

    sfContext::getInstance()->getRequest()->getParameterHolder()->set("section_name", $section->getName());
    $this->section = $section;
  }

  public function executePrintPreview()
  {
    $this->executeGetArticleById();
  }

  public function executeGetArticleById()
  {
    $this->includes_navigation();
    $this->mailto=$this->getRequestParameter("mailto", null);
    $this->from=$this->getRequestParameter("from", null);

    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("id"));
    $this->forward404Unless($this->article && $this->article->canBeShown());

    SlotletManager::setMainContent($this->article);

    $section = $this->article->getSection();
    if (!$section)
      $section = SectionPeer::retrieveHomeSection();

    sfContext::getInstance()->getRequest()->getParameterHolder()->set("section_name", $section->getName());
    $this->section = $section;
  }

  public function executeGetArticleByName()
  {
    $this->includes_navigation();
    $name = $this->getRequestParameter('name');
    $date = sprintf('%d-%d-%d', $this->getRequestParameter('year'), $this->getRequestParameter('month'), $this->getRequestParameter('day'));
    $article = ArticlePeer::retrieveByCreationDateAndName($date, $this->getRequestParameter('name'));

    $this->forward404Unless($article && $article->canBeShown());

    $section = $article->getSection();

    if (!$section)
    {
      $section = SectionPeer::retrieveHomeSection();
    }

    sfContext::getInstance()->getRequest()->getParameterHolder()->set("section_name", $section->getName());

    $this->article = $article;

    SlotletManager::setMainContent($this->article);

    $this->setTemplate('getArticleById');
  }

  protected function processQuery($c)
  {
    $c->addDescendingOrderByColumn(ArticlePeer::UPDATED_AT);

    //text
    if (!is_null($this->search_text) && trim($this->search_text) != '') {
      $query = '%' . strip_tags($this->search_text) . '%';
      $c->setIgnoreCase(true);
      $criterion = $c->getNewCriterion(ArticlePeer::TITLE, $query, Criteria::LIKE);
      $criterion->addOr($c->getNewCriterion(ArticlePeer::HEADING, $query, Criteria::LIKE));
      $criterion->addOr($c->getNewCriterion(ArticlePeer::BODY, $query, Criteria::LIKE));
      $c->add($criterion);
    }

    //section_id
    if (!is_null($this->section_id) && trim($this->section_id) != '') {
      $section = SectionPeer::retrieveByPK($this->section_id);
      if (!is_null($section)) {
        $tree = array();
        $section->getPublishedSectionsTree($tree);
        $c->add(ArticlePeer::SECTION_ID, $tree, Criteria::IN);
      }
    }

    //updated_at
    if (!is_null($this->updated_at_from) && trim($this->updated_at_from) != '') {
      $criterion = $c->getNewCriterion(ArticlePeer::UPDATED_AT, $this->updated_at_from, Criteria::GREATER_EQUAL);
      if (!is_null($this->updated_at_to) && $this->updated_at_to != '') {
        $criterion->addAnd($c->getNewCriterion(ArticlePeer::UPDATED_AT, $this->updated_at_to, Criteria::LESS_EQUAL));
      }
      $c->add($criterion);
    }

    //types (institutional and news)
    $criterion = $c->getNewCriterion(ArticlePeer::TYPE, Article::INSTITUTIONAL);
    $criterion->addOr($c->getNewCriterion(ArticlePeer::TYPE, Article::NEWS));
    $c->add($criterion);

    //status (published or archived)
    if (!is_null($this->status) && $this->status != 'any') {
      if ($this->status == 'published') {
        $c->add(ArticlePeer::IS_PUBLISHED, true);
      } else {
        $c->add(ArticlePeer::IS_ARCHIVED, true);
      }
    } else {
      $criterion = $c->getNewCriterion(ArticlePeer::IS_PUBLISHED, true);
      $criterion->addOr($c->getNewCriterion(ArticlePeer::IS_ARCHIVED, true));
      $c->add($criterion);
    }

    $this->getUser()->setAttribute('article.show_all', array(
      'text'       => $this->search_text,
      'section_id' => $this->section_id,
      'updated_at' => array('from' => $this->updated_at_from_original, 'to' => $this->updated_at_to_original),
      'status'     => $this->status
    ));
  }

  public function executeShowAll()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_ALL_NEWS);
    }

    if ($this->getRequest()->getMethod() == sfWebRequest::GET && $this->getRequestParameter('page', 1) == 1)
    {
      $this->getUser()->setAttribute('article.show_all', null);
      $this->getUser()->getAttributeHolder()->remove('article.show_all');
    }

    $show_all = $this->getRequestParameter('show_all');

    if (null === $show_all)
    {
      $show_all = $this->getUser()->getAttribute('article.show_all', array(
        'text'       => null,
        'status'     => null,
        'section_id' => null,
        'updated_at' => array('from' => null, 'to' => null))
      );
    }

    $this->search_text = $show_all['text'];
    $this->section_id = trim($show_all['section_id']);
    $this->status = $show_all['status'];

    $updated_at_from = $show_all['updated_at']['from'];
    if (!is_null($updated_at_from) && $updated_at_from != '')
    {
      $this->updated_at_from = sfI18N::getTimestampForCulture($updated_at_from, $this->getUser()->getCulture());
      $this->updated_at_from_original = $show_all['updated_at']['from'];
    }
    else
    {
      $this->updated_at_from = null;
      $this->updated_at_from_original = null;
    }
    $updated_at_to = $show_all['updated_at']['to'];

    if (!is_null($updated_at_to) && $updated_at_to != '')
    {
      $this->updated_at_to = sfI18N::getTimestampForCulture($updated_at_to, $this->getUser()->getCulture());
      $this->updated_at_to_original = $show_all['updated_at']['to'];
    }
    else
    {
      $this->updated_at_to = null;
      $this->updated_at_to_original = null;
    }

    $section_name = $this->getRequestParameter('show_all_section_name');

    if (is_null($section_name))
    {
      $section_name = 'todas_las_noticias';
    }

    $this->getRequest()->setParameter('section_name', $section_name);

    // pager
    $this->pager = new sfPropelPager('Article', 15);
    $c = new Criteria();
    $this->processQuery($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }

  protected function includes_navigation()
  {
    $this->with_navigation = CmsConfiguration::getUseNavigationInArticles();
  }

  public function executeShowGallery()
  {
    $article = ArticlePeer::retrieveByPk($this->getRequestParameter('article_id'));
    $gallery = GalleryPeer::retrieveByPk($this->getRequestParameter('gallery_id'));

    $this->gallery = $gallery->getShowHTMLRepresentation();
    $this->article = $article;

    $section = $this->article->getSection();

    sfContext::getInstance()->getRequest()->getParameterHolder()->set("section_name", $section->getName());
  }

  public function executeGetArticleForPop()
  {
    $this->with_navigation = false;
    $id = $this->getRequestParameter('id');
    $this->setLayout('clean_layout');
    $article = ArticlePeer::retrieveByPK($id);
    $this->article = $article;
    $this->setTemplate('getArticleById');
  }
}