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
 * News slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class NewsSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return $this->getRenderer($this->options)->getJavascripts();
  }

  public function getStylesheets()
  {
    return $this->getRenderer($this->options)->getStylesheets();
  }

  public function getRenderer($options = array())
  {
    if (null === $options['renderer'])
    {
      $options['renderer'] = 'DefaultNewsSlotletRenderer';
    }

    return SlotletRendererFactory::get($options['renderer']);
  }

  public function getDefaultOptions()
  {
    return array(
      'id'                        => 'news',
      'title'                     => __('Novedades'),
      'class'                     => 'news_slotlet',
      'show_when_empty'           => true,
      'sort_by_priority'          => CmsConfiguration::get('check_sort_news_by_priority', true),
      'include_ancestor_sections' => CmsConfiguration::get('check_include_ancestor_sections', true),
      'include_children_sections' => CmsConfiguration::get('check_include_children_sections', true),
      'maximum_elements'          => CmsConfiguration::get('max_news', 5),
      'section_name'              => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'renderer'                  => 'DefaultNewsSlotletRenderer',
      '%title_hover_color%'       => '#444',
      'columns'                   => 2
    );
  }

  public function render($options = array())
  {
    $section = $this->getSection($options);
    $news    = $this->getNews($section, $options);

    if (0 == count($news) && false === $options['show_when_empty'])
    {
      return;
    }

    $options['section'] = $section;
    $options['news']    = $news;

    return $this->getRenderer($options)->render($this, $options);
  }

  protected function getSection($options)
  {
    $section = SectionPeer::retrieveByName($options['section_name']);

    if (!$section)
    {
      $section = SectionPeer::retrieveHomeSection();
    }

    return $section;
  }

  protected function getNews($section, $options)
  {
    return $section->getSortedNews($options['include_ancestor_sections'], $options['include_children_sections'], $options['maximum_elements'], $options['sort_by_priority']);
  }

  public static function getDescription()
  {
    return 'Slotlet contextual que muestra las novedades para la sección actual.';
  }

  public static function getName()
  {
    return 'Novedades';
  }
  
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'title'                     => __('Título'),
      'class'                     => __('Clase CSS'),
      'show_when_empty'           => __('Mostrar si vacío'),
      'sort_by_priority'          => __('Ordenar por prioridad'),
      'maximum_elements'          => __('Número máximo de novedades'),
      'include_ancestor_sections' => __('Incluir novedades de secciones ascendientes'),
      'include_children_sections' => __('Incluir novedades de secciones descendientes'),
      'renderer'                  => __('Representación'),
      'columns'                   => __('Columnas (tabular)'),
      'title_hover_color'         => __('Color hover (tabular)')
    );

    $form          = '';
    $validation_js = "if (!(/^\d+$/.match(jQuery(this).val()))) { alert('Por favor ingrese un valor numérico.'); jQuery(this).val(".$values['maximum_elements']."); return false; }";

    foreach (array('title', 'class', 'maximum_elements','title_hover_color') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, isset($values[$key])?$values[$key]:'', array('class' => 'slotlet_option', 'onchange' => ($key == 'maximum_elements' ? $validation_js : '')))
      ));
    }

    foreach (array('show_when_empty', 'sort_by_priority', 'include_ancestor_sections', 'include_children_sections') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    $form .= strtr($row, array(
      '%id%'    => 'renderer',
      '%label%' => $labels['renderer'],
      '%field%' => select_tag('renderer', $this->getRendererOptions($values['renderer']), array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'columns',
      '%label%' => $labels['columns'],
      '%field%' => select_tag('columns', options_for_select(array_combine(range(1, 5), range(1, 5)), $values['columns']), array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  protected function getRendererOptions($selected_value = null)
  {
    $options = array();

    foreach (SlotletRendererFactory::getFor($this) as $class_name => $renderer)
    {
      $options[$class_name] = strval($renderer);
    }

    return options_for_select($options, $selected_value);
  }

}