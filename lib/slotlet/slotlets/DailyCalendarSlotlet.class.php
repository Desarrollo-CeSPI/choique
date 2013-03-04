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
 * Daily calendar slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class DailyCalendarSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_daily_calendar.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'           => 'daily_calendar_slotlet',
      'title'           => __('Agenda'),
      'maximum_events'  => intval(CmsConfiguration::get('max_events', false)),
      'link_text'       => __('Ver agenda completa'),
      'show_when_empty' => false,
      'content_when_empty'    => __('Sin eventos para hoy'),
      'include_date_on_title' => true
    );
  }

  public function render($options = array())
  {
    $events = EventPeer::retrieveTodaysEvents();

    if (0 == count($events) && false != $options['show_when_empty'])
    {
      return;
    }

    // Sanitize maximum events options
    $options['maximum_events'] = intval($options['maximum_events']);

    $template = <<<EOF
<div class="slotlet %class%">
  <div class="da_title">
    %title%
  </div>
  <div class="da_content">
    <div class="da_subtitle">
    </div>
    <div class="da_events">
      %events%
    </div>
    <div class="da_footer">
      %link%
    </div>
  </div>
</div>
EOF;

    return strtr($template, array(
      '%class%'  => $options['class'],
      '%title%'  => $this->renderTitle($options),
      '%events%' => $this->renderEvents($events, $options),
      '%link%'   => $this->renderLink($options)
    ));
  }

  protected function renderLink($options)
  {
    return strtr('<a href="%url%" class="da_search">%text%</a>', array(
      '%url%'  => url_for('@events_search'),
      '%text%' => $options['link_text']
    ));
  }

  protected function renderTitle($options)
  {
    $title = $options['title'];

    if (false != $options['include_date_on_title'])
    {
      $title .= ' - '.date('d/m/Y');
    }

    return $title;
  }

  protected function renderEvents(array $events, $options)
  {
    if ($options['maximum_events'] > 0 && count($events) > $options['maximum_events'])
    {
      $events = array_slice($events, 0, $options['maximum_events']);
    }

    $first = true;
    $html  = '';

    foreach ($events as $event)
    {
      $html .= $this->renderEvent($event, $options, $first);
      $first = false;
    }

    if ('' == trim($html))
    {
      $html = $options['content_when_empty'];
    }

    return $html;
  }

  protected function renderEvent(Event $event, $options, $first)
  {
    $template = <<<EVENT
<div class="da_event%first%">
  <div class="da_event_type">%type% / %time% hs.</div>
  <div class="da_event_title">%title%</div>
  <div class="da_event_location">%location%</div>
</div>
EVENT;

    return strtr($template, array(
      '%type%'     => $event->getEventType(),
      '%time%'     => $event->getStartingHour(),
      '%title%'    => $event->getTitle(),
      '%location%' => $event->getLocation(),
      '%first%'    => $first ? ' first' : ''
    ));
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'                 => __('Clase CSS'),
      'title'                 => __('Título'),
      'include_date_on_title' => __('Incluir fecha en título'),
      'maximum_events'        => __('Máximo de eventos por día'),
      'link_text'             => __('Texto acceso a agenda completa'),
      'show_when_empty'       => __('Mostrar si vacío'),
      'content_when_empty'    => __('Contenido cuando vacío')
   );

    $form          = '';
    $validation_js = "if (!(/^\d+$/.match(jQuery(this).val()))) { alert('Por favor ingrese un valor numérico.'); jQuery(this).val(".$values['maximum_events']."); return false; }";

    foreach (array('title', 'maximum_events', 'class', 'link_text', 'content_when_empty') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option', 'onchange' => ($key == 'maximum_events' ? $validation_js : '')))
      ));
    }

    foreach (array('include_date_on_title', 'show_when_empty') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra los eventos del día actual, con la posibilidad de acceder al buscador de eventos.';
  }

  public static function getName()
  {
    return 'Agenda diaria';
  }

}