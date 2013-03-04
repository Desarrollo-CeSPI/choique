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
<?php use_helper('Object', 'UJS', 'UJSJavascript') ?>

<div class="search-results-top show-all-search-results-top">
  <?php echo form_tag('article/showAll', array('id' => 'show_all_search_form')) ?>
    <h1><?php echo __('Novedades') ?></h1>
    <h2><?php echo __('Filtros') ?></h2>

    <div class="form-content">
      <div class="form-row">
        <?php echo label_for('show_all[text]', __('Con el texto:')) ?>
        <div class="content">
          <?php echo input_tag('show_all[text]', $search_text) ?>
        </div>
      </div>

      <div class="form-row">
        <?php echo label_for('show_all[section_id]', __('En la sección:')) ?>
        <div class="content">
          <?php echo UJS_write(input_hidden_tag('show_all[section_id]', (empty($section_id) ? ' ' : $section_id))); ?>
          <?php echo UJS_input_auto_complete_tag('section_autocomplete_query',
                                             (empty($section_id) ? '' : SectionPeer::retrieveByPK($section_id)->getTitle()),
                                             'section/autocomplete?_csrf_token='.csrf_token(),
                                             array('size' => 80),
                                             array('use_style' => true,
                                                   'indicator' => 'search_indicator',
                                                   'min_chars' => 3,
                                                   'after_update_element' => "function (inputField, selectedItem) { $('show_all_section_id').value = selectedItem.id; }")); ?>
          <?php echo UJS_write(image_tag('/sf/sf_admin/images/help.png', 'alt="Ayuda" title="' . __('Comience a tipear y se le ofrecerán opciones') . '" style="vertical-align: middle"')); ?>
          <noscript>
            <?php echo select_tag("show_all[section_id]", options_for_select(SectionPeer::getOptionsForSelect(), null, array('include_custom' => __("Cualquiera")))); ?>
          </noscript>
          <div id="search_indicator" style="display: none">
            <?php echo image_tag('common/indicator.gif', array('alt' => __('Por favor espere.'), 'title' => __('Por favor espere.'))) . ' ' . __('Buscando...') ?>
          </div>
        </div>
      </div>

      <div class="form-row" style="clear: both">
        <?php echo label_for('show_all[updated_at]', __('Fecha de última modificación:'), array('id' => 'updated_at_label')) ?>
        <div class="content">
          <?php echo UJS_input_date_range_tag('show_all[updated_at]',
                                          array('from' => $updated_at_from,
                                                'to' => $updated_at_to),
                                          array('rich' => true,
                                                'withtime' => false,
                                                'before' => label_for('', __('Desde:'), 'class=inner_label'),
                                                'middle' => label_for('', __('Hasta:'), 'class=inner_label'),
                                                'from_help' => __("Ingrese una fecha con formato dd/mm/yyyy"),
                                                'to_help' => __("Ingrese una fecha con formato dd/mm/yyyy"),
                                                'class' => 'date',
                                                'calendar_button_img' => '/sf/sf_admin/images/date.png')); ?>
        </div>
      </div>

      <div class="form-row">
        <?php echo label_for('show_all[status]', __('Con el estado:')) ?>
        <div class="content">
          <?php echo select_tag('show_all[status]',
                                options_for_select(array('published' => __('Publicado'),
                                                         'archived'  => __('Archivado'),
                                                         'any'       => __('Cualquiera')),
                                                   $status)) ?>
        </div>
      </div>

      <div class="form-actions">
        <div class="form-actions-child"><?php echo submit_tag(__('Buscar')) ?></div>
        <div class="form-actions-child"><?php echo UJS_button_to_function(__('Limpiar'), "reset_show_all_search_form();") ?></div>
      </div>
    </div>
  </form>
</div>

<?php echo javascript_tag("
function reset_show_all_search_form()
{
  $('show_all_text').value = '';
  $('show_all_section_id').value = '';
  $('show_all_status').down(0).selected = true;
  $('show_all_updated_at_from').value = '';
  $('show_all_updated_at_to').value = '';
  $('section_autocomplete_query').value = '';

  return false;
}") ?>

<div class="show-all-item">
  <?php echo format_number_choice('[0] sin resultados|[1] 1 resultado|(1,+Inf] %1% resultados',
                                  array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?>
</div>

<div class="search-results-bottom show-all-search-results-bottom">
<?php foreach ($pager->getResults() as $article): ?>
  <div class="show-all-item">
    <h1 class="title">
      <?php echo $article->getLinkedTitle() ?>
    </h1>
    <div class="updated_at">
      <?php echo __('Actualizado el') . ' ' . $article->getUpdatedAt('d/m/Y') ?>
    </div>
    <div class="heading">
      <?php echo $article->getHeading() ?>
    </div>
  </div>
<?php endforeach?>

<?php if ($pager->haveToPaginate()): ?>
  <div class="show-all-item pagination">
    <?php echo link_to('&laquo;', 'article/showAll?page=1', array('title' => "Ir a la primera página")) ?>
    <?php echo link_to('&lt;', 'article/showAll?page='.$pager->getPreviousPage(), array('title' => "Ir a la página anterior")) ?>

    <?php foreach ($pager->getLinks() as $page): ?>
      <?php echo link_to_unless($page == $pager->getPage(), $page, 'article/showAll?page='.$page, array('title' => "Ir a la página ".$page)) ?>
    <?php endforeach ?>

    <?php echo link_to('&gt;', 'article/showAll?page='.$pager->getNextPage(), array('title' => "Ir a la página siguiente")) ?>
    <?php echo link_to('&raquo;', 'article/showAll?page='.$pager->getLastPage(), array('title' => "Ir a la última página")) ?>
  </div>
<?php endif ?>
</div>
