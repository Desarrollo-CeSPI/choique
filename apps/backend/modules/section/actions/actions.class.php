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
 * seccion actions.
 *
 * @package    new_cms
 * @subpackage section
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class sectionActions extends autosectionActions
{
  public function getCredential()
  {
    return $this->getUser()->requiredCredentialsFor($this, parent::getCredential());
  }

  public function executeIncreasePriority()
  {
    $section = SectionPeer::retrieveByPK($this->getRequestParameter('id'));
    $section->setPriority($section->getPriority()+1);
    $section->save();
    
    $this->redirect('section/list');
  }

  public function executeDecreasePriority()
  {
    $section = SectionPeer::retrieveByPK($this->getRequestParameter('id'));
    $priority = $section->getPriority();
    if ($priority > 0)
    {
      $priority--;
    }
    $section->setPriority($priority);
    $section->save();

    $this->redirect('section/list');
  }

  public function executeUnpublish()
  {
    $this->section = SectionPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->section);
    $this->section->setIsPublished(false);
    $this->section->save();
    $this->setFlash('notice', 'Se han guardado los cambios');

    $this->redirect('section/index');
  }

  public function executePublish()
  {
    $this->section = SectionPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->section);
    $this->section->setIsPublished(true);
    $this->section->save();
    $this->setFlash('notice', 'Se han guardado los cambios');

    $this->redirect('section/index');
  }

  public function executeEditInstitutionalPriorities()
  {
    $section_id = $this->getRequestParameter('id');
    $section = SectionPeer::retrieveByPK($section_id);
    $c=new Criteria();
    $c->addDescendingOrderByColumn(ArticleSectionPeer::PRIORITY);
    $this->article_sections = $section->getArticleSections($c);
    $already_in_section = $this->getAlreadyInSection();
    $this->articles = $this->getNewsArticlesBut($already_in_section);
    $this->section_id = $section_id;
  }

  public function executeAddAjaxArticle()
  {
    $article_id = $this->getRequestParameter('id');
    $section_id = $this->getRequestParameter('section_id');
    $article_section = new ArticleSection();
    $article_section->setArticleId($article_id);
    $article_section->setSectionId($section_id);
    $article_section->save();

    $c = new Criteria();
    $c->addDescendingOrderByColumn(ArticleSectionPeer::PRIORITY);
    $section=SectionPeer::retrieveByPK($section_id);
    $this->article_sections = $section->getArticleSections($c);
    $already_in_section = $this->getAlreadyInSection();
    $this->articles = $this->getNewsArticlesBut($already_in_section);
    $this->section_id = $section_id;
    $this->setLayout(false);
  }

  public function executeRemoveAjaxArticle()
  {
    $article_id=$this->getRequestParameter('id');
    $section_id=$this->getRequestParameter('section_id');
    ArticleSectionPeer::doDelete($article_id);

    $c = new Criteria();
    $c->addDescendingOrderByColumn(ArticleSectionPeer::PRIORITY);
    $section = SectionPeer::retrieveByPK($section_id);
    $this->article_sections = $section->getArticleSections($c);
    $already_in_section = $this->getAlreadyInSection();
    $this->articles = $this->getNewsArticlesBut($already_in_section);
    $this->section_id = $section_id;
    $this->setLayout(false);
  }

  private function getNewsArticlesBut($articles_included)
  {
    $c = new Criteria();
    $c->add(ArticlePeer::TYPE,Article::NEWS);
    $c->add(ArticlePeer::IS_DELETED,false);
    $c->add(ArticlePeer::IS_PUBLISHED,true);
    $c->add(ArticlePeer::IS_ARCHIVED,false);
    $c->add(ArticlePeer::ID,$articles_included,Criteria::NOT_IN);
    
    return ArticlePeer::doSelect($c);
  }

  private function getAlreadyInSection()
  {
    $already_in_section = array();
    $sections = $this->article_sections;
    foreach ($sections as $a_section)
    {
      $already_in_section[]=$a_section->getArticleId();
    }
    
    return $already_in_section;
  }

  /* new version */
  public function executeEditPriorities()
  {
    $this->section = $this->getSectionOrCreate();
  }

  public function executeAutocomplete()
  {
  	sfLoader::loadHelpers(array('I18N'));
    $text = $this->getRequestParameter('section_autocomplete_query', $this->getRequestParameter('section_autocomplete_query_bis'));
    $section_id = $this->getRequestParameter('section_id');
    $for_filter = $this->getRequestParameter('for_filter', false);
    $query = '%'.$text.'%';

    $c = new Criteria();
    $c->setIgnoreCase(true);
    $c->addAscendingOrderByColumn(SectionPeer::TITLE);
    $crit = $c->getNewCriterion(SectionPeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(SectionPeer::NAME, $query, Criteria::LIKE));
    $c->add($crit);
    $c->setLimit(12);
    $sections = SectionPeer::doSelectExcludingDescendants($c, $section_id, $for_filter);

    $txt = '<ul>';
    if (!empty($sections))
    {
      foreach ($sections as $section)
      {
        $txt .= '<li id="' . $section->getId() . '">' . $section->getTreeToString(true) . '</li>';
      }
    }
    else
    {
      $txt .= '<li id="">' . __('No se encontraron coincidencias') . '</li>';
    }
    $txt .= '</ul>';

    $this->renderText($txt);

    return sfView::NONE;
  }

  public function executeAutocompleteArticle()
  {
    $query = '%'.$this->getRequestParameter('article_id_search').'%';

    $c = new Criteria();
    $crit = $c->getNewCriterion(ArticlePeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(ArticlePeer::DESCRIPTION, $query, Criteria::LIKE));
    $crit->addOr($c->getNewCriterion(ArticlePeer::COMMENT, $query, Criteria::LIKE));
    $crit->addOr($c->getNewCriterion(ArticlePeer::BODY, $query, Criteria::LIKE));
    $c->add($crit);
    $c->addDescendingOrderByColumn(ArticlePeer::UPDATED_AT);
    if ($this->getRequestParameter('only_articles', false))
    {
      $c->add(ArticlePeer::TYPE, Article::ARTICLE);
    }
    else
    {
      $c->add(ArticlePeer::TYPE, Article::NEWS);
    }

    $c->setLimit(12);

    $this->articles = ArticlePeer::doSelect($c);
  }

  public function executeAddArticleSection()
  {
    $this->section = $this->getSectionOrCreate('section_id');
    $this->forward404Unless($this->article = ArticlePeer::retrieveByPk($this->getRequestParameter('article_id')));

    if (!ArticleSectionPeer::exists($this->section, $this->article))
    {
      $as = new ArticleSection();
      $as->setSection($this->section);
      $as->setArticle($this->article);
      $as->save();
    }
  }

  public function executeSortArticleSections()
  {
    $sorts = array_reverse($this->getRequestParameter('sortable-list', array()));
    for ($i = 0; $i < count($sorts); $i++)
    {
      $article_section = ArticleSectionPeer::retrieveByPk($sorts[$i]);
      $section = $article_section->getSection();
      $max_depth = Section::$MAX_DEPTH;
      $boost = 10 * ($max_depth - $section->getDepth());
      $priority = $boost + $i;

      $article_section->setPriority($priority);
      $article_section->save();
    }
    
    return sfView::NONE;
  }

  public function executeDeleteArticleSection()
  {
    $this->section = $this->getSectionOrCreate();
    $this->forward404Unless($article_section = ArticleSectionPeer::retrieveByPk($this->getRequestParameter('article_section_id')));

    $article_section->delete();
  }


  protected function saveSection($section)
  {
    $section->save();

    switch ($this->getActionName()) {
      case 'create':
          // Update many-to-many for "section_links"
          $c = new Criteria();
          $c->add(SectionLinkPeer::SECTION_ID, $section->getPrimaryKey());
          SectionLinkPeer::doDelete($c);

          $ids = $this->getRequestParameter('associated_section_links');
          if (is_array($ids))
          {
            foreach ($ids as $id)
            {
              $SectionLink = new SectionLink();
              $SectionLink->setSectionId($section->getPrimaryKey());
              $SectionLink->setLinkId($id);
              $target_id = $this->getRequestParameter('associated_section_links_target_' . $id);
              
              if (is_null($target_id))
              {
                $SectionLink->setTargetBlank(false);
              }
              $SectionLink->save();
            }
          }

          // Update many-to-many for "section_documents"
          $c = new Criteria();
          $c->add(SectionDocumentPeer::SECTION_ID, $section->getPrimaryKey());
          SectionDocumentPeer::doDelete($c);

          $ids = $this->getRequestParameter('associated_section_documents');
          if (is_array($ids))
          {
            foreach ($ids as $id)
            {
              $SectionDocument = new SectionDocument();
              $SectionDocument->setSectionId($section->getPrimaryKey());
              $SectionDocument->setDocumentId($id);
              $SectionDocument->save();
            }
          }

        break;
      case 'edit':
          // Update many-to-many for "section_links"
          $c = new Criteria();
          $c->add(SectionLinkPeer::SECTION_ID, $section->getPrimaryKey());
          SectionLinkPeer::doDelete($c);

          $ids = $this->getRequestParameter('associated_section_links');
          
          
          if (is_array($ids))
          {
            foreach ($ids as $id)
            {
              $SectionLink = new SectionLink();
              $SectionLink->setSectionId($section->getPrimaryKey());
              $SectionLink->setLinkId($id);

              $target_id = $this->getRequestParameter('associated_section_links_target_' . $id);
              
              if (is_null($target_id))
              {
                $SectionLink->setTargetBlank(false);
              }

              $SectionLink->save();
            }
          }

          // Update many-to-many for "section_documents"
          $c = new Criteria();
          $c->add(SectionDocumentPeer::SECTION_ID, $section->getPrimaryKey());
          SectionDocumentPeer::doDelete($c);

          $ids = $this->getRequestParameter('associated_section_documents');
          
          if (is_array($ids))
          {
            foreach ($ids as $id)
            {
              $SectionDocument = new SectionDocument();
              $SectionDocument->setSectionId($section->getPrimaryKey());
              $SectionDocument->setDocumentId($id);
              $SectionDocument->save();
            }
          }

        break;
    }
  }
}