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

<div id="gallery_editor">
  <?php if (count($multimedias = $sf_user->getAttribute('multimedia'))): ?>

    <?php $width = CmsConfiguration::get('small_image_default_width', 75) + 20 ?>

    <?php echo link_to(image_tag('/sf/sf_admin/images/edit.png', array('alt' => '')) . ' ' . __('Modificar el orden de los elementos'),
                       'gallery/editPriorities?id=' . $gallery->getId(),
                       array('class'   => 'modify-order-button',
                             'confirm' => __('¿Está seguro que desea continuar? Las modificaciones no guardadas se perderán'))) ?>
    <div style="clear: both; font-size: 1px; height: 1px;">&nbsp;</div>

    <div class="elements-list">
      <?php foreach ($multimedias as $multimedia): ?>
        <div class="element" style="width: <?php echo $width ?>px;">
          <div class="delete-icon">
            <?php echo link_to_remote(image_tag('backend/delete.gif', array('alt' => __('Eliminar de la lista'), 'title' => __('Eliminar de la lista'))),
                                      array('url'    => 'gallery/deleteTmpMultimedia',
                                            'update' => 'multimedia_gallery',
                                            'with'   => "'gallery_id=".$gallery->getId()."&multimedia_id=".$multimedia->getMultimediaId()."&_csrf_token=".csrf_token()."'",
                                            'script' => true)) ?>
          </div>
          <div class="thumb">
            <?php echo $multimedia->getMultimedia()->getHTMLRepresentation('s') ?>
          </div>
          <div class="title">
            <?php echo $multimedia->getMultimedia()->__toString() ?>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <div style="clear: both; font-size: 1px; height: 10px;">&nbsp;</div>

  <?php else: ?>

    <?php echo __('La galería no tiene elementos aún. Puede comenzar a agregarlos utilizando el campo de arriba.') ?>

  <?php endif ?>
</div>