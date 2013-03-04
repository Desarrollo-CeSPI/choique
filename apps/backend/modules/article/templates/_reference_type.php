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
<div>
  <?php echo radiobutton_tag('article[reference_type]',
                             (string) Article::REFERENCE_TYPE_NONE,
                             $article->getReferenceType( )== Article::REFERENCE_TYPE_NONE,
                             array("onclick" => 'toggle_reference('.Article::REFERENCE_TYPE_NONE.')')) ?>

  <?php echo __(Article::NONE_STRING) ?>
</div>

<div>
  <?php echo radiobutton_tag('article[reference_type]',
                             Article::REFERENCE_TYPE_EXTERNAL,
                             $article->getReferenceType( )== Article::REFERENCE_TYPE_EXTERNAL,
                             array("onclick" => 'toggle_reference('.Article::REFERENCE_TYPE_EXTERNAL.')')) ?>

  <?php echo __(Article::EXTERNAL_STRING) ?>
</div>


<div>
  <?php echo radiobutton_tag('article[reference_type]',
                             Article::REFERENCE_TYPE_SECTION,
                             $article->getReferenceType() == Article::REFERENCE_TYPE_SECTION,
                             array("onclick" => 'toggle_reference('.Article::REFERENCE_TYPE_SECTION.');')) ?>

  <?php echo __(Article::SECTION_STRING) ?>
</div>
<div>
  <?php echo radiobutton_tag('article[reference_type]',
                             Article::REFERENCE_TYPE_ARTICLE,
                             $article->getReferenceType() == Article::REFERENCE_TYPE_ARTICLE,
                             array("onclick" => 'toggle_reference('.Article::REFERENCE_TYPE_ARTICLE.');')) ?>

  <?php echo __(Article::ARTICLE_STRING) ?>
</div>