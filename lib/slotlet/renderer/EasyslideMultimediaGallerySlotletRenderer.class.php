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
 * EasyslideMultimediaGallerySlotletRenderer
 *
 * Slotlet renderer for multimedia galleries that groups them
 * according to the types of their Multimedia elements into:
 * Image, Audio and Video.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class EasyslideMultimediaGallerySlotletRenderer extends BaseSlotletRenderer
{
  public function getName()
  {
    return 'Deslizante';
  }

  public function getDescription()
  {
    return 'Desliza fluidamente los elementos';
  }

  protected function renderableClasses()
  {
    return array('MultimediaGallerySlotlet');
  }

  public function getJavascripts()
  {
    return array('jquery-1.4.2.min.js', 'slotlets/easySlider1.7.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/multimedia_gallery.css', 'frontend/slotlet/sl_easy_slider.css');
  }

  protected function doRender()
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %gallery%
</div>
SLOTLET;

    $gallery = $this->getSlotlet()->getGallery($this->getOption('gallery_id'), $this->getOption('main_content'));

    return strtr($template, array(
      '%id%'      => $this->getOption('id'),
      '%class%'   => $this->getOption('class'),
      '%gallery%' => $this->renderGallery($gallery)
    ));
  }

  protected function renderGallery($gallery)
  {
    $template = <<<GALLERY
<div class="cq_gallery">
  %content%
</div>
%js%
GALLERY;

    return strtr($template, array(
      '%id%'      => $this->getOption('id'),
      '%content%' => $this->renderContent($gallery),
      '%js%'      => $this->getJavascript($gallery)
    ));
  }

  protected function getJavascript($gallery)
  {
    if (null === $gallery)
    {
      return '';
    }

    $javascript = <<<JAVASCRIPT
<script type="text/javascript">
//<![CDATA[
  jQuery(document).ready(function() {
    jQuery('#%id% .cq_gallery').easySlider(jQuery.extend({}, %options%));
  });
//]]>
</script>
JAVASCRIPT;

    return strtr($javascript, array(
      '%id%'      => $this->getOption('id'),
      '%options%' => $this->getGalleryOptions()
    ));
  }

  protected function getGalleryOptions()
  {
    return json_encode(array(
      'continuous' => false,
      'auto'       => false != $this->getOption('auto_start'),
      'nextText'   => __(''),
      'prevText'   => __(''),
      'controlsFade' => true
    ));
  }

  protected function renderContent($gallery)
  {
    $template = '<li class="cq_easy %class%">%content% %text%</li>';
    $content  = '';

    if (null !== $gallery)
    {
      foreach ($gallery->getMultimedia() as $i => $multimedia)
      {
        $content .= strtr($template, array(
          '%content%' => $this->renderMultimedia($multimedia),
          '%text%'    => $this->renderDescription($multimedia),
          '%class%'   => $i === 0 ? 'first' : ''
        ));
      }
    }

    if ('' == trim($content))
    {
      $content = $this->getOption('content_when_empty');
    }

    return '<ul>'.$content.'</ul>';
  }

  protected function renderMultimedia($object)
  {
    return $object->getHTMLRepresentation('g');
  }

  protected function renderDescription($multimedia)
  {
    return sprintf('<div class="cq_description"><div class="cq_description_title">%s</div><div class="cq_description_detail">%s</div></div>', $multimedia->getTitle(), $multimedia->getDescription());
  }

  public function getDefaultOptions()
  {
    return array(
      'border_location' => 'bottom',
      'generate_styles' => true
    );
  }

}