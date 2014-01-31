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
 * Subclass for representing a row from the 'event' table.
 *
 *
 *
 * @package lib.model
 */
class Event extends BaseEvent implements SlotletInterface
{
  public function __toString()
  {
    return sprintf("%s (%s - %s)", $this->getTitle(), $this->getBeginsAt('d/m/Y'), $this->getEndsAt('d/m/Y'));
  }

  protected function canBeModifiedWhenPublished()
  {
    return
        ($context= sfContext::getInstance()) &&
          (
          $context->getUser()->isSuperAdmin()
          ||
          (
            (
            (
            $context->getUser()->hasCredential(array('designer','reporter'),false) &&
            !$this->getIsPublished()
            )
            ||
            $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
            )
            && $context->getUser()->getGuardUser() !== null && $context->getUser()->getGuardUser()->getId() == $this->getAuthor()
          ));
  }


  public function canEdit()
  {
    return $this->canBeModifiedWhenPublished();
  }

  public function canDelete()
  {
    return $this->canBeModifiedWhenPublished();
  }

  public function canUnpublish()
  {
    return $this->canBeModifiedWhenPublished() && $this->getIsPublished();
  }

  public function canPublish()
  {
    return
        ($context= sfContext::getInstance()) &&
          (
          $context->getUser()->isSuperAdmin()
          ||
          (
            $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
            && $context->getUser()->getGuardUser() !== null &&  $context->getUser()->getGuardUser()->getId() == $this->getAuthor()
          )) && !$this->getIsPublished();
  }

