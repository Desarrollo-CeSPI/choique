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
 * First level navigation menu slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SecondLevelNavigationMenuSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class' => __('Clase CSS')
    );

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $depths = array_combine(range(1, Section::$MAX_DEPTH), range(2, Section::$MAX_DEPTH + 1));

    $form .= strtr($row, array(
      '%id%'    => 'depth',
      '%label%' => __('Nivel de profundidad'),
      '%field%' => select_tag('depth', options_for_select($depths, array_key_exists('depth', $values)? $values['depth']: Section::$MAX_DEPTH), array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'        => 'second_level_slotlet',
      'depth'        => Section::$MAX_DEPTH, 
      'section_name' => sfContext::getInstance()->getRequest()->getParameter('section_name')
    );
  }

  public function getJavascripts()
  {
    return array('slotlets/jquery.hoverIntent.js', 'slotlets/supersubs.js', 'slotlets/superfish.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_second_level_slotlet.css');
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div class="slotlet %class%">
  <ul class="cq_menu sf-menu">
    %content%
  </ul>
  <div class="cq_end">&nbsp;</div>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
  jQuery('.%class% .cq_menu').supersubs({
    minWidth: 12,
    maxWidth: 27,
    extraWidth: 1
  }).superfish({
    autoArrows: false,
    animation: { opacity: 'show', height: 'show' },
    speed: 'fast'
  });
});
//]]>
</script>
<style type="text/css">
%styles%
</style>
SLOTLET;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%styles%'  => $this->renderStyles($options),
      '%content%' => $this->renderContent($options)
    ));
  }

  protected function renderStyles($options)
  {
    $section = $this->getSection($options['section_name']);

    if (null === $section || !$section->hasColor() || !$this->outputCSS())
    {
      return;
    }

    $css = <<<CSS
.%class% .cq_menu .cq_item .cq_submenu,
.%class% .cq_menu .cq_item .cq_submenu .cq_sub_submenu
{
  background-color: %lowered_hex%;
  background-color: %rgba%;
  *background-color: %lowered_hex%;
}

.%class% .cq_menu .cq_item:hover > a
{
  background-color: %hex%;
}

.%class% .cq_menu .cq_item a.selected,
.%class% .cq_menu .cq_item:hover > a,
.%class% .cq_menu .cq_item .cq_submenu .cq_submenu_item:hover,
.%class% .cq_menu .cq_item .cq_submenu .cq_submenu_item .cq_sub_submenu .cq_sub_submenu_item:hover
{
  background-color: %hex%;
}

.%class% .cq_menu a.selected,
.%class% .cq_menu .cq_item .cq_submenu .cq_submenu_item:hover > a,
.%class% .cq_menu .cq_item .cq_submenu .cq_submenu_item .cq_sub_submenu .cq_sub_submenu_item:hover > a
{
  color: #fff;
}

.%class%
{
  border-bottom: 2px solid %hex%;
}
CSS;

    return strtr($css, array(
      '%class%'       => $options['class'],
      '%rgba%'        => $section->getRGBAColor(),
      '%hex%'         => $section->getColor(),
      '%lowered_hex%' => $this->lowerHex($section->getColor())
    ));
  }

  protected function outputCSS()
  {
    // If the file _extra_stylesheet.php exists in the section module of the
    // flavor, the styles are assumed to be defined there.
    return !file_exists(choiqueFlavors::getModulePath('section').'/templates/_extra_stylesheet.php');
  }

  protected function lowerHex($hex_color, $decrement = 40)
  {
    // Clean up $hex_color
    $hex_color = preg_replace('/[^0-9A-Fa-f]/', '', $hex_color);

    if (3 == strlen($hex_color))
    {
      $hex_color = $hex_color[0].$hex_color[0].$hex_color[1].$hex_color[1].$hex_color[2].$hex_color[2];
    }

    $red   = hexdec(substr($hex_color, 0, 2));
    $green = hexdec(substr($hex_color, 2, 2));
    $blue  = hexdec(substr($hex_color, 4, 2));

    $red   = max(0, $red - $decrement);
    $green = max(0, $green - $decrement);
    $blue  = max(0, $blue - $decrement);

    return sprintf('#%02s%02s%02s', dechex($red), dechex($green), dechex($blue));
  }

  protected function renderContent($options)
  {
    $content  = '';
    $template = <<<MENU
<li class="cq_item%is_parent%">
  %content%
  %menu%
</li>
MENU;

    foreach ($this->getSections($options['section_name']) as $section)
    {
      $options['id'] = str_replace(' ', '_', $options['class']).'_'.$section->getId();

      $content .= strtr($template, array(
        '%class%'     => $options['class'],
        '%content%'   => $section->getHtmlRepresentation($options['section_name'], array('use_color_block' => false)),
        '%menu%'      => $this->renderChildrenMenu($section, $options),
        '%is_parent%' => $section->hasPublishedChildren() ? ' cq_parent' : ''
      ));
    }

    return $content;
  }

  protected function renderChildrenMenu(Section $section, $options)
  {
    if (!$section->hasPublishedChildren() )
    {
      return;
    }

    if ( isset($options['depth']) && ($section->getDepth() > $options['depth']) )
    {
      return;
    }

    $template = <<<MENU
<ul class="cq_submenu" id="%id%_children">
  %content%
</ul>
MENU;

    return strtr($template, array(
      '%id%'      => $options['id'],
      '%content%' => $this->renderChildren($section, $options)
    ));
  }

  protected function renderChildren(Section $section, $options)
  {
    $content  = '';
    $template = <<<CHILD
<li class="cq_submenu_item%is_parent%" id="%id%_grandchildren_father">
  %content%
  %menu%
</li>
CHILD;


    foreach ($section->getPublishedChildren() as $child)
    {
      $content .= strtr($template, array(
        '%id%'        => $options['id'],
        '%content%'   => $child->getHTMLRepresentation($options['section_name'], array('use_color_block' => false)),
        '%menu%'      => $this->renderGrandchildrenMenu($child, $options),
        '%is_parent%' => $child->hasChildren() && isset($options['depth']) && ($section->getDepth() < $options['depth']) ? ' cq_parent' : ''
      ));
    }

    return $content;
  }

  protected function renderGrandchildrenMenu(Section $section, $options)
  {
    if (!$section->hasPublishedChildren())
    {
      return;
    }

    if ( isset($options['depth']) && ($section->getDepth() > $options['depth']) )
    {
      return '';
    }

    $template = <<<MENU
<ul class="cq_sub_submenu" id="%id%_grandchildren">
  %content%
</ul>
MENU;

    return strtr($template, array(
      '%id%'      => $options['id'],
      '%content%' => $this->renderGrandchildren($section, $options)
    ));
  }

  protected function renderGrandchildren(Section $section, $options)
  {
    $content  = '';
    $template = <<<GRANDCHILD
<li class="cq_sub_submenu_item">
  %content%
</li>
GRANDCHILD;

    foreach ($section->getPublishedChildren() as $child)
    {
      $content .= strtr($template, array(
        '%content%' => $child->getHTMLRepresentation($options['section_name'], array('use_color_block' => false))
      ));
    }

    return $content;
  }

  protected function getSections($current_name)
  {
    $section = SectionPeer::retrieveFirstLevelSectionByName($current_name);

    if (null === $section)
    {
      return array();
    }

    return $section->getPublishedChildren();
  }

  protected function getSection($name)
  {
    return SectionPeer::retrieveByName($name);
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el segundo nivel de navegación por secciones.';
  }

  public static function getName()
  {
    return 'Menú segundo nivel';
  }

}