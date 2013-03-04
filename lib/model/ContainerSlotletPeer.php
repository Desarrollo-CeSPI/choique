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
 * Subclass for performing query and update operations on the 'container_slotlet' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ContainerSlotletPeer extends BaseContainerSlotletPeer
{
  static public function doSort($order)
  {
    $reverse_order=array_reverse($order);
    $con = Propel::getConnection(self::DATABASE_NAME);
    try 
    {
      $con->begin();

      foreach ($reverse_order as $priority => $id) 
      {
        $item = self::retrieveByPk($id);
        if ($item->getPriority() != $priority)
        {
          $item->setPriority($priority);
          $item->save();
        }
      }

      $con->commit();

      return true;    
    }
    catch (Exception $e)
    {
      $con->rollback();

      return false;
    }
  }

  public static function retrieveByClass($class_name)
  {
    $c = new Criteria();
    $c->addJoin(SlotletPeer::ID, self::SLOTLET_ID);
    $c->add(SlotletPeer::CLS, $class_name);
    $c->addDescendingOrderByColumn(self::NAME);

    return self::doSelect($c);
  }
}