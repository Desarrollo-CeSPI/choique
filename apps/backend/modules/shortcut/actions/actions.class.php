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
 * shortcut actions.
 *
 * @package    cmsunlp
 * @subpackage shortcut
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class shortcutActions extends autoshortcutActions
{
  protected function updateShortcutFromRequest()
  {
    parent::updateShortcutFromRequest();
    
   


    switch ($this->getActionName()) {
      case 'create':
         $this->shortcut->setCreatedBy($this->getUser()->getGuardUser()->getId());;
      break;
      case 'edit':
         $this->shortcut->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }


    $shortcut = $this->getRequestParameter('shortcut');
    if (isset($shortcut['reference_type']))
    {
      $reference_type = $shortcut['reference_type'];
      switch ($reference_type)
      {
        case 0:
        case 4:
          //External reference
          $this->shortcut->setReference($shortcut['external_reference_value']);
          break;
        case 1:
          //Article reference
          $this->shortcut->setReference($shortcut['article_reference_value']);
          break;
        case 2:
          //Section reference
          $this->shortcut->setReference($shortcut['section_reference_value']);
          break;
        case 3:
          //None reference
          $this->shortcut->setReference(Shortcut::NONE_STRING);
          break;
        case 5:
          // Mobile version
          $this->shortcut->setReference(Shortcut::REFERENCE_TYPE_MOBILE);
          break;
        case 6:
          $this->shortcut->setReference(Shortcut::REFERENCE_TYPE_NO_MOBILE);
          // Normal version
          break;
      }
    }
  }

  public function executeIncreasePriority()
	{
		$shortcut = ShortcutPeer::retrieveByPK($this->getRequestParameter('id'));
		$shortcut->setPriority($shortcut->getPriority()+1);
		$shortcut->save();

		$this->redirect('shortcut/list');
	}
	
	public function executeDecreasePriority()
	{
		$shortcut = ShortcutPeer::retrieveByPK($this->getRequestParameter('id'));
		$priority = $shortcut->getPriority();
		if ($priority > 0)
    {
			$priority--;
		}
		$shortcut->setPriority($priority);
		$shortcut->save();
    
		$this->redirect('shortcut/list');
	}

  public function executeUnpublish()
  {
		$shortcut = ShortcutPeer::retrieveByPK($this->getRequestParameter('id'));

    if (null === $shortcut)
    {
      $this->setFlash('notice', 'Debe seleccionar un atajo para despublicarlo.');
    }
    else if ($shortcut->unpublish())
    {
      $this->setFlash('notice', 'El atajo fue despublicado correctamente. Ya no estará visible en el sitio público.');
    }
    else
    {
      $this->setFlash('notice', 'No se pudo despublicar el atajo, verifique que ya no se encuentre despublicado.');
    }

    $this->redirect('shortcut/index');
  }

  public function executePublish()
  {
		$shortcut = ShortcutPeer::retrieveByPK($this->getRequestParameter('id'));

    if (null === $shortcut)
    {
      $this->setFlash('notice', 'Debe seleccionar un atajo para publicarlo.');
    }
    else if ($shortcut->publish())
    {
      $this->setFlash('notice', 'El atajo fue publicado correctamente. Ahora podrá ser visible en el sitio público.');
    }
    else
    {
      $this->setFlash('notice', 'No se pudo publicar el atajo, verifique que ya no se encuentre despublicado.');
    }

    $this->redirect('shortcut/index');
  }

  public function getLabels()
  {
    return array_merge(parent::getLabels(),
                       array('shortcut{external_reference_value}' => 'Destino del enlace:',
                             'shortcut{article_reference_value}'  => 'Destino del enlace:',
                             'shortcut{section_reference_value}'  => 'Destino del enlace:'));
  }

  public function validateCreate() 
  {
    return $this->referenceValidation();
  }

  public function validateEdit() 
  {
    return $this->referenceValidation();
  }
  
  protected function referenceValidation() 
  {
  	sfLoader::loadHelpers(array('I18N'));

    $response = true;
    if ($this->getRequest()->getMethodName() == 'POST')
    {
      $references = array('external_reference_value', 'article_reference_value', 'section_reference_value', 'none_reference_value', 'none_reference_value', 'none_reference_value', 'none_reference_value');
      $shortcut = $this->getRequestParameter('shortcut');
      $reference_type = $shortcut['reference_type'];
      $reference_value = $shortcut[$references[$reference_type]];
      if (!$reference_value)
      {
        $this->getRequest()->setError('shortcut{reference}', __('El Destino del enlace no puede estar vacío.'));
        $response = false;
      }
      //solo controlo que sea una URL si $reference_type = 0 o 4, o sea, externo o externo en popup
      $validator = new sfUrlValidator();
      $validator->initialize(sfContext::getInstance());
      $error = null;
      if (    (($reference_type == 0) || ($reference_type == 4))
           && (!$validator->execute($shortcut['external_reference_value'],$error ))
         )
      {
        $this->getRequest()->setError('shortcut{reference}', __('La URL ingresada no es válida.'));
        $response = false;
      }
    }
    
    return $response;
  }


  public function executeList()
  {
    $this->processSort();

    $this->processFilters();

    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/shortcut/filters');

    // pager
    $this->pager = new sfPropelPager('Shortcut', 25);
    $c = new Criteria();
    $this->addSortCriteria($c);
    $c->addJoin(ShortcutPeer::CONTAINER_SLOTLET_ID,ContainerSlotletPeer::ID, Criteria::LEFT_JOIN);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }
}
