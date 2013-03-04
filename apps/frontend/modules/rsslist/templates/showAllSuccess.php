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
<?php use_helper('Object', 'Javascript', 'CmsText') ?>

<?php if (count($feed_items) == 0): ?>

	<?php echo __('Sin resultados para mostrar');?>

<?php else: ?>

<div id="article_feed_<?php echo sfInflector::underscore($rss_channel->getTitle());?>_<?php echo $rss_channel->getId() ?>" class="feed">
  <div class="title">
      <?php echo __('Noticias para: ') . $rss_channel->getTitle(); ?>
  </div>
  <div class="source">
  	<?php echo __('Fuente: ') . link_to($rss_channel->getLink()); ?>
  </div>
  <div class="content">
    <?php foreach ($feed_items  as $feed): ?>
      <div class="content-child">
        <?php echo link_to($feed->getTitle(), $feed->getLink(), array('popup' => true)) ?>
        <div class="description">
          <?php 
          // function for delete html tags only permits <p>, <a>, <strong>, <em>
          // echo truncate_text(strip_tags($feed->getDescription(),'<p>, <a>, <strong>, <em>, <ul>, <li>' ),150);
          echo truncate(strip_tags($feed->getDescription(),'<p>, <a>, <strong>, <em>, <ul>, <li>' ),200,'...', true, true) ;
          ?>
        </div>
      </div>
    <?php endforeach?>
  </div>
  <div class="footer" >
    
  </div>
</div>

<?php endif; ?>