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
 * rsschannel actions.
 *
 * @package    cms
 * @subpackage rsschannel
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class rsschannelActions extends autorsschannelActions
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

  public function updateRssChannelFromRequest()
  {
    parent::updateRssChannelFromRequest();
    switch ($this->getActionName()) {
      case 'create':
        $this->rss_channel->setCreatedBy($this->getUser()->getGuardUser()->getId());
      break;
      case 'edit':
        $this->rss_channel->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }
  }


  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));
    $this->rss_channel = $this->getRssChannelOrCreate();
    if ($this->rss_channel->canDelete())
    {
      try
      {
        $this->rss_channel->delete();
        $this->setFlash('notice', 'El canal RSS seleccionado fue borrado exitosamente');
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete','El canal RSS '.$this->rss_channel->getTitle().' no se puede borrar debido a que esta referenciado en un artículo.');
        return $this->forward('rsschannel', 'list');
      }
    }
    else
    {
      $this->getRequest()->setError('delete','El canal RSS '.$this->rss_channel->getTitle().' no se puede borrar debido a que esta referenciado en un artículo.');
    }

    return $this->forward('rsschannel','list');
 
  }
}