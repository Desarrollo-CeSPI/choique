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
 * multimedia actions.
 *
 * @package    cmsunlp
 * @subpackage multimedia
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class multimediaActions extends automultimediaActions
{
  public function executeCreateRelated()
  {
    $this->getUser()->setAttribute('convenience_creation', true);
    $this->forward($this->getModuleName(), 'create');
  }

  public function executeDownload()
  {
    $this->multimedia = $this->getMultimediaOrCreate();
    $file_name = $this->multimedia->getLargeUri();
    $path = sfConfig::get('cms_images_dir') . '/' . $file_name ;
    $response = $this->getContext()->getResponse();
    $data = fread(fopen($path, "r"), filesize($path));
    $response->setHttpHeader('Content-Disposition', 'attachment; filename='.$file_name);
    $response->setContent($data);
    $this->setLayout(false);
    return sfView::NONE;
  }
  
  public function executeCreate()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->setLayout('cleanLayout');
    }

    return parent::executeCreate();
  }

  public function executeCloseWindow()
  {
    $this->getUser()->setAttribute('convenience_creation', false);
    $this->setLayout('cleanLayout');
  }
  
  public function executeList()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->forward($this->getModuleName(), 'closeWindow');
    }

    return parent::executeList();
  }

  public function validateCreate()
  {
    return $this->multimediaValidation();
  }

  public function validateEdit()
  {
    return $this->multimediaValidation(true);
  }
  
  protected function multimediaValidation($edit = false) 
  {
	  sfLoader::loadHelpers(array('I18N', 'CmsEscaping'));
	
    $response = true;

    if ($this->getRequest()->getMethodName() == 'POST')
    {
      $multimedia = $this->getRequestParameter('multimedia');
      $title = $this->getRequestParameter("multimedia[title]");

      //Title validation
      if (empty($title))
      {
        $this->getRequest()->setError('multimedia{title}', __('El título no puede estar en blanco.'));
        $response = false;
      }

      $name = $this->getRequestParameter('multimedia[name]');
      $name = (empty($name) ? escape_string($title) : $name);
      if (empty($name))
      {
        $response = false;
        $this->getRequest()->setError('multimedia{name}', __('El nombre y el título no pueden estar en blanco.'));
      }
      $sfpuv = new sfPropelUniqueValidator();
      $sfpuv->initialize($this->getContext(), array('class' => 'Multimedia',
                                                    'column' => 'name',
                                                    'unique_error' => 'El nombre de archivo multimedia ya existe.'));
      if (!$sfpuv->execute($name, $error) || empty($name))
      {
        $response = false;
        $this->getRequest()->setError('multimedia{name}', __('El nombre de archivo multimedia ya existe'));
      }

      if (isset($multimedia['uses_a_player']) && $multimedia['uses_a_player'])
      {
        // Player validation
        if (!isset($multimedia['player_id']) || empty($multimedia['player_id']) || '' == trim($multimedia['player_id']))
        {
          $response = false;
          $this->getRequest()->setError('multimedia{player_id}', __('Este campo es requerido para los elementos multimediales que utilizan reproductor'));
        }
      }

      if (isset($multimedia['is_external']) && $multimedia['is_external'])
      {
        // External asset validation (ignore files)
        if (!isset($multimedia['external_uri']) || empty($multimedia['external_uri']) || '' == trim ($multimedia['external_uri']))
        {
          $response = false;
          $this->getRequest()->setError('multimedia{external_uri}', __('Este campo es requerido para los elementos multimediales externos'));
        }
      }
      else
      {
        // Regular local file validation
        $files = $this->getRequest()->getFiles();
        $names = $files['multimedia']['name'];

        if ($edit)
        {
          //Validate large_uri if not empty
          if (!(empty($names['large_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[large_uri]')))
            {
              $this->getRequest()->setError('multimedia{large_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          //Validate medium_uri if not empty
          if (!(empty($names['medium_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[medium_uri]')))
            {
              $this->getRequest()->setError('multimedia{medium_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          //Validate small_uri if not empty
          if (!(empty($names['small_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[small_uri]')))
            {
              $this->getRequest()->setError('multimedia{small_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          
          //Validate longdesc_uri if not empty
          if (!(empty($names['longdesc_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[longdesc_uri]'), false))
            {
              $this->getRequest()->setError('multimedia{longdesc_uri}', __('El tipo de archivo ingresado no es válido, sólo se aceptan archivos de texto (.txt)'));
              $response = false;
            }
          }
        }
        else
        {
          //Creation
          //Validate large_uri if not empty
          if (!(empty($names['large_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[large_uri]')))
            {
              $this->getRequest()->setError('multimedia{large_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          else
          {
            //File existence validation
            $this->getRequest()->setError('multimedia{large_uri}', __('Debe ingresar un archivo en tamaño completo'));
            $response = false;
          }
          //Validate medium_uri if not empty
          if (!(empty($names['medium_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[medium_uri]')))
            {
              $this->getRequest()->setError('multimedia{medium_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          //Validate small_uri if not empty
          if (!(empty($names['small_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[small_uri]')))
            {
              $this->getRequest()->setError('multimedia{small_uri}', __('El tipo de archivo ingresado no es válido'));
              $response = false;
            }
          }
          
          //Validate longdesc_uri if not empty
          if (!(empty($names['longdesc_uri'])))
          {
            if (!$this->validateMime($this->getRequest()->getFile('multimedia[longdesc_uri]'), false))
            {
              $this->getRequest()->setError('multimedia{longdesc_uri}', __('El tipo de archivo ingresado no es válido, sólo se aceptan archivos de texto (.txt)'));
              $response = false;
            }
          }
        }
      }
    }

    return $response;
  }

  protected function validateMime($file, $is_multimedia=true)
  {
    $guesser = new MimeTypeGuesser();
    return $is_multimedia?$guesser->validateMultimedia($file['tmp_name'], $file['name']):$guesser->validateTextFile($file['tmp_name'],$file['name']); 
  }
  
  protected function updateMultimediaFromRequest()
  {
    $multimedia = $this->getRequestParameter('multimedia');
    
    switch ($this->getActionName())
    {
      case 'create':
        $this->multimedia->setUploadedBy($this->getUser()->getGuardUser()->getId());        
        if (isset($multimedia['title']))
        {
          $this->multimedia->setTitle($multimedia['title']);
        }
        if (isset($multimedia['name']))
        {
          $this->multimedia->setName($multimedia['name']);
        }
        if (isset($multimedia['description']))
        {
          $this->multimedia->setDescription($multimedia['description']);
        }
        if (isset($multimedia['comment']))
        {
          $this->multimedia->setComment($multimedia['comment']);
        }
        if (isset($multimedia['copyright']))
        {
          $this->multimedia->setCopyright($multimedia['copyright']);
        }
        if (isset($multimedia['location']))
        {
          $this->multimedia->setLocation($multimedia['location']);
        }
        if (isset($multimedia['subject']))
        {
          $this->multimedia->setSubject($multimedia['subject']);
        }
        if (isset($multimedia['topics']))
        {
          $this->multimedia->setTopics($multimedia['topics']);
        }
        if (isset($multimedia['language']))
        {
          $this->multimedia->setLanguage($multimedia['language']);
        }
        if (isset($multimedia['duration']))
        {
          $this->multimedia->setDuration($multimedia['duration']);
        }
        if (isset($multimedia['external_uri']))
        {
          $this->multimedia->setExternalUri($multimedia['external_uri']);
        }
        if (isset($multimedia['uses_a_player']) && $multimedia['uses_a_player'])
        {
          $this->multimedia->setPlayerId($multimedia['player_id']);
          $this->multimedia->setFlvParams($multimedia['flv_params']);
        }
        else
        {
          $this->multimedia->setPlayerId(null);
          $this->multimedia->setFlvParams(null);
        }

        if (isset($multimedia['is_external']))
        {
          $this->multimedia->setIsExternal(true);

          $this->multimedia->setType('external');

          $this->multimedia->setPlayerId(FlashVideoPlayerOptions::PLAYER_EXTERNAL);
        }

        if (isset($multimedia['multimedia_tag']))
        {
          $this->multimedia->setMultimediaTag($multimedia['multimedia_tag']);
        }
        
	      $currentFile = sfConfig::get('cms_images_dir').'/'.$this->multimedia->getLargeUri();
	      if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[large_uri]'))
        {
          $large_uri = Multimedia::largeUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[large_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[large_uri]', sfConfig::get('cms_images_dir').'/'.$large_uri);
          $this->multimedia->setLargeUri($large_uri);
        }

        $currentFile = sfConfig::get('cms_images_dir')."/".$this->multimedia->getMediumUri();
	      if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[medium_uri]'))
        {
          $medium_uri = Multimedia::mediumUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[medium_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[medium_uri]', sfConfig::get('cms_images_dir').'/'.$medium_uri);
          $this->multimedia->setMediumUri($medium_uri);
        }

        $currentFile = sfConfig::get('cms_images_dir')."/".$this->multimedia->getSmallUri();
	      if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[small_uri]'))
        {
          $small_uri = Multimedia::smallUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[small_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[small_uri]', sfConfig::get('cms_images_dir').'/'.$small_uri);
          $this->multimedia->setSmallUri($small_uri);
        }
        
        $currentFile = sfConfig::get('cms_longdesc_images_dir')."/".$this->multimedia->getLongdescUri();
        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[longdesc_uri]'))
        {
          $longdesc_uri = Multimedia::longdescUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[longdesc_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[longdesc_uri]', sfConfig::get('cms_longdesc_images_dir').'/'.$longdesc_uri);
          $this->multimedia->setLongdescUri($longdesc_uri);
        }
	      break;

      case 'edit':   ///EDIT///
        $this->multimedia->setUpdatedBy($this->getUser()->getGuardUser()->getId());
        if (isset($multimedia['title']))
        {
          $this->multimedia->setTitle($multimedia['title']);
        }
        if (isset($multimedia['description']))
        {
          $this->multimedia->setDescription($multimedia['description']);
        }
        if (isset($multimedia['comment']))
        {
          $this->multimedia->setComment($multimedia['comment']);
        }
        if (isset($multimedia['copyright']))
        {
          $this->multimedia->setCopyright($multimedia['copyright']);
        }
        if (isset($multimedia['location']))
        {
          $this->multimedia->setLocation($multimedia['location']);
        }
        if (isset($multimedia['subject']))
        {
          $this->multimedia->setSubject($multimedia['subject']);
        }
        if (isset($multimedia['topics']))
        {
          $this->multimedia->setTopics($multimedia['topics']);
        }
        if (isset($multimedia['language']))
        {
          $this->multimedia->setLanguage($multimedia['language']);
        }
        if (isset($multimedia['duration']))
        {
          $this->multimedia->setDuration($multimedia['duration']);
        }
        if (isset($multimedia['flv_params']))
        {
          $this->multimedia->setFlvParams($multimedia['flv_params']);
        }
        if (isset($multimedia['external_uri']))
        {
          $this->multimedia->setExternalUri($multimedia['external_uri']);
        }
        if (isset($multimedia['uses_a_player']) && $multimedia['uses_a_player'])
        {
          $this->multimedia->setPlayerId($multimedia['player_id']);
          $this->multimedia->setFlvParams($multimedia['flv_params']);
        }
        else
        {
          $this->multimedia->setPlayerId(null);
          $this->multimedia->setFlvParams(null);
        }

        if (isset($multimedia['is_external']))
        {
          $this->multimedia->setIsExternal(true);

          $this->multimedia->setType('external');

          $this->multimedia->setPlayerId(FlashVideoPlayerOptions::PLAYER_EXTERNAL);
        }

        if (isset($multimedia['multimedia_tag']))
        {
          $this->multimedia->setMultimediaTag($multimedia['multimedia_tag']);
        }
        
        $currentFile = sfConfig::get('cms_images_dir')."/".$this->multimedia->getLargeUri();
        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[large_uri]'))
        {
          $large_uri = Multimedia::largeUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[large_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[large_uri]', sfConfig::get('cms_images_dir').'/'.$large_uri);
          $this->multimedia->setLargeUri($large_uri);
        }
        
	      $currentFile = sfConfig::get('cms_images_dir')."/".$this->multimedia->getMediumUri();
	      if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[medium_uri]'))
        {
          $medium_uri = Multimedia::mediumUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[medium_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[medium_uri]', sfConfig::get('cms_images_dir').'/'.$medium_uri);
          $this->multimedia->setMediumUri($medium_uri);
	      }
	      $currentFile = sfConfig::get('cms_images_dir')."/".$this->multimedia->getSmallUri();
	      if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[small_uri]'))
        {
          $small_uri = Multimedia::smallUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[small_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[small_uri]', sfConfig::get('cms_images_dir').'/'.$small_uri);
          $this->multimedia->setSmallUri($small_uri);
	      }
	      
        $currentFile = sfConfig::get('cms_longdesc_images_dir')."/".$this->multimedia->getLongdescUri();
        if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('multimedia[longdesc_uri]'))
        {
          $longdesc_uri = Multimedia::longdescUriFor($this->multimedia->getName().strstr($this->getRequest()->getFileName('multimedia[longdesc_uri]'), '.'));
          if (is_file($currentFile))
          {
            unlink($currentFile);
          }
          $this->getRequest()->moveFile('multimedia[longdesc_uri]', sfConfig::get('cms_longdesc_images_dir').'/'.$longdesc_uri);
          $this->multimedia->setLongdescUri($longdesc_uri);
        }
	      break;
    }
  }

  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->multimedia = $this->getMultimediaOrCreate();

    if ($this->multimedia->canDelete())
    {
      try
      {
        $this->multimedia->delete();
        $this->setFlash('notice', 'The selected element has been successfully deleted'); 
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete', 'Could not delete the selected Multimedia. Make sure it does not have any associated items.');
        return $this->forward('multimedia', 'list');
      }
   }
   else
   {
     $this->getRequest()->setError('delete', 'El archivo multimedia '.$this->multimedia->__toString().' no se puede borrar debido a que esta referenciado en un artículo y/o galería.');
   }
   return $this->forward('multimedia','list');
  }
}