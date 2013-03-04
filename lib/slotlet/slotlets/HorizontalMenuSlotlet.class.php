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
 * Horizontal menu slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class HorizontalMenuSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'             => __('Clase CSS'),
      'color_block_class' => __('Clase CSS color'),
      'use_color_as_bg'   => __('Usar color como fondo')
    );

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'color_block_class',
      '%label%' => $labels['color_block_class'],
      '%field%' => input_tag('color_block_class', $values['color_block_class'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'use_color_as_bg',
      '%label%' => $labels['use_color_as_bg'],
      '%field%' => checkbox_tag('use_color_as_bg', true, $values['use_color_as_bg'] != false, array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'             => 'slotlet_horizontal_menu',
      'color_block_class' => 'section-color-block',
      'id'                => 'top_menu',
      'section_name'      => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'use_color_as_bg'   => false
    );
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_menu.css');
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  <table class="%class%_menu" border="0" cellpadding="0" cellspacing="0" >
    <tr>
      %content%
    </tr>
  </table>
</div>
SLOTLET;

    return strtr($template, array(
      '%id%'      => $options['id'],
      '%class%'   => $options['class'],
      '%content%' => $this->getContent($options)
    ));
  }

  protected function getContent($options)
  {
    $content  = '';
    $template = $this->getItemTemplate($options);
    $extra    = $options['use_color_as_bg'] ? null : '<span class="%class%" style="background-color: %color%;">&nbsp;</span>';
    
    $extra_content = null;

    foreach ($this->getSections() as $section)
    {
      if ($extra !== null)
      {
        $extra_content = strtr($extra, array(
          '%class%' => $options['color_block_class'],
          '%color%' => $section->hasColor() ? $section->getColor() : ''
        ));
      }

      $content .= strtr($template, array(
        '%class%' => $options['color_block_class'],
        '%item%'  => $section->getHTMLRepresentation($options['section_name'], array(), $extra_content),
        '%color%' => $section->hasColor() ? $section->getColor() : ''
      ));
    }

    return $content;
  }

  protected function getItemTemplate($options)
  {
    if ($options['use_color_as_bg'])
    {
      $template = '<td class="%class%" style="background-color: %color%;">%item%</td>';
    }
    else
    {
      $template = '<td>%item%</td>';
    }

    return $template;
  }

  protected function getSections()
  {
    return Section::getSections(Section::HORIZONTAL);
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra las secciones hijas de Horizontal.';
  }

  public static function getName()
  {
    return 'Menú horizontal';
  }
  
}