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

<?php if ($article_sections = $section->getArticleSectionsByPriority()): ?>
  <ul style="list-style:none;" id="sortable-list">
  <?php foreach ($article_sections as $article_section): ?>
    <li id="article_section_<?php echo $article_section->getId() ?>">
      <?php echo $article_section->getArticle() ?><?php echo link_to_remote(image_tag('backend/bullet_delete', array('title' => 'desasociar artículo',
                                                                                                             'alt'=>'Desasociar artículo')),
                                                                            array('update'   => 'article_section',
                                                                                  'url'      => 'section/deleteArticleSection?_csrf_token='.csrf_token().'&article_section_id='.$article_section->getId().'&id='.$section->getId(),
                                                                                  'script'   => true,
                                                                                  'loading'    => "Element.show('indicator')",
                                                                                  'complete'   => "Element.hide('indicator')")) ?>
    </li>
  <?php endforeach ?>
  </ul>
  <?php echo sortable_element('sortable-list', array('url' => 'section/sortArticleSections?_csrf_token='.csrf_token(),
                                                     'script' => true,
                                                     'loading' => 'Element.show("indicator")',
                                                     'complete' => 'Element.hide("indicator")')) ?>
<?php else: ?>
  <?php echo __('La sección no tiene novedades.') ?>
<?php endif ?>