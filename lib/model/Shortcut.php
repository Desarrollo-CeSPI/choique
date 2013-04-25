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
 * Subclass for representing a row from the 'shortcut' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Shortcut extends BaseShortcut implements SlotletInterface
{
  const REFERENCE_TYPE_EXTERNAL        = 0;
  const EXTERNAL_REFERENCE_STRING      = 'Externo';
  const REFERENCE_TYPE_ARTICLE         = 1;
  const ARTICLE_STRING                 = 'Artículo';
  const REFERENCE_TYPE_SECTION         = 2;
  const SECTION_STRING                 = 'Sección';
  const REFERENCE_TYPE_NONE            = 3;
  const NONE_STRING                    = 'Ninguno';
  const REFERENCE_TYPE_EXTERNAL_POP_UP = 4;
  const EXTERNAL_POP_UP_STRING         = 'Externo en Pop Up';
  const REFERENCE_TYPE_MOBILE = 5;
  const REFERENCE_TYPE_NO_MOBILE = 6;
  
  public function __toString() 
  {
    return $this->getTitle();
  }
  
  public function hasMultimedia()
  {
    $multimedia_id = $this->getMultimediaId();

    return (!empty($multimedia_id));
  }

  public function getRepresentedMultimedia($size = 's')
  {
    sfLoader::loadHelpers(array('I18N'));

    if ($this->hasMultimedia())
    {
      $ret = $this->getMultimedia()->getHTMLRepresentation($size);
    }
    else
    {
      $ret = __('Ninguno');
    }

    return $ret;
  }

  public function getHTMLRepresentation($multimedia_size = 'n')
  {
    sfLoader::loadHelpers(array('Url', 'Tag', 'Javascript'));

    $options = $this->getLinkOptions();
    if ($this->getReferenceType() == self::REFERENCE_TYPE_NONE)
    {
      return $this->getReferenceContent($multimedia_size);
    }
    elseif ($this->getReferenceType() == self::REFERENCE_TYPE_EXTERNAL_POP_UP)
    {
      return link_to($this->getReferenceContent($multimedia_size),$this->getReferenceUrl(), array("onclick" => "window.open('".$this->getReferenceUrl()."','New Window','height=500,width=700');return false;"));
    }
    else
    {
      return link_to($this->getReferenceContent($multimedia_size), $this->getReferenceUrl(), $options);
    }
  }

  public function getReferenceUrl()
  {
    sfLoader::loadHelpers(array('Url', 'Tag'));

    $route = '';
    switch ($this->getReferenceType())
    {
      case self::REFERENCE_TYPE_EXTERNAL:
      case self::REFERENCE_TYPE_EXTERNAL_POP_UP:
        $route = $this->getReference();
        break;
      case self::REFERENCE_TYPE_ARTICLE:
        $article = ArticlePeer::retrieveByPK($this->getReference());
        $route   = ($article) ? $article->getURLReference() : '';
        break;
      case self::REFERENCE_TYPE_SECTION:
        $section = SectionPeer::retrieveByPK($this->getReference());
        $route   = ($section) ? sprintf("@template_by_name?name=%s", $section->getName()) : '';
        break;
      case self::REFERENCE_TYPE_MOBILE:
        $route = '@mobile';
        break;
      case self::REFERENCE_TYPE_NO_MOBILE:
        $route = '@no_mobile';
        break;
      case self::REFERENCE_TYPE_NONE:
        break;
    }

    return $route;
  }

  public function getReferenceContent($multimedia_size = 'm')
  {
    if ($this->hasMultimedia())
    {
      return $this->getMultimedia()->getHTMLRepresentation($multimedia_size);
    }
    else
    {
      sfLoader::loadHelpers(array('Asset', 'I18N'));
      return $this->getTitle().(($this->getOpenInNewWindow() || ($this->getReferenceType() == Shortcut::REFERENCE_TYPE_EXTERNAL_POP_UP))?"&nbsp;".image_tag('frontend/external_shortcut.jpg', array("class" => "external-shortcut-image","alt" => __("Se abre en una ventana nueva"), "title" => __("Se abre en una ventana nueva"))):"");
    }
  }

  public static function getSlotletMethods()
  {
    return array('getSlotletLeft', 'getSlotletRight', 'getSlotletAd');
}

  public static function getSlotletName()
  {
    sfLoader::loadHelpers(array('I18N'));

    return __('Atajos');
  }

  /**
   *  Returns a piece of HTML code holding the representation
   *  of the Slotlet for Shortcut class. The elements to include
   *  will be obtained through the 'container_slotlet_id' option.
   *  The resulting HTML code should look like this:
   *
   *  <code>
   *    <ul id="sl_shortcut_PASSED_ID" class="sl_shortcut">
   *      <li class="first-level">
   *        //Actual representation of a Shortcut
   *      </li>
   *      <li class="first-level">
   *        //Actual representation of another Shortcut
   *      </li>
   *      // ...
   *    </ul>
   *  </code>
   *
   *  @param $options Array The options passed to the Slotlet.
   *
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
    $type_string = array(
        self::REFERENCE_TYPE_EXTERNAL        => "type-external",
        self::REFERENCE_TYPE_EXTERNAL_POP_UP => "type-external-pop-up",
        self::REFERENCE_TYPE_ARTICLE         => "type-article",
        self::REFERENCE_TYPE_SECTION         => "type-section",
        self::REFERENCE_TYPE_NONE            => "type-none"
    );

    //Mandatory parameters check and initialization
    if (!(array_key_exists('container_slotlet_id', $options) && isset($options['container_slotlet_id'])))
    {
      throw new Exception('Trying to include a Shortcut slotlet without providing mandatory "container_slolet_id" option.');
    }

    if (!array_key_exists('id', $options))
    {
      $options['id'] = 'sl_shortcut';
    }
    else
    {
      $options['id'] = 'sl_shortcut_' . $options['id'];
    }

    //HTML code generation
    $html = "<div id=\"" . $options['id'] . "\" class=\"sl_shortcut\">";
    foreach (ShortcutPeer::retrieveByContainerSlotletId($options['container_slotlet_id']) as $shortcut)
    {
      $html .= "<div class=\"first-level ".$type_string[$shortcut->getReferenceType()] . (($shortcut->hasMultimedia())? ' with-image' : '') . (($shortcut->getOpenInNewWindow() || ($shortcut->getReferenceType() == Shortcut::REFERENCE_TYPE_EXTERNAL_POP_UP))?" external-shortcut":"") ."\">" . $shortcut->getHTMLRepresentation() . "</div>";
    }
    $html .= "</div>";

    return $html;
  }

  public function isPublished()
  {
    return $this->getIsPublished();
  }

  public function isNotPublished()
  {
    return !$this->getIsPublished();
  }

  public function publish()
  {
    if (!$this->getIsPublished())
    {
      try
      {
        $this->setIsPublished(true);
        $this->save();

        return true;
      }
      catch (Exception $e)
      {
      }
    }

    return false;
  }

  public function unpublish()
  {
    if ($this->getIsPublished())
    {
      try
      {
        $this->setIsPublished(false);
        $this->save();

        return true;
      }
      catch (Exception $e)
      {
      }
    }

    return false;
  }

  private function getLinkOptions()
  {
    return array_merge( $this->getComment() != ''?array('title'=>$this->getComment()): array() , $this->getOpenInNewWindow() ? array('target' => '_blank') : array());
  }

  public function renderBoxSlotlet($str)
  {
    $options = $this->getLinkOptions();

    return $this->getReferenceType() == self::REFERENCE_TYPE_NONE?
      $str :
      link_to($str,$this->getReferenceUrl(), $options);
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



  public function getCreatedByName()
  {
    return $this->getCreatedByAsGuardUser()?$this->getCreatedByAsGuardUser()->getName():'';
  }

  public function getCreatedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getCreatedBy());
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
