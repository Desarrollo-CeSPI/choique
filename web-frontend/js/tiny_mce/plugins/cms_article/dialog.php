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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#cms_article_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
	<script type="text/javascript" src="../../../../sf/prototype/js/prototype.js"></script>
  <link rel="stylesheet" type="text/css" href="css/dialog.css" />
</head>
<body>

	<h3>{#cms_article_dlg.title}</h3>

<form onsubmit="CmsArticleDialog.insert(); return false;" action="#">
  <input id="element_id" name="element_id" value="" type="hidden" />

  <div class="form-row">
    <label for="query">{#cms_article_dlg.query}</label>
    <input id="query" name="query" type="text" class="text" onChange="search();" />
  </div>

  <script type="text/javascript">
  //<![CDATA[
    function search() {
      if ($('query').getValue() != '') {
        new Ajax.Updater('results', '../../../../backend.php/ajax/performSearch?on=0', {
          parameters: { query: $F('query') }
        });
      }
    }
  //]]>
  </script>

  <div id="results">
    {#cms_article_dlg.doSomething}
  </div>

  <div class="form-row">
    <label for="description">{#cms_article_dlg.description}:</label>
    <input id="description" name="description" type="text" class="text" />
  </div>

	<div class="mceActionPanel">
		<div style="float: right">
			<input type="button" id="insert" name="insert" value="{#cms_article_dlg.insert}" onclick="CmsArticleDialog.insert();" />
		</div>

		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="{#cms_article_dlg.cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

</body>
</html>