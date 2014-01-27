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
<?php use_stylesheet('backend/editor') ?>
<table border="0">
  <tbody>
    <tr>
      <td style="vertical-align: top; border: 1px solid #ccc; background-color: #fff;">
        <?php echo textarea_tag('event[description]',
                                ($event) ? $event->getDescription() : "",
                                array('rich' => true,
                                      'size' => '30x20',
                                      'tinymce_options' => 'theme_advanced_styles: "Parrafo=paragraph;Subtitulo=subtitle;Destacado=highlighted;Epigrafe=epigraph",
                                                            theme_advanced_toolbar_align : "center",
                                                            theme_advanced_buttons1: "bold,italic,underline,justifyleft,justifycenter,justifyright,justifyfull,forecolor,|,bullist,numlist,outdent,indent,|,cut,copy,pastetext,pasteword,undo,redo,styleselect,removeformat,|,link,cms_multimedia,cms_article,cms_gallery,cms_document,cms_form,cms_rss",
                                                            theme_advanced_buttons2: "tablecontrols",
                                                            theme_advanced_buttons3: "",
                                                            theme_advanced_disable: "strikethrough",
                                                            content_css: "' . stylesheet_path('editor', true) . '?" + new Date().getTime(),
                                                            language:"' . substr($sf_user->getCulture(), 0, 2) . '"')) ?>
      </td>
     </tr>
  </tbody>
</table>