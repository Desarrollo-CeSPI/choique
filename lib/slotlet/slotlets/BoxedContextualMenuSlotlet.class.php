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
 * BoxedContextualMenuSlotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BoxedContextualMenuSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row  = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => __('Clase CSS'),
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'section_name',
      '%label%' => __('Mostrar hijas de'),
      '%field%' => select_tag('section_name', objects_for_select(SectionPeer::retrievePublished(), 'getName', 'getTitle', $values['section_name']), array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'id'              => 'boxed_contextual_menu_'.(time() % 53),
      'class'           => 'boxed_contextual_menu_slotlet',
      'section_name'    => sfContext::getInstance()->getRequest()->getParameter('section_name', SectionPeer::retrieveHomeSection()->getName()),
      'max_first_level' => 4
    );
  }

  public function getJavascripts()
  {
    return array('slotlets/choique.boxed_contextual_menu.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/boxed_contextual_menu.css');
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('Javascript', 'I18N'));

    $section = SectionPeer::retrieveByName($options['section_name']);

    if (null === $section || !$section->getIsPublished())
    {
      return;
    }

    $root_section = $section->getFirstLevelSection();

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  <div class="boxed_contextual_menu_container">
    %visible_menu%
    %hidden_menues%
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
  Choique.boxedContextualMenu.contextualize('#%id%');
//]]>
</script>
SLOTLET;

    return strtr($template, array(
      '%id%'             => $options['id'],
      '%class%'          => $options['class'],
      '%visible_menu%'   => $this->renderVisibleMenu($root_section, $options),
      '%hidden_menues%'  => $this->renderHiddenMenues($root_section, $options)
    ));
  }

  protected function renderVisibleMenu(Section $root, $options = array())
  {
    $template = <<<UL
<ul class="hidden_menu">
  %sections%
</ul>
UL;

    return strtr($template, array(
      '%sections%' => $this->renderItems($this->getChildren($root, $options), $options)
    ));
  }

  protected function getChildren(Section $root, $options = array())
  {
    $criteria = new Criteria();

    if (array_key_exists('max_first_level', $options) && intval($options['max_first_level']) > 0)
    {
      $criteria->setLimit(intval($options['max_first_level']));
    }

    return $root->getPublishedChildren($criteria);
  }

  protected function renderHiddenMenues(Section $root, $options = array())
  {
    $html = '';
    $template = <<<HTML
<div id="%root_name%_children" class="boxed_contextual_menu_children_container" style="display: none;">
  <div class="hidden_menu_title">%root_title%</div>
  <ul class="hidden_menu_items">
    %items%
  </ul>
</div>
HTML;

    foreach ($this->getChildren($root, $options) as $section)
    {
      $html .= strtr($template, array(
        '%root_title%' => $section->getTitle(),
        '%root_name%'  => $section->getName(),
        '%items%'      => $this->renderItems($section->getPublishedChildren(), $options, 2)
      ));
    }

    return $html;
  }

  protected function renderItems(array $sections, $options = array(), $level = 1)
  {
    $items = '';
    $template = <<<LI
<li class="boxed_item_level_%level%"><a href="%href%">%content%</a></li>
LI;

    foreach ($sections as $section)
    {
      $items .= strtr($template, array(
        '%level%'   => strval($level),
        '%content%' => $section->getTitle(),
        '%href%'    => $level === 1 ? '#'.$section->getName().'_children' : url_for($section->getRoute())
      ));
    }

    return $items;
  }

  public static function getDescription()
  {
    return 'Menú contextual desarrollado en cajas que al hacer click despliegan sus hijos en todo el espacio disponible.';
  }

  public static function getName()
  {
    return 'Menú contextual en cajas';
  }

}