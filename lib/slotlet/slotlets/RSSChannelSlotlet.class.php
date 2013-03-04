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
 * RSS Channel slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class RSSChannelSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_feed.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'            => 'slotlet_feed',
      'title'            => __('Canal RSS'),
      'show_when_empty'  => true,
      'section_name'     => sfContext::getInstance()->getRequest()->getParameterHolder()->get('section_name', 'NONE'),
      'rss_channel_id'   => null,
      'visible_elements' => 5
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Url'));

    $rss_channel = RssChannelPeer::retrieveByPK($options['rss_channel_id']);
    $title       = $options['title'];

    if (null == $rss_channel)
    {
      if (false === (bool) $options['show_when_empty'])
      {
        return null;
      }
      else
      {
        $id    = time() % 50 + 1;
        $url   = '#';
      }
    }
    else
    {
      $id  = $rss_channel->getId();
      $url = url_for('@rss_view_more?nombre='.sfInflector::underscore($title).'&id='.$id.'&section='.$options['section_name']);
    }

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  <div class="title">%title%</div>
  <div class="content">
    %content%
  </div>
  <div class="footer">
  </div>
  <a href="%url%" class="feed_view_more">%more%</a>
</div>
SLOTLET;

    return strtr($template, array(
      '%id%'      => 'slotlet_feed_'.sfInflector::underscore($title).'_'.$id,
      '%class%'   => $options['class'],
      '%title%'   => $title,
      '%content%' => $this->getContent($rss_channel, $options),
      '%url%'     => $url,
      '%more%'    => __('Ver más')
    ));
  }

  /**
   * Get the HTML 'content' part for this slotlet.
   *
   * @param  RssChannel $rss_channel
   * @param  arrauy     $options
   *
   * @return string
   */
  protected function getContent($rss_channel, $options)
  {
    if (null == $rss_channel)
    {
      return __('Sin contenidos para mostrar');
    }

    $row_template = '<div class="content-child%first%"><a href="%url%" target="_blank">%content%</a></div>';
    $visible = is_numeric($options['visible_elements']) ? intval($options['visible_elements']) : 5;

    try
    {
      $reader  = $rss_channel->getReader();
      $items   = $reader->getItems();
      $count   = min(count($items), $visible);
      $content = '';

      for ($i = 0; $i < $count; $i++)
      {
        $content .= strtr($row_template, array('%content%' => $items[$i]->getTitle(), '%url%' => $items[$i]->getLink(), '%first%' => $i == 0 ? ' first' : ''));
      }
    }
    catch (Exception $e)
    {
      $content = __('Sin contenidos para mostrar');
    }

    return $content;
  }

  static public function getDescription()
  {
    return 'Lector de canal de noticias RSS.';
  }

  static public function getName()
  {
    return 'Canal RSS';
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'            => __('Clase CSS'),
      'title'            => __('Título'),
      'show_when_empty'  => __('Mostrar si vacío'),
      'rss_channel_id'   => __('Canal RSS'),
      'visible_elements' => __('Elementos visibles')
    );

    $form = strtr($row, array(
      '%id%'    => 'rss_channel_id',
      '%label%' => $labels['rss_channel_id'],
      '%field%' => select_tag('rss_channel_id', objects_for_select(RssChannelPeer::retrieveAll(), 'getId', '__toString', $values['rss_channel_id']), array('class' => 'slotlet_option'))
    ));

    foreach (array('title', 'class', 'visible_elements') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    $form .= strtr($row, array(
      '%id%'    => 'show_when_empty',
      '%label%' => $labels['show_when_empty'],
      '%field%' => checkbox_tag('show_when_empty', true, $values['show_when_empty'] != false, array('class' => 'slotlet_option'))
    ));

    return $form;
  }

}