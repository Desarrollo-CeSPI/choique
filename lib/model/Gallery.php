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
 * Subclass for representing a row from the 'gallery' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Gallery extends BaseGallery
{
  /**
   *    Return a string holding an HTML representation of this
   *    Gallery 
   *
   *    @return string
   */
  public static function getNullHTMLRepresentation($description)
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($description) ? __('Referencia a galería inválida') : $description);
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
    if ( !$this->canBeModifiedWhenPublished() ) return false;
    $criteria = new Criteria();
    $criteria->add(ArticleGalleryPeer::GALLERY_ID,$this->getId());
    return  count(ArticleGalleryPeer::doSelect($criteria)) == 0;
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

  public function getMultimedia($criteria = null)
  {
    if ($this->isNew())
    {
      return $this->getDynamicMultimedia();
    }

    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    return array_map(create_function('$multimedia_gallery', 'return $multimedia_gallery->getMultimedia();'), $this->getMultimediaGallerysJoinMultimedia($criteria));
  }

  public function getMultimediasByPriority($criteria = null)
  {
    if ($this->isNew())
    {
      return $this->getDynamicMultimedia();
    }

    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $criteria->addDescendingOrderByColumn(MultimediaGalleryPeer::PRIORITY);

    return $this->getMultimedia($criteria);
  }

  /**
   *    Return a string holding an HTML representation of this
   *    Gallery 
   *
   *    @return string
   */
  public function getHTMLRepresentation()
  {
    sfLoader::loadHelpers(array('CarouselComponent', 'Lightview'));

    $representations = array();
    foreach ($this->getMultimediasByPriority() as $multimedia)
    {
      if ($multimedia->getType() == 'image')
      {
        $representations[] = lightview_image(Multimedia::relativeUriFor($multimedia->getLargeUri()),
                                         image_tag(Multimedia::relativeUriFor($multimedia->getSmallUri()), array('alt' => $multimedia->getTitle(), 'title' => $multimedia->getDescription())),
                                         $multimedia->getTitle(),
                                         $multimedia->getDescription(),
                                         $this->getName());
      }
      else
      {
        $representations[] = $multimedia->getHTMLRepresentation('l');
      }
    }

    $orientation_method = $this->getIsHorizontal() ? 'carousel_component_horizontal' : 'carousel_component_vertical';

    return $orientation_method($this->getName(), $representations, array('numVisible' => $this->getVisibleItems(), 'navMargin' => 12));
  }

  public static function belongsToGallery($multimedia_id)
  {
    $c = new Criteria();
    $c->add(MultimediaPeer::ID, $multimedia_id);

    return (MultimediaGalleryPeer::doCount($c) > 0);
  }
  
  //Used for validating the max number of images to be shown in a gallery
  static function visible_items_check($value) 
  {
    return ($value <= CmsConfiguration::get('max_gallery_images', 5));
  }

  public function getMultimediaGallerysByPriority()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(MultimediaGalleryPeer::PRIORITY);
    
    return parent::getMultimediaGallerysJoinMultimedia($c);
  }
  
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

  public function getShowHTMLRepresentation()
  {
    $tag = '<div id="gallery-list">';
    foreach($this->getMultimediasByPriority() as $media)
    {
      $tag .= "<div class='gallery-element'>";
      $tag .= $media->getHTMLRepresentation();
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

  public function hasImages()
  {
    return (0 < count($this->getImages()));
  }

  public function getImages()
  {
    return $this->getMultimediaByType('image');
  }

  public function hasVideos()
  {
    return (0 < count($this->getVideos()));
  }

  public function getVideos()
  {
    return $this->getMultimediaByType(array('video', 'external', 'application'));
  }

  public function hasAudios()
  {
    return (0 < count($this->getAudios()));
  }

  public function getAudios()
  {
    return $this->getMultimediaByType('audio');
  }

  public function getMultimediaByType($type)
  {
    if ($this->isNew())
    {
      return $this->getDynamicMultimediaByType($type);
    }

    $criteria = new Criteria();

    $criteria->setIgnoreCase(true);
    
    if (is_array($type))
    {
      $criteria->add(MultimediaPeer::TYPE, $type, Criteria::IN);
    }
    else
    {
      $criteria->add(MultimediaPeer::TYPE, $type);
    }

    return $this->getMultimedia($criteria);
  }

  public function getDynamicMultimediaByType($types)
  {
    $matches = array();
    
    if (!is_array($types))
    {
      $types = array($types);
    }

    foreach ($this->collMultimediaGallerys as $multimedia_gallery)
    {
      if (in_array($multimedia_gallery->getMultimedia()->getType(), $types))
      {
        $matches[] = $multimedia_gallery->getMultimedia();
      }
    }

    return $matches;
  }

  /**
   * Add $multimedia to the collection of Multimedia objects
   * related to this Gallery (creating a new MultimediaGallery object
   * but not saving it).
   * 
   * @param Multimedia $multimedia
   */
  public function addMultimedia(Multimedia $multimedia)
  {
    $multimedia_gallery = new MultimediaGallery();

    $multimedia_gallery->setMultimedia($multimedia);

    $this->addMultimediaGallery($multimedia_gallery);
  }

  public function hasMultimedia()
  {
    if (0 == count($this->collMultimediaGallerys))
    {
      return (0 != $this->countMultimediaGallerys());
    }

    return true;
  }

  public function getDynamicMultimedia()
  {
    $matches = array();

    foreach ($this->collMultimediaGallerys as $multimedia_gallery)
    {
      $matches[] = $multimedia_gallery->getMultimedia();
    }

    return $matches;
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

  public function getPublishedByUser()
  {
    $author = $this->getsfGuardUserRelatedByPublishedBy();

    return ($author) ? $author->getName() : '';
  }

}