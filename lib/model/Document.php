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
 * Subclass for representing a row from the 'document' table.
 *
 *
 *
 * @package lib.model
 */
class Document extends BaseDocument
{
  public function __toString()
  {
    return $this->getTitle();
  }

  public function setName($v)
  {
    sfLoader::loadHelpers(array('CmsEscaping'));

    $v = escape_string($v);

    parent::setName($v);
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



  /**
   *    Return a string holding an HTML representation of this
   *    document
   *
   *    @return string
   */
  public static function getNullHTMLRepresentation($description)
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($description) ? __('Referencia a documento inválida') : $description);
  }

  public function getHTMLRepresentation($innerHTML = null)
  {
    sfLoader::loadHelpers(array('I18N', 'Url', 'Tag'));

    $innerHTML = trim($innerHTML);

    if ('' == $innerHTML)
    {
      $innerHTML = __('descargar el documento') . ' "' . $this->getTitle() . '"';
    }

    return link_to($innerHTML, $this->getUrl(), array('title' => $this->getTitle()));
  }

  public function getSearchResultRepresentation()
  {
    $template = <<<HTML
<div class="search-result">
  <div class="result-title">"%title%": %link%</div>
  <div class="result-body">&nbsp;</div>
</div>
HTML;

    return strtr($template, array(
      '%title%' => $this->getTitle(),
      '%link%'  => $this->getHtmlRepresentation('Descargar el documento')
    ));
  }

  public function getUrl()
  {
    $root = sfContext::getInstance()->getRequest()->getRelativeUrlRoot();
    $path = sfConfig::get('cms_docs_path');

    return sprintf('%s/%s/%s', $root, $path, $this->getUri());
  }

  public function removeFile($name)
  {
    $dir = sfConfig::get('cms_docs_dir').'/'. $name;
    return @unlink($dir);
  }

  public function canDelete()
  {
    if (! $this->canEdit()) return false;
    return $this->countArticleDocuments() + $this->countSectionDocuments() == 0;
  }

  public function  delete($con = null)
  {
    if ($this->canDelete())
    {
      $this->removeFile($this->getUri());

      return parent::delete($con);
    }
    else
    {
      return false;
    }
  }

  private function getExtractorForExtension( $ext )
  {
    $extractors = sfConfig::get('app_lucene_extractors');
    return array_key_exists($ext,  $extractors)? $extractors[$ext]:null;
  }

  private function getFileInFilesystem()
  {
    return sfConfig::get('cms_docs_dir').'/'.$this->getUri();
  }

  /* Try to get content of binary files using txt extractors. Used by Lucene full text search engine */
  public function getContent()
  {
    $ret = '';
    $ext = strtolower ( pathinfo( $this->getUri(), PATHINFO_EXTENSION ) );
    $extractor = $this->getExtractorForExtension($ext );
    if ( !is_null( $extractor) )
    {
        $extractor_command = explode(' ',$extractor);
        if (file_exists($extractor_command[0]) )
        {
          $output = array();
          @exec(sprintf("$extractor 2> /dev/null ", $this->getFileInFilesystem()), $output, $status);
          if ($status == 0 && count($output) )
          {
            foreach($output as $line) $ret.=" $line";
          }
        }
    }
    return $ret;
  }

  /**
   * Alias for getTitle() used by sfLucene to distinguish between generic titles
   * and the titles of the documents.
   *
   * @return string
   */
  public function getDocument_title()
  {
    return $this->getTitle();
  }

}
sfLucenePropelBehavior::getInitializer()->setupModel('Document');