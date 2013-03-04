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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RssChannelWithMultimediaSlotlet
 *
 * @author gramirez
 */
class RssChannelWithMultimediaSlotlet implements ISlotlet
{
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

  public function getDefaultOptions()
  {
    return array(
      'class'            => 'rm_slotlet_feed',
      'title'            => __('Canal RSS'),
      'show_when_empty'  => true,
      'section_name'     => sfContext::getInstance()->getRequest()->getParameterHolder()->get('section_name', 'NONE'),
      'rss_channel_id'   => null,
      'visible_elements' => 2
    );
  }

  public function getJavascripts()
  {
    return array('slotlets/multimedia_rss.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/multimedia_rss.css');
  }

  public function render($options = array())
  {
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
        $id  = time() % 50 + 1;
        $url = '#';

        $template = <<<SLOTLET
        <div class="slotlet %class%">
          <div class="image_thumb">
            <ul>
              <li>%content%</li>
            </ul>
          </div>
        </div>
SLOTLET;

        return strtr($template, array(
          '%content%' => __('Sin contenidos para mostrar'),
          '%class%'   => $options['class']
        ));
      }
    }
    else
    {
      $id  = $rss_channel->getId();
      $url = url_for('@rss_view_more?nombre='.sfInflector::underscore($title).'&id='.$id.'&section='.$options['section_name']);
    }

    $feed       = $rss_channel->getReader();
    $feed_items = $feed->getItems();

    $template = <<<SLOTLET
    <div id="%id%" class="slotlet %class%">
      <div class="image_thumb">
        <ul>
          %rss%
        </ul>
      </div>
      <div class="main_image">
        <a href="%url%" ><img src="%image%" alt="%image_alt%"></a>
        <div class="desc">
          <div class="block" style="opacity: 0.75; margin-bottom: 0px;">
            <a class="rm_feed_view_more" href="%url%"><h2>1. %title%</h2></a>
            <p>%description%</p>
          </div>
        </div>
      </div>
    </div>
SLOTLET;

    $template = strtr($template, array(
        '%title%'   => $feed_items[0]->getTitle(),
        '%image%'   => ($feed_items[0]->getEnclosure())? $feed_items[0]->getEnclosure()->getUrl() : 'no-image' ,
        '%description%' => $feed_items[0]->getDescription(),        
        '%id%'      => 'slotlet_feed_'.sfInflector::underscore($title).'_'.$id,
        '%class%'   => $options['class'],
        '%url%' => $feed_items[0]->getLink()
      ));

    $rss = '';
    $count = min($options['visible_elements'], count($feed_items));
    for ($i = 0; $i <  $count; $i++)
    {      
      $rss_i = <<<SLOTLET
          <li class="%feed_class%">
            <a href="%image%" style="display:none;" class='image_name'></a>
            <div class="block">
              <a href="%url%" class="rm_feed_view_more"><h2>%title%</h2></a>
              <p>%description%</p>
            </div>
          </li>
          <style>
            div.image_thumb ul li.active.%feed_class% {
              background: url('%arrow_rss_image%') 95% 50% no-repeat %section_color% !important;
            }

            .main_image.%feed_class% {
              border-left: 3px solid %section_color% !important;
            }
          </style>
SLOTLET;
      $feed_categories = $feed_items[$i]->getCategories();
      $rss .= strtr($rss_i, array(
        '%feed_class%' => 'feed_item_'.$i,
        '%title%'   => $feed_items[$i]->getTitle(),
        '%image%'   => ($feed_items[$i]->getEnclosure())? $feed_items[$i]->getEnclosure()->getUrl() : 'no-image' ,
        '%description%' => $feed_items[$i]->getDescription(),
        '%url%' => $feed_items[$i]->getLink(),
        '%section_color%' => $feed_categories[0],
        '%arrow_rss_image%' => image_path('frontend/arrow_rss.png')
      ));
    }   

    return strtr($template, array(
        '%rss%' => $rss
    ));
  }

  public static function getDescription()
  {
    return 'Lector de RSS que incluye las imágenes obtenidas del canal RSS.';
  }

  public static function getName()
  {
    return 'Canal RSS con imágenes';
  }

}