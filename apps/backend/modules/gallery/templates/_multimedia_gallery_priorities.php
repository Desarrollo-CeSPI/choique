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
<?php use_helper('Javascript') ?>
<?php use_stylesheet('backend/editor') ?>

<?php $width = CmsConfiguration::get('small_image_default_width', 75) + 20 ?>
<div id="gallery_editor">
  <div id="multimedia_gallery"> 
    <ul style="list-style:none;" id="sortable-list" class="elements-list">
    <?php foreach ($multimedia_gallerys as $multimedia_gallery): ?>
      <li id="multimedia_gallery_<?php echo $multimedia_gallery->getId() ?>" class="element" style ="width:<?php echo $width ?>px; cursor: move;">
          <div class="delete-icon">
            <?php echo link_to_remote(image_tag('backend/delete.gif', array('alt' => __('Eliminar de la lista'), 'title' => __('Eliminar de la lista'))),
                                      array('url'      => 'gallery/deleteMultimediaGallery?_csrf_token='.csrf_token().'&multimedia_gallery_id='.$multimedia_gallery->getId().'&id='.$gallery->getId(),
                                            'update'   => 'multimedia_gallery',
                                            'with'     => "'gallery_id=".$gallery->getId()."&multimedia_id=".$multimedia_gallery->getMultimediaId()."'",
                                            'loading'  => "Element.show('searching')",
                                            'complete' => "Element.hide('searching'); $('message').innerHTML='".__("Modificaciones guardadas")."'",
                                            'script'   => true)) ?>
          </div>
          <div class="thumb">
            <?php echo $multimedia_gallery->getMultimedia()->getHTMLRepresentation('l') ?>
          </div>
          <div class="title">
            <?php echo $multimedia_gallery->getMultimedia()->__toString() ?>
          </div>
      </li>
    <?php endforeach ?>
    </ul>
    <?php echo sortable_element('sortable-list', array('url'        => 'gallery/sortMultimediaGallery?_csrf_token='.csrf_token(),
                                                       'overlap'    => 'horizontal',
                                                       'constraint' => '',
                                                       'script'     => true,
                                                       'loading'    => 'Element.show("searching")',
                                                       'complete'   => 'Element.hide("searching")')) ?>
  </div>
  <div style="clear: both; font-size: 1px; height: 10px;">&nbsp;</div>
</div>

<?php echo javascript_tag("
  $$('#gallery_editor .elements-list li.element .thumb a').each(function (e,i) {
    $(e).writeAttribute('onclick', '');
    $(e).writeAttribute('href', '');
  });") ?>