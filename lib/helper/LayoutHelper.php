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
   * Includes the corresponding layout for the Section named $section_name.
   *
   * @param $section_name string The name of the Section whose layout is to be included.
   * @param $content      string The content of the page to be displayed (as HTML).
   * @param $options      array  The options for the layout.
   *
   * @return string The rendered layout.
   */
  function include_layout_for_section($section_name, $content, $options = array())
  {
    if (!CmsConfiguration::get('check_use_layout_per_section', false))
    {
      return $content;
    }

    // If there is an active layout set to LayoutPeer, use it.
    // This is a hack for the backend's editor.
    if (null !== $layout = LayoutPeer::active())
    {
      $options['section_name']='NONE';
      return include_layout($layout, $content, $options);
    }

    if (sfContext::hasInstance())
    {
      $sf_user = sfContext::getInstance()->getUser();

      if ($sf_user && $sf_user->hasAttribute('mobile_mode') && $sf_user->getAttribute('mobile_mode'))
      {
        return include_layout_for_mobile($section_name, $content, $options);
      }
    }

    // If there is a current virtual section set, use its layout.
    if (null !== $virtual_section_id = VirtualSection::getCurrentId())
    {
      return include_layout_for_virtual_section($virtual_section_id, $content, $options);
    }
      
    $section = SectionPeer::retrieveByName($section_name);

    if (null === $section)
    {
      $section = SectionPeer::retrieveHomeSection();
    }

    $options['section_name'] = $section->getName();

    include_layout($section->getLayout(), $content, $options);
  }

  function include_layout_for_mobile($section_name, $content, $options) 
  {
    if (!isset($options['main_content']))
    {
      $options['main_content'] = SlotletManager::getMainContent();
    }
    $mobile_layout = 
      $options['main_content'] instanceof Article ?
      VirtualSection::VS_MOBILE_CONTENT :
      VirtualSection::VS_MOBILE_HOME; 
    return include_layout_for_virtual_section($mobile_layout, $content, $options);
  }

  /**
   * Includes a particular layout.
   *
   * @param $layout   Layout The Layout to include.
   * @param $content  string The content of the page to be displayed (as HTML).
   * @param $options  array  The options for the layout.
   *
   * @return string The rendered layout.
   */
  function include_layout(Layout $layout, $content, $options = array())
  {
    if (!isset($options['main_content']))
    {
      $options['main_content'] = SlotletManager::getMainContent();
    }
    
    if ($options['main_content'] instanceof Template)
    {
      $aspect = 'template';
    }
    else
    {
      $aspect = 'article';
    }

    $options = array_merge(array(
      'aspect'  => $aspect,
      'content' => $content
    ), $options);


    if ('article' == $options['aspect'] || ('template' == $options['aspect'] && $layout->isTemplateLayoutEmpty()))
    {
      $configuration = $layout->getArticleLayout();
    }
    elseif ('template' == $options['aspect'])
    {
      $configuration = $layout->getTemplateLayout();
    }
    else
    {
      throw new LogicException('Invalid layout aspect: '.$options['aspect']);
    }

    $layout_configuration = new LayoutConfiguration($configuration);
    echo '<div class="choique-layout" id="layout_for_'.$options['section_name'].'">'.$layout_configuration->render($options).'</div>';
/* COMENTADO PORQUE DABA SEGMENTATION FAULT
===========================================
    echo strtr('<div class="choique-layout" id="layout_for_%section%">%content%</div>', array(
      '%id%'      => $layout->getId(),
      '%section%' => $options['section_name'],
      '%content%' => $layout_configuration->render($options)
    ));
*/
  }

  /**
   * Includes the corresponding layout for the virtual section with id $virtual_section_id.
   *
   * @param $virtual_section_id string The id of the virtual section whose layout is to be included.
   * @param $content            string The content of the page to be displayed (as HTML).
   * @param $options            array  The options for the layout.
   *
   * @return string The rendered layout.
   */
  function include_layout_for_virtual_section($virtual_section_id, $content, $options = array())
  {
    if (!CmsConfiguration::get('check_use_layout_per_section', false))
    {
      return $content;
    }

    // If there is an active layout set to LayoutPeer, use it.
    // This is a hack for the backend's editor.
    if (null !== $layout = LayoutPeer::active())
    {
      return include_layout($layout, $content, $options);
    }

    $layout = LayoutPeer::retrieveByVirtualSectionId($virtual_section_id);

    if (null === $layout)
    {
      $layout = LayoutPeer::retrieveDefault();

      $options['section_name'] = SectionPeer::retrieveHomeSection()->getName();
    }
    else
    {
      $sections = $layout->getSections();
      $section = array_shift($sections);
      $options['section_name'] = ( $section === null )? $layout->getVirtualSection(): $section->getName();
    }

    $options['aspect'] = 'article';

    include_layout($layout, $content, $options);
  }
