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
 * layout actions.
 *
 * @package    choique
 * @subpackage layout
 * @author     José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 * @version    SVN: $Id: actions.class.php 614 2012-08-09 18:43:54Z jpablop $
 */
class layoutActions extends autolayoutActions
{
  public function preExecute()
  {
    // Check if layout per section is enabled in the configuration
    if (!CmsConfiguration::get('check_use_layout_per_section', false))
    {
      $this->setFlash('notice', 'ChoiqueCMS está configurado para utilizar una única distribución para todo el sitio. En este módulo podrá editarla.');

      // If it's not, redirect to the old slotlet editor
      return $this->redirect('slotlet/index');
    }

    // Otherwise, continue normally
    parent::preExecute();
  }

  public function executeSetDefault()
  {
    $layout = $this->getLayoutOrCreate();

    if ($layout->canBeSetAsDefault() && $layout->becomeDefault())
    {
      $this->setFlash('notice', 'La distribución seleccionada ha sido convertida en la distribución por defecto del sitio.');
    }
    else
    {
      $this->setFlash('error', 'La distribución seleccionada no pudo ser convertida en la distribución por defecto del sitio. ¿Ya lo era?');
    }

    $this->redirect('layout/index');
  }

  public function executeEditor()
  {
    $this->layout = $this->getLayoutOrCreate();
    $this->aspect = $this->getRequestParameter('ref', 'article');

    if (!in_array($this->aspect, array('article', 'template')))
    {
      $this->setFlash('error', 'Imposible editar el aspecto indicado.');
      $this->redirect('layout/index');
    }

    $configuration = call_user_func(array($this->layout, 'get'.ucfirst($this->aspect).'Layout'));

    $this->layout_configuration = new LayoutConfiguration($configuration);
    $this->configuration = $this->layout->encode((array) $configuration);
  }

  public function executeEditTemplateLayout()
  {
    $this->getRequest()->setParameter('ref', 'template');
    $this->forward('layout', 'editor');
  }

  public function executeEditArticleLayout()
  {
    $this->getRequest()->setParameter('ref', 'article');
    $this->forward('layout', 'editor');
  }

  public function executeUpdate()
  {
    if ($this->getRequest()->hasParameter('_preview'))
    {
      $this->forward('layout', 'preview');
    }

    $this->layout  = $this->getLayoutOrCreate();
    $data          = $this->getRequestParameter('layout');
    $aspect        = $this->getRequestParameter('aspect');
    $configuration = (array) $this->layout->decode($data);

    if (in_array($aspect, array('article', 'template')))
    {
      call_user_func(array($this->layout, 'set'.ucfirst($aspect).'Layout'), $configuration);
      $this->layout->save();

      $this->setFlash('notice', 'Se han guardado los cambios a la distribución.');
    }
    else
    {
      $this->setFlash('error', 'Aspecto inválido.');
    }

    $this->redirect(sprintf('layout/edit%sLayout?id=%d', ucfirst($aspect), $this->layout->getId()));
  }

  public function executeSconfig()
  {
    sfLoader::loadHelpers(array('Helper', 'Object'));

    $class = $this->getRequestParameter('class');

    return $this->renderText(SlotletManager::getConfigurationForm($class));
  }

  public function executeDuplicate()
  {
    $this->layout = $this->getLayoutOrCreate();

    if (null !== $this->layout && $clone = $this->layout->duplicate())
    {
      $clone->save();

      $this->setFlash('notice', 'Se hace creado una copia de la distribución con el nombre "'.$clone->getName().'". Puede aquí editar sus datos.');

      $this->redirect('layout/edit?id='.$clone->getId());
    }
    else
    {
      $this->setFlash('error', 'No se puede crear una copia a partir de la distribución seleccionada. Por favor, intente nuevamente.');

      $this->redirect('layout/index');
    }
  }

  public function executePreview()
  {
    $data    = $this->getRequestParameter('layout');
    $section = $this->getRequestParameter('preview_section');

    $temporary_layout = TemporaryLayoutPeer::create($data);

    $this->redirect(choiqueUtil::generateUrl('frontend', 'section/layout?name='.$section.'&layout='.$temporary_layout->getId()));
  }


  protected function addFiltersCriteria($c)
  {
    parent:: addFiltersCriteria($c);   
    if (isset($this->filters['virtual_section_id']) && $this->filters['virtual_section_id'] !== '')
    {
      $c->add(LayoutPeer::VIRTUAL_SECTION_ID, $this->filters['virtual_section_id']);
    }

    if (isset($this->filters['section_id']) && $this->filters['section_id'] !== '')
    {
      $c->addJoin(SectionPeer::LAYOUT_ID,LayoutPeer::ID);      
      $c->add(SectionPeer::ID, $this->filters['section_id']);
    }
  }

  

}