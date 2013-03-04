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
 * new_slotlet actions.
 *
 * @package    cms
 * @subpackage new_slotlet
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class slotletActions extends sfActions
{
  public function preExecute()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      $this->setFlash('notice', 'ChoiqueCMS está configurado para utilizar distribuciones por sección. En este módulo podrá editarlas.');

      return $this->redirect('layout/index');
    }

    parent::preExecute();
    $this->setFlash('notice', 'ChoiqueCMS le recomienda que cambie al modo de distribuciones por seccion.');
  }

  public function executeIndex()
  {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(SlotletPeer::NAME);
    $this->slotlets = SlotletPeer::doSelect($c);
  }
  
  public function executeLoadSlotlets() 
  {
  	$c = new Criteria();
    $c->addAscendingOrderByColumn(SlotletPeer::NAME);
    $this->slotlets = SlotletPeer::doSelect($c);
  }
  
  public function executeDeleteContainerSlotlet()
  {
  	$container_slotlet_id = $this->getRequestParameter('id');
  	$container_slotlet = ContainerSlotletPeer::retrieveByPk($container_slotlet_id);
  	$container_id = $container_slotlet->getContainerId();
  	$this->container_name = $container_slotlet->getContainer()->getName();
  	$container_slotlet->delete();

    $this->forward('slotlet','loadContainers');
  }
  
  public function executeAddSlotlet()
  {
  	$container_id = $this->getRequestParameter('container_id');
  	$slotlet_id = $this->getRequestParameter('slotlet_id');
  	$slotlet = SlotletPeer::retrieveByPK($slotlet_id);
    $container_slotlet = new ContainerSlotlet();
    $container_slotlet->setContainerId($container_id);
    $container_slotlet->setSlotletId($slotlet_id);
    $container_slotlet->setName($slotlet->getName());
    $container_slotlet->setPriority($container_slotlet->getLastPriority()+1);       
    $container_slotlet->save();

    $this->forward('slotlet','loadContainers');
  }
  
  public function executeCreateSlotlet()
  {
  	$sl_name = $this->getRequestParameter('slotlet_name');
  	$cls     = $this->getRequestParameter('cls');
    if (!$sl_name == "")
    {
      $slotlet = new Slotlet();
      $slotlet->setName($sl_name);
      $slotlet->setCls($cls);
      $slotlet->save();
    }
    $this->forward('slotlet','loadSlotlets');
  }
  
  public function executeDeleteSlotlet() 
  {
    try
    {
      $slotlet_id = $this->getRequestParameter('id');
      SlotletPeer::doDelete($slotlet_id);	
    }
    catch (Exception $e)
    {
    }

    $this->forward('slotlet','loadSlotlets');
  }
  
  public function executeAddContainer() 
  {
    $container_name = $this->getRequestParameter('container_name');
    if (!$container_name == "")
    {
      $container = new Container();
      $container->setName($container_name);
      $container->save();
    }
    
    $this->forward('slotlet','loadContainers');
  }
  
  public function executeDeleteContainer() 
  {
    try
    {
      $container_id = $this->getRequestParameter('id');
      ContainerPeer::doDelete($container_id);
    }
    catch (Exception $e)
    {
    }
    
    $this->forward('slotlet','loadContainers');
  }
  
  public function executeEditContainerName() 
  {
    $container_id = $this->getRequestParameter('id');
    $container = ContainerPeer::retrieveByPk($container_id);
    $value = trim($this->getRequestParameter('value'));
    if (!$value == "")
    {
      $container->setName($value);
      $container->save();
    }

    return $this->renderText($container->getName());
  }

  public function executeEditRssChannel()
  {
    $container_id = $this->getRequestParameter('id');
    $rss_id       = $this->getRequestParameter('rss_id');
    $container_sl = ContainerSlotletPeer::retrieveByPk($container_id);
    $container_sl->setRssChannelId($rss_id);
    $container_sl->save();

    return sfView::NONE;
  }

  public function executeSort()
  {
    $container_id = $this->getRequestParameter('container_id');
    $order        = $this->getRequestParameter('cont_slotlets_ul_'.$container_id);
    $flag         = ContainerSlotletPeer::doSort($order);
    $text         = $flag ? "Cambios guardados":"Hubo algún error";

    return $this->renderText($text);
  }
  
  public function executeEditContainerSlName() 
  {
    $container_sl_id = $this->getRequestParameter('id');
    $container_sl    = ContainerSlotletPeer::retrieveByPk($container_sl_id);
    $value           = trim($this->getRequestParameter('value'));
    if (!$value == "")
    {
      $container_sl->setName($value);
      $container_sl->save();
    }
    
    return $this->renderText($container_sl->getName());
  }
  
  public function executeEditContainerSlVisibleRss()
  {
    $container_sl_id = $this->getRequestParameter('id');
    $container_sl    = ContainerSlotletPeer::retrieveByPk($container_sl_id);
    $value           = trim($this->getRequestParameter('value'));
    if (!$value == "")
    {
      $container_sl->setVisibleRss($value);
      $container_sl->save();
    }
    
    return $this->renderText($container_sl->getVisibleRss());
  }
  
  public function executeLoadContainers()
  {
    $this->containers = ContainerPeer::doSelect(new Criteria());
  }
}