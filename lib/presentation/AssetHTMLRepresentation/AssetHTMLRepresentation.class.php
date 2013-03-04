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

abstract class AssetHTMLRepresentation
{
  protected $multimedia;

  public function __construct($m)
  {
    $this->multimedia=$m;
  }

  /**
   * Return this object's inner Multimedia element.
   * 
   * @return Multimedia
   */
  public function getMultimedia()
  {
    return $this->multimedia;
  }

  protected function getMediumUri($absolute = false)
  {
    return Multimedia::relativeUriFor($this->getMultimedia()->getMediumUri());
  }

  protected function getLargeUri($absolute = false)
  {
    if ($this->getMultimedia()->getIsExternal())
    {
      return $this->getMultimedia()->getExternalUri();
    }
    else
    {
      return Multimedia::relativeUriFor($this->getMultimedia()->getLargeUri());
    }
  }

  protected function getSmallUri($absolute = false)
  {
    return Multimedia::relativeUriFor($this->getMultimedia()->getSmallUri());
  }

  protected function renderSmall()
  {
    return image_tag($this->getSmallUri(), array('alt' => $this->getMultimedia()->getDescription(), 'title' => $this->getMultimedia()->getDescription()));
  }

  protected function renderForGallery()
  {
    return $this->renderLarge();
  }

  protected function renderClickable()
  {
    return UJS_lightview_media(
        $this->getLargeUri(),
        image_tag($this->getMultimedia()->getMediumUri() ? $this->getMediumUri() : Multimedia::DEFAULT_VIDEO_ICON_URI),
        $this->getMultimedia()->getTitle(),
        $this->getMultimedia()->getDescription()
      );
  }

  protected function renderMedium()
  {
    return '&nbsp;';
  }

  protected function renderLarge()
  {
      $tag= UJS_lightview_media($this->getLargeUri(), 
                image_tag($this->getSmallUri(), array(
                'alt' => $this->getMultimedia()->getDescription(), 
                'title' => $this->getMultimedia()->getDescription())), 
                $this->getMultimedia()->getTitle(), 
                $this->getMultimedia()->getDescription());
      $tag .= sprintf("<noscript>%s</noscript>",image_tag($this->getSmallUri(), array(
                'alt' => $this->getMultimedia()->getDescription(), 
                'title' => $this->getMultimedia()->getDescription())));
  }

  protected function loadHelpers()
  {
    sfLoader::loadHelpers(array('Asset', 'Tag', 'Lightview', 'JWFLVMediaPlayer', 'UJS'));
  }

  public function render($size='m')
  {
    $this->loadHelpers();
    switch(strtolower($size)){
      case 'm':
        return $this->renderMedium();
      case 's':
        $ret= $this->renderSmall();
        if (CmsConfiguration::get('check_use_description_in_article_multimedia', false))
          $ret .= '<div class="multimedia_description">' . $this->getMultimedia()->getDescription() . '</div>';
        return $ret;
      case 'l':
        $ret= $this->renderLarge();
        if (CmsConfiguration::get('check_use_description_in_article_multimedia', false))
          $ret .= '<div class="multimedia_description">' . $this->getMultimedia()->getDescription() . '</div>';
        return $ret;
      case 'c':
        return $this->renderClickable();
      case 'g':
        return $this->renderForGallery();
    }
    throw new Exception(__CLASS__.": $size is not a valid size to render");
  }
}