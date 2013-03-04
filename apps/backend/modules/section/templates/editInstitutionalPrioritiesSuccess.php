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
<?php /*@var artcile_section ArticleSection*/ ?>
<div id="institutional_header"><h2>Editando prioridades de los Artículos pertenecientes a la sección: <?php echo SectionPeer::retrieveByPK($section_id)->getTitle() ?></h2></div>
<div id="indicator" style="height:20px;display:none">
	<?php echo image_tag('common/indicator.gif') ?> Actualizando Articulos...
</div>
<div id="ajax_container">
	<?php include_partial('layout_ajax',array('article_sections'=>$article_sections,'section_id'=>$section_id,'articles'=>$articles)); ?>
</div>