  public function getUploadedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getAuthor());
  }

  public function isPublished()
  {
    return $this->getIsPublished();
  }

  public function isNotPublished()
  {
    return !$this->isPublished();
  }

  public function publish()
  {
    $this->setIsPublished(true);
    $this->save();
  }

  public function unpublish()
  {
    $this->setIsPublished(false);
    $this->save();
  }
  

  public function getAuthorName()
  {
    $author = sfGuardUserPeer::retrieveByPK($this->getAuthor());
    return ($author) ? $author->getName() : '';

  }

  /*Interface implementation*/
  public static function getSlotletMethods()
  {
    return array("getSlotlet");
  }

  public static function getSlotletName()
  {
    return __("Calendario");
  }

  /**
   *  Returns a piece of HTML code holding the representation
   *  of the (Calendar) Slotlet for Event class.
   *  The current section will be obtained through the option
   *  parameter 'section_name'.
   *  The resulting HTML code should look like this:
   *
   *  <code>
   *    <div id="agenda" class="sl_agenda">
   *        <div id="calendardiv" class="content">Actual representation of the Agenda</div>
   *        <div class="footer"></div>
   *    </div>
   *  </code>
   *
   *  @param $options Array The options passed to the Slotlet.
   *
   *  @return string The HTML code of the Slotlet.
   */

  public static function getSlotlet($options)
  {
    sfLoader::loadHelpers(array('Javascript'));
    sfContext::getInstance()->getResponse()->addStylesheet('frontend/slotlet/sl_agenda');

    $options = array_merge(array('id' => 'agenda_'.time(), 'title' => __('Eventos'), 'container_slotlet_id' => null), $options);

    $user  = sfContext::getInstance()->getUser();
    $month = $user->getAttribute('events.'.$options['id'].'.month');
    $year  = $user->getAttribute('events.'.$options['id'].'.year');

    $collapse      = CmsConfiguration::get('check_collapse_calendar', false);
    $open_today    = CmsConfiguration::get('check_open_calendar_today', false);
    $show_calendar = CmsConfiguration::get('check_show_events_calendar_in_slotlet', true);

    $template = <<<EOF
<div id="%id%" class="sl_agenda">
  <div id="%id%_calendardiv" class="content">
    %calendar%
  </div>
  <div class="footer">
  </div>
</div>
EOF;

    return strtr($template, array(
      '%id%'       => $options['id'],
      '%calendar%' => self::getCalendarFor($month, $year, $collapse, $open_today, $show_calendar, $options['title'], $options['id'], $options['container_slotlet_id'])
    ));
  }
  /* End Interface implementation */

  public static function getCalendarHeader($months, $month, $year, $previous_month, $previous_year, $next_month, $next_year, $title, $id, $container_slotlet_id, $section_id = null, $add_go_back = false)
  {
    if (CmsConfiguration::get('check_collapse_calendar', false))
    {
      $str   = sprintf('<span id="%s_calendar_title_arrow" class="calendar_title_arrow">%s</span>', $id, $title);
      $title = link_to_function($str, "$('calendar_$id').toggle(); $('${id}_calendar_title_arrow').toggleClassName('calendar_title_arrow_up');");
    }

    $template = <<<EOF
<a class="rss_icon" href="%rss_url%"><img src="%rss_icon%" alt="%rss_alt%" title="%rss_title%" /></a>
<div class="title">
  <div class="arrows">
    %left_arrow% %month%
    <span class="calendar_year">-%year%</span>
    %right_arrow%
  </div>
  %title%
</div>
EOF;

    return strtr($template, array(
      '%left_arrow%'  => link_to_remote('&lt;', array(
                            'update'   => $id.'_calendardiv',
                            'url'      => sprintf('event/getCalendar?month=%d&year=%d&cls_id=%d&id=%s&ip=%d&_csrf_token=%s&section_id=%s', $previous_month, $previous_year, $container_slotlet_id, $id, $add_go_back,csrf_token(), $section_id),
                            'complete' => "EventsCalendar.setCurrent('$id', '').setOld('$id', '');",
                            'title'    => __('Mes anterior')
                         )),
      '%right_arrow%' => link_to_remote('&gt;', array(
                            'update'   => $id.'_calendardiv',
                            'url'      => sprintf('event/getCalendar?month=%d&year=%d&cls_id=%d&id=%s&ip=%d&_csrf_token=%s&section_id=%s', $next_month, $next_year, $container_slotlet_id, $id, $add_go_back, csrf_token(), $section_id),
                            'complete' => "EventsCalendar.setCurrent('$id', '').setOld('$id', '');",
                            'title'    => __('Mes siguiente')
                         )),
      '%month%'       => strtolower(substr($months[$month - 1], 0, 3)),
      '%year%'        => $year,
      '%title%'       => $title,
      '%rss_icon%'    => image_path('common/rss.png'),
      '%rss_url%'     => url_for('feed/calendar'),
      '%rss_title%'   => __('Canal RSS de eventos'),
      '%rss_alt%'     => __('RSS')
    ));
  }

  public static function getCalendarFor($monthNumber, $year, $collapsed = false, $open_today = false, $show_calendar = true, $title = '', $id = 'agenda', $container_slotlet_id = null, $section_id = null, $add_js = true, $add_go_back = false)
  {
    sfLoader::loadHelpers(array('I18N', 'Javascript','CmsCSRFToken'));

    if ($add_js)
    {
      sfContext::getInstance()->getResponse()->addJavascript('eventcalendar');
    }

    if (!$monthNumber) $monthNumber = intval(date("m"));
    if (!$year) $year = intval(date("Y"));

    $month      = new sfDate("$year-$monthNumber-01 00:00:00");
    $monthStart = $month->firstDayOfMonth()->get();
    $month      = new sfDate("$year-$monthNumber-01 23:59:59");
    $monthEnd   = $month->finalDayOfMonth()->get();

    $days       = EventPeer::groupEvents($year, $monthNumber, $monthStart, $monthEnd, $section_id);

    $culture     = sfContext::getInstance()->getUser()->getCulture();
    $userDateTimeFormat = sfDateTimeFormatInfo::getInstance(sfCultureInfo::getInstance($culture));
    $userDayInitials   = $userDateTimeFormat->getNarrowDayNames();

    $months    = $userDateTimeFormat->getMonthNames();
    $nextMonth = ($monthNumber % 12) + 1;
    $nextYear  = ($nextMonth == 1) ? $year + 1 : $year;
    $prevMonth = ($monthNumber > 1) ? $monthNumber - 1 : 12;
    $prevYear  = ($prevMonth == 12) ? $year - 1 : $year;

    $txt  = '<div class="calendar_in_place">';
    $txt .= self::getCalendarHeader($months, $monthNumber, $year, $prevMonth, $prevYear, $nextMonth, $nextYear, $title, $id, $container_slotlet_id, $section_id, $add_go_back);
    $txt .= self::getCalendarTable($year, $monthNumber, $days, $open_today, $userDayInitials, $collapsed, $id);
    $txt .= '</div>';

    $txt .= self::getCalendarEvents($days, $monthNumber, $id, $add_go_back);

    return $txt;
  }

  static public function getCalendarEvents($days, $month, $id, $add_go_back = false)
  {
    $events       = '';
    $i            = 0;
    $max_events   = intval(CmsConfiguration::get('max_events', false));
    $day_template = <<<EOF
<div class="%class%" id="%id%">
  <div class="datetype day-title">%day_title% %day_number%%go_back%</div>
  %events%
</div>
EOF;

    if ($month == intval(date('m')))
    {
      $today = intval(date('d'));
    }

    foreach ($days as $day)
    {
      $i++;

      if (empty($day))
      {
        continue;
      }

      // Sort the events in the current day by their starting hour in order to list them
      // in a sorted fashion.
      $starting_hours = array_map(create_function('$event', 'return $event->getStartingHour();'), $day);
      array_multisort($starting_hours, $day);

      $content      = '';
      $events_count = 0;

      foreach ($day as $event)
      {
        $events_count++;

        if ($max_events == 0 || $events_count <= $max_events)
        {
          $content .= '<div class="event">' . $event->getAgendaRepresentation() . '</div>';
        }
        else
        {
          $content .= sprintf('<div class="event %s" style="display: none;">%s</div>', self::getExtraEventsClass($id, $i), $event->getAgendaRepresentation());
        }
      }

      if ($max_events > 0 && $events_count > $max_events)
      {
        $content .= sprintf('<div class="more-information"><a href="#" onclick="EventsCalendar.toggleAll(\'%s\', \'%s\', this); return false;">%s</a></div>', $id, self::getExtraEventsClass($id, $i), __('Ver todos'));
      }

      $events_id = self::getEventsId($id, $i);

      $events .= strtr($day_template, array(
        '%class%'      => (isset($today) && $today == $i) ? 'visible' : 'hidden',
        '%id%'         => $events_id,
        '%day_title%'  => __('DÍA'),
        '%day_number%' => $i,
        '%events%'     => $content,
        '%go_back%'    => $add_go_back ? sprintf('<a href="#" onclick="EventsCalendar.toggle(\'%s\', \'%s\'); return false;" class="calendar_go_back">Volver</a>', $id, $events_id) : ''
      ));
    }

    return $events;
  }

  static protected function getEventsId($id, $day)
  {
    return sprintf('%s_events_%s', $id, $day);
  }

  static protected function getExtraEventsClass($id, $day)
  {
    return sprintf('%s_extra_events_%s', $id, $day);
  }

  static public function getCalendarTable($year, $month_number, $days, $open_today, $user_day_initials, $collapsed, $id)
  {
    $template = <<<EOF
<table id="calendar_%id%" class="calendar-table" style="%style%">
  <tr>
    %days_initials%
  </tr>
  %days%
</table>
%js%
EOF;

    //initial offset
    $enDTF                 = sfDateTimeFormatInfo::getInstance(sfCultureInfo::getInstance('en'));
    $enDayInitials         = $enDTF->getNarrowDayNames();
    $month                 = new sfDate("$year-$month_number-01 00:00:00");
    $firstDayOfMonthOffset = intval($month->firstDayOfMonth()->format('w'));
    $javascript            = '';

    $weekday = 0;

    $days_text = '<tr>';
    for ($j = $weekday; $j < $firstDayOfMonthOffset; $j++)
    {
      $days_text .= '<td></td>';
    }

    // If today's events should be shown by default, and the calendar is to show the events
    // for the current year and month, set it to do so:
    if ($year == intval(date('Y')) && $month_number == intval(date('m')))
    {
      $today = intval(date('d'));

      if ($open_today)
      {
        $current_event = (empty($days[$today]) ? '' : self::getEventsId($id, $today));

        $javascript = javascript_tag("EventsCalendar.setCurrent('$id', '$current_event');");
      }
    }
    else
    {
      $javascript = '';

      $today = false;
    }

    $i       = 1;
    $weekday = $firstDayOfMonthOffset;

    foreach ($days as $day)
    {
      if ($weekday == 0)
      {
        $days_text .= '<tr>';
      }

      if (!empty($day))
      {
        $events_class =
        $events_id    = self::getEventsId($id, $i);

        $days_text .= sprintf('<td class="event-highlighted%s">%s</td>',
          ($today && $today == $i) ? ' today' : '',
          link_to_function($i, "EventsCalendar.toggle('$id', '$events_id')")
        );
      }
      else
      {
        $days_text .= sprintf('<td%s>%s</td>',
          ($today && $today == $i) ? ' class="today"' : '',
          $i
        );
      }

      $i++;
      $weekday++;

      if ($weekday == 7)
      {
        $weekday = 0;

        $days_text .= '</tr>';
      }
    }

    if ($weekday > 0)
    {
      for ($j = $weekday; $j < 7; $j++)
      {
        $days_text .= '<td></td>';
      }

      $days_text .= '</tr>';
    }

    return strtr($template, array(
      '%id%'            => $id,
      '%style%'         => $collapsed ? 'display: none;' : '',
      '%days_initials%' => implode('', array_map(create_function('$i', 'return "<th>$i</th>";'), $user_day_initials)),
      '%days%'          => $days_text,
      '%js%'            => $javascript
    ));
  }

  public function getAgendaRepresentation()
  {
    $txt  = sprintf("<div class=\"datetype\">%s</div>", ($this->getEventType()) ? $this->getEventType()->__toString() : '');
    $txt .= sprintf("<div class=\"hour\">%s</div>", $this->getStartingHour());
    $txt .= sprintf("<div class=\"name\">%s</div>", $this->getTitle());
    $txt .= sprintf("<div class=\"address\">%s", $this->getLocation());
    if ($this->hasArticle())
    {
      $article = $this->getArticle();
      $txt .= "&nbsp;" . link_to('[+]', $article->getURLReference(), array('class' => 'more'));
    }
    $txt .= "</div>";

    return $txt;
  }

  public function getStartingHour()
  {
    return $this->getBeginsAt('H:i');
  }

  public function hasArticle()
  {
    $article_id = $this->getArticleId();

    return (!empty($article_id));
  }

  public function getTimestampForRssFeed()
  {
    return null;
  }

  public function getHeadingForRssFeed()
  {
    return sprintf('%s - %s. %s', $this->getBeginsAt('d/m/Y H:i'), $this->getEndsAt('d/m/Y H:i'), $this->getTitle(), $this->getDescription());
  }

  public function getGuidForRssFeed()
  {
    return sprintf('%s | %s: %s', $this->getBeginsAt('d/m/Y'), $this->getEventType(), $this->getTitle());
  }

  public function getLinkForRssFeed($default = '@homepage')
  {
    return ($this->hasArticle() ? $this->getArticle()->getURLReference() : $default);
  }

  public function getRssFeedEnclosure()
  {
    return ($this->hasArticle() ? $this->getArticle()->getRssFeedEnclosure() : null);
  }

  /**
   * Answer whether this event has any related section.
   *
   * @param  PDO $con  Optional PDO object.
   * 
   * @return bool
   */
  public function hasSections($con = null)
  {
    return ($this->countEventSections(null, null, $con) > 0);
  }

  /**
   * Get the sections related to this event.
   * 
   * @param  PDO $con  Optional PDO object.
   *
   * @return Section[]
   */
  public function getSections($con = null)
  {
    $sections = array();

    foreach ($this->getEventSectionsJoinSection(null, $con) as $event_section)
    {
      $sections[] = $event_section->getSection($con);
    }

    return $sections;
  }


  public function getUpdatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByUpdatedBy();

    return ($author) ? $author->getName() : '';
  }

	public function hasMultimedia()
	{
		return !is_null($this->getMultimedia());
	}
}