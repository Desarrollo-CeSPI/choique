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
 * Footer slotlet
 *
 * @author ncuesta
 */
class FooterSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row  = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'id'    => 'footer',
      'class' => 'slotlet_footer'
    );
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array();
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %content%
</div>
SLOTLET;

    return strtr($template, array(
      '%id%'      => $options['id'],
      '%class%'   => $options['class'],
      '%content%' => CmsConfiguration::get('footer')
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra un pie de página para el sitio.';
  }

  public static function getName()
  {
    return 'Pie de página';
  }
  
}