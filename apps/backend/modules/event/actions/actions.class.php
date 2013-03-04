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
 * event actions.
 *
 * @package    new_cms
 * @subpackage event
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class eventActions extends autoeventActions
{
  public function updateEventFromRequest()
  {
    switch ($this->getActionName()) {
      case 'create':
        $this->event->setAuthor($this->getUser()->getGuardUser()->getId());
      break;
      case 'edit':
        $this->event->setUpdatedBy($this->getUser()->getGuardUser()->getId());
      break;
    }

    return parent::updateEventFromRequest();
  }

  public function executeExport()
  {
    sfLoader::loadHelpers(array('I18N'));

    //List generation: input
    $this->processSort();
    $this->processFilters();
    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/event/filters');
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);

    //Xls generation: output
    $this->setLayout(false);
    $filename = 'listevents.xls';
    $this->getResponse()->setHttpHeader('Content-Disposition', ' attachment; filename="'.$filename.'"');
    $full_filename = '/tmp/' . $filename; 
    $this->title = __('Listado de eventos');
    $this->file = $full_filename;
    $excel_builder = EventPeer::getXls($c);
    @$excel_builder->toXLS($full_filename);
  }

  public function executeAutocompleteArticle()
  {
    $query = '%'.$this->getRequestParameter('article_id_search').'%';

    $c = new Criteria();
    $c->setIgnoreCase(true);
    $crit = $c->getNewCriterion(ArticlePeer::TITLE, $query, Criteria::LIKE);
    $crit->addOr($c->getNewCriterion(ArticlePeer::DESCRIPTION, $query, Criteria::LIKE));
    $crit->addOr($c->getNewCriterion(ArticlePeer::NAME, $query, Criteria::LIKE));
    $c->add($crit);
    $c->addDescendingOrderByColumn(ArticlePeer::UPDATED_AT);

    $this->articles = ArticlePeer::doSelect($c);
  }

  public function executeAutocompleteSection()
  {
    $query = '%'.$this->getRequestParameter('event_section_search').'%';

    $criteria = new Criteria();
    
    $criteria->setLimit(20);
    $criteria->setIgnoreCase(true);
    $criteria->addAscendingOrderByColumn(SectionPeer::TITLE);

    $criteria->add(SectionPeer::TITLE, $query, Criteria::LIKE);
    $criterion = $criteria->getNewCriterion(SectionPeer::TITLE, $query, Criteria::LIKE);
    $criterion->addOr($criteria->getNewCriterion(SectionPeer::NAME, $query, Criteria::LIKE));
    $criteria->add($criterion);

    $this->sections = SectionPeer::doSelect($criteria);
  }

  public function executeUnpublish()
  {
		$event = EventPeer::retrieveByPK($this->getRequestParameter('id'));

    if (null === $event)
    {
      $this->setFlash('notice', 'Debe seleccionar un evento para despublicarlo.');
    }
    else if ($event->unpublish())
    {
      $this->setFlash('notice', 'El evento fue despublicado correctamente. Ya no estará visible en el sitio público.');
    }
    else
    {
      $this->setFlash('notice', 'No se pudo despublicar el evento, verifique que ya no se encuentre despublicado.');
    }

    $this->redirect('event/index');
  }

  public function executePublish()
  {
		$event = EventPeer::retrieveByPK($this->getRequestParameter('id'));

    if (null === $event)
    {
      $this->setFlash('notice', 'Debe seleccionar un evento para publicarlo.');
    }
    else if ($event->publish())
    {
      $this->setFlash('notice', 'El evento fue publicado correctamente. Ahora podrá ser visible en el sitio público.');
    }
    else
    {
      $this->setFlash('notice', 'No se pudo publicar el evento, verifique que ya no se encuentre despublicado.');
    }

    $this->redirect('event/index');
  }

}