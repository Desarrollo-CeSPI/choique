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
 * Subclass for representing a row from the 'multimedia' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Multimedia extends BaseMultimedia
{
  const DEFAULT_AUDIO_ICON_URI = "common/audio-generic.png";
  const DEFAULT_PDF_ICON_URI   = "common/pdf-generic.png";
  const DEFAULT_VIDEO_ICON_URI = "common/video-generic.png";
  const FLV_LOGO               = "common/flv-logo.png";

  const FLV_PARAMS_DELIMITER   = ';';

  /**
   *    Return a string holding an HTML representation of this
   *    multimedia content in the specified size.
   *        $size is a char: 's' | 'm' | 'l' | 'c'.
   *
   *    @return string
   */
  public static function getNullHTMLRepresentation($size = 'm', $alt = '')
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($alt) ? __('Referencia a imagen inválida') : $alt);
  }

  public function setName($v)
  {
    sfLoader::loadHelpers(array('CmsEscaping'));

    if (empty($v))
    {
      $v = $this->getTitle();
    }

    if ($v !== null && !is_string($v))
    {
      $v = (string) $v;
    }
    $v = escape_string($v);

    if ($this->name !== $v)
    {
      $aux = $this->name;
      $this->name = $v;
      if ($aux2 = $this->getLargeUri())
      {
        $this->setLargeUri(str_replace($aux, $this->getName(), $aux2));
        rename(sfConfig::get('cms_images_dir').'/'.$aux2, sfConfig::get('cms_images_dir').'/'.$this->getLargeUri());

        /* recalcula el tipo mime porque se pierde en setLargeUri() */
        $guesser = new MimeTypeGuesser();
        $mime = $guesser->guess(sfConfig::get('cms_images_dir').'/'.$this->getLargeUri());
        $this->setMimeType($mime);
        $ext = strstr($this->getMimeType(), '/');
        $this->setType(substr($this->getMimeType(), 0, strlen($this->getMimeType())-strlen($ext)));
      }

      if ($aux2 = $this->getMediumUri())
      {
        $this->setMediumUri(str_replace($aux, $this->getName(), $aux2));
        rename(sfConfig::get('cms_images_dir').'/'.$aux2, sfConfig::get('cms_images_dir').'/'.$this->getMediumUri());
      }

      if ($aux2 = $this->getSmallUri())
      {
        $this->setSmallUri(str_replace($aux, $this->getName(), $aux2));
        rename(sfConfig::get('cms_images_dir').'/'.$aux2, sfConfig::get('cms_images_dir').'/'.$this->getSmallUri());
      }
      
      if($aux2 = $this->getLongdescUri())
      {
        $this->setLongdescUri(str_replace($aux, $this->getName(), $aux2));
        rename(sfConfig::get('cms_longdesc_images_dir').'/'.$aux2, sfConfig::get('cms_longdesc_images_dir').'/'.$this->getLongdescUri());
      }
      $this->modifiedColumns[] = MultimediaPeer::NAME;
    }
  }

  public function setLargeUri($v)
  {
    if ($v !== null && !is_string($v))
    {
      $v = (string) $v;
    }

    //if ($this->large_uri !== $v) {
      $this->large_uri = $v;
      if ($this->large_uri != '')
      {
        $guesser = new MimeTypeGuesser();
        $mime = $guesser->guess(sfConfig::get('cms_images_dir').'/'.$this->getLargeUri());
        $this->setMimeType($mime);
        $ext = strstr($this->getMimeType(), '/');
        $this->setType(substr($this->getMimeType(), 0, strlen($this->getMimeType())-strlen($ext)));

        if ($this->getType() == 'image')
        {
          if (is_file($this->getMediumUri()))
          {
            unlink($this->getMediumUri());
          }
          $im = new ImageResizer($this);

          if ( $this->getMediumUri() == null ) {
            $im->resize('m');
          }

          if (!$this->isDefaultSmallUri() && is_file($this->getSmallUri()))
          {
            unlink($this->getSmallUri());
          }
          
          if ( $this->getSmallUri() == null ) {
            $im->resize('s');
          }
        }
      if (file_exists(sfConfig::get('cms_images_dir').'/'.$this->getLargeUri()))
      {
        $arr = getimagesize(sfConfig::get('cms_images_dir').'/'.$this->getLargeUri());
        $this->setHeight($arr[1]);
        $this->setWidth($arr[0]);
      }
      $this->modifiedColumns[] = MultimediaPeer::LARGE_URI;
    }
  }

  public function isDefaultSmallUri()
  {
    return ($this->small_uri == '' || is_null($this->small_uri));
  }

  public function getSmallUri() 
  {
    $return = $this->small_uri;
    if ($this->isDefaultSmallUri())
    {
      switch ($this->getType())
      {
        case 'application':
          $return = self::DEFAULT_VIDEO_ICON_URI;
        break;
        case 'audio':
          $return = self::DEFAULT_AUDIO_ICON_URI;
        break;
        case 'external':
        case 'video':
          $return = self::DEFAULT_VIDEO_ICON_URI;
        break;
      }
    }

    return $return;
  }

  public static function largeUriFor($filename)
  {
    $ext   = strstr($filename, '.');
    $tmpfn = substr($filename, 0, strlen($filename)-strlen($ext));
    $str   = $tmpfn.'_large'.$ext;

    return $str;
  }

  public static function mediumUriFor($filename)
  {
    $ext   = strstr($filename, '.');
    $tmpfn = substr($filename, 0, strlen($filename)-strlen($ext));
    $str   = $tmpfn.'_medium'.$ext;

    return $str;
  }

  public static function smallUriFor($filename)
  {
    $ext   = strstr($filename, '.');
    $tmpfn = substr($filename, 0, strlen($filename)-strlen($ext));
    $str   = $tmpfn.'_small'.$ext;

    return $str;
  }
  
  public static function longdescUriFor($filename)
  {
    $ext   = strstr($filename, '.');
    $tmpfn = substr($filename, 0, strlen($filename)-strlen($ext));
    $str   = $tmpfn.'_longdesc'.$ext;

    return $str;
  }

  public static function relativeURIFor($path)
  {
    sfLoader::loadHelpers(array('Tag','Asset'));
    
    $str = str_replace(sfConfig::get('sf_web_dir'), '', sfConfig::get('cms_images_dir').'/'.$path);
    return image_path($str);
  }

  public static function absoluteURIFor($path)
  {
    if ('' == trim($path))
    {
      return;
    }

    sfLoader::loadHelpers(array('Tag', 'Asset'));
    
    $str = str_replace(sfConfig::get('sf_web_dir'), '', sfConfig::get('cms_images_dir').'/'.$path);
    return image_path($str, true);
  }

  /**
   *    Return a string with a reference to this content
   *    in the following manner:
   *        {{multimedia:id_of_the_content}}
   *    which is better for database treatment.
   *
   *    @return string
   */
  public function getIdReferenceTag()
  {
    return sprintf("{{multimedia:%d}}", $this->getId());
  }

  /**
   *    Return a string holding an HTML representation of this
   *    multimedia content in the specified size.
   *        $size is a char: 's' | 'm' | 'l' | 'c' | 'n' | 'g'.
   *
   *    @return string
   */
  public function getHTMLRepresentation($size = 'm', $gallery = null)
  {
    sfLoader::loadHelpers(array('Asset', 'Tag', 'Lightview', 'JWFLVMediaPlayer', 'UJS', 'I18N'));

    $tag = '';
    $im = new ImageResizer($this);

    switch ($size)
    {
      case 's':
        $tag = $im->getImageTag('s');
        if (CmsConfiguration::get('check_use_description_in_article_multimedia', false))
        {
          $tag .= '<div class="multimedia_description">' . $this->getDescription() . '</div>';
        }
      break;
      case 'c':
        switch ($this->getType())
        {
          case 'image':
            $tag = UJS_lightview_image(Multimedia::relativeURIFor($this->getLargeUri()),
                                   $im->getImageTag('m'),
                                   $this->getTitle(),
                                   $this->getDescription(),
                                   $gallery
            );
            $tag .= sprintf("<noscript>%s</noscript>", $im->getImageTag('m'));
          break;
          case 'external':
            if ($representer = AssetHTMLRepresentationFactory::getInstance($this))
            {
              $tag = UJS_lightview_ajax(url_for('multimedia/external?id='.$this->getId()), $im->getImageTag('s'), $this->getTitle(), $this->getDescription(), array('fullscreen' => true));
              $tag .= sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador."));
            }
            else
            {
              $tag = $this->getHTMLRepresentation('m', $gallery);
            }
            break;
          case 'audio':
            $tag = $this->getHTMLRepresentation('m', $gallery);
            break;
          default:
            $tag = UJS_lightview_media(Multimedia::relativeURIFor($this->getLargeUri()),
                                   $im->getImageTag('m'),
                                   $this->getTitle(),
                                   $this->getDescription());
          break;
        }
      case 'g':
        switch ($this->getType())
        {
          case 'image':
            $tag = UJS_lightview_image(Multimedia::relativeURIFor($this->getLargeUri()), $im->getImageTag('s'), $this->getTitle(), $this->getDescription(), $gallery);
            $tag .= sprintf("<noscript>%s</noscript>", $im->getImageTag('s'));
          break;
          case 'external':
            if ($representer = AssetHTMLRepresentationFactory::getInstance($this))
            {
              $tag = $representer->render('l');
              $tag .= sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador."));
            }
            else
            {
              $tag = $this->getHTMLRepresentation('s', $gallery);
            }
            break;
          case 'audio':
            $tag = $this->getHTMLRepresentation('m', $gallery);
            break;
          case 'video':
            $tag = $this->getHTMLRepresentation('m', $gallery);
            break;
          default:
            $tag = UJS_lightview_media(Multimedia::relativeURIFor($this->getLargeUri()), $im->getImageTag('s'), $this->getTitle(), $this->getDescription());
          break;
        }
      break;
      case 'm':
        if ($this->getType() == 'image')
        {
          $tag = $im->getImageTag('m');
        }
        elseif ($this->getType() == 'audio')
        {
          $tag = UJS_write(mediaplayer_audio(Multimedia::relativeURIFor($this->getLargeUri()), array('backcolor' => '0xb2b2b2', 'frontcolor' => '0x646464')));
          $tag .= sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador."));
        }
        else
        {
          if ($representer = AssetHTMLRepresentationFactory::getInstance($this))
          {
            $tag = $representer->render($size);
            $tag .= sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador."));
          }
          else
          {
            $tag = $this->getBanner();
          }
        }
      break;
      case 'l':
        switch ($this->getType())
        {
          case 'image':
            $tag = UJS_lightview_image(Multimedia::relativeURIFor($this->getLargeUri()),
                                   $im->getImageTag('s'),
                                   $this->getTitle(),
                                   $this->getDescription());
            $tag .= sprintf("<noscript>%s</noscript>", $im->getImageTag('m'));
          break;
          default:
            if ($representer = AssetHTMLRepresentationFactory::getInstance($this))
            {
              $tag = $representer->render($size);
            }
            else
            {
              $tag = UJS_lightview_media(Multimedia::relativeURIFor($this->getLargeUri()), $im->getImageTag('s'));
            }
            $tag .= sprintf("<noscript>%s</noscript>", $im->getImageTag('s'));
          break;
        }
        if (CmsConfiguration::get('check_use_description_in_article_multimedia', false))
          $tag .= '<div class="multimedia_description">' . $this->getDescription() . '</div>';
      break;
      case 'n':
        if ($this->getType() == 'image')
        {
          $tag = $im->getImageTag('l');
        }
        else
        {
          $tag = UJS_lightview_media(Multimedia::relativeURIFor($this->getLargeUri()), $im->getImageTag('s'));
          $tag .= sprintf("<noscript>%s</noscript>", $im->getImageTag('s'));
        }
      break;
      default:
        printf('UNRECOGNIZED MULTIMEDIA SIZE: %s', $size);
      break;
    }

    return $tag;
  }

  /**
   *    Return a string holding an HTML reference (anchor) to this
   *    multimedia content in the specified size.
   *        $size is a char: 's' | 'm' | 'l'.
   *
   *    @return string
   */
  public function getHTMLReference($size)
  {
    switch ($size)
    {
      case 's':
        $uri = $this->getSmallUri();
      break;
      case 'm':
        $uri = $this->getMediumUri();
      break;
      case 'l':
        $uri = $this->getLargeUri();
      break;
      default:
        printf('UNRECOGNIZED MULTIMEDIA SIZE: %s', $size);
      break;
    }

    return sprintf('<a href="%s" title="%s">%s</a>', $uri, $this->getDescription(), $this->getTitle());
  }

  public function __toString()
  {
    return $this->getTitle();
  }

  public function getBanner()
  {
    sfLoader::loadHelpers(array('Asset', 'Tag'));

    if (false !== strstr($this->getMimeType(), 'application/x-shockwave-flash'))
    {
      $params = sprintf('%s %s',
        tag('param', array('value' => Multimedia::relativeURIFor($this->getLargeUri()), 'name' => 'movie')),
        tag('param', array('value' => 'opaque', 'name' => 'wmode'))
      );

      $tag = content_tag('object', $params . $this->getDescription(), array(
        'title'       => $this->getDescription(),
        'width'       => $this->getWidth(),
        'height'      => $this->getHeight(),
        'quality'     => 'high',
        'pluginspage' => 'http://www.adobe.com/go/getflashplayer',
        'type'        => 'application/x-shockwave-flash',
        'data'        => Multimedia::relativeURIFor($this->getLargeUri()))
      );

      $tag .= sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar     ciertos contenidos en su navegador."));
    }
    else
    {
      $im = new ImageResizer($this);
      $tag = $im->getImageTag('l');
    }

    return $tag;
  }

  public function canEdit()
  {
    return ($context= sfContext::getInstance()) &&
          ((
          $context->getUser()->isSuperAdmin()
          ||
          $context->getUser()->hasCredential(array('designer', 'reporter_admin','reporter'),false) &&
          ($this->getUploadedByAsGuardUser()) && ($this->getUploadedByAsGuardUser()->getUsername() == $context->getUser()->getUsername())
          ) ||
          $context->getUser()->hasCredential('designer_admin')
          );

  }

  public function getUploadedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getUploadedBy());
  }
  
  public function getUploadedByUser()
  {
    $author = $this->getUploadedByAsGuardUser();

    return ($author) ? $author->getName() : '';
  }

  public function getUpdatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByUpdatedBy();

    return ($author) ? $author->getName() : '';
  }


  public function canDelete()
  {
    if ( !$this->canEdit()) return false;
    $criteria = new Criteria();
    $criteria->add(ArticleMultimediaPeer::MULTIMEDIA_ID, $this->getId());
    $count_articles = ArticleMultimediaPeer::doCount($criteria);

    if ($count_articles)
      return false;

    $criteria2= new Criteria();
    $criteria2->add(MultimediaGalleryPeer::MULTIMEDIA_ID, $this->getId());
    $criteria2->addJoin(ArticleGalleryPeer::GALLERY_ID, MultimediaGalleryPeer::GALLERY_ID);
    $count_galleries = MultimediaGalleryPeer::doCount($criteria2);

    if ($count_galleries)
      return false;

    $criteria3 = new Criteria();
    $criteria3->add(ArticlePeer::MULTIMEDIA_ID, $this->getId());
    $count_article = ArticlePeer::doCount($criteria3);

    if ($count_article)
      return false;

    $criteria5 = new Criteria();
    $criteria5->add(ShortcutPeer::MULTIMEDIA_ID, $this->getId());
    $count_shortcut = ShortcutPeer::doCount($criteria5);
 
    if ($count_shortcut)
      return false;

    $criteria6 = new Criteria();
    $criteria6->add(SectionPeer::MULTIMEDIA_ID, $this->getId());
    $count_sections = SectionPeer::doCount($criteria6);

    if ($count_sections)
      return false;

    return true;
  }

  public function deleteRelatedFiles()
  {
    $large_path = sfConfig::get('cms_images_dir') . '/' . $this->getLargeUri();
    if (is_file($large_path))
    {
      @unlink($large_path);
    }

    $medium_path = sfConfig::get('cms_images_dir') . '/'. $this->getMediumUri();
    if (is_file($medium_path))
    {
      @unlink($medium_path);
    }

    $small_path = sfConfig::get('cms_images_dir') . '/' . $this->getSmallUri();
    if (is_file($small_path))
    {
      @unlink($small_path);
    }
    
    $longdesc_path = sfConfig::get('cms_longdesc_images_dir') . '/' . $this->getLongdescUri();
    if (is_file($longdesc_path))
    {
      @unlink($longdesc_path);
    }
  }

  public function delete ($con = null)
  {
    if ($this->canDelete())
    {
      try
      {
        $this->deleteRelatedFiles();
        return parent::delete($con);
      }
      catch (Exception $e)
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }

  public function getDescription()
  {
    $description = parent::getDescription();

    if ('' == trim($description) && CmsConfiguration::get('check_use_title_when_description_is_empty', true))
    {
      $description = $this->getTitle();
    }

    return $description;
  }

  public function getAllArticles()
  {
    $articles = $this->getArticles();

    if (!is_array($articles))
    {
      $articles = array();
    }

    $article_multimedias = $this->getArticleMultimediasJoinArticle();
    foreach ($article_multimedias as $am)
    {
      $articles[] = $am->getArticle();
    }

    return array_unique($articles);
  }

  /**
   * Answer whether this multimedia has flv params set.
   * 
   * @return Boolean True if this Multimedia has flv params set.
   */
  public function hasFlvParams()
  {
    return ('' != trim($this->getFlvParams()));
  }

  public function getFlashVideoPlayerInstance()
  {
    return FlashVideoPlayer::getInstance($this);
  }
  
  public function usesPlayer()
  {
    return (!is_null($this->getPlayerId()));
  }

  /**
   * Return an associative array with this multimedia's flv_params attribute.
   * 
   * @return Array
   */
  public function getFlvParamsAsArray()
  {
    if (is_array($this->getFlvParams()))
    {
      $array = $this->getFlvParams();
    }
    else
    {
      $array = array();
      $values = explode(self::FLV_PARAMS_DELIMITER, $this->getFlvParams());
      foreach ($values as $value)
      {
        $exploded_value = explode('=', $value, 2);
        if (count($exploded_value) == 0)
        {
          continue;
        }
        elseif (count($exploded_value) == 1)
        {
          $exploded_value[1] = '';
        }

        $array[$exploded_value[0]] = trim($exploded_value[1]);
      }
    }

    return $array;
  }
  
  public function getLongdescRelativeUri($longdesc_uri = null)
  {
    if($longdesc_uri === null)
    {
      return $this->getLongdescRelativeUri($this->getLongdescUri());
    }
    sfLoader::loadHelpers(array('Tag','Asset','I18N'));
    $str = str_replace(sfConfig::get('sf_web_dir'), '', sfConfig::get('cms_longdesc_images_dir').'/'.$longdesc_uri);
    return image_path($str);
  }
  
  public function getLongdescDownloadLink()
  {
    $html = "";
    $longdesc_uri = $this->getLongdescUri();
    if(!empty($longdesc_uri)){
      sfLoader::loadHelpers(array('I18N'));
      $html .= "<a href='".$this->getLongdescRelativeUri($longdesc_uri)."' alt='".__("Descripción completa")."'>".__("Ver descripción")."</a>";
    }
    else{
      $html .= " - ";
    }
    return $html;
  }
  
  public function hasLongdesc()
  {
    $longdesc_uri = $this->getLongdescUri();
    return !empty($longdesc_uri);
  }

  public function asRssFeedEnclosure()
  {
    $enclosure = new sfFeedEnclosure();

    $enclosure->setUrl(Multimedia::absoluteURIFor($this->getMediumUri()));
    $enclosure->setMimeType($this->getMimeType());

    return $enclosure;
  }
}