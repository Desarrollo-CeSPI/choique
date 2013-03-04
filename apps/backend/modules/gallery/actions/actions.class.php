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
 * gallery actions.
 *
 * @package    cmsunlp
 * @subpackage gallery
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class galleryActions extends autogalleryActions
{
  public function executeCreateRelated()
  {
    $this->getUser()->setAttribute('convenience_creation', true);
    $this->forward($this->getModuleName(), 'create');
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

  public function executeDeleteMultimediaGallery()
  {
    $multimedia_gallery = MultimediaGalleryPeer::retrieveByPK($this->getRequestParameter('multimedia_gallery_id'));
    $this->gallery = GalleryPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->gallery);
    if (!empty($multimedia_gallery))
    {
      $multimedia_gallery->delete();
    }
  }


  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->gallery = $this->getGalleryOrCreate();
    if ($this->gallery->canDelete())
    {
      try
      {
        $this->gallery->delete();
        $this->setFlash('notice', 'La galeria seleccionada fue borrada exitosamente');
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete','La galeria '.$this->gallery->__toString().' no se puede borrar debido a que esta referenciado en un artículo.');
        return $this->forward('gallery', 'list');
      }
    }
    else
    {
      $this->getRequest()->setError('delete','La galeria '.$this->gallery->__toString().' no se puede borrar debido a que esta referenciado en un artículo.');
    }

    return $this->forward('gallery','list');
  }

  public function executeCreate()
  {
    if ($this->getUser()->getAttribute('convenience_creation', false))
    {
      $this->setLayout('cleanLayout');
    }

    $ret = parent::executeCreate();
    if ($this->getRequest()->getMethod() == sfRequest::GET)
    {
      if ($this->getUser()->hasAttribute('multimedia'))
      {
        $this->getUser()->getAttributeHolder()->remove('multimedia');
      }
      $this->getUser()->setAttribute('multimedia', array());

      if ($this->getUser()->hasAttribute('deleted_multimedia'))
      {
        $this->getUser()->getAttributeHolder()->remove('deleted_multimedia');
      }
      $this->getUser()->setAttribute('deleted_multimedia', array());
    }

    return $ret;
  }

  public function executeEdit()
  {
    $ret = parent::executeEdit();

    if ($this->getRequest()->getMethod() == sfRequest::GET)
    {
      if ($this->getUser()->hasAttribute('multimedia'))
      {
        $this->getUser()->getAttributeHolder()->remove('multimedia');
      }
      $this->getUser()->setAttribute('multimedia', $this->gallery->getMultimediaGallerys());

      if ($this->getUser()->hasAttribute('deleted_multimedia'))
      {
        $this->getUser()->getAttributeHolder()->remove('deleted_multimedia');
      }
      $this->getUser()->setAttribute('deleted_multimedia', array());
    }

    return $ret;
  }

  public function saveGallery($gallery)
  {
    $gallery->save();

    foreach ($this->getUser()->getAttribute('multimedia') as $multimedia)
    {
      $multimedia->setGallery($this->gallery);
      $multimedia->save();
    }

    foreach ($this->getUser()->getAttribute('deleted_multimedia') as $multimedia_gallery_id)
    {
      $multimedia_gallery = MultimediaGalleryPeer::retrieveByPK($multimedia_gallery_id);
      $multimedia_gallery->delete();
    }
  }

  public function executeUnpublish()
  {
    $this->gallery = GalleryPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->gallery);
    $this->gallery->setUnpublished(true);
    $this->gallery->save();
    $this->setFlash('notice', 'Your modifications have been saved');
    $this->redirect('gallery/index');
  }

  public function executePublish()
  {
    $this->gallery = GalleryPeer::retrieveByPK($this->getRequestParameter('id'));
    $this->forward404Unless($this->gallery);
    $this->gallery->setIsPublished(true);
    $this->gallery->setPublishedBy($this->getUser()->getGuardUser()->getId());
    $this->gallery->setPublishedAt(time());
    $this->gallery->save();
    $this->setFlash('notice', 'Your modifications have been saved');
    
    $this->redirect('gallery/index');
  }

  public function executeAutocompleteMultimedia()
  {
    $query = '%'.$this->getRequestParameter('multimedia_id_search').'%';

    $c = new Criteria();
    $crit = $c->getNewCriterion(MultimediaPeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(MultimediaPeer::DESCRIPTION, $query, Criteria::LIKE));
    $c->add($crit);
    $c->setLimit(12);

    $this->multimedias = MultimediaPeer::doSelect($c);
  }

  public function executeAddTmpMultimedia()
  {
    $this->gallery = $this->getGalleryOrCreate('gallery_id');
    $multimedia = $this->getUser()->getAttribute('multimedia');
    $this->getUser()->getAttributeHolder()->remove('multimedia');

    $multimedia_gallery = new MultimediaGallery();
    //$multimedia_gallery->setGallery($this->gallery);
    $multimedia_gallery->setMultimediaId($this->getRequestParameter('multimedia_id'));

    $multimedia[] = $multimedia_gallery;

    $this->getUser()->setAttribute('multimedia', $multimedia);
  }

  public function executeDeleteMultimedia()
  {
    $mg = MultimediaGalleryPeer::retrieveByPk($this->getRequestParameter('multimedia_gallery_id'));
    $this->gallery = $mg->getGallery();

    $dmultimedia = $this->getUser()->getAttribute('deleted_multimedia');
    $this->getUser()->getAttributeHolder()->remove('deleted_multimedia');

    $array = array();
    foreach ($dmultimedia as $dm)
    {
      if ($dm->getId() != $mg->getId())
      {
        $array[] = $dm;
      }
    }

    $this->getUser()->setAttribute('deleted_multimedia', $array);
  }

  public function executeDeleteTmpMultimedia()
  {
    $this->gallery = $this->getGalleryOrCreate('gallery_id');
    $multimedia = $this->getUser()->getAttribute('multimedia', array());
    $this->getUser()->getAttributeHolder()->remove('multimedia');
    $deleted_multimedia = $this->getUser()->getAttribute('deleted_multimedia', array());
    $this->getUser()->getAttributeHolder()->remove('deleted_multimedia');

    $multimedia_id = $this->getRequestParameter('multimedia_id');

    $array = array();
    $array_d = $deleted_multimedia;
    foreach ($multimedia as $m)
    {
      if ($m->getMultimediaId() != $multimedia_id)
      {
        $array[] = $m;
      }
      else
      {
        if (!$m->isNew())
        {
          $array_d[] = $m->getId();
        }
      }
    }

    $this->getUser()->setAttribute('multimedia', $array);
    $this->getUser()->setAttribute('deleted_multimedia', array_unique($array_d));
  }
  
  public function executeEditPriorities() 
  {
  	$gallery = $this->getGalleryOrCreate();
  	$this->gallery = $gallery;
  	$this->multimedia_gallerys = $gallery->getMultimediaGallerysByPriority();
  }
  
  public function executeSortMultimediaGallery() 
  {
    $sorts = array_reverse($this->getRequestParameter('sortable-list', array()));
    for ($i = 0; $i < count($sorts); $i++)
    {
      $multimedia_gallery = MultimediaGalleryPeer::retrieveByPk($sorts[$i]);
      $multimedia_gallery->setPriority($i);
      $multimedia_gallery->save();
    }
    
    return sfView::NONE;
  }


  protected function updateGalleryFromRequest()
  {
    parent::updateGalleryFromRequest();   
    $gallery = $this->getRequestParameter('gallery');
    
    switch ($this->getActionName()) {
      case 'create':
        $this->gallery->setCreatedBy($this->getUser()->getGuardUser()->getId());
      break;
      case 'edit':
        $this->gallery->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }
    if(isset($galley['is_published']))
    {
        $this->gallery->setPublishedBy($this->getUser()->getGuardUser()->getId());
        $this->gallery->setPublishedAt(time());
    }

  }


}