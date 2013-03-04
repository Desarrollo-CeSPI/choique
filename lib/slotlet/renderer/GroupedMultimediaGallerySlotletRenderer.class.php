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
 * GroupedMultimediaGallerySlotletRenderer
 *
 * Slotlet renderer for multimedia galleries that groups them
 * according to the types of their Multimedia elements into:
 * Image, Audio and Video.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class GroupedMultimediaGallerySlotletRenderer extends BaseSlotletRenderer
{
  public function getName()
  {
    return 'Agrupada';
  }

  public function getDescription()
  {
    return 'en Imagen, Audio y Video';
  }

  protected function renderableClasses()
  {
    return array('MultimediaGallerySlotlet');
  }

  public function getJavascripts()
  {
    return array('jquery-1.4.2.min.js', 'jquery-ui.min.js', 'slotlets/jquery.loadmask.js', 'slotlets/choique.multimedia_gallery.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/multimedia_gallery.css');
  }

  protected function doRender()
  {
    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %gallery%
</div>
<style type="text/css">
%styles%
</style>
SLOTLET;

    $gallery = $this->getSlotlet()->getGallery($this->getOption('gallery_id'), $this->getOption('main_content'));

    return strtr($template, array(
      '%id%'      => $this->getOption('id'),
      '%class%'   => $this->getOption('class'),
      '%gallery%' => $this->renderGallery($gallery),
      '%styles%'  => $this->getOption('generate_styles') ? $this->renderStyles() : ''
    ));
  }

  protected function renderStyles()
  {
    $section = $this->getSlotlet()->getSection($this->getOption('section_name'));

    if (null === $section || !$section->hasColor())
    {
      return;
    }

    $css = <<<CSS
.%class% .cq_gallery
{
  border-%location%: 4px solid %hex%;
}

.%class% .cq_tabs
{
  border-bottom: 2px solid %hex%;
}

.%class% .cq_gallery .cq_tabs .cq_tab a:hover,
.%class% .cq_gallery .cq_tabs .cq_tab a.cq_selected_tab
{
  background-color: %hex%;
}

.%class% .cq_description .cq_description_detail
{
  color: %hex%;
}
CSS;

    return strtr($css, array(
      '%class%'    => $this->getOption('class'),
      '%hex%'      => $section->getColor(),
      '%location%' => $this->getOption('border_location')
    ));
  }

  protected function renderGallery($gallery)
  {
    $template = <<<GALLERY
<div class="cq_gallery">
  %tabs%
  %arrows%
  %content%
  %footer%
</div>
%js%
GALLERY;

    return strtr($template, array(
      '%id%'      => $this->getOption('id'),
      '%tabs%'    => $this->renderTabs($gallery),
      '%content%' => $this->renderContent($gallery),
      '%footer%'  => $this->renderFooter(),
      '%arrows%'  => $this->renderNavigation($gallery),
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
    Choique.multimedia_gallery.create('#%id% .cq_gallery', %options%);
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
      'playbackIndicator' => image_path('frontend/cmg_playing.gif'),
      'autoStart'         => false != $this->getOption('auto_start'),
      'indicatorSrc'      => image_path('frontend/cmg_playing.gif')
    ));
  }

  protected function renderTabs($gallery)
  {
    $template     = '<div class="cq_tabs">%tabs%</div>';
    $tab_template = '<span class="cq_tab"><a href="#%id%_%target%">%text%</a></span>';

    $tabs = '';

    if (null !== $gallery)
    {
      if ($gallery->hasVideos())
      {
        $tabs .= strtr($tab_template, array(
          '%id%'     => $this->getOption('id'),
          '%target%' => 'videos',
          '%text%'   => $this->getOption('videos_tab_text')
        ));
      }

      if ($gallery->hasImages())
      {
        $tabs .= strtr($tab_template, array(
          '%id%'     => $this->getOption('id'),
          '%target%' => 'images',
          '%text%'   => $this->getOption('images_tab_text')
        ));
      }

      if ($gallery->hasAudios())
      {
        $tabs .= strtr($tab_template, array(
          '%id%'     => $this->getOption('id'),
          '%target%' => 'audios',
          '%text%'   => $this->getOption('audios_tab_text')
        ));
      }
    }

    if ('' == trim($tabs))
    {
      $tabs = '&nbsp;';
    }

    return strtr($template, array(
      '%tabs%' => $tabs
    ));
  }

  protected function renderNavigation($gallery)
  {
    if (null === $gallery)
    {
      return '';
    }

    $template = <<<NAV
<div class="cq_controls">
  <a href="#previous" onclick="Choique.multimedia_gallery.go(-1); return false;" class="cq_control cq_control_previous"><img src="%previous_src%" alt="%previous%" title="%previous%" /></a>
  <a href="#next" onclick="Choique.multimedia_gallery.go(1); return false;" class="cq_control cq_control_next"><img src="%next_src%" alt="%next%" title="%next%" /></a>
  <img src="%indicator_src%" alt="%indicator%" title="%indicator%" class="cq_control cq_playback_indicator" style="display: none;" />
NAV;

    if ($this->getOption('use_play_and_pause'))
    {
      $template .= <<<NAV
  <a href="#play" onclick="Choique.multimedia_gallery.play(); return false;" class="cq_control cq_control_play"><img src="%play_src%" alt="%play%" title="%play%" /></a>
  <a href="#pause" onclick="Choique.multimedia_gallery.pause(); return false;" class="cq_control cq_control_pause"><img src="%pause_src%" alt="%pause%" title="%pause%" /></a>
NAV;
    }

    $template .= '</div>';

    return strtr($template, array(
      '%previous%'      => __('Anterior'),
      '%previous_src%'  => image_path('frontend/cmg_left.png'),
      '%next%'          => __('Siguiente'),
      '%next_src%'      => image_path('frontend/cmg_right.png'),
      '%play%'          => __('Iniciar presentación'),
      '%play_src%'      => image_path('frontend/cmg_play.png'),
      '%pause%'         => __('Pausar'),
      '%pause_src%'     => image_path('frontend/cmg_pause.png'),
      '%indicator%'     => __('Reproduciendo'),
      '%indicator_src%' => image_path('frontend/cmg_playing.gif')
    ));
  }

  protected function renderContent($gallery)
  {
    $template = '<div id="%id%_%type%" class="cq_slideable"><div class="cq_content">%content%</div></div>';
    $content  = '';

    if (null !== $gallery)
    {
      if ($gallery->hasVideos())
      {
        $content .= strtr($template, array(
          '%id%'      => $this->getOption('id'),
          '%type%'    => 'videos',
          '%class%'   => $this->getOption('class'),
          '%content%' => $this->renderMultimedia($gallery->getVideos(), 'videos')
        ));
      }

      if ($gallery->hasImages())
      {
        $content .= strtr($template, array(
          '%id%'      => $this->getOption('id'),
          '%type%'    => 'images',
          '%class%'   => $this->getOption('class'),
          '%content%' => $this->renderMultimedia($gallery->getImages(), 'images')
        ));
      }

      if ($gallery->hasAudios())
      {
        $content .= strtr($template, array(
          '%id%'      => $this->getOption('id'),
          '%type%'    => 'audios',
          '%class%'   => $this->getOption('class'),
          '%content%' => $this->renderMultimedia($gallery->getAudios(), 'audios')
        ));
      }
    }

    if ('' == trim($content))
    {
      $content = $this->getOption('content_when_empty');
    }

    return $content;
  }

  protected function renderMultimedia($objects, $set_name)
  {
    $template = '<div class="cq_gallery_item cq_gallery_item_content" title="%title%" longdesc="%description%">%object%</div>';

    $rendered_multimedia = '';

    foreach ($objects as $object)
    {
      $rendered_multimedia .= strtr($template, array(
        '%title%'       => $object->getTitle(),
        '%description%' => $object->getDescription(),
        '%object%'      => $object->getHTMLRepresentation($this->getOption('size'), $set_name)
      ));
    }

    return $rendered_multimedia;
  }

  protected function renderFooter()
  {
    return '<div class="cq_description"><div class="cq_description_title">&nbsp;</div><div class="cq_description_detail"></div></div>';
  }

  public function getDefaultOptions()
  {
    return array(
      'border_location' => 'bottom',
      'generate_styles' => true
    );
  }

}