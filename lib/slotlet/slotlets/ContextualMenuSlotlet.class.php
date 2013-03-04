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
 * Contextual menu slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ContextualMenuSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array('slotlets/choique.contextual_menu.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_contextual_menu.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'id'                        => 'contextual_menu',
      'class'                     => 'sl_section',
      'section_name'              => sfContext::getInstance()->getRequest()->getParameter('section_name', SectionPeer::retrieveHomeSection()->getName()),
      'force_home_section'        => CmsConfiguration::get('check_include_home_section_in_section_slotlet', false),
      'arrow_class'               => 'arrow',
      'arrow_alt'                 => __('Mostrar hijos'),
      'arrow_title'               => __('Mostrar hijos'),
      'up_arrow_image'            => image_path('frontend/arrow_white.png'),
      'down_arrow_image'          => image_path('frontend/arrow_black.png'),
      'start_depth'               => 2,
      'go_deep'			  => true
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('Javascript', 'I18N'));

    $section = SectionPeer::retrieveByName($options['section_name']);

    if (null === $section || !$section->getIsPublished() || ($section->getDepth() < $options['start_depth']))
    {
      return;
    }

    $open_section = (null !== $section->getSectionId() ? $section->getSectionRelatedBySectionId()->getName() : $options['section_name']);
    $root_section = $section->getFirstLevelSection();

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %home_section%
  %sections_tree%
  <div class="footer">
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
  Choique.contextualMenu.contextualize('#%id%', { slideUpArrow: '%up_arrow_src%', slideDownArrow: '%down_arrow_src%' });
//]]>
</script>
SLOTLET;

    return strtr($template, array(
      '%id%'             => $options['id'],
      '%class%'          => $options['class'],
      '%home_section%'   => $options['force_home_section'] ? $this->renderFirstLevel(SectionPeer::retrieveHomeSection(), $options, false) : '',
      '%sections_tree%'  => $this->getSectionsTree($section, $root_section, $options),
      '%up_arrow_src%'   => $options['up_arrow_image'],
      '%down_arrow_src%' => $options['down_arrow_image']
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra el arbol de secciones contextual a la sección en la que se encuentra el navegante del sitio.';
  }

  public static function getName()
  {
    return 'Menú contextual';
  }

  protected function getSectionsTree(Section $section, Section $root_section, $options)
  {
    $tree = '';

    foreach ($section->getNthLevelSections($options['start_depth']) as $child)
    {
      $tree .= $this->getSectionsBranch($child, $options);
    }

    return $tree;
  }

  protected function getSectionsBranch(Section $section, $options)
  {
    return $this->renderFirstLevel($section, $options, $options['go_deep']);
  }

  protected function renderFirstLevel(Section $section, $options, $go_deep = true)
  {
    $template = <<<BRANCH
<div id="%name%" class="first-level %class%">
  %title%
  %children%
</div>
BRANCH;

    $is_ancestor = ($section->isAncestorOf($options['section_name']) || $section->getName() === $options['section_name']);

    return strtr($template, array(
      '%name%'     => $section->getName(),
      '%title%'    => $this->renderFirstLevelTitle($section, $is_ancestor, $options, $go_deep),
      '%class%'    => $go_deep && $section->hasChildren() ? 'with-arrow' : '',
      '%children%' => $this->getSecondLevel($section, $is_ancestor, $options, $go_deep)
    ));
  }

  protected function renderFirstLevelTitle(Section $section, $is_ancestor, $options, $go_deep)
  {
    $title = '';

    if ($go_deep && $section->hasChildren())
    {
      $template  = '<a href="#%name%" class="%class%"><img src="%arrow_src%" alt="%arrow_alt%" title="%arrow_title%" id="%name%-arrow" /></a>';
      $arrow_src = ($is_ancestor ? 'frontend/arrow_black.png' : 'frontend/arrow_white.png');
      $title     = strtr($template, array(
        '%name%'        => $section->getName(),
        '%class%'       => $options['arrow_class'],
        '%arrow_src%'   => image_path($arrow_src),
        '%arrow_alt%'   => $options['arrow_alt'],
        '%arrow_title%' => $options['arrow_title']
      ));
    }

    $title .= $section->getHTMLRepresentation($options['section_name'], array('id' => $section->getName().'-anchor'));

    return $title;
  }

  protected function getSecondLevel(Section $section, $is_ancestor, $options, $go_deep)
  {
    if (false === $go_deep || !$section->hasChildren())
    {
      return '';
    }

    $second_level = $this->renderSecondLevel($section, $options['section_name'], !$is_ancestor);

    if (!$is_ancestor)
    {
      $second_level .= sprintf('<noscript>%s</noscript>', $this->renderSecondLevel($section, $options['section_name'], false));
    }

    return $second_level;
  }

  protected function renderSecondLevel($section, $current_section_name, $allow_collapse)
  {
    $content           = '';
    $template          = '<div id="%name%-children" class="second-level %allow_collapse%">%content%</div>';
    $children_template = '<div class="second-level-child">%content%</div>';

    foreach ($section->getPublishedChildren() as $child)
    {
      $content .= strtr($children_template, array(
        '%content%' => $child->getHTMLRepresentation($current_section_name)
      ));
    }

    return strtr($template, array(
      '%name%'           => $section->getName(),
      '%allow_collapse%' => $allow_collapse === false ? '' : 'collapseable',
      '%content%'        => $content
    ));
  }

  public function getConfigurationForm($values = array())
  {
    $row  = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    
    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'force_home_section',
      '%label%' => __('Incluir siempre sección inicio'),
      '%field%' => checkbox_tag('force_home_section', true, $values['force_home_section'] != false, array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'go_deep',
      '%label%' => __('Mostrar hijos'),
      '%field%' => checkbox_tag('go_deep', true, $values['go_deep'] != false, array('class' => 'slotlet_option'))
    ));

    $depths = array_combine(range(0, Section::$MAX_DEPTH), range(1, Section::$MAX_DEPTH + 1));

    $form .= strtr($row, array(
      '%id%'    => 'start_depth',
      '%label%' => __('Nivel de anidamiento inicial'),
      '%field%' => select_tag('start_depth', options_for_select($depths, $values['start_depth']), array('class' => 'slotlet_option'))
    ));

    return $form;
  }

}