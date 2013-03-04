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
<?php use_helper('Object','Javascript') ?>
<?php include_partial('articles_section',array('article_sections'=>$article_sections,'section_id'=>$section_id)); ?>
<div id="article_list">
	<h4>Arrastrar el artículo hacia la lista de prioridades:</h4>
	<div id="articles">
		<?php include_partial('articles',array('articles'=>$articles)); ?>
	</div>
</div>
<?php
// The <div> ajax_container is in editInstitutionalPriorities.php template

echo drop_receiving_element('art_section', array(
  'update'     => 'ajax_container',
  'url'        => 'section/addAjaxArticle',
  'with'       => "'section_id=".$section_id."&id=' + encodeURIComponent(element.id) + '&_csrf_token=".csrf_token()."'",
  'accept'     => 'article',
  'script'     =>  true,
  'hoverclass' => 'active',
  'loading'    => "Element.show('indicator')",
  'complete'   => "Element.hide('indicator')"
)) ?>