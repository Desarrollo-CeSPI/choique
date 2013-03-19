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
 * Horizontal click menu slotlet
 *
 * @author Matías Eduardo Brown Barnetche <mbrown@cespi.unlp.edu.ar>
 */
class HorizontalClickMenuSlotlet implements ISlotlet
{
  private $actual_level = 1;

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field% <div>%help%</div></div><div style="clear:both;"></div>';
    $labels = array(
      'class'               => __('Clase CSS'),
      'color_block_class'   => __('Clase CSS color'),
      'use_color_as_bg'     => __('Usar color como fondo'),
      'visible_menu_levels' => __('Niveles de menú visibles primero'),
      'use_onmouseover'     => __('¿Utilizar evento onmouseover para mostrar el menú?'),
      'add_section_root'    => __('Agregar en el menú link a la sección raíz')
    );

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option')),
      '%help%'  => ''
    ));

    $form .= strtr($row, array(
      '%id%'    => 'color_block_class',
      '%label%' => $labels['color_block_class'],
      '%field%' => input_tag('color_block_class', $values['color_block_class'], array('class' => 'slotlet_option')),
      '%help%'  => ''
    ));

    $form .= strtr($row, array(
      '%id%'    => 'use_color_as_bg',
      '%label%' => $labels['use_color_as_bg'],
      '%field%' => checkbox_tag('use_color_as_bg', true, $values['use_color_as_bg'] != false, array('class' => 'slotlet_option')),
      '%help%'  => ''
    ));

    $form .= strtr($row, array(
      '%id%'    => 'visible_menu_levels',
      '%label%' => $labels['visible_menu_levels'],
      '%field%' => input_tag('visible_menu_levels', $values['visible_menu_levels'], array('class' => 'slotlet_option')),
      '%help%'  => 'Hasta que nivel mostrar completo. -1 para mostrar todos los niveles.'
    ));

    $form .= strtr($row, array(
      '%id%'    => 'use_onmouseover',
      '%label%' => $labels['use_onmouseover'],
      '%field%' => checkbox_tag('use_onmouseover', true, $values['use_onmouseover'] != false, array('class' => 'slotlet_option')),
      '%help%'  => 'El menú se mostrará haciendo click en la opción o dejando el cursor del mouse arriba de la opción.'
    ));

    $form .= strtr($row, array(
      '%id%'    => 'add_section_root',
      '%label%' => $labels['add_section_root'],
      '%field%' => checkbox_tag('add_section_root', true, $values['add_section_root'] != false, array('class' => 'slotlet_option')),
      '%help%'  => 'Se mostrará en el menú desplegable el link a la sección raíz.'
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'               => 'slotlet_horizontal_menu_click',
      'color_block_class'   => 'section-color-block',
      'id'                  => 'top_menu_click',
      'section_name'        => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'use_color_as_bg'     => false,
      'visible_menu_levels' => -1,
      'use_onmouseover'     => false,
      'add_section_root'    => false
    );
  }

  public function getJavascripts()
  {
    return array('slotlets/menu_horizontal_click.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_menu_horizontal_click.css');
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet sl_menu_click %class%">
  <ul class="sl_menu_click_sections_list %class%_menu">
    %content%
  </ul>
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
    $section_template = '<li class="sl_menu_click_section %section_name%">%section_link% %section_content%</li>';
    $content  = '';
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

      $click_options = array();
      if($section->hasPublishedChildren())
      {
        $click_options = array('onclick' => "return toggleMenuContent('".$section->getName()."');");
        if($options['use_onmouseover'])
        {
          $click_options = array_merge($click_options, array('onmouseover' => "return toggleMenuContent('".$section->getName()."');"));
        }
      }
      $content .= strtr($section_template, array(
        '%section_link%'    => $section->getHTMLRepresentation($options['section_name'], $click_options, $extra_content),
        '%section_content%' => $this->getSectionContent($section, $options),
        '%section_name%'    => $section->getName()
      ));
    }

    return $content;
  }

  protected function getSectionContent($section, $options)
  {
    $content = '';
    $section_template = '<div class="dropdown-menu %section_underscored_name%_content" style="display: none;"><h1 class="section_title">%section_link%</h1>%section_childrens_content%</div>';
    if($options['add_section_root'])
      $this->actual_level++;
    $section_childrens_content = '';
    if($section->hasPublishedChildren())
    {
      foreach($section->getPublishedChildren() as $section_children)
      {
        $section_childrens_content .= $this->getSectionChildrensContent($section_children, $options);
      }
    }
    $content .= strtr($section_template, array(
      '%section_underscored_name%'  => sfInflector::underscore($section->getName()),
      '%section_link%'              => $options['add_section_root']?$section->getHTMLRepresentation('', array()):'',
      '%section_childrens_content%' => ($section_childrens_content != '')?'<ul class="childrens_list level_'.$this->actual_level.'">'.$section_childrens_content.'</ul>':$section_childrens_content
    ));
    if($options['add_section_root'])
      $this->actual_level--;
    return $content;
  }

  protected function getSectionChildrensContent($section, $options)
  {
    $visible_menu_levels = (int)$options['visible_menu_levels'];
    $content = '';
    if(($visible_menu_levels == -1) || ($this->actual_level <= $visible_menu_levels) || !$section->hasPublishedChildren())
    {
      $section_childrens_template = '<div class="section_children level_'.$this->actual_level.'"><h'.$this->actual_level.' class="section_title">%section_children_link%</h'.$this->actual_level.'>%childrens_content%</div>';

      if($section->hasPublishedChildren())
      {
        $this->actual_level++;
        $childrens_content = '';
        foreach($section->getPublishedChildren() as $section_children)
        {
          $childrens_content .= $this->getSectionChildrensContent($section_children, $options); 
        }

        $content .= '<li>'.strtr($section_childrens_template, array(
          '%section_children_link%' => $section->getHTMLRepresentation('', array()),
          '%childrens_content%'     => '<ul class="childrens_list level_'.$this->actual_level.'">'.$childrens_content.'</ul>'
        )).'</li>'; 
        $this->actual_level--;
      }
      else
      {
        $content .= strtr($section_childrens_template, array(
          '%section_children_link%' => $section->getHTMLRepresentation('', array()),
          '%childrens_content%'     => ""
        ));
      }
    }
    else
    {
      $section_childrens_template = '<div class="section_children expandable level_'.$this->actual_level.'"><h'.$this->actual_level.' class="section_title">%section_children_link%&nbsp;%hide_show_link%</h'.$this->actual_level.'>%childrens_content%</div>';

      if($section->hasPublishedChildren())
      {
        $this->actual_level++;
        $childrens_content = '';
        foreach($section->getPublishedChildren() as $section_children)
        {
          $childrens_content .= $this->getSectionChildrensContent($section_children, $options); 
        }

        $content .= '<li>'.strtr($section_childrens_template, array(
          '%section_children_link%' => $section->getHTMLRepresentation('', array()),
          '%childrens_content%'     => '<ul class="childrens_list level_'.$this->actual_level.'" style="display: none;">'.$childrens_content.'</ul>',
          '%hide_show_link%'        => '<a href="#" class="hide_show_link show" onclick="return hideShowChildrens(this);">&nbsp;</a>'
        )).'</li>'; 
        $this->actual_level--;
      }
    }
    return $content;
  }

  protected function getSectionFirstLevelChildrensContent($section, $options)
  {
    $content = '';
    $section_childrens_template = '<div class="section_children"><h2 class="section_title">%section_children_link%</h2>%childrens_content%</div>';

    if($section->hasPublishedChildren())
    {
      foreach($section->getPublishedChildren() as $section_children)
      {
        $content .= strtr($section_childrens_template, array(
          '%section_children_link%' => $section_children->getHTMLRepresentation('', array()),
          '%childrens_content%'     => $this->getSectionChildrensContent($section_children->getPublishedChildren(), $options)
        )); 
      }
    }
    return $content;
  }

  protected function getSections()
  {
    return Section::getSections(Section::HORIZONTAL);
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra las secciones hijas de Horizontal y se les puede hacer click.';
  }

  public static function getName()
  {
    return 'Menú horizontal con click';
  }
  
}
