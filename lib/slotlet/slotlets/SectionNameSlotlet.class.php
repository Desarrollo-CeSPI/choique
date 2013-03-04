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
 * Section name slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SectionNameSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_section_name.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'          => 'section_name_slotlet',
      'use_fl_section' => false,
      'section_name'   => sfContext::getInstance()->getRequest()->getParameter('section_name')
    );
  }

  public function render($options = array())
  {
    $section = $this->getSection($options['section_name'], $options['use_fl_section']);
    
    if (null === $section)
    {
      return;
    }

    $template = <<<SLOTLET
<div class="slotlet %class%">
  %section%
</div>
SLOTLET;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%section%' => $section->getTitle()
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el nombre de la sección actual.';
  }

  public static function getName()
  {
    return 'Nombre de sección';
  }
  
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class' => __('Clase CSS'),
      'use_fl_section' => __('Usar sección de primer nivel')
    );

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'use_fl_section',
      '%label%' => $labels['use_fl_section'],
      '%field%' => checkbox_tag('use_fl_section', true, $values['use_fl_section'] != false, array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  protected function getSection($name, $first_level = false)
  {
    $section = SectionPeer::retrieveByName($name);

    if ($first_level)
    {
      $section = $section->getFirstLevelSection();
    }

    return $section;
  }
  
}