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
 * document actions.
 *
 * @package    cmsunlp
 * @subpackage document
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class documentActions extends autodocumentActions
{
  public function executeCreateRelated()
  {
    $this->getUser()->setAttribute('convenience_creation', true);
    $this->forward($this->getModuleName(), 'create');
  }

  public function executeCreate()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->setLayout('cleanLayout');
    }

    return parent::executeCreate();
  }

  public function executeList()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->forward($this->getModuleName(), 'closeWindow');
    }
    
    return parent::executeList();
  }

  public function executeCloseWindow()
  {
    $this->getUser()->setAttribute('convenience_creation', false);
    $this->setLayout('cleanLayout');
  }

  public function validateCreate()
  {
    return $this->documentValidation();
  }

  public function validateEdit()
  {
    return $this->documentValidation(true);
  }

  protected function documentValidation($edit = false) 
  {
    sfLoader::loadHelpers(array('I18N', 'CmsEscaping'));
  
    $response = true;

    if ($this->getRequest()->getMethodName() == 'POST')
    {
      $document = $this->getRequestParameter('document');
      $name   = $document['name'];
      $title  = $document['title'];
      $files  = $this->getRequest()->getFiles();
      $uri    = $files['document']['name']['uri'];

      //Title validation
      if (empty($title))
      {
        $this->getRequest()->setError('document{title}', __('El título no puede estar en blanco.'));
        $response = false;
      }

      //Title validation
      if (empty($name))
      {
        $this->getRequest()->setError('document{name}', __('El nombre no puede estar en blanco.'));
        $response = false;
      }

      $sfpuv = new sfPropelUniqueValidator();
      $sfpuv->initialize($this->getContext(), array('class' => 'Document',
                                                    'column' => 'title',
                                                    'unique_error' => 'El tituo del documento ya existe.'));
      if (!$sfpuv->execute($title, $error) || empty($title))
      {
        $response = false;
        $this->getRequest()->setError('document{title}', __('El tituo del documento ya existe.'));
      }

      $sfpuv = new sfPropelUniqueValidator();
      $sfpuv->initialize($this->getContext(), array('class' => 'Document',
                                                    'column' => 'name',
                                                    'unique_error' => 'El nombre del documento ya existe.'));
      if (!$sfpuv->execute($name, $error) || empty($name))
      {
        $response = false;
        $this->getRequest()->setError('document{name}', __('El nombre del documento ya existe.'));
      }
      if (!(empty($uri)))
      {
        if (!$this->validateMime($this->getRequest()->getFile('document[uri]')))
        {
              $this->getRequest()->setError('document{uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
        }
      }
      elseif(!$edit)
      {
          $this->getRequest()->setError('document{uri}', __('Debe ingresar un archivo'));
          $response = false;
      }
    }
    return $response;
  }


  protected function validateMime($file)
  {
    $guesser = new MimeTypeGuesser();
    return $guesser->validateDocument($file['tmp_name'], $file['name']);
  }



  protected function updateDocumentFromRequest()
  {
    $document = $this->getRequestParameter('document');
    

    switch ($this->getActionName())
    {
      case 'create':
        $this->document->setUploadedBy($this->getUser()->getGuardUser()->getId());
        if (isset($document['name']))
        {
          $this->document->setName($document['name']);
        }
        if (isset($document['title']))
        {
          $this->document->setTitle($document['title']);
        }
        $currentFile = sfConfig::get('cms_docs_dir').'/'.$this->document->getUri();
        if (!$this->getRequest()->hasErrors() && isset($document['uri_remove']))
        {
          $this->document->setUri('');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
        }
       if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('document[uri]'))
       {
          $ext = strstr($this->getRequest()->getFileName('document[uri]'), '.');
          $filename = $this->document->getName().$ext;
          $this->getRequest()->moveFile('document[uri]', sfConfig::get('cms_docs_dir').'/'.$filename);
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->document->setUri($filename);
        }
      break;
      case 'edit':
        $this->document->setUpdatedBy($this->getUser()->getGuardUser()->getId());
        if (isset($document['name']))
        {
          $this->document->setName($document['name']);
        }
        if (isset($document['title']))
        {
          $this->document->setTitle($document['title']);
        }
        $currentFile = sfConfig::get('cms_docs_dir').'/'.$this->document->getUri();
        if (!$this->getRequest()->hasErrors() && isset($document['uri_remove']))
        {
          $this->document->seturi('');
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
        }
        $old_file = $this->document->getUri();
        $ext = strstr($this->getRequest()->getFileName('document[uri]'), '.');
        $filename = $this->document->getName().$ext;
        if ($old_file != $filename && $this->getRequest()->getFileSize('document[uri]'))
        {
          $this->document->removeFile($old_file);
        }

        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('document[uri]'))
        {
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->document->setUri($filename);
          $this->getRequest()->moveFile('document[uri]', sfConfig::get('cms_docs_dir').'/'.$filename);
        }
      break;
    }
  }

  public function executeDelete()
  {
    $this->document = $this->getDocumentOrCreate();
    if ($this->document->canDelete())
    {
      try
      {
        $this->document->delete();
        
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete', 'El documento ' . $this->document->getName() .' no se puede borrar, debido a que esta referenciada en un articulo ');
      }
      $this->setFlash('notice', 'The selected element has been successfully deleted'); 
    }
    else
    {
      $this->getRequest()->setError('delete', 'El documento ' . $this->document->getName() .' no se puede borrar, debido a que esta referenciada en un articulo ');
    }
    return $this->forward('document','list');
  }
}