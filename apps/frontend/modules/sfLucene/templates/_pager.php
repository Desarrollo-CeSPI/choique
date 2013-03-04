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
<div class="search-page-numbers">
  <span class="pager-title">
    <?php echo __('Páginas') ?>
  </span>
  
  <?php if ($pager->getPage() != $pager->getPreviousPage()): ?>
    <?php echo link_to(__('Anterior'), sprintf('@search?query=%s&page=%d', $query, $pager->getPreviousPage()), array('class' => 'bookend', 'id' => $pager->getPreviousPage())) ?>
  <?php endif ?>

  <?php foreach ($links as $page): ?>
    <?php if ($page == $pager->getPage()): ?>
      <strong><?php echo $page ?></strong>
    <?php else: ?>
      <?php echo link_to($page, sprintf('@search?query=%s&page=%d', $query, $page), array('id' => $page)) ?>
    <?php endif ?>
  <?php endforeach ?>

  <?php if ($pager->getPage() != $pager->getNextPage()): ?>
    <?php echo link_to(__('Siguiente'), sprintf('@search?query=%s&page=%d', $query, $pager->getNextPage()), array('class' => 'bookend', 'id' => $pager->getNextPage())) ?>
  <?php endif ?>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery(function() {
  jQuery('.search-page-numbers > a').click(function() {
    jQuery('#choique_search_page').val(jQuery(this).attr('id'));
    jQuery('#choique_search_form').submit();
    
    return false;
  });
});
//]]>
</script>