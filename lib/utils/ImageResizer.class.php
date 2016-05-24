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

class ImageResizer
{
  protected
    $multimedia,
    $extension_loaded;

  public function __construct($multimedia)
  {
    $this->extension_loaded = extension_loaded('imagick');
    $this->multimedia = $multimedia;
  }

  public function canResizeImages()
  {
    return $this->extension_loaded;
  }

  public function resize($size = 'm')
  {
    if ($this->extension_loaded)
    {
      switch ($size)
      {
        case 's':
          $this->createSmallImage(intval(CmsConfiguration::get('small_image_default_width', 75)));
          break;
        case 'm':
          $this->createMediumImage(intval(CmsConfiguration::get('medium_image_default_width', 200)));
          break;
      }
    }
  }
  
  private function getCompleteImageTag($uri, $tag_attributes = array())
  {
    if($this->multimedia->hasLongdesc()){
      $tag_attributes = array_merge($tag_attributes, array('longdesc' => $this->multimedia->getLongdescRelativeUri()));
    }

    $tag_attributes['absolute'] = true;

    return image_tag($uri, $tag_attributes);
  }

  public function getImageTag($size = 'm')
  {
    $tag = '';

    switch ($size)
    {
      case 's':
        if ($this->extension_loaded)
        {
          if ($this->multimedia->getType() != 'image')
          {
            $uri = $this->multimedia->getSmallUri();
            if (!$this->multimedia->isDefaultSmallUri())
            {
              $uri = Multimedia::relativeURIFor($uri);
            }
          }
          else
          {
              $uri = $this->multimedia->getSmallUri() == null?
                        Multimedia::relativeURIFor($this->multimedia->getLargeUri()):
                        Multimedia::relativeURIFor($this->multimedia->getSmallUri());
          }
          
          $tag = $this->getCompleteImageTag($uri, array('alt' => $this->multimedia->getDescription(), 'title' => $this->multimedia->getDescription()));
        }
        else
        {
          if ($this->multimedia->getType() != 'image')
          {
            $uri = $this->multimedia->getSmallUri();
            if (!$this->multimedia->isDefaultSmallUri())
            {
              $uri = Multimedia::relativeURIFor($uri);
            }
          }
          else
          {
            $uri = Multimedia::relativeURIFor($this->multimedia->getLargeUri());
          }
          
          $tag = $this->getCompleteImageTag($uri, array('width' => CmsConfiguration::get('small_image_default_width', 75), 'alt' => $this->multimedia->getDescription(), 'title' => $this->multimedia->getDescription()));
        }
        break;
      case 'm':
        if ($this->extension_loaded)
        {
          $uri = $this->multimedia->getMediumUri() == null?
                        Multimedia::relativeURIFor($this->multimedia->getLargeUri()):
                        Multimedia::relativeURIFor($this->multimedia->getMediumUri());
          $tag_attributes = array('alt' => $this->multimedia->getDescription(), 'title' => $this->multimedia->getDescription());
        }
        else
        {
          $uri = Multimedia::relativeURIFor($this->multimedia->getLargeUri());
          $tag_attributes = array('width' => CmsConfiguration::get('medium_image_default_width', 200), 'alt' => $this->multimedia->getDescription(), 'title' => $this->multimedia->getDescription());
        }
        $tag = $this->getCompleteImageTag($uri, $tag_attributes);
        break;
      case 'l':
        $uri = Multimedia::relativeURIFor($this->multimedia->getLargeUri());
        $tag_attributes = array('alt' => $this->multimedia->getDescription(), 'title' => $this->multimedia->getDescription());
        $tag = $this->getCompleteImageTag($uri, $tag_attributes);
        break;
    }

    return $tag;
  }

  public function createSmallImage($width, $height = null)
  {
    try{
      $im = new Imagick();
      $im->readImage(sfConfig::get('cms_images_dir').'/'.$this->multimedia->getLargeUri());
      // thumbnailImage($width, $height); // null = preserve dimensions
      $im->thumbnailImage($width, $height);
      $this->multimedia->setSmallUri(str_replace('_large', '_small', $this->multimedia->getLargeUri()));
      $im->writeImage(sfConfig::get('cms_images_dir').'/'.$this->multimedia->getSmallUri());
      $im->destroy();
    }catch(Exception $e){};
  }

  public function createMediumImage($width, $height = null)
  {
    try{
      $im = new Imagick();
      $im->readImage(sfConfig::get('cms_images_dir').'/'.$this->multimedia->getLargeUri ());
      // thumbnailImage($width, $height); // null = preserve dimensions
      $im->thumbnailImage($width, $height);
      $this->multimedia->setMediumUri(str_replace('_large', '_medium', $this->multimedia->getLargeUri()));
      $im->writeImage(sfConfig::get('cms_images_dir').'/'.$this->multimedia->getMediumUri());
      $im->destroy();
    }catch(Exception $e){};
  }
}
