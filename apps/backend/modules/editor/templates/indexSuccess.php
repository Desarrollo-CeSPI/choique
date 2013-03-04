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
<?php include_partial('editor/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Editor de estilos visuales - editando estilo "%flavor%"', array('%flavor%' => choiqueFlavors::getInstance()->current())) ?></h1>

  <div id="sf_admin_header">
    <?php include_partial('editor/flashes') ?>
    <div class="editor-note">
      <?php echo __('Tenga en cuenta que sus modificaciones serán visibles en la parte pública del sitio cuando seleccione la opción "Publicar cambios".') ?>
    </div>
  </div>

  <div id="sf_admin_content" class="css-editor-container">
    <table class="css-editor">
      <tbody>
        <tr>
          <td id="navigator-container">
            <div id="navigator-loader" class="editor-loader" style="display: none;">
              <?php echo __('Cargando, por favor espere...') ?>
            </div>

            <div id="navigator">
              <?php include_component('editor', 'navigator', array('base' => $base, 'base_path' => $base_path, 'b_upload' => $b_upload)) ?>
            </div>
            
            <div id="editor_uploader">
              <?php include_partial('editor/upload', array('base' => $b_upload)) ?>
            </div>
          </td>
          <td id="editor-container">
            <div id="editor-loader" class="editor-loader" style="display: none;">
              <?php echo __('Cargando, por favor espere...') ?>
            </div>
            
            <div id="editor-notice" class="initial-notice">
              <?php echo __('Seleccione un archivo de la izquierda para editarlo.') ?>
            </div>

            <div id="editor"></div>
          </td>
        </tr>
      </tbody>
    </table>

    <ul class="sf_admin_actions">
      <li><?php echo link_to(__('Publicar cambios'), 'editor/publish', array('class' => 'sf_admin_action_save')) ?></li>
    </ul>
  </div>
</div>

<script type="text/javascript">
//<![CDATA[

refreshUpload('<?php echo $b_upload?>');

function navigate(path)
{
  jQuery('#navigator').empty();
  jQuery('#navigator-loader').slideDown(50);

  jQuery.post('<?php echo url_for('editor/navigate?_csrf_token='.csrf_token()) ?>', { r: path }, function(data) {
    jQuery('#navigator-loader').slideUp(50, function() {
      jQuery('#navigator').html(data);
    });
  });
}

function edit(file)
{
  if (jQuery('#editor > form').is(':visible') && !confirm('<?php echo __('Actualmente está editando un archivo. ¿Desea descartar los cambios realizados y editar el nuevo archivo seleccionado?') ?>'))
  {
    return false;
  }

  jQuery('#editor-notice').slideUp(50);
  
  jQuery('#editor').fadeOut(250, function() {
    jQuery('#editor-loader').slideDown(50);

    jQuery.post('<?php echo url_for('editor/edit?_csrf_token='.csrf_token()) ?>', { f: file }, function(data) {
      jQuery('#editor-loader').slideUp(50, function() {
        jQuery('#editor').html(data).fadeIn(250);
      });
    });
  });
}


//]]>
</script>