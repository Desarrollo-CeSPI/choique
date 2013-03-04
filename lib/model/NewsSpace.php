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
<?php

/**
 * Subclass for representing a row from the 'news_space' table.
 *
 * 
 *
 * @package lib.model
 */ 
class NewsSpace extends BaseNewsSpace
{
	/**
	 *	Return a string holding the Article associated to
	 *	this NewsSpace, already represented with the
	 *	corresponding representation type.
	 *
	 *	@return string
	 */
	function getDisplayableArticle($editor = false, $width_percentage = 100)
	{
		return $this->getArticle()->getRepresentationByNumber($this->getType(), $editor, $width_percentage);
	}
	
	function __toString()
	{
		return sprintf('[%d, %d] id: %d art_id: %d type: %d tmp_id: %d', $this->getRowNumber(), $this->getColumnNumber(), $this->getId(), $this->getArticleId(), $this->getType(), $this->getTemplateId());
	}
}