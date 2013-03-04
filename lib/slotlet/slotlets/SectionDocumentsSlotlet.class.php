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
 * Section Documents slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SectionDocumentsSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'           => __('Clase CSS'),
      'title'           => __('Título'),
      'show_when_empty' => __('Mostrar si vacío')
    );

    $form = '';

    foreach (array('class', 'title') as $field)
    {
      $form .= strtr($row, array(
        '%id%'    => $field,
        '%label%' => $labels[$field],
        '%field%' => input_tag($field, $values[$field], array('class' => 'slotlet_option'))
      ));
    }

    foreach (array('show_when_empty') as $key)
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
      'class'           => 'documents_slotlet',
      'section_name'    => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'title'           => __('Documentos'),
      'show_when_empty' => true
    );
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_document.css');
  }

  public function render($options = array())
  {
    $section = $this->getSection($options['section_name']);
    $objects = $this->getObjects($section);

    if (false == $options['show_when_empty'] && (null === $section || 0 == count($objects)))
    {
      return;
    }

    $template = <<<SLOTLET
<div class="slotlet %class%">
  <div class="title">%title%</div>
  <div class="content">
    %content%
  </div>
  <div class="footer">
  </div>
</div>
SLOTLET;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%title%'   => $options['title'],
      '%content%' => $this->renderContent($section, $objects, $options)
    ));
  }

  /**
   * Get the Section named $section_name.
   * 
   * @param  string $section_name The name of the Section.
   *
   * @return Section
   */
  protected function getSection($section_name)
  {
    return SectionPeer::retrieveByName($section_name);
  }

  protected function renderContent($section, $objects, $options)
  {
    if (null === $section)
    {
      return __('Sin contenidos para mostrar');
    }

    $content      = '';    
    $row_template = '<div class="content-child"><a href="%url%" target=%target%>%content%</a></div>';

    try
    {
      if (0 == count($objects))
      {
        throw new Exception('Nothing to do.');
      }
      
      foreach ($objects as $object)
      {
        if (null !== $object)
        {
          if (! ($object instanceof Link))
          {
            $target = '_blank';
          }
          else
          {
            $section_link = SectionLinkPeer::retrieveBySectionIdAndLinkId($section->getId(), $object->getId());
            $target = is_null($section_link) ? '_blank' : $section_link->getTargetBlank() ? '_blank' : '';
          }          

          $content .= strtr($row_template, array(
            '%url%' => $object->getUrl(), 
            '%content%' => $this->renderObject($object),
            '%target%' => $target
            ));
        }
      }
    }
    catch (Exception $e)
    {
      $content = __('Sin contenidos para mostrar');
    }

    return $content;
  }

  protected function getObjects($section)
  {
    if (null !== $section)
    {
      return $section->getDocuments();
    }

    return array();
  }

  protected function renderObject($object)
  {
    return $object->getTitle();
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra los documentos asociados a la sección actual.';
  }

  public static function getName()
  {
    return 'Documentos';
  }
  
}