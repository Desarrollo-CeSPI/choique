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
 * SidePreviewMultimediaGallerySlotletRenderer
 *
 * Slotlet renderer class for multimedia galleries that renders a list of
 * elements on one side and a preview for the current element on the other
 * side.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SidePreviewMultimediaGallerySlotletRenderer extends BaseSlotletRenderer
{
  protected function doRender()
  {
    $this->options['class'] .= $this->options['class_suffix'];
    $this->options['size'] = 'g';

    $gallery = $this->getSlotlet()->getGallery($this->getOption('gallery_id'), $this->getOption('main_content'));

    $template = <<<SLOTLET
<div class="slotlet %class%">
  <div class="cqs_gallery">
    <div class="cqs_title">
      %title%
    </div>
    <div class="cqs_previews">
      %previews%
    </div>
    <div class="cqs_list">
      %list%
    </div>
    <div class="cqs_clearer">&nbsp;</div>
    <div class="cqs_description">&nbsp;</div>
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
  jQuery(document).ready(function() {
    Choique.side_multimedia_gallery.create('.%class% .cqs_gallery');
  });
//]]>
</script>
SLOTLET;

    return strtr($template, array(
      '%class%'    => $this->getOption('class'),
      '%title%'    => $this->getOption('title'),
      '%previews%' => $this->renderPreviews($gallery->getMultimediasByPriority()),
      '%list%'     => $this->renderList($gallery->getMultimediasByPriority())
    ));
  }

  protected function renderPreviews($elements)
  {
    $previews = '';
    $template = <<<PREVIEW
<div id="cqs_preview_%id%" class="cqs_preview">
  %multimedia%
</div>
PREVIEW;

    foreach ($elements as $multimedia)
    {
      $previews .= strtr($template, array(
        '%id%'         => $multimedia->getId(),
        '%multimedia%' => $multimedia->getHTMLRepresentation($this->getOption('size'))
      ));
    }

    return $previews;
  }

  protected function renderList($elements)
  {
    $list = '';
    $template = <<<LIST_ITEM
<a class="cqs_list_item %selected% %first%" href="#cqs_preview_%id%" title="%description%">
  %title%
</a>
LIST_ITEM;

    foreach ($elements as $i => $multimedia)
    {
      $list .= strtr($template, array(
        '%id%'       => $multimedia->getId(),
        '%selected%' => $i == 0 ? 'cqs_selected_item' : '',
        '%first%'    => $i == 0 ? 'cqs_first_item' : '',
        '%title%'    => $multimedia->getTitle(),
        '%description%' => $multimedia->getDescription()
      ));
    }

    return $list;
  }

  protected function renderableClasses()
  {
    return array('MultimediaGallerySlotlet');
  }

  public function getDescription()
  {
    return 'Previsualización + listado';
  }

  public function getName()
  {
    return 'Horizontal';
  }

  public function getDefaultOptions()
  {
    return array('class_suffix' => '_side');
  }

  public function getJavascripts()
  {
    return array('slotlets/choique.side_multimedia_gallery.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/side_multimedia_gallery.css');
  }
  
}