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
<?php use_helper('Object', 'UJS', 'UJSJavascript', 'Date') ?>

<div class="search-results-top events-search-results-top">
  <form action="<?php echo url_for('@events_search') ?>" method="post" id="events_search_form">
    <h1><?php echo __('Eventos') ?></h1>
    <h2><?php echo __('Filtros') ?></h2>

    <div class="events_search_form_content">
      <div class="events_search_form_row">
        <?php echo label_for('events_search[event_type]', __('Por categoría:')) ?>
        <div class="content">
          <?php echo select_tag('events_search[event_type]', objects_for_select(EventTypePeer::doSelect($event_type_criteria), 'getId', 'getTitle', $event_type, array('include_custom' => ' '))) ?>
        </div>
      </div>

      <div class="events_search_form_row">
        <?php echo label_for('events_search[title]', __('Por palabra:')) ?>
        <div class="content">
          <?php echo input_tag('events_search[title]', $title, array('placeholder' => __('Ingrese parte del título o descripción del evento a buscar'), 'title' => __('Ingrese parte del título o descripción del evento a buscar'))) ?>
        </div>
      </div>

      <div class="events_search_form_row">
        <?php echo label_for('events_search[organizer]', __('Por organizador:')) ?>
        <div class="content">
          <?php echo input_tag('events_search[organizer]', $organizer, array('placeholder' => __('Ingrese parte del nombre del organizador a buscar'), 'title' => __('Ingrese parte del nombre del organizador a buscar'))) ?>
        </div>
      </div>

      <div class="events_search_form_row" style="clear: both">
        <?php echo label_for('events_search[date]', __('Por fecha:')) ?>
        <div class="content">
          <?php echo UJS_input_date_range_tag('events_search[date]',
            array('from' => $date['from'], 'to' => $date['to']),
            array(
              'rich'                => true,
              'withtime'            => false,
              'before'              => label_for('', __('Desde:'), array('class' => 'inner_label')),
              'middle'              => label_for('', __('Hasta:'), array('class' => 'inner_label')),
              'from_help'           => __('Ingrese una fecha con formato dd/mm/aaaa'),
              'to_help'             => __('Ingrese una fecha con formato dd/mm/aaaa'),
              'class'               => 'date',
              'calendar_button_img' => '/sf/sf_admin/images/date.png'
          )) ?>
        </div>
      </div>

      <div class="form-actions">
        <div class="form-actions-child">
          <?php echo submit_tag(__('Buscar')) ?>
          <input type="submit" name="reset" value="<?php echo __('Limpiar') ?>" />
        </div>
      </div>
    </div>
  </form>
</div>

<div class="events_search_item">
  <?php echo format_number_choice('[0] sin resultados|[1] 1 resultado|(1,+Inf] %1% resultados', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?>
</div>

<div class="search-results-bottom events-search-results-bottom">
  <?php foreach ($pager->getResults() as $event): ?>
<?php /* @var $event Event */ ?>
    <div class="events_search_item">
      <h1 class="title">
        <?php echo $event->getEventType() ?>:
        <?php if ($event->hasArticle()): ?>
          <?php echo link_to($event->getTitle(), $event->getArticle()->getUrlReference()) ?>
        <?php else: ?>
          <?php echo $event->getTitle() ?>
        <?php endif; ?>
      </h1>
      <div class="address">
        <?php echo __('Lugar:') ?>
        <?php echo $event->getLocation() ?>
      </div>
      <div class="datetime">
        <?php if ($event->getBeginsAt('Y-m-d') == $event->getEndsAt('Y-m-d')): ?>
          <?php echo __('El %beginning% a las %start_hour%hs', array(
            '%beginning%'  => format_date($event->getBeginsAt('U'), 'P'),
            '%start_hour%' => $event->getBeginsAt('H:i')
          )) ?>
        <?php else: ?>
          <?php echo __('Desde el %beginning% al %end%, a las %start_hour%hs', array(
            '%beginning%'  => format_date($event->getBeginsAt('U'), 'P'),
            '%end%'        => format_date($event->getEndsAt('U'), 'P'),
            '%start_hour%' => $event->getBeginsAt('H:i')
          )) ?>
        <?php endif; ?>
      </div>
      <?php if ('' != trim($event->getOrganizer())): ?>
      <div class="description">
        <?php echo __('Organiza:') ?>
        <?php echo $event->getOrganizer() ?>
      </div>
      <?php endif; ?>
      <?php if ('' != trim($event->getDescription())): ?>
      <div class="description">
        <?php echo $event->getDescription() ?>
      </div>
      <?php endif; ?>
    </div>
  <?php endforeach?>

  <?php if ($pager->haveToPaginate()): ?>
    <div class="events_search_item pagination">
      <?php echo link_to('&laquo;', '@events_search?page=1', array('title' => "Ir a la primera página")) ?>
      <?php echo link_to('&lt;', '@events_search?page='.$pager->getPreviousPage(), array('title' => "Ir a la página anterior")) ?>

      <?php foreach ($pager->getLinks() as $page): ?>
        <?php echo link_to_unless($page == $pager->getPage(), $page, '@events_search?page='.$page, array('title' => "Ir a la página ".$page)) ?>
      <?php endforeach ?>

      <?php echo link_to('&gt;', '@events_search?page='.$pager->getNextPage(), array('title' => "Ir a la página siguiente")) ?>
      <?php echo link_to('&raquo;', '@events_search?page='.$pager->getLastPage(), array('title' => "Ir a la última página")) ?>
    </div>
  <?php endif ?>
</div>