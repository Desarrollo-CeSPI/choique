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
<?php use_helper('I18N', 'Url', 'Tag') ?>

<div id="breaking-line">&nbsp;</div>

<?php foreach ($news_spaces as $row): ?>
<div class="preview">
  <div class="preview-content">
    <div class="preview-row">
      <?php foreach ($row as $news_space): ?>
        <div class="preview-td" width="<?php echo 100 / count($row) ?>%">
          <?php echo $news_space->getDisplayableArticle(true) ?>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>
<?php endforeach ?>

<div style="position: absolute; left: 10%; right: 10%; top: 0; border: 1px solid #6f8a91; border-top: none; padding: 10px; margin: 0 auto; -moz-border-radius-bottomleft: 4px; -moz-border-radius-bottomright: 4px; background-color: #b7c4c8; text-align: center;">
  <h1 style="color: #6f8a91; margin: 3px; font-size: 16px;">
    <?php echo __('Esto es una previsualización de portada') ?>.
  </h1>
  <h2 style="color: #000; margin: 3px; font-size: 13px;">
    <?php echo __('Recuerde que sus cambios no están guardados aún') ?>.
  </h2>
  <div style="text-align: right; margin-top: 6px;">
    <a href="#" onclick="window.close();" style="color: #fff; border: 1px outset #6f8a91; background-color: #6f8a91; padding: 3px;">
      <?php echo __('Cerrar ventana de previsualización y volver al editor') ?>
    </a>
  </div>
</div>