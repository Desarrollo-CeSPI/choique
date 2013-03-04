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
 * formbuilder actions.
 *
 * @package    plugins
 * @subpackage formbuilder
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class formbuilderActions extends sfActions
{

  protected function canEdit()
  {
    if (is_null($this->form) || !$this->form->canEdit())
    {
      $this->redirect('@homepage');
    }
  }

  public function executeIndex()
  {
    $this->form = $this->getUser()->getAttribute('form');
    $this->canEdit();
  }

  public function executeAddField()
  {
    $this->form = FormPeer::retrieveByPK($this->getRequestParameter('form_id'));
    $this->canEdit();

    $field = new Field();
    $field->setForm($this->form);
    $field->setType($this->getRequestParameter('field_type'));

    $field->save();

    $this->getUser()->setAttribute('form', $this->form);
  }

  public function executeDeleteField()
  {
    $this->form = FormPeer::retrieveByPK($this->getRequestParameter('form_id'));
    $this->canEdit();

    $field = FieldPeer::retrieveByPK($this->getRequestParameter('field_id'));

    $field->delete();

    $this->getUser()->setAttribute('form', $this->form);
  }

  public function executeShowField()
  {
    $this->efield = FieldPeer::retrieveByPK($this->getRequestParameter('field_id'));

    $this->setLayout(false);
  }

  public function executeEditField()
  {
    $this->efield = FieldPeer::retrieveByPK($this->getRequestParameter('field_id'));

    /* LABEL */
    $label = $this->getRequestParameter('label');
    if ($label != $this->efield->getLabel())
    {
      $this->efield->setLabel($label);
    }

    /* DEFAULT VALUE */
    $defaultValue = $this->getRequestParameter('default_value');

    if ($defaultValue != $this->efield->getDefaultValue())
    {
      $this->efield->setDefaultValue($defaultValue);
    }

    /* IS REQUIRED */
    if ($this->hasRequestParameter('is_required'))
    {
      $this->efield->setIsRequired(true);
    }
    else
    {
      $this->efield->setIsRequired(false);
    }

    $this->efield->save();

    $this->form = $this->efield->getForm();
    $this->canEdit();

    $this->getUser()->setAttribute('form', $this->form);

    $this->setLayout(false);
  }

  public function executeSortFields()
  {
    $sorts = $this->getRequestParameter('sortable-form');
    for ($i = 0; $i < count($sorts); $i++)
    {
      $field = FieldPeer::retrieveByPk($sorts[$i]);
      $field->setSort($i);
      $field->save();
    }
    
    return sfView::NONE;
  }
}