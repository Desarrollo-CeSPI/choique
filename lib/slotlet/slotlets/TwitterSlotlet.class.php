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
 * Twitter slotlet
 *
 * @author Christian A. Rodriguez
 */
class TwitterSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array('slotlets/jquery.tweet.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/jquery.tweet.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'             => 'slotlet_twitter',
      'title'             => __('Twitter'),
      'show_title'        => true,
      'username'          => null,
      'count'             => 4,
      'avatar_size'       => 48,
      'auto_refresh'      => false, 
      'refresh_interval'  => 60,
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Url'));

    $id  = time() % 50 + 1;
    $id = 'slotlet_twitter_'.$id;
    $title_template = <<<TEMPLATE
  <div class="title">
    %title%
  </div>
TEMPLATE;

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %title%
  <div class="content">
  </div>
  %script%
</div>
SLOTLET;
    
    return strtr($template, array(
      '%id%'      => $id,
      '%class%'   => $options['class'],
      '%title%'   => false != $options['show_title'] ? strtr($title_template, array('%title%' => $options['title'])) : '',
      '%script%' => $this->getScript($id, $options),
    ));
  }

  public function getScript($id, $options)
  {
    $script= <<<SCRIPT
jQuery(function($){
        $("#%id% .content").tweet({
          join_text: "auto",
          username: %username%,
          avatar_size: %avatar_size%,
          count: %count%,
          loading_text: "Cargando...",
          template: "{avatar}{text}",
          refresh_interval: %refresh_interval%
        });
      });
SCRIPT;
    $username = explode(',', $options['username']);
    $username = count($username) == 0? 'choique': ((count($username) > 1)? json_encode($username): "'".array_shift($username)."'");
    return javascript_tag(strtr($script, array(
      '%id%'                =>  $id,
      '%username%'          =>  $username,
      '%avatar_size%'       =>  $options['avatar_size'],
      '%count%'             =>  $options['count'],
      '%refresh_interval%'  =>  $options['auto_refresh']?$options['refresh_interval']:'null',
    )));
  }

  static public function getDescription()
  {
    return 'Cliente de Twitter';
  }

  static public function getName()
  {
    return 'Cliente Twitter';
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'             => __('Clase CSS'),
      'title'             => __('Título'),
      'show_title'        => __('Mostrar título'),
      'username'          => __('Usuario Twitter. Varios separados por coma'),
      'count'             => __('Elementos visibles'),
      'avatar_size'       => __('Tamaño del avatar'),
      'auto_refresh'      => __('Refresco automático?'),
      'refresh_interval'  => __('Intervalo de refresco en segundos'),
    );

    $form = strtr($row, array(
        '%id%'    => 'username',
        '%label%' => $labels['username'],
        '%field%' => input_tag('username', $values['username'], array('class' => 'slotlet_option'))
      ));

    foreach (array('show_title', 'auto_refresh' ) as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    foreach (array('title', 'class', 'count', 'avatar_size', 'refresh_interval' ) as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

}