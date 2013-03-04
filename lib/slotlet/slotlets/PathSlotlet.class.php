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
 * Path slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class PathSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_path.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'        => 'path',
      'force_home'   => false,
      'section_name' => ''
    );
  }

  public function render($options = array())
  {
    $template = '<div class="slotlet %class%">%path%</div>';

    return strtr($template, array(
      '%class%' => $options['class'],
      '%path%'  => Section::getPath($options['section_name'], (bool) $options['force_home'])
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra la ruta actual (en el arbol de secciones) en donde se encuentra el navegante del sitio.';
  }

  public static function getName()
  {
    return 'Ruta';
  }

  public function getConfigurationForm($values = array())
  {
    $row = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';

    $html = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $html .= strtr($row, array(
      '%id%'    => 'force_home',
      '%label%' => __('Forzar sección inicio'),
      '%field%' => checkbox_tag('force_home', true, $values['force_home'] != false, array('class' => 'slotlet_option'))
    ));

    return $html;
  }

}