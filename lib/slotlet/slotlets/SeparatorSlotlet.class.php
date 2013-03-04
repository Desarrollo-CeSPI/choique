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
 * Separator slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SeparatorSlotlet implements ISlotlet
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
      'id'     => 'separator_slotlet_'.time(),
      'class'  => 'sl_separator',
      'height' => CmsConfiguration::get('separator_default_height', 5)
    );
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%" style="height: %height%px;">
  &nbsp;
</div>
SLOTLET;

    return strtr($template, array(
      '%id%'     => $options['id'],
      '%class%'  => $options['class'],
      '%height%' => $options['height']
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet para separar visualmente otros slotlets';
  }

  public static function getName()
  {
    return 'Separador';
  }
  
  public function getConfigurationForm($values = array())
  {
    $row  = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $form = strtr($row, array(
      '%id%'    => 'height',
      '%label%' => __('Altura (px)'),
      '%field%' => input_tag('height', $values['height'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    return $form;
  }

}