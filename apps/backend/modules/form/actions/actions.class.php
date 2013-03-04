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
 * form actions.
 *
 * @package    plugins
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class formActions extends autoformActions
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

  public function executeEditForm()
  {
    $this->form = $this->getFormOrCreate();

    $this->getUser()->setAttribute('form', $this->form);

    $this->redirect('formbuilder/index');
  }

  public function executeShowResults()
  {
    $this->form = $this->getFormOrCreate();
  }

  protected function updateFormFromRequest()
  {
    
    parent::updateFormFromRequest();
    switch ($this->getActionName()) {
      case 'create':
        $this->form->setCreatedBy($this->getUser()->getGuardUser()->getId());
      break;
      case 'edit':
        $this->form->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }
  }

  public function executeDelete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->form = $this->getFormOrCreate();
    if ($this->form->canDelete())
    {
      try
      {
        $this->form->delete();
        $this->setFlash('notice', 'La encuesta seleccionada fue borrada exitosamente');
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete','La encuesta '.$this->form->getTitle().' no se puede borrar debido a que esta referenciado en un artículo.');
        return $this->forward('form', 'list');
      }
    }
    else
    {
      $this->getRequest()->setError('delete','La encuesta '.$this->form->getTitle().' no se puede borrar debido a que esta referenciado en un artículo.');
    }

    return $this->forward('form','list');
 
  }
}