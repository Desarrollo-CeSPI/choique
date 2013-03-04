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
<div class="sitemap">
  <div class="sitemap-content">
  
      <h1 class="sitemap-title">
        <?php echo __('Mapa del Sitio') ?>
      </h1>
 

    <div class="container">
      <?php $first_level_count = (count($first_level) > 0)?count($first_level):1; ?>
      <?php $col_width = 100 / $first_level_count; ?>
      <?php foreach ($first_level as $first_level_section): ?>
        <div class="cols" style="width:<?php echo $col_width;?>%;">
          <?php echo link_to($first_level_section->getTitle(), '@template_by_name?name=' . $first_level_section->getName()) ?>
          <?php foreach ($first_level_section->getPublishedChildren() as $second_level_section): ?>
            <div class="second-level-group">
              <?php echo link_to($second_level_section->getTitle(), '@template_by_name?name=' . $second_level_section->getName()) ?>
              <div class="third-level-group">
                <?php foreach ($second_level_section->getPublishedChildren() as $third_level_section): ?>
                  <?php echo link_to($third_level_section->getTitle(), '@template_by_name?name=' . $third_level_section->getName()) ?> <br />
                <?php endforeach ?>
              </div> <!--third-level-group-->
            </div><!--end second-level-group-->
          <?php endforeach ?>
        </div><!--end cols-->
      <?php endforeach ?>
    </div><!--end container-->
    
  </div><!--end sitemap-content-->
  
</div><!--end sitemap-->