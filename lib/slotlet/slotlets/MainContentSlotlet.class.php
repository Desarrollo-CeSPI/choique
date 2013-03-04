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
 * Main content slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class MainContentSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_main_content.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'content' => '&nbsp;',
      'class'   => 'slotlet_main_content'
    );
  }

  public function render($options = array())
  {
    return strtr('<div class="slotlet %class%">%content%</div>', array(
      '%class%'   => $options['class'],
      '%content%' => $options['content']
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet en el que se mostrará el contenido principal del sitio: artículo, portada, resultados de búsqueda, etc.';
  }

  public static function getName()
  {
    return 'Contenido principal';
  }
  
  public function getConfigurationForm($values = array())
  {
    $row = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';

    return strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));
  }

}