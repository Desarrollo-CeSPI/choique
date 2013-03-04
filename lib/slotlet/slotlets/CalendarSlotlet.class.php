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
 * Calendar slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class CalendarSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array('slotlets/eventcalendar.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_agenda.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'id'                   => 'slotlet_agenda_'.(time() % 50 + 1),
      'class'                => 'sl_agenda',
      'title'                => __('Eventos'),
      'container_slotlet_id' => null,
      'collapse_calendar'    => CmsConfiguration::get('check_collapse_calendar', false),
      'open_today'           => CmsConfiguration::get('check_open_calendar_today', false),
      'show_calendar'        => CmsConfiguration::get('check_show_events_calendar_in_slotlet', true),
      'maximum_events'       => intval(CmsConfiguration::get('max_events', false)),
      'user'                 => sfContext::getInstance()->getUser(),
      'section_name'         => sfContext::getInstance()->getRequest()->getParameter('section_name', SectionPeer::retrieveHomeSection()->getName()),
      'use_all_sections'     => true,
      'toggle_in_place'      => false
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Javascript','CmsCSRFToken'));

    $user  = $options['user'];
    $month = $user->getAttribute('events.'.$options['id'].'.month');
    $year  = $user->getAttribute('events.'.$options['id'].'.year');

    // Sanitize maximum events options
    $options['maximum_events'] = intval($options['maximum_events']);

    $template = <<<EOF
<div id="%id%" class="slotlet %class%">
  <div id="%id%_calendardiv" class="content">
    %calendar%
  </div>
  <div class="footer">
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
EventsCalendar.calendar = jQuery('#%id%_calendardiv');
EventsCalendar.toggle_in_place = %toggle_in_place%;
//]]>
</script>
EOF;

    return strtr($template, array(
      '%id%'              => $options['id'],
      '%class%'           => $options['class'],
      '%calendar%'        => $this->renderCalendar($month, $year, $options),
      '%toggle_in_place%' => $options['toggle_in_place'] ? 'true' : 'false'
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra en un calendario los eventos publicados organizados mes a mes.';
  }

  public static function getName()
  {
    return 'Calendario';
  }

  protected function getCurrentSectionId($options)
  {
    $section_id = null;

    if (!$options['use_all_sections'] && '' !== trim($options['section_name']))
    {
      $section = SectionPeer::retrieveByName($options['section_name']);

      if (null !== $section)
      {
        $section_id = $section->getId();
      }
    }

    return $section_id;
  }

  protected function renderCalendar($month_number, $year, $options)
  {
    if (!$month_number)
    {
      $month_number = intval(date('m'));
    }

    if (!$year)
    {
      $year = intval(date('Y'));
    }

    $month       = new sfDate("$year-$month_number-01 00:00:00");
    $month_start = $month->firstDayOfMonth()->get();
    $month       = new sfDate("$year-$month_number-01 23:59:59");
    $month_end   = $month->finalDayOfMonth()->get();
    $section_id  = $this->getCurrentSectionId($options);
    $days        = EventPeer::groupEvents($year, $month_number, $month_start, $month_end, $section_id);

    $culture           = sfContext::getInstance()->getUser()->getCulture();
    $user_dt_format    = sfDateTimeFormatInfo::getInstance(sfCultureInfo::getInstance($culture));
    $user_day_initials = $user_dt_format->getNarrowDayNames();

    $months         = $user_dt_format->getMonthNames();
    $next_month     = ($month_number % 12) + 1;
    $next_year      = ($next_month == 1) ? $year + 1 : $year;
    $previous_month = ($month_number > 1) ? $month_number - 1 : 12;
    $previous_year  = ($previous_month == 12) ? $year - 1 : $year;

    $template = <<<SLOTLET
<div class="calendar_in_place">
  %header%
  %calendar%
</div>
%events%
SLOTLET;

    return strtr($template, array(
      '%header%'   => $this->renderHeader($months, $month_number, $year, $previous_month, $previous_year, $next_month, $next_year, $options),
      '%calendar%' => $this->getCalendarTable($year, $month_number, $days, $user_day_initials, $options),
      '%events%'   => $this->renderEvents($days, $month_number, $options)
    ));
  }

  public function renderHeader($months, $month, $year, $previous_month, $previous_year, $next_month, $next_year, $options)
  {
    if ($options['collapse_calendar'])
    {
      $title = link_to_function(sprintf('<span id="%s_calendar_title_arrow" class="calendar_title_arrow">%s</span>', $options['id'], $options['title']), strtr("$('calendar_%id%').toggle(); $('%id%_calendar_title_arrow').toggleClassName('calendar_title_arrow_up');", array('%id%' => $options['id'])));
    }
    else
    {
      $title = $options['title'];
    }

    $template = <<<SLOTLET
<a class="rss_icon" href="%rss_url%"><img src="%rss_icon%" alt="%rss_alt%" title="%rss_title%" /></a>
<div class="title">
  <div class="arrows">
    %left_arrow% %month%
    <span class="calendar_year">-%year%</span>
    %right_arrow%
  </div>
  %title%
</div>
SLOTLET;

    return strtr($template, array(
      '%left_arrow%'  => link_to_remote('&lt;', array(
                            'update'   => $options['id'].'_calendardiv',
                            'url'      => sprintf('event/getCalendar?month=%d&year=%d&cls_id=%d&id=%s&ip=%d&_csrf_token=%s&section_id=%s', $previous_month, $previous_year, $options['container_slotlet_id'], $options['id'], $options['toggle_in_place'], csrf_token(), $this->getCurrentSectionId($options)),
                            'complete' => "EventsCalendar.setCurrent('".$options['id']."', '').setOld('".$options['id']."', '');",
                            'title'    => __('Mes anterior')
                         )),
      '%right_arrow%' => link_to_remote('&gt;', array(
                            'update'   => $options['id'].'_calendardiv',
                            'url'      => sprintf('event/getCalendar?month=%d&year=%d&cls_id=%d&id=%s&ip=%d&_csrf_token=%s&section_id=%s', $next_month, $next_year, $options['container_slotlet_id'], $options['id'], $options['toggle_in_place'], csrf_token(), $this->getCurrentSectionId($options)),
                            'complete' => "EventsCalendar.setCurrent('".$options['id']."', '').setOld('".$options['id']."', '');",
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

  protected function renderEvents($days, $month, $options)
  {
    $events       = '';
    $i            = 0;
    $day_template = <<<TEMPLATE
<div class="%class%" id="%id%">
  <div class="datetype day-title">%day_title% %day_number%%go_back%</div>
  %events%
</div>
TEMPLATE;

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

        if ($options['maximum_events'] == 0 || $events_count <= $options['maximum_events'])
        {
          $content .= '<div class="event">' . $event->getAgendaRepresentation() . '</div>';
        }
        else
        {
          $content .= sprintf('<div class="event %s" style="display: none;">%s</div>', $this->generateExtraEventsClass($options['id'], $i), $event->getAgendaRepresentation());
        }
      }

      if ($options['maximum_events'] > 0 && $events_count > $options['maximum_events'])
      {
        $content .= sprintf('<div class="more-information"><a href="#" onclick="EventsCalendar.toggleAll(\'%s\', \'%s\', this); return false;">%s</a></div>', $options['id'], $this->generateExtraEventsClass($options['id'], $i), __('Ver todos'));
      }

      $events_id = $this->generateEventsId($options['id'], $i);
      
      $events .= strtr($day_template, array(
        '%class%'      => (isset($today) && $today == $i && $options['open_today']) ? 'visible' : 'hidden',
        '%id%'         => $events_id,
        '%day_title%'  => __('DÍA'),
        '%day_number%' => $i,
        '%events%'     => $content,
        '%go_back%'    => $options['toggle_in_place'] ? link_to_function(__('Volver'), "EventsCalendar.toggle('".$options['id']."', '$events_id');", array('class' => 'calendar_go_back')) : ''
      ));
    }

    return $events;
  }

  protected function generateEventsId($id, $day)
  {
    return sprintf('%s_events_%s', $id, $day);
  }

  protected function generateExtraEventsClass($id, $day)
  {
    return sprintf('%s_extra_events_%s', $id, $day);
  }

  protected function getCalendarTable($year, $month_number, $days, $user_day_initials, $options)
  {
    if (false == $options['show_calendar'])
    {
      return '';
    }

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
    $en_dt_format     = sfDateTimeFormatInfo::getInstance(sfCultureInfo::getInstance('en'));
    $en_day_initials  = $en_dt_format->getNarrowDayNames();
    $month            = new sfDate("$year-$month_number-01 00:00:00");
    $first_dom_offset = intval($month->firstDayOfMonth()->format('w')); // first day of month offset
    $javascript       = '';

    $weekday = 0;

    $days_text = '<tr>';
    for ($j = $weekday; $j < $first_dom_offset; $j++)
    {
      $days_text .= '<td></td>';
    }

    // If today's events should be shown by default, and the calendar is to show the events
    // for the current year and month, set it to do so:
    if ($year == intval(date('Y')) && $month_number == intval(date('m')))
    {
      $today = intval(date('d'));

      if ($options['open_today'])
      {
        $current_event = (empty($days[$today]) ? '' : $this->generateEventsId($options['id'], $today));
        $javascript    = javascript_tag("EventsCalendar.setCurrent('".$options['id']."', '$current_event');");
      }
    }
    else
    {
      $javascript = '';
      $today      = false;
    }

    $i       = 1;
    $weekday = $first_dom_offset;

    foreach ($days as $day)
    {
      if ($weekday == 0)
      {
        $days_text .= '<tr>';
      }

      if (!empty($day))
      {
        $events_class = // ???
        $events_id    = $this->generateEventsId($options['id'], $i);

        $days_text .= sprintf('<td class="event-highlighted%s">%s</td>',
          ($today && $today == $i) ? ' today' : '',
          link_to_function($i, "EventsCalendar.toggle('".$options['id']."', '$events_id')")
        );
      }
      else
      {
        $days_text .= sprintf('<td%s>%s</td>', ($today && $today == $i) ? ' class="today"' : '', $i);
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
      '%id%'            => $options['id'],
      '%style%'         => $options['collapse_calendar'] ? 'display: none;' : '',
      '%days_initials%' => implode('', array_map(create_function('$i', 'return "<th>$i</th>";'), $user_day_initials)),
      '%days%'          => $days_text,
      '%js%'            => $javascript
    ));
  }

  public function getConfigurationForm($values = array())
  {
    $row = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';

    $labels = array(
      'class'             => __('Clase CSS'),
      'title'             => __('Título'),
      'collapse_calendar' => __('Hacer ocultable el calendario'),
      'open_today'        => __('Desplegar día actual'),
      'show_calendar'     => __('Mostrar calendario'),
      'maximum_events'    => __('Máximo de eventos por día'),
      'use_all_sections'  => __('Mostrar eventos de cualquier sección'),
      'toggle_in_place'   => __('Ocultar calendario al desplegar eventos')
    );

    $form          = '';
    $validation_js = "if (!(/^\d+$/.match(jQuery(this).val()))) { alert('Por favor ingrese un valor numérico.'); jQuery(this).val(".$values['maximum_events']."); return false; }";

    foreach (array('title', 'maximum_events', 'class') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option', 'onchange' => ($key == 'maximum_events' ? $validation_js : '')))
      ));
    }

    foreach (array('collapse_calendar', 'open_today', 'show_calendar', 'use_all_sections', 'toggle_in_place') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

}