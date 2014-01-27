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
 * @package    nwe_cms
 * @subpackage event
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class eventActions extends sfActions
{
	public function executeGetCalendar()
	{
    sfLoader::loadHelpers(array('CmsEscaping'));

		$this->setLayout(false);
		$this->month = intval($this->getRequestParameter('month', date("m")));
		$this->year = intval($this->getRequestParameter('year', date("Y")));

    $this->section_id = trim($this->getRequestParameter('section_id'));
    $this->in_place = (bool) $this->getRequestParameter('ip');

    $slotlet_container = ContainerSlotletPeer::retrieveByPK($this->getRequestParameter('cls_id'));

    if ($slotlet_container)
    {
      $this->title = $slotlet_container->getName();
      $this->id    = escape_string($slotlet_container->getName());
    }
    else
    {
      $this->title = 'Eventos';
      $this->id    = escape_string($this->getRequestParameter('id', 'agenda'));
    }

		$this->getUser()->setAttribute('events.'.$this->id.'.month', $this->month);
		$this->getUser()->setAttribute('events.'.$this->id.'.year', $this->year);
	}

  protected function getDefaultSearchParameters()
  {
		$to = CmsConfiguration::get('check_use_default_search_to_tomorrow',true)? date('d/m/Y', strtotime('tomorrow')):'';
    return array_merge(array(
      'title'      => null,
      'event_type' => null,
      'organizer'  => null,
      'date'       => array('from' => date('d/m/Y'), 'to' => $to)
    ), $this->getUserSearchParameters());
  }

  /**
   * Clear the dates held in $dates' 'from' and 'to' values and return a
   * similar array with the timestamps (or null).
   *
   * @param  array $dates Input dates.
   *
   * @return array 
   */
  protected function clearDateRange($dates = array('from' => null, 'to' => null))
  {
    $clean_dates = array('from' => null, 'to' => null);

    foreach (array('from', 'to') as $index)
    {
      if (array_key_exists($index, $dates) && '' !== trim($dates[$index]))
      {
        if (is_int($dates[$index]))
        {
          $clean_dates[$index] = $dates[$index];
        }
        else
        {
          $clean_dates[$index] = sfI18N::getTimestampForCulture($dates[$index], $this->getUser()->getCulture());
        }
      }
    }

    return $clean_dates;
  }

  protected function getSectionName()
  {
    return 'events';
  }

  protected function getResultsPerPage()
  {
    return 15;
  }

  protected function setUserSearchParameters($parameters)
  {
    $this->getUser()->setAttribute('events.search.parameters', $parameters);
  }

  protected function getUserSearchParameters()
  {
    return $this->getUser()->getAttribute('events.search.parameters', array());
  }

  protected function buildCriteria(sfWebRequest $request)
  {
    if ($request->hasParameter('reset'))
    {
      $this->setUserSearchParameters(array());

      $this->redirect('event/index');
    }

    $search_parameters = array_merge($this->getDefaultSearchParameters(), $request->getParameter('events_search', array()));

    $this->title      = trim($search_parameters['title']);
    $this->event_type = trim($search_parameters['event_type']);
    $this->organizer  = trim($search_parameters['organizer']);
    $this->date       = $this->clearDateRange($search_parameters['date']);

    $this->setUserSearchParameters(array(
      'title'      => $this->title,
      'event_type' => $this->event_type,
      'organizer'  => $this->organizer,
      'date'       => $this->date
    ));

    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(EventTypePeer::TITLE);
    $this->event_type_criteria = $criteria;
    
    $request->setParameter('section_name', $this->getSectionName());

    $criteria = new Criteria();

    $criteria->add(EventPeer::IS_PUBLISHED, true);
    $criteria->addAscendingOrderByColumn(EventPeer::BEGINS_AT);

    if ('' != $this->title)
    {
      $criteria->setIgnoreCase(true);
      $criterion = $criteria->getNewCriterion(EventPeer::TITLE, '%'.$this->title.'%', Criteria::LIKE);
      $criterion->addOr($criteria->getNewCriterion(EventPeer::DESCRIPTION, '%'.$this->title.'%', Criteria::LIKE));
      $criteria->add($criterion);
    }

    if ('' != $this->event_type)
    {
      $criteria->add(EventPeer::EVENT_TYPE_ID, $this->event_type);
    }

    if ('' != $this->organizer)
    {
      $criteria->setIgnoreCase(true);
      $criteria->add(EventPeer::ORGANIZER, '%'.$this->organizer.'%', Criteria::LIKE);
    }

    if (null !== $this->date['from'] && null !== $this->date['to'])
    {
      EventPeer::addBetweenCriteria($criteria, $this->date['from'], $this->date['to']);
    }
    else if (null !== $this->date['from'])
    {
      //$criterion = $criteria->getNewCriterion(EventPeer::ENDS_AT, $this->date['from'], Criteria::LESS_THAN);
			//$criterion->addOr($criteria->getNewCriterion(EventPeer::ENDS_AT, $this->date['from'], Criteria::GREATER_EQUAL));
      $criteria->addAnd($criteria->getNewCriterion(EventPeer::BEGINS_AT, $this->date['from'], Criteria::GREATER_EQUAL));

//      $criteria->add($criterion);
    }
    else if (null !== $this->date['to'])
    {
      $criterion = $criteria->getNewCriterion(EventPeer::BEGINS_AT, $this->date['to'], Criteria::LESS_EQUAL);
      
      $criterion->addOr($criteria->getNewCriterion(EventPeer::ENDS_AT, $this->date['to'], Criteria::LESS_EQUAL));
      
      $criteria->add($criterion);
    }

    return $criteria;
  }

  /**
   * Build the pager for Event model with the given $criteria.
   *
   * @param Criteria $criteria
   */
  protected function buildPager(Criteria $criteria)
  {
    $pager = new sfPropelPager('Event', $this->getResultsPerPage());

    $pager->setCriteria($criteria);
    $pager->setPage($this->getRequestParameter('page', 1));
    
    $pager->init();

    return $pager;
  }

  public function executeIndex()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_ALL_EVENTS);
    }

    $criteria    = $this->buildCriteria($this->getRequest());
    $this->pager = $this->buildPager($criteria);
  }

}