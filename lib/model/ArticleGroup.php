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
 * Subclass for representing a row from the 'article_group' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ArticleGroup extends BaseArticleGroup
{

	public function __toString()
	{
	  return $this->getTitle();
	}

	public function getDescriptionExcerpt()
	{
		sfLoader::loadHelpers(array('Text'));

		return truncate_text($this->getDescription(), 40);
	}

  public function getTitle()
  {
    return $this->getName();
  }

  public function getCurrentStatus()
  {
    if ($this->getIsPublished())
    {
      return __('Publicada'). ' (' . $this->getPublishedAt('d/m/Y') . ')';
    }
    else
    {
      return __('Sin publicar');
    }
  }

  protected function canBeModifiedWhenPublished()
  {
    return
        ($context= sfContext::getInstance()) && 
          (
          $context->getUser()->isSuperAdmin()
          ||
          (
            (
            (
            $context->getUser()->hasCredential(array('designer','reporter'),false) &&
            !$this->getIsPublished() 
            ) 
            ||
            $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
            )
            && $context->getUser()->getGuardUser() !== null && $context->getUser()->getGuardUser()->getId() == $this->getCreatedBy()
          ));
  }

  public function canEdit()
  {
    return $this->canBeModifiedWhenPublished();
  }
  public function canDelete()
  {
    return $this->canBeModifiedWhenPublished();
  }

  public function canUnpublish()
  {
    return $this->canBeModifiedWhenPublished() && $this->getIsPublished();
  }

  public function canPublish()
  {
    return
        ($context= sfContext::getInstance()) &&
          (
          $context->getUser()->isSuperAdmin()
          ||
          (
            $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
            && $context->getUser()->getGuardUser() !== null &&  $context->getUser()->getGuardUser()->getId() == $this->getCreatedBy()
          )) && !$this->getIsPublished();
  }

  public function delete ($con = null)
  {
    if ($this->canDelete())
    {
      return parent::delete($con);
    }
    else
    {
      return false;
    }
  }

  public function setIsPublished($isPublished)
  {
    if ($isPublished)
    {
      $this->setPublishedAt(date('Y-m-d H:i'));
    }

    return parent::setIsPublished($isPublished);
  }

  public function setUnpublished()
  {
    $this->setIsPublished(false);

    return true;
  }

  public function getArticleArticleGroupsByPriority($criteria = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }
    $criteria->addJoin(ArticlePeer::ID, ArticleArticleGroupPeer::ARTICLE_ID);
    $criteria->add(ArticlePeer::IS_PUBLISHED, true);
    $criteria->add(ArticleArticleGroupPeer::ARTICLE_GROUP_ID, $this->getId());
    $criteria->addDescendingOrderByColumn(ArticleArticleGroupPeer::PRIORITY);
    
    return ArticleArticleGroupPeer::doSelect($criteria);
  }

  public function getArticlesByPriority($criteria = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }
    $criteria->addJoin(ArticlePeer::ID, ArticleArticleGroupPeer::ARTICLE_ID);
    $criteria->add(ArticlePeer::IS_PUBLISHED, true);
    $criteria->add(ArticleArticleGroupPeer::ARTICLE_GROUP_ID, $this->getId());
    $criteria->addDescendingOrderByColumn(ArticleArticleGroupPeer::PRIORITY);
    return ArticlePeer::doSelect($criteria);
  }

  //Used for validating the max number of images to be shown in a gallery
  static function visible_items_check($value)
  {
    return ($value <= CmsConfiguration::get('max_gallery_articles', 15));
  }

  /*
  public function getURLReference($article_id = null)
  {
    if ($article_id == null)
    {
      $url = "@homepage";
    }
    else
    {
      $url = sprintf('@show_gallery?gallery_id=%d&article_id=%d',
                    $this->getId(), $article_id);
    }

    return $url;
  }
   *
   */

  public function getArticleArticleGroups($criteria = null, $con = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $criteria->addDescendingOrderByColumn(ArticleArticleGroupPeer::PRIORITY);
    
    return parent::getArticleArticleGroups($criteria, $con);
  }

  public function getShowHTMLRepresentation()
  {
    $tag = '<div id="gallery-list">';
    foreach($this->getMultimediasByPriority() as $article)
    {
      $tag .= "<div class='gallery-element'>";
      $tag .= $article->__toString();
      $tag .= "</div>";
    }
    $tag .= '</div>';

    return $tag;
  }

  public function isPublish()
  {
    return $this->getIsPublished();
  }

  public function isUnpublish()
  {
    return !$this->getIsPublished();
  }


  public function getCreatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByCreatedBy();

    return ($author) ? $author->getName() : '';
  }

  public function getUpdatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByUpdatedBy();

    return ($author) ? $author->getName() : '';
  }
}