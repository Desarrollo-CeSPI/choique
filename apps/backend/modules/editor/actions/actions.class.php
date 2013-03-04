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
 * editor actions.
 *
 * @package    choique
 * @subpackage editor
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 614 2012-08-09 18:43:54Z jpablop $
 */
class editorActions extends sfActions
{
  private function getRoot()
  {
    try
    {
      return PathHelper::getRoot();
    }
    catch (DomainException $e)
    {
      $this->setFlash('error', $e->getMessage(), false);
    }
  }

  private function getPath($relpath, $check_write = false)
  {
    try
    {
      return PathHelper::getPath($relpath, $check_write);
    }
    catch (InvalidArgumentException $e)
    {
      $this->setFlash('error', $e->getMessage());
      $this->redirect('editor/index');
    }
    catch (DomainException $e)
    {
      $this->setFlash('error', $e->getMessage());
      $this->redirect('editor/index');
    }
  }

  /**
   * Executes index action
   *
   */
  public function executeIndex()
  {
    if ($this->getRequest()->getMethod() == sfWebRequest::POST)
    {
      $this->base_path = choiqueFlavors::getInstance()->current().'/'.ltrim($this->getRequestParameter('r'), '/');
      $this->base      = $this->getPath($this->getRequestParameter('r'));
      $this->b_upload  = ltrim($this->getRequestParameter('r'), '/');
    }
    else
    {
      $this->base_path = choiqueFlavors::getInstance()->current();
      $this->base      = $this->getRoot();
      $this->b_upload  = '';
      if (!is_writable($this->base))
      {
        $this->setFlash('error', 'El directorio de estilos visuales no puede ser modificado.', false);
        return sfView::ERROR;
      }
    }
  }

  public function executeEdit()
  {
    if ($this->getRequest()->getMethod() != sfWebRequest::POST)
    {
      $this->setFlash('error', 'Seleccione un archivo para editarlo.');
      $this->redirect('editor/index');
    }
    $this->file = $this->getRequest()->getParameter('f');
    sfConfig::set('sf_web_debug', false);
    $ph = $this->getResponse()->getParameterHolder();
    $ph->removeNamespace('helper/asset/auto/javascript');
    $ph->removeNamespace('helper/asset/auto/stylesheet');

  }

  public function executeNavigate()
  {
    if ($this->getRequest()->getMethod() != sfWebRequest::POST)
    {
      $this->setFlash('error', 'Seleccione un directorio para inspeccionar.');
      $this->redirect('editor/index');
    }

    $this->base_path = choiqueFlavors::getInstance()->current().'/'.ltrim($this->getRequestParameter('r'), '/');
    $this->base      = $this->getPath($this->getRequestParameter('r'));
    $this->b_upload  = ltrim($this->getRequestParameter('r'), '/');
  }

  public function executeSave()
  {
    if ($this->getRequest()->getMethod() != sfWebRequest::POST)
    {
      $this->setFlash('error', 'Seleccione un archivo para editarlo.');
      $this->redirect('editor/index');
    }

    $this->file = $this->getRequestParameter('f');
    $path       = $this->getPath($this->file, true);

    if ($this->getRequest()->hasParameter('c'))
    {
      file_put_contents($path, $this->getRequestParameter('c'));

      $this->setFlash('notice', sprintf('Los cambios al archivo "%s" fueron guardados correctamente.', $this->file), false);
    }
    else
    {
      $this->setFlash('error', 'No se recibió el contenido del archivo a editar.', false);
    }
  }

  public function executeDelete()
  {
    if ($this->getRequest()->getMethod() != sfWebRequest::POST)
    {
      $this->setFlash('error', 'Seleccione un archivo para editarlo.');
      $this->redirect('editor/index');
    }

    $this->file = $this->getRequestParameter('f');
    $path       = $this->getPath($this->file, true);

    if ( @unlink($path) )
    {

      $this->setFlash('notice', sprintf('El archivo "%s" fue eliminado correctamente.', $this->file), false);
    }
    else
    {
      $this->setFlash('error', 'No se pudo eliminar el archivo.', false);
    }
  }

  public function executePublish()
  {

    try
    {
      $flavor = choiqueFlavors::getInstance()->current();
      choiqueFlavors::publishResources($flavor);
      choiqueFlavors::getInstance()->clearCache('all');
      $this->setFlash('notice', 'Se han publicado correctamente los cambios realizados.');
    }
    catch(Exception $e)
    {
      $this->setFlash('error',"No se ha podido publicar! El error ocurrido es: ".$e->getMessage());
    }
    $this->redirect('editor/index');

  }

  public function executeUpload()
  {
    $r       = ltrim($this->getRequestParameter('r'), '/');
    $current = choiqueFlavors::getInstance()->current();

    if (substr($r, 0, strlen($current)) == $current)
    {
      $r = ltrim(substr($r, strlen($current)), '/');
    }

    $this->base_path = choiqueFlavors::getInstance()->current().'/'.$r;

    $path = $this->getPath($r);

    $files = $this->getRequest()->getFiles();

    try
    {
      $this->getRequest()->moveFile('f', $path.'/'.$files['f']['name']);
      $this->setFlash('notice', 'Se ha subido exitosamente el archivo:'. $files['f']['name']);
    }catch(Exception $e)
    {
      $this->setFlash('error', 'No se ha podido subir el archivo:'. $files['f']['name']. ' '.$e->getMessage());
    }
    $this->forward('editor','index');
  }

  public function validateUpload()
  {
    $r       = ltrim($this->getRequestParameter('r'), '/');
    $current = choiqueFlavors::getInstance()->current();

    if (substr($r, 0, strlen($current)) == $current)
    {
      $r = ltrim(substr($r, strlen($current)), '/');
    }

    $this->base_path = choiqueFlavors::getInstance()->current().'/'.$r;

    $path = $this->getPath($r);
    $files = $this->getRequest()->getFiles();
    if ( !is_writable($path.'/'))
    {
      $this->setFlash('error',"No es posible escribir en el  directorio $path");
      return false;
    }
    if ( ! preg_match('/^web\/(images|css)/', $r ) )
    {
      $this->setFlash('error',"Solo se permiten subir archivos en web/images o web/css");
      return false;
    }
    $guesser = new MimeTypeGuesser();
    if (preg_match('/^web\/images/', $r) && !$guesser->validateEditorImages($files['f']['tmp_name'], $files['f']['name']) )
    {
      $this->setFlash('error',"El tipo de archivo que intenta subir no está permitido en el directorio images");
      return false;
    }
    if (preg_match('/^web\/css/', $r) && !$guesser->validateEditorCss($files['f']['tmp_name'], $files['f']['name']) )
    {
      $this->setFlash('error',"El tipo de archivo que intenta subir no está permitido en el directorio css");
      return false;
    }
    return true;
  }

  public function handleErrorUpload()
  {
    $this->forward('editor','index');
  }

}