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
 * Subclass for representing a row from the 'link' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Link extends BaseLink
{
  public static function relativeURIFor($path)
  {
    $str = str_replace(sfConfig::get('sf_web_dir'), '', $path);

    return image_path($str);
  }

  public function getHTMLRepresentation()
  {
    use_helper('Lightview');
    return lightview_iframe($this->getUrl(), $this->getImageRepresentation(), $this->getName(), '', array('fullscreen' => 'true'));
  }

  public function getImageRepresentation()
  {
    return  $this->getUri()? image_tag(self::relativeUriFor($this->getUri())): $this->getName();
  }

  public function __toString()
  {
    return $this->getName();
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

  public function getCreatedByUser()
  {
    return $this->getCreatedByAsGuardUser()?$this->getCreatedByAsGuardUser()->getName():'';
  }

  public function getCreatedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getCreatedBy());
  } 

  public function canDelete()
  {
    if ( !$this->canBeModifiedWhenPublished() ) return false;
    return $this->countArticles() + $this->countSectionLinks() == 0;
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
      try
      {
        @unlink($this->getUri());
        return parent::delete($con);
      }
      catch (Exception $e)
      {
        throw $e->getMessage();
      }
    }
    else
    {
      return false;
    }
  }


  public function getUpdatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByUpdatedBy();

    return ($author) ? $author->getName() : '';
  }


}
