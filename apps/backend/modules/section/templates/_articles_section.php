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
<div id="art_section_list">
	<h4>Lista de prioridades:</h4>
	<div id="art_section" class="article_section">
		<ul id="articles_section">
			<?php foreach($article_sections as $article_section): ?>
				<li class="sortable" id="item_<?php echo $article_section->getId()?>"><?php echo $article_section->getArticle()->getTitle()  ?>
				<?php
				// The <div> ajax_container is in editInstitutionalPriorities.php template
				echo link_to_remote(image_tag('bullet_delete',array('title'=>'Desasociar artículo','alt'=>'Desasociar artículo')),array(
										  'update'   => 'ajax_container',
	    								  'url'      => 'section/removeAjaxArticle?id='.$article_section->getId().'&section_id='.$article_section->getSectionId(),
										  'script'   => true,
										  'loading'    => "Element.show('indicator')",
							  			  'complete'   => "Element.hide('indicator')"
				)) ?>
				</li>
			<?php endforeach; ?>
		</ul>
		&nbsp;
		<div id="feedback"></div>
	</div>
</div>
<?php echo sortable_element('articles_section', array(
  'url'    => 'section/sort',
  'update' => 'feedback',
  'script' => true,
  'loading'    => "Element.show('indicator')",
  'complete'   => "Element.hide('indicator')"
)) ?>