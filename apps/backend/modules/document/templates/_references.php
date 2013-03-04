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
      <?php $article_documents = $document->getArticleDocumentsJoinArticle() ?>
      <?php if(empty($article_documents)): ?>
        <li><?php echo __("Este documento no está referenciado en ningún artículo.") ?></li>
      <?php else: ?>
        <?php foreach ($article_documents as $article_document):?>
          <li><?php echo link_to($article_document->getArticle()->getTitle(), 'article/edit?id='.$article_document->getArticle()->getId(), array('title' =>__("Haga click aqui para editar el artículo"), 'target' => '_blank')) ?></li>
        <?php endforeach;?>
      <?php endif; ?>
    </ul>
  </li>
</ul>