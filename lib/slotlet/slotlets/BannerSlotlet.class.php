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
 * Banner slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BannerSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array();
  }

  public function getDefaultOptions()
  {
    return array(
      'class'        => 'slotlet_banner',
      'section_name' => ''
    );
  }

  public function render($options = array())
  {
    $content  = '';
    $template = '<div class="slotlet %class%">%banner%</div>';

    return strtr($template, array(
      '%class%'  => $options['class'],
      '%banner%' => Section::getBannerByName($options['section_name'])
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el contenido multimedial asociado a la sección en la que se encuentre el navegante del sitio.';
  }

  public static function getName()
  {
    return 'Banner';
  }

  protected function getMultimedia($section_name)
  {
    $section = SectionPeer::retrieveByName($section_name);

    return (null !== $section ? $section->getMultimedia() : null);
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