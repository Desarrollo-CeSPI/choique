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
<?php use_helper('I18N', 'sfLucene') ?>

<div class="search-results-top">
  <form action="<?php echo url_for('@search') ?>" method="post" class="choique_search" id="choique_search_form">
    <input type="hidden" name="page" id="choique_search_page" value="" />
    <div class="choique_search_container">
      <div class="query_container">
        <input class="choique_search_query" type="text" autocomplete="off" name="query" accesskey="q" value="<?php echo htmlspecialchars($query) ?>" placeholder="<?php echo __('Ingrese su búsqueda') ?>" />
      </div>

      <div class="fields_container">
        <?php echo __('Buscar sobre:') ?>
        <ul>
          <li>
            <?php echo checkbox_tag('fields[all]', true, $fields['all']) ?>
            <label for="fields_all"><?php echo __('Todo el contenido') ?></label>
          </li>
          <li>
            <?php echo checkbox_tag('fields[titles]', true, $fields['titles'], array('class' => 'field')) ?>
            <label for="fields_titles"><?php echo __('Solo títulos') ?></label>
          </li>
          <li>
            <?php echo checkbox_tag('fields[articles]', true, $fields['articles'], array('class' => 'field')) ?>
            <label for="fields_articles"><?php echo __('Solo artículos') ?></label>
          </li>
          <li>
            <?php echo checkbox_tag('fields[uploads]', true, $fields['uploads'], array('class' => 'field')) ?>
            <label for="fields_uploads"><?php echo __('Solo documentos') ?></label>
          </li>
        </ul>
      </div>

      <div class="search_actions_container">
        <input type="submit" name="local" value="<?php echo __('Buscar') ?>" />
        <input type="submit" name="google" value="<?php echo __('Buscar con Google') ?>" />
      </div>
    </div>
  </form>
</div>

<?php if ($results !== false): ?>
  <div class="search-heading">
    <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?>
  </div>

  <?php if ($results > 0): ?>
    <div class="search-results-bottom">
      <ol start="<?php echo $pager->getFirstIndice() ?>" class="search-results">
        <?php foreach ($pager->getResults() as $result): ?>
          <li><?php include_search_result($result, $query) ?></li>
        <?php endforeach ?>
      </ol>

      <?php if ($pager->haveToPaginate()): ?>
        <?php include_component('sfLucene', 'pager', array('pager' => $pager, 'radius' => sfConfig::get('app_lucene_pager_radius', 5), 'category' => null)) ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
//<![CDATA[
jQuery(function() {
  var $all = jQuery('#fields_all');

  $all.change(function() {
    var $this = jQuery(this);
    var checkboxes = $this.closest('.fields_container').find('input.field:checkbox');

    if ($this.is(':checked'))
    {
      checkboxes
        .attr('checked', true)
        .attr('disabled', true);
    }
    else
    {
      checkboxes
        .attr('checked', false)
        .removeAttr('disabled');
    }
  });

  if ($all.is(':checked'))
  {
    $all.change();
  }
});
//]]>
</script>