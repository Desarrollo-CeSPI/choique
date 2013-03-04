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
<ul style="list-style: none;">
  <li>
    <?php echo __("Artículos:") ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php $articles = $multimedia->getAllArticles() ?>
      <?php if (empty($articles)): ?>
        <li><?php echo __("Este multimedia no está referenciado en ningún artículo.") ?></li>
      <?php else: ?>
        <?php foreach ($articles as $article):?>
          <li><?php echo link_to($article->getTitle(), 'article/edit?id='.$article->getId(), array('title' =>__("Haga click aqui para editar el artículo"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
  <li>
    <?php echo __("Atajos:") ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php $shortcuts = $multimedia->getShortcuts() ?>
      <?php if (empty($shortcuts)): ?>
        <li><?php echo __("Este multimedia no está referenciado en ningún atajo.") ?></li>
      <?php else: ?>
        <?php foreach ($shortcuts as $shortcut):?>
          <li><?php echo link_to($shortcut, 'shortcut/edit?id='.$shortcut->getId(), array('title' =>__("Haga click aqui para editar el atajo"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
  <li>
    <?php echo __("Galerías:") ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php $multimedia_galleries = $multimedia->getMultimediaGallerys() ?>
      <?php if (empty($multimedia_galleries)): ?>
        <li><?php echo __("Este multimedia no está referenciado en ninguna galería.") ?></li>
      <?php else: ?>
        <?php foreach ($multimedia_galleries as $multimedia_gallery):?>
          <li><?php echo link_to($multimedia_gallery->getGallery(), 'gallery/edit?id='.$multimedia_gallery->getGallery()->getId(), array('title' =>__("Haga click aqui para editar la galería"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
  <li>
    <?php echo __("Secciones:") ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php $sections = $multimedia->getSections() ?>
      <?php if (empty($sections)): ?>
        <?php echo __("Este multimedia no está referenciado en ninguna sección.") ?>
      <?php else: ?>
        <?php foreach ($sections as $section):?>
          <li><?php echo link_to($section->getTitle(), 'section/edit?id='.$section->getId(), array('title' =>__("Haga click aqui para editar la sección"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
</ul>