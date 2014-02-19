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
 * Subclass for performing query and update operations on the 'event' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EventPeer extends BaseEventPeer
{
  public static function getFieldNamesArray()
  {
    sfLoader::loadHelpers(array('I18N'));

    return array('is_published' =>  __('Publicado'),
                 'title'        =>  __('Título'),
                 'description'  =>  __('Descripción'),
                 'location'     =>  __('Lugar'),
                 'contact'      =>  __('Información de contacto'),
                 'organizer'    =>  __('Organizador'),
                 'author'       =>  __('Autor'),
                 'comment'      =>  __('Comentario'),
                 'begins_at'    =>  __('Inicio'),
                 'ends_at'      =>  __('Finalización'),
                 'article'      =>  __('Artículo'),
                 'event_type_id'   =>  __('Tipo'));
  }

  public static function getFieldMethodsArray()
  {
    return array('is_published' =>  'getIsPublished',
                 'title'        =>  'getTitle',
                 'description'  =>  'getDescription',
                 'location'     =>  'getLocation',
                 'contact'      =>  'getContact',
                 'organizer'    =>  'getOrganizer',
                 'author'       =>  'getAuthor',
                 'comment'      =>  'getComment',
                 'begins_at'    =>  'getBeginsAt',
                 'ends_at'      =>  'getEndsAt',
                 'article'      =>  'getArticle',
                 'event_type_id'   =>  'getEventType');
  }

  public static function getXls($criteria)
  {
    sfLoader::loadHelpers(array('I18N'));

    $fields      = array('is_published', 'title', 'description', 'location', 'contact', 'organizer', 'author', 'comment', 'begins_at', 'ends_at', 'article', 'event_type_id');
    $eb          = new ExcelBuilder(__('Listado de Eventos'), PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $row         = 1;
    $header      = array();
    $field_names = self::getFieldNamesArray();
    foreach ($fields as $field)
    {
      $header[] = $field_names[$field];
    }
    $max_fields_count = count($header);
    
    //List's title
    $eb->writeCell($row++, 'A', __('Listado de Eventos'), ExcelBuilder::$TitleFormat);
    $eb->mergeCells("A1:" . chr(ord('A') + $max_fields_count - 1) . "1");
    $dateFormat = new sfDateFormat(sfContext::getInstance()->getUser()->getCulture());

    //Header
    $eb->writeRow($row++, 'A', $header, ExcelBuilder::$HeaderFormat);

    //Events
    $events = EventPeer::doSelect($criteria);
    $values = array();
    $field_methods = self::getFieldMethodsArray();
    foreach ($events as $event)
    {
      $a_row = array();
      foreach ($fields as $field)
      {
        $v = $event->$field_methods[$field]();
        if (is_array($v))
        {
          if (count($v) > 0)
          {
            $txt = '';
            foreach ($v as $i)
            {
              $txt .= $i . "\n";
            }
            $v = $txt;
          }
          else
          {
            $v = '-';
          }
        }
        elseif (is_bool($v))
        {
          $v = ($v) ? __('Sí') : __('No');
        }
        elseif (is_object($v))
        {
          $v = $v->__toString();
        }
        else
        {
          $v = (is_null($v) ? '-' : $v);
        }
        $a_row[] = $v;
      }
      $values[] = $a_row;
    }

    $first_row_value = $row;
    $row += $eb->writeTable($row, 'A', $values, ExcelBuilder::$CellFormat);
    $last_row_value = ($row == $first_row_value) ? $row++ : $last_row_value = $row - 1;

    return $eb;
  }

  static public function addBetweenCriteria(Criteria $criteria, $month_start, $month_end)
  {
		$criterion0 = $criteria->getNewCriterion(self::BEGINS_AT, $month_start, Criteria::GREATER_EQUAL);
		$criterion1 = $criteria->getNewCriterion(self::BEGINS_AT, $month_end, Criteria::LESS_EQUAL);

		$criterion2 = $criteria->getNewCriterion(self::ENDS_AT, $month_start, Criteria::GREATER_EQUAL);
		$criterion3 = $criteria->getNewCriterion(self::ENDS_AT, $month_end, Criteria::LESS_EQUAL);

    $criterion4 = $criteria->getNewCriterion(self::BEGINS_AT, $month_start, Criteria::LESS_EQUAL);
    $criterion5 = $criteria->getNewCriterion(self::ENDS_AT, $month_end, Criteria::GREATER_EQUAL);


		$criterion0->addAnd($criterion1);
		$criterion2->addAnd($criterion3);
    $criterion4->addAnd($criterion5);
		$criterion0->addOr($criterion2);
    $criterion0->addOr($criterion4);
		$criteria->add($criterion0);
  }

  static public function addSortCriteria($criteria)
  {
    $criteria->addDescendingOrderByColumn(self::COMMENT);
    $criteria->addAscendingOrderByColumn(self::BEGINS_AT);
  }

  static public function retrieveBetween($month_start, $month_end, $published = true, $section_id = null)
  {
		$criteria = new Criteria();

    self::addBetweenCriteria($criteria, $month_start, $month_end);

		$criteria->add(EventPeer::IS_PUBLISHED, $published);

    if (null !== $section_id)
    {
      self::addSectionCriteria($criteria, $section_id);
    }

    self::addSortCriteria($criteria);

		return self::doSelect($criteria);
  }

  static public function addStartingBetweenCriteria(Criteria $criteria, $from, $to)
  {
		$criterion = $criteria->getNewCriterion(self::BEGINS_AT, $from, Criteria::GREATER_EQUAL);
		$criterion->addAnd($criteria->getNewCriterion(self::BEGINS_AT, $to, Criteria::LESS_EQUAL));

		$criteria->add($criterion);
  }

  static public function retrieveStartingBetween($from, $to, $only_published = true)
  {
		$criteria = new Criteria();

    self::addStartingBetweenCriteria($criteria, $from, $to);

    if ($only_published)
    {
      $criteria->add(EventPeer::IS_PUBLISHED, true);
    }
    
    self::addSortCriteria($criteria);

		return self::doSelect($criteria);
  }

  static public function groupEvents($year, $month, $month_start, $month_end, $section_id = null)
  {
    $events = self::retrieveBetween($month_start, $month_end, true, $section_id);

    $month  = new sfDate("$year-$month-01 00:00:00");
    $amount = intval($month->finalDayOfMonth()->format('d'));

    $days = array_fill(1, $amount, array());

    foreach ($events as $event)
    {
      //adjust dates to be in the desired range
      $init = new sfDate($event->getBeginsAt());
      while ($init->cmp($month_start) < 0)
      {
        $init->tomorrow();
      }

      $end = new sfDate($event->getEndsAt());
      while ($end->cmp($month_end) > 0)
      {
        $end->yesterday();
      }

      //$init and $end establish the range for the current event in the requested month-year
      $init = intval($init->format('d'));
      $end  = intval($end->format('d'));

      for ($day = $init; $day <= $end; $day++)
      {
        $days[$day][] = $event;
      }
    }

    return $days;
  }

  static public function retrieveTodaysEvents()
  {
    return self::retrieveBetween(strtotime('today midnight'), strtotime('tomorrow midnight'));
  }

  static public function addSectionCriteria(Criteria $criteria, $section_id)
  {
    $section = SectionPeer::retrieveByPK($section_id);

    if (null !== $section)
    {
      $section = $section->getFirstLevelSection();
      
      $ids = array_map(create_function('$s', 'return $s->getId();'), $section->getLineage());
      
      $criteria->addJoin(EventPeer::ID, EventSectionPeer::EVENT_ID, Criteria::INNER_JOIN);
      $criteria->add(EventSectionPeer::SECTION_ID, $ids, Criteria::IN);
    }
  }

}