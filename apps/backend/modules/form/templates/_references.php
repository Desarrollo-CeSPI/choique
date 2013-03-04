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
<?php echo __("Artículos:") ?>
<ul style="list-style: none; margin-left: 2em">
  <?php $articles = $form->getAllArticles() ?>
  <?php if (empty($articles)): ?>
    <li><?php echo __("Este form no está referenciado en ningún artículo.") ?></li>
  <?php else: ?>
    <?php foreach ($articles as $article):?>
      <li><?php echo link_to($article->getTitle(), 'article/edit?id='.$article->getId(), array('title' =>__("Haga click aqui para editar el artículo"), 'target' => '_blank')) ?></li>
    <?php endforeach;?>
  <?php endif; ?>
</ul>