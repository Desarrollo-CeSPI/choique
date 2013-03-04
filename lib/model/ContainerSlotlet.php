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
 * Subclass for representing a row from the 'container_slotlet' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ContainerSlotlet extends BaseContainerSlotlet
{
  public function getLastPriority() 
  {
    $c = new Criteria();
    $c->add(ContainerSlotletPeer::CONTAINER_ID, $this->getContainerId());
    $c->addDescendingOrderByColumn(ContainerSlotletPeer::PRIORITY);
    $container_slotlet = ContainerSlotletPeer::doSelectOne($c);
    $p = -1;
    if ($container_slotlet)
    {
      $p = $container_slotlet->getPriority();
    }

    return $p;
  }

  public function getRssChannelActive()
  {
    $c = new Criteria();
    $c->add(RssChannelPeer::IS_ACTIVE,1);

    return RssChannelPeer::doSelect($c);
  }

  public function __toString() 
  {
    return $this->getName();
  }
}