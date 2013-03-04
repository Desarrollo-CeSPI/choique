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
 * Multimedia slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class MultimediaSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'         => __('Clase CSS'),
      'multimedia_id' => __('Contenido multimedial'),
      'size'          => __('Tamaño')
    );

    $sizes = array(
      's' => 'Pequeño',
      'm' => 'Mediano',
      'n' => 'Grande'
    );

    $form = strtr($row, array(
      '%id%'    => 'multimedia_id',
      '%label%' => $labels['multimedia_id'],
      '%field%' => select_tag('multimedia_id', objects_for_select(MultimediaPeer::retrieveAll(), 'getId', '__toString', $values['multimedia_id']), array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'size',
      '%label%' => $labels['size'],
      '%field%' => select_tag('size', options_for_select($sizes, $values['size']), array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'         => 'slotlet_multimedia',
      'multimedia_id' => null,
      'size'          => 'm'
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
    $template = '<div class="slotlet %class%">%multimedia%</div>';

    return strtr($template, array(
      '%class%'      => $options['class'],
      '%multimedia%' => $this->renderMultimedia($options['multimedia_id'], $options['size'])
    ));
  }

  protected function renderMultimedia($id, $size)
  {
    $multimedia = MultimediaPeer::retrieveByPK($id);

    if (null === $multimedia)
    {
      return '';
    }
    else
    {
      return $multimedia->getHTMLRepresentation($size);
    }
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra un contenido multimedial fijo.';
  }

  public static function getName()
  {
    return 'Contenido multimedial';
  }
  
}