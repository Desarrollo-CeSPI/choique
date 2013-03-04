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
<?php use_stylesheet('backend/colorpicker.css') ?>
<?php use_javascript('colorpicker.js') ?>

<div class="choique-color-picker">
  <div class="cp-content"<?php $section->hasOwnColor() and print ' style="background-color: '.$section->getOwnColor().';"' ?>>
  </div>
</div>

<div style="clear: both;">
<a href="#" onclick="jQuery('#section_color').val(''); jQuery('.choique-color-picker .cp-content').css('backgroundColor', ''); return false;"><?php echo __('Borrar color') ?></a>
</div>

<?php echo input_hidden_tag('section[color]', $section->getColor(), array('id' => 'section_color')) ?>

<script type="text/javascript">
  jQuery('.choique-color-picker').ColorPicker({
    onShow: function (colpkr) {
      jQuery(colpkr).fadeIn(500);
      return false;
    },
    
    onHide: function (colpkr) {
      jQuery(colpkr).fadeOut(500);
      return false;
    },
    
    onChange: function (hsb, hex, rgb) {
      jQuery('.choique-color-picker .cp-content').css('backgroundColor', '#' + hex);

      jQuery('#section_color').val('#' + hex);
    }
  });

  <?php if ($section->hasOwnColor()): ?>
    jQuery('.choique-color-picker').ColorPickerSetColor('<?php echo $section->getOwnColor() ?>');
  <?php endif; ?>
</script>