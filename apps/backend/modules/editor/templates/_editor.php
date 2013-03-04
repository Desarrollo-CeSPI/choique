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
<?php if (isset($error) && $error): ?>
  <div class="editor-error">
    <?php echo __($error) ?>
  </div>
<?php else: ?>

<form action="<?php echo url_for('editor/save') ?>" method="post" id="editor-form">
  <input type="hidden" name="f" value="<?php echo $file ?>" />

  <div class="editor-actions">
    <span class="base-path">
      <?php echo $file ?>
    </span>

    <a href="#" class="editor-action editor-action-discard" onclick="if (confirm('<?php echo __('¿Está seguro que desea descartar sus modificaciones?') ?>')) { discard(); }; return false;">
      <?php echo __('Descartar cambios') ?>
    </a>

    <a href="#" class="editor-action editor-action-delete" onclick="if (confirm('<?php echo __('¿Está seguro que desea eliminar el archivo?')?>')) { deleteFile();}; return false;">
      <?php echo __('Eliminar') ?>
    </a>

    <a href="#" class="editor-action editor-action-save" onclick="save(); return false;">
      <?php echo __('Guardar') ?>
    </a>
  </div>

  <textarea id="editor-ta" name="c"><?php echo $content ?></textarea>
</form>

<script type="text/javascript">
//<![CDATA[
var code_mirror = CodeMirror.fromTextArea(document.getElementById('editor-ta'), {
  lineNumbers: true
});

function discard()
{
  jQuery('#editor').fadeOut(250, function() {
    jQuery('#editor').empty();
    jQuery('#editor-notice').slideDown(50);
  });
}

function deleteFile()
{
  jQuery.post('<?php echo url_for('editor/delete') ?>', jQuery('#editor-form').serializeArray(), function(data) {
      jQuery('#editor-loader').slideUp(50, function() {
        jQuery('#editor').html(data).fadeIn(250);
        jQuery('#editor-notice').slideDown(50);
        refreshNavigator();
      });
    });
}

function save()
{
  code_mirror.save();
  
  jQuery('#editor').fadeOut(250, function() {
    jQuery('#editor-loader').slideDown(50);

    jQuery.post('<?php echo url_for('editor/save') ?>', jQuery('#editor-form').serializeArray(), function(data) {
      jQuery('#editor-loader').slideUp(50, function() {
        jQuery('#editor').html(data).fadeIn(250);
        jQuery('#editor-notice').slideDown(50);
      });
    });
  });
}
//]]>
</script>

<style type="text/css">
.CodeMirror
{
  border: 1px solid #ccc;
}
</style>

<?php endif; ?>