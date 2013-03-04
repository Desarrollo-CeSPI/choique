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
 * Multimedia gallery slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class MultimediaGallerySlotlet implements ISlotlet
{
  protected function getOptionsForGalleryId($selected_value = null)
  {
    return objects_for_select(GalleryPeer::retrievePublished(), 'getId', 'getName', $selected_value, array('include_custom' => 'Contextual (Obtener del contenido principal)'));
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

  protected function getMediaTypes($selected_value = null)
  {
    return options_for_select(array(-1 => 'Ninguno', 0 => 'Imágenes', 1 => 'Audios', 2 => 'Videos'), $selected_value);
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'gallery_id'      => __('Galería'),
      'class'           => __('Clase CSS'),
      'images_tab_text' => __('Título para imágenes (Agrupado)'),
      'audios_tab_text' => __('Título para audios (Agrupado)'),
      'videos_tab_text' => __('Título para videos (Agrupado)'),
      'title'           => __('Título (para rep. Horizontal)'),
      'show_when_empty' => __('Mostrar si vacío'),
      'auto_start'      => __('Iniciar en modo presentación'),
      'renderer'        => __('Representación'),
      'border_location' => __('Borde de color en la parte inferior'),
      'generate_styles' => __('Incluir estilos CSS')
    );

    $form = strtr($row, array(
      '%id%'    => 'gallery_id',
      '%label%' => $labels['gallery_id'],
      '%field%' => select_tag('gallery_id', $this->getOptionsForGalleryId($values['gallery_id']), array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'renderer',
      '%label%' => $labels['renderer'],
      '%field%' => select_tag('renderer', $this->getRendererOptions($values['renderer']), array('class' => 'slotlet_option'))
    ));

    foreach (array('images_tab_text', 'audios_tab_text', 'videos_tab_text', 'class') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    foreach (array('auto_start', 'show_when_empty', 'border_location', 'generate_styles') as $key)
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
      'id'                 => 'slotlet_multimedia_gallery_'.((time() % 500) + 1),
      'class'              => 'slotlet_multimedia_gallery',
      'renderer'           => 'GroupedMultimediaGallerySlotletRenderer',
      'gallery_id'         => null,
      'main_content'       => null,
      'size'               => 'g',
      'images_tab_text'    => __('Imagen'),
      'audios_tab_text'    => __('Audio'),
      'videos_tab_text'    => __('Video'),
      'title'              => __('Galería'),
      'show_when_empty'    => false,
      'auto_start'         => false,
      'content_when_empty' => __('Sin contenidos para mostrar'),
      'use_play_and_pause' => false,
      'border_location'    => true,
      'generate_styles'    => true
    );
  }

  public function getJavascripts()
  {
    return $this->getRenderer($this->options)->getJavascripts();
  }

  public function getStylesheets()
  {
    return $this->getRenderer($this->options)->getStylesheets();
  }

  public function getRenderer($options)
  {
    if (null === $options['renderer'])
    {
      $options['renderer'] = 'GroupedMultimediaGallerySlotletRenderer';
    }

    return SlotletRendererFactory::get($options['renderer']);
  }

  public function render($options = array())
  {
    $gallery = $this->getGallery($options['gallery_id'], $options['main_content']);

    if ((null === $gallery || !$gallery->hasMultimedia()) && false == $options['show_when_empty'])
    {
      return '';
    }

    $options['border_location'] = $options['border_location'] ? 'bottom' : 'top';

    return $this->getRenderer($options)->render($this, $options);
  }

  public function getSection($name)
  {
    return SectionPeer::retrieveByName($name);
  }

  /**
   * @return Gallery
   */
  public function getGallery($id, $main_content = null)
  {
    $gallery = null;

    if (null !== $id)
    {
      $gallery = GalleryPeer::retrieveByPK($id);
    }

    if (null === $gallery && null !== $main_content)
    {
      try
      {
        $gallery = $main_content->getGallery();
      }
      catch (Exception $e)
      {
        $gallery = null;
      }
    }

    return $gallery;
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra una galería de contenidos multimediales en particular o la asociada al contenido principal de cada página.';
  }

  public static function getName()
  {
    return 'Galería multimedial';
  }

}