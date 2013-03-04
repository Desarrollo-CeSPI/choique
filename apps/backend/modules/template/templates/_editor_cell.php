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
<?php use_helper('Javascript', 'Text') ?>

<?php $article = $news_space->getArticle() ?>
<?php $id = "${row_number}_${cell_number}" ?>

<li class="item" id="news_space_<?php echo $id ?>">

  <!-- form fields -->
  <?php echo input_hidden_tag("template[${row_number}][${cell_number}][type]", $news_space->getType()) ?>
  <?php echo input_hidden_tag("template[${row_number}][${cell_number}][article_id]", $article->getId()) ?>
  <?php echo input_hidden_tag("template[${row_number}][${cell_number}][order]", $cell_number) ?>
  <!-- /form fields -->

  <div class="interactions">
    <div class="interaction">
      <?php echo image_tag(
        'backend/move_handle.png',
        array(
          'alt'   => 'M',
          'title' => __('Cambiar orden'),
          'class' => 'handle'
        )) ?>
    </div>
    <div class="interaction">
    <?php if ($sf_user->hasCredential(array('reporter_admin', 'designer_admin'), false)): ?>
    <div class="interaction">
      <?php echo image_tag('backend/rep'.$news_space->getType().'.png',
              array(
                'id'    => "representation_image_${id}",
                'alt'   => 'R',
                'title' => $representations[$news_space->getType()]
            )) ?>
    </div>
    <div class="interaction">
      <?php echo link_to_function(image_tag(
            '/sf/sf_admin/images/edit.png',
            array(
              'alt'   => 'v',
              'title' => __('Cambiar representación')
            )),
           "$('selector_${id}').clonePosition(
              $('news_space_${id}'), { setHeight: false, offsetLeft: 20, offsetTop: 30 }
            ).setStyle(
              { zIndex: 9999, width: $('news_space_${id}').getWidth() + 'px', height: $('news_space_${id}').getHeight()  + 'px' }
            ).toggle();"
        ) ?>
    </div>
    <?php endif?>
    <div class="interaction">
      <?php echo select_tag('template['.$row_number.']['.$cell_number.'][width]', options_for_select(array_combine(range(5, 100, 5), array_map(create_function('$p', 'return $p.\'%\';'), range(5, 100, 5))), $news_space->getWidth(), array('include_custom' => __('auto'))), array('title' => __('Ancho de la celda'))) ?>
    </div>
      <?php echo link_to_function(image_tag('backend/delete_item.png',
            array(
              'alt'   => 'X',
              'title' => __('Quitar este artículo')
            )),
            "$('news_space_${id}').remove();",
            array('confirm' => __('¿Está seguro?'))
          ) ?>
    </div>
    <div id="selector_<?php echo $id ?>" class="selector" style="display: none;">
      <?php foreach ($representations as $index => $representation): ?>
        <?php echo link_to_function(image_tag(
                "backend/rep${index}.png",
                array(
                  'alt'   => $representation,
                  'title' => $representation
                )),
              "$('template_${id}_type').value = '$index';
               $('representation_image_${id}').src = '".image_path("backend/rep${index}.png", true)."';
               $('representation_image_${id}').title = '".__($representation)."';
               $('selector_${id}').hide();"
            ) ?>
      <?php endforeach ?>
      <?php //echo javascript_tag("$('selector_${id}').insert(new Element('img', { src: '".image_path('backend/hint_arrow.png', true)."' }).setStyle({ position: 'absolute', left: $('selector_${id}').cumulativeOffset().left + 'px', top: $('selector_${id}').cumulativeOffset().top + 'px', background: 'transparent' }));") ?>

      <div class="hint"><?php echo __('Seleccione una representación para este artículo') ?></div>
    </div>
  </div>
  <div class="description">
<!--
    <div class="multimedia <?php !$article->hasMultimedia() and print ' absent' ?>">
      <?php if ($article->hasMultimedia()): ?>
        <?php echo $article->getMultimedia()->getHTMLRepresentation('s') ?>
      <?php else: ?>
        <?php echo __('Sin imagen') ?>
      <?php endif ?>
    </div>
-->
    <div class="title" title="<?php echo $article->getTitle()  ?>">
      <?php echo truncate_text($article->getTitle(), 22)  ?>
    </div>
  </div>
</li>