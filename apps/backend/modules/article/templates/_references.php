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
    <?php echo __("Portadas:") ?>
    <?php $news_spaces = $article->getNewsSpaces() ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php if(empty($news_spaces)): ?>
        <li><?php echo __("Este artículo no está referenciado en ninguna portada.") ?></li>
      <?php else: ?>
        <?php foreach ($news_spaces as $news_space):?>
          <?php $template = $news_space->getTemplate() ?> 
          <li><?php echo link_to(__("Portada %%name%%", array('%%name%%' => $template)), 'template/edit?id='.$template->getId(), array('title' =>__("Haga click aqui para editar la portada"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
  <li>
    <?php echo __("Artículos:") ?>	
    <?php $article_references = $article->getArticleArticlesRelatedByArticleRefereeId() ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php if(empty($article_references)): ?>
        <li><?php echo __("Este artículo no está referenciado en ningún artículo.") ?></li>
      <?php else: ?>
        <?php foreach ($article_references as $article_reference):?>
          <?php $article_referer = $article_reference->getArticleRelatedByArticleRefererId() ?>
          <li><?php echo link_to(__("Artículo %%name%%", array('%%name%%' => $article_referer)), 'article/edit?id='.$article_referer->getId(), array('title' =>__("Haga click aqui para editar el artículo"), 'target' => '_blank')) ?></li>
      <?php endforeach;?>
    <?php endif; ?>
    </ul>
  </li>
  <?php if($article->getType() == Article::NEWS):?>
  <li>
    <?php echo __("Secciones:") ?>  
    <?php $article_sections = $article->getArticleSectionsJoinSection() ?>
    <ul style="list-style: none; margin-left: 2em">
      <?php if(empty($article_sections)): ?>
        <li><?php echo __("Este artículo no está referenciado en ninguna sección.") ?></li>
      <?php else: ?>
        <?php foreach ($article_sections as $article_section):?>
          <?php $section = $article_section->getSection() ?>
          <li><?php echo link_to(__("Sección %%name%%", array('%%name%%' => $section)), 'section/edit?id='.$section->getId(), array('title' =>__("Haga click aqui para editar la sección"), 'target' => '_blank')) ?></li>
      <?php endforeach;?>
    <?php endif; ?>
    </ul>
  </li>
  <?php endif;?>
</ul>