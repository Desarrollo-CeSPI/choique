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
 * link actions.
 *
 * @package    cms
 * @subpackage link
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class linkActions extends autolinkActions
{
  protected function updateLinkFromRequest()
  {
    $link = $this->getRequestParameter('link');


    switch ($this->getActionName())
    {
      case 'create':
        $this->link->setCreatedBy($this->getUser()->getGuardUser()->getId());
        if (isset($link['name']))
        {
          $this->link->setName($link['name']);
        }
        $currentFile = sfConfig::get('cms_links_dir')."//".$this->link->getUri();
        if (!$this->getRequest()->hasErrors() && isset($link['uri_remove']))
        {
          $this->link->setUri('');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
        }

        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('link[uri]'))
        {
          $fileName = $this->getRequest()->getFileName('link[uri]');
          $ext = $this->getRequest()->getFileExtension('link[uri]');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('link[uri]', sfConfig::get('cms_links_dir')."//".$link['name'].$ext);
          $this->link->setUri(sfConfig::get('cms_links_dir')."/".$link['name'].$ext);
        }
        if (isset($link['url']))
        {
          $this->link->setUrl($link['url']);
        }
      break;
      case 'edit':
        $this->link->setUpdatedBy($this->getUser()->getGuardUser()->getId());
        if (isset($link['name']))
        {
          $this->link->setName($link['name']);
        }
        $currentFile = sfConfig::get('cms_links_dir')."//".$this->link->getUri();
        if (!$this->getRequest()->hasErrors() && isset($link['uri_remove']))
        {
          $this->link->setUri('');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
        }

        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('link[uri]'))
        {
          $fileName = $this->getRequest()->getFileName('link[uri]');
          $ext = $this->getRequest()->getFileExtension('link[uri]');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('link[uri]', sfConfig::get('cms_links_dir')."//".$link['name'].$ext);
          $this->link->setUri(sfConfig::get('cms_links_dir')."/".$link['name'].$ext);
        }
        if (isset($link['url']))
        {
          $this->link->setUrl($link['url']);
        }
      break;
    }
  }

  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->link = $this->getLinkOrCreate();
    if ($this->link->canDelete())
    {
      try
      {
        $this->link->delete();
        $this->setFlash('notice', 'El vinculo seleccionado fue borrado exitosamente');
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete','El vinculo '.$this->link->getName().' no se puede borrar debido a que esta referenciado en un artículo.');
        return $this->forward('link', 'list');
      }
    }
    else
    {
      $this->getRequest()->setError('delete','El vinculo '.$this->link->getName().' no se puede borrar debido a que esta referenciado en un artículo.');
    }

    return $this->forward('link','list');
 
  }

  public function validateCreate()
  {
    return $this->linkValidation();
  }

  public function validateEdit()
  {
    return $this->linkValidation();
  }
  
  protected function linkValidation($edit = false) 
  {
	  sfLoader::loadHelpers(array('I18N', 'CmsEscaping'));
	
    $response = true;

    if ($this->getRequest()->getMethodName() == 'POST')
    {
      $name = $this->getRequestParameter("link[name]");

      //Title validation
      if (empty($name) || $name == '')
      {
        $this->getRequest()->setError('link{name}', __('Es necesario ingresar un nombre para el vínculo'));
        $response = false;
      }
      else
      {
        $sfpuv = new sfPropelUniqueValidator();
        $sfpuv->initialize($this->getContext(), array('class' => 'Link',
                                                    'column' => 'name',
                                                    'unique_error' => 'Ya existe un vínculo con ese nombre'));
        if (!$sfpuv->execute($name, $error) || empty($name))
        {
          $response = false;
          $this->getRequest()->setError('link{name}', __('Ya existe un vínculo con ese nombre'));
        }  
      }

      $url = $this->getRequestParameter("link[url]");    

      $sfurlv = new sfUrlValidator();
      $sfurlv->initialize($this->getContext(), array('url_error'=>'La url es inválida'));
      if (!$sfurlv->execute($url, $error) || empty($url))
      {
        $response = false;
        $this->getRequest()->setError('link{url}', __('La url es inválida'));
      }

      $files = $this->getRequest()->getFiles();
      $names = $files['link']['name'];

      if (!(empty($names['uri'])))
      {
        if (!$this->validateMime($this->getRequest()->getFile('link[uri]')))
        {
          $this->getRequest()->setError('link{uri}', __('El tipo de archivo ingresado no es válido'));
          $response = false;
         }
      }
    }
    return $response;
  }

  protected function validateMime($file)
  {
    $guesser = new MimeTypeGuesser();
    return $guesser->validateLink($file['tmp_name'], $file['name']);
  }
}