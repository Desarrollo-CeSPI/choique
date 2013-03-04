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

<div id="no_reference">
  <?php echo __('El tipo seleccionado no requiere datos adicionales') ?>
</div>


<div id="external_reference">
  <?php echo input_tag('article[external_reference_value]',
                       (($article && $article->getReferenceType() == Article::REFERENCE_TYPE_EXTERNAL) ? $article->getReference() : null),
                       array('size' => '80')) ?>
</div>

<div id="section_reference">
  <?php $value = ($article->getReferenceType() == Article::REFERENCE_TYPE_SECTION) ? $article->getReference() : " " ?>
  <?php $related_object = (0 == strcmp(trim($value), '') ? null : SectionPeer::retrieveByPK($value)) ?>
  <?php echo input_hidden_tag('article[section_reference_value]', $value) ?>
  <?php echo input_auto_complete_tag('section_autocomplete_query_bis',
                                     $related_object,
                                     'section/autocomplete?for_filter=true&_csrf_token='.csrf_token(),
                                     array('size' => 100),
                                     array('use_style' => true,
                                           'indicator' => 'search_indicator',
                                           'min_chars' => 3,
                                           'after_update_element' => "function (inputField, selectedItem) { $('article_section_reference_value').value = selectedItem.id; }")) ?>
</div>

<div id="article_reference">
  <?php $value = ($article->getReferenceType() == Article::REFERENCE_TYPE_ARTICLE) ? $article->getReference() : " " ?>
  <?php $related_object = (0 == strcmp(trim($value), '') ? null : ArticlePeer::retrieveByPK($value)) ?>
  <?php echo input_hidden_tag('article[article_reference_value]', $value) ?>
  <?php echo input_auto_complete_tag('article_autocomplete_query',
                                     $related_object,
                                     'article/autocomplete?_csrf_token='.csrf_token(),
                                     array('size' => 100),
                                     array('use_style' => true,
                                           'indicator' => 'search_indicator',
                                           'min_chars' => 3,
                                           'after_update_element' => "function (inputField, selectedItem) { $('article_article_reference_value').value = selectedItem.id; }")) ?>
</div>

<?php echo javascript_tag("
function toggle_reference(option)
{
  switch(parseInt(option)) {
    case ".Article::REFERENCE_TYPE_NONE.":
      $('no_reference').show();
      $('external_reference').hide();
      $('section_reference').hide();
      $('article_reference').hide();
      break;
    case ".Article::REFERENCE_TYPE_EXTERNAL.":
      $('no_reference').hide();
      $('external_reference').show();
      $('section_reference').hide();
      $('article_reference').hide();
      break;
    case ".Article::REFERENCE_TYPE_SECTION.":
      $('no_reference').hide();
      $('external_reference').hide();
      $('section_reference').show();
      $('article_reference').hide();
      break;
    case ".Article::REFERENCE_TYPE_ARTICLE.":
      $('no_reference').hide();
      $('external_reference').hide();
      $('section_reference').hide();
      $('article_reference').show();
      break;      
      
  }

  return false;
}

toggle_reference(" . $article->getReferenceType() . ");") ?>