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
 * Sitemap slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SitemapSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'                => __('Clase CSS'),
      'depth'                => __('Niveles a mostrar'),
      'include_home_section' => __('Incluir sección inicio'),
      'start_level'          => __('Comenzar desde el nivel'),
      'color_on_bottom'      => __('Ubicar color abajo'),
      'color_on_text'        => __('Ubicar color en texto del primer nivel')
    );

    $form = '';

    foreach (array('depth', 'class', 'start_level') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    foreach (array('include_home_section', 'color_on_bottom', 'color_on_text') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'                          => 'sitemap_slotlet',
      'use_horizontal_as_base_section' => CmsConfiguration::get('check_use_horizontal_as_base_sitemap_section', true),
      'include_home_section'           => CmsConfiguration::get('check_include_home_in_sitemap', false),
      'depth'                          => 2,
      'start_level'                    => 0,
      'color_on_bottom'                => false,
      'color_on_text'                  => false
    );
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_sitemap.css');
  }

  public function render($options = array())
  {
    if (!is_numeric($options['depth']) || intval($options['depth']) < 1)
    {
      $options['depth'] = 2;
    }

    $template = <<<TEMPLATE
<div class="slotlet %class%">
  <table class="content">
    <tbody>
      <tr>
        %content%
      </tr>
      %bottom%
    </tbody>
  </table>
</div>
TEMPLATE;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%content%' => $this->renderContent($options),
      '%bottom%'  => $options['color_on_bottom'] ? $this->renderColorsOnBottom($options) : ''
    ));
  }

  protected function renderColorsOnText($options)
  {
    $styles = <<<CSS
.%class% .content tbody tr td .sitemap-link
{
  color: %color%;
}
CSS;
  }

  protected function renderColorsOnBottom($options)
  {
    $template = '<td><div class="color" style="background-color: %color%;"></div></td>';
    $colors   = '';

    foreach ($this->getFirstLevelSections($options) as $first_level_section)
    {
      $colors .= strtr($template, array(
        '%color%' => ($first_level_section->hasColor() ? $first_level_section->getColor() : '')
      ));
    }
    
    return strtr('<tr>%colors%</tr>', array('%colors%' => $colors));
  }

  protected function renderContent($options)
  {
    $content  = '';
    $first_level_style = false;

    if ($options['color_on_bottom'])
    {
      $template = '<td class="row">%first_level%%children%</td>';
    }
    else if ($options['color_on_text'])
    {
      $first_level_style = 'color: %s;';
      $template = '<td class="row">%first_level%%children%</td>';
    }
    else
    {
      $template = '<td class="row">%first_level%<div class="color" style="%section_style%"></div>%children%</td>';
    }

    $first_level_options = array('use_color_block' => false, 'class' => 'sitemap-link');
    
    foreach ($this->getFirstLevelSections($options) as $first_level_section)
    {
      unset($first_level_options['style']);
      
      if ($first_level_style && $first_level_section->hasColor())
      {
        $first_level_options['style'] = sprintf($first_level_style, $first_level_section->getColor());
      }
      
      $content .= strtr($template, array(
        '%first_level%'      => $first_level_section->getHTMLRepresentation(null, $first_level_options),
        '%section_style%'    => ($first_level_section->hasColor() ? 'background-color: '.$first_level_section->getColor() : ''),
        '%section_fg_style%' => ($first_level_section->hasColor() ? 'color: '.$first_level_section->getColor() : ''),
        '%children%'         => $this->renderChildren($first_level_section, $options, 2)
      ));
    }

    return $content;
  }

  protected function renderChildren(Section $parent_section, $options, $level)
  {
    if (!$parent_section->hasChildren() || $level > intval($options['depth']))
    {
      return;
    }

    $content  = '';
    $template = '<div class="level-group-%level%">%section%</div>%children%';

    foreach ($parent_section->getPublishedChildren() as $child)
    {
      $content .= strtr($template, array(
        '%level%'    => $level,
        '%section%'  => $child->getHTMLRepresentation(null, array('use_color_block' => false)),
        '%children%' => $this->renderChildren($child, $options, $level + 1)
      ));
    }

    return $content;
  }

  protected function getBaseSection($options)
  {
    if ($options['use_horizontal_as_base_section'])
    {
      return SectionPeer::retrieveHorizontalSection();
    }
    else
    {
      return SectionPeer::retrieveHomeSection();
    }
  }

  protected function getSectionsToRemove($options)
  {
    if ($options['include_home_section'])
    {
      return array();
    }
    else
    {
      return array(SectionPeer::retrieveHomeSection());
    }
  }

  protected function getFirstLevelSections($options)
  {
    $base_section       = $this->getBaseSection($options);
    $sections_to_remove = $this->getSectionsToRemove($options);

    if (0 < $options['start_level'])
    {
      $roots = array($base_section);

      for ($i = 0; $i < $options['start_level']; $i++)
      {
        $new_roots = array();

        foreach ($roots as $section)
        {
          foreach ($section->getPublishedChildren() as $child)
          {
            $new_roots[] = $child;
          }
        }

        $roots = $new_roots;
      }
    }
    else
    {
      $roots = $base_section->getPublishedChildren();
    }

    return array_diff($roots, $sections_to_remove);
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el mapa del sitio.';
  }

  public static function getName()
  {
    return 'Mapa del sitio';
  }
  
}