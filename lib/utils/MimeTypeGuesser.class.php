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

class MimeTypeGuesser
{
  public function guess($filename)
  {
    $type = null;

    if (extension_loaded('fileinfo'))
    {
      $guesser='fileinfo';
      $type = $this->guessUsingFileinfo($filename);
    }
    else
    {
      $guesser='mime_content_type';
      $type = $this->guessUsingMimeContentType($filename);
    }
    if (sfConfig::get('sf_logging_enabled'))
    {
      $context = sfContext::getInstance();
      if ($context) $context->getLogger()->debug("MimeTypeGuesser [$guesser] detection gives: $type");
    }
    return $type;
  }

  private function guessUsingFileinfo($filename)
  {
    if (!is_readable($filename))
    {
      return null;
    }

    if (!$finfo = finfo_open(FILEINFO_MIME))
    {
      return null;
    }

    $type = finfo_file($finfo, $filename);

    if (false !== $pos = strpos($type, ';'))
    {
      $type = substr($type, 0, $pos);
    }

    return $type;
  }

  private function guessUsingMimeContentType($filename)
  {
    if (!is_readable($filename))
    {
      return null;
    }

    return mime_content_type($filename);
  }

  private function getValidMultimediaMimeTypes()
  {
    $ret = array();
    foreach (sfConfig::get('app_valid_mime_types_multimedia', array()) as $media)
    {
      foreach ($media as $ext=>$mimes)
      {
        $m_arr = is_array($mimes)? $mimes: array($mimes);
        $ret = array_merge($ret, $m_arr);
      }
    }
    return array_unique($ret);
  }

  private function getValidTextMimeTypes()
  {
    $ret = array();
    foreach (sfConfig::get('app_valid_mime_types_text', array()) as $key=>$mimes)
    {
      $m_arr = is_array($mimes)? $mimes: array($mimes);
      $ret = array_merge($ret, $m_arr);
    }
  
    return array_unique($ret);
  }

  private function getValidDocumentMimeTypes()
  {
    $ret = array();
    foreach (sfConfig::get('app_valid_mime_types_document', array()) as $key=>$mimes)
    {
      $m_arr = is_array($mimes)? $mimes: array($mimes);
      $ret = array_merge($ret, $m_arr);
    }
  
    return array_unique($ret);
  }

  private function getValidLinkMimeTypes()
  {
    $ret = array();
    foreach (sfConfig::get('app_valid_mime_types_link', array()) as $key=>$mimes)
    {
      $m_arr = is_array($mimes)? $mimes: array($mimes);
      $ret = array_merge($ret, $m_arr);
    }
  
    return array_unique($ret);
  }

  private function getValidEditorImagesMimeTypes()
  {
    $ret = array();
    $editor_arr = sfConfig::get('app_valid_mime_types_editor', array());
    foreach ($editor_arr['images'] as $key=>$mimes)
    {
      $m_arr = is_array($mimes)? $mimes: array($mimes);
      $ret = array_merge($ret, $m_arr);
    }
  
    return array_unique($ret);
  }

  private function getValidEditorCssMimeTypes()
  {
    $ret = array();
    $editor_arr = sfConfig::get('app_valid_mime_types_editor', array());
    foreach ($editor_arr['css'] as $key=>$mimes)
    {
      $m_arr = is_array($mimes)? $mimes: array($mimes);
      $ret = array_merge($ret, $m_arr);
    }
  
    return array_unique($ret);
  }

  private function getExtension($filename)
  {
    $parts = pathinfo($filename);
    return strtolower($parts['extension']);
  }

  public function validateMultimedia($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidMultimediaMimeTypes()) && in_array($this->getExtension($filename), $this->getValidMultimediaExtensions());
  }

  public function validateDocument($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidDocumentMimeTypes()) && in_array($this->getExtension($filename), $this->getValidDocumentExtensions());
  }

  public function validateEditorImages($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidEditorImagesMimeTypes()) && in_array($this->getExtension($filename), $this->getValidEditorImagesExtensions());
  }

  public function validateEditorCss($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidEditorCssMimeTypes()) && in_array($this->getExtension($filename), $this->getValidEditorCssExtensions());
  }

  public function validateLink($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidLinkMimeTypes()) && in_array($this->getExtension($filename),$this->getValidLinkExtensions());
  }

  public function validateTextFile($file, $filename)
  {
    return in_array($this->guess($file), $this->getValidTextMimeTypes()) && in_array($this->getExtension($filename), $this->getValidTextExtensions());
  }

  public function getValidMultimediaExtensions($media=null)
  {
    $valid_mimes = sfConfig::get('app_valid_mime_types_multimedia', array());
    if (is_null($media))
    {
      $ret=array();
      foreach (array_keys($valid_mimes) as $m)
      {
        $ret = array_merge($ret, $this->getValidMultimediaExtensions($m));
      }
      return array_unique($ret);
    }

    if ( array_key_exists($media, $valid_mimes))
    {
      return array_map('strtolower',array_unique(array_keys($valid_mimes[$media])));
    }
    return array();
  }

  public function getValidTextExtensions()
  {
    $valid_mimes = sfConfig::get('app_valid_mime_types_text', array());
    return array_map('strtolower',array_unique(array_keys($valid_mimes)));
  }

  public function getValidDocumentExtensions()
  {
    $valid_mimes = sfConfig::get('app_valid_mime_types_document', array());
    return array_map('strtolower', array_unique(array_keys($valid_mimes)));
  }

  public function getValidLinkExtensions()
  {
    $valid_mimes = sfConfig::get('app_valid_mime_types_link', array());
    return array_map('strtolower', array_unique(array_keys($valid_mimes)));
  }

  public function getValidEditorImagesExtensions()
  {
    $editor_arr = sfConfig::get('app_valid_mime_types_editor', array());
    $valid_mimes = $editor_arr['images'];
    return array_map('strtolower', array_unique(array_keys($valid_mimes)));
  }

  public function getValidEditorCssExtensions()
  {
    $editor_arr = sfConfig::get('app_valid_mime_types_editor', array());
    $valid_mimes = $editor_arr['css'];
    return array_map('strtolower', array_unique(array_keys($valid_mimes)));
  }
}