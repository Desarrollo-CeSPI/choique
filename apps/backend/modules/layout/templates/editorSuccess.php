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
<?php use_stylesheet('backend/admin_theme_black.css') ?>
<?php use_stylesheet('backend/layout.css') ?>
<?php use_stylesheet('backend/jquery-ui/jquery-ui-1.8.4.custom.css') ?>
<?php use_helper('Object') ?>

<div class="section" id="available_slotlets_section" style="display: none;" title="<?php echo __('Slotlets disponibles') ?>">
  <?php include_component('layout', 'available') ?>
</div>

<?php include_partial('layout/mock') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Editar distribución "%name%" para %aspect%', array('%name%' => $layout->getName(), '%aspect%' => ($aspect == 'article' ? 'artículos' : 'portadas'))) ?></h1>

  <?php include_partial('layout/editor_messages') ?>

  <div class="section" id="layout_container_section">
    <h2><?php echo __('Distribución') ?></h2>

    <form action="<?php echo url_for('layout/update') ?>" method="post" onsubmit="Layout.read();" id="layout_editor">
      <input type="hidden" name="id" value="<?php echo $layout->getId() ?>" />
      <input type="hidden" name="aspect" id="aspect_holder" value="<?php echo $aspect ?>" />
      <textarea style="display: none;" name="layout" id="configuration_holder"><?php echo $configuration ?></textarea>

      <div class="layout_actions">
        <a href="#" onclick="Layout.row.add('first'); return false;" class="prepend_row">
          <?php echo __('Agregar fila arriba') ?>
        </a>
        <a href="#" onclick="Layout.row.add('last'); return false;" class="append_row">
          <?php echo __('Agregar fila abajo') ?>
        </a>

        <input type="submit" value="<?php echo __('Guardar cambios a la distribución') ?>" class="submit" onclick="jQuery('#layout_editor').removeAttr('target');" />

        <a href="<?php echo url_for('layout/index') ?>" class="list" onclick="return Layout.checkChanges();">
          <?php echo __('Cancelar y volver al listado') ?>
        </a>

        <div class="preview_action">
          <span class="preview">
            <input type="submit" name="_preview" value="<?php echo __('Previsualizar distribución') ?>" onclick="jQuery('#layout_editor').attr('target', '_preview');" />
            <?php echo __('en la sección') ?>
            <?php echo select_tag('preview_section', objects_for_select(SectionPeer::retrieveOrdered(), 'getName', 'getTitle', SectionPeer::retrieveHomeSection()->getName())) ?>
          </span>
        </div>

      </div>

      <table id="layout_container" cellspacing="1">
        <tbody>
          <?php foreach ($layout_configuration->getRows() as $row): ?>
            <tr class="row">
              <td>
                <table>
                  <tbody>
                    <tr>
                      <?php include_partial('layout/row_controls', array('row' => $row)) ?>

                      <?php foreach ($row->getColumns() as $column): ?>
                        <td class="column<?php count($column->getSlotlets()) == 0 and print ' empty' ?>">
                          <?php include_partial('layout/column_controls', array('hide' => true, 'column' => $column)) ?>

                          <?php foreach ($column->getSlotlets() as $slotlet): ?>
                            <table class="slotlet">
                              <thead>
                                <tr>
                                  <td colspan="2" class="name">
                                    <span class="slotlet_name"><?php echo $slotlet->getName() ?></span>
                                    <a href="#" onclick="Layout.slotlet.edit(this); return false;"><?php echo image_tag('backend/layer--pencil.png', array('alt' => __('Propiedades'), 'title' => __('Mostrar u ocultar las propiedades de este slotlet'))) ?></a>
                                  </td>
                                </tr>
                              </thead>
                              <tbody style="display: none;">
                                <tr>
                                  <?php include_partial('layout/slotlet_controls') ?>

                                  <td class="content">
                                    <div class="form">
                                      <input type="hidden" class="slotlet_class" value="<?php echo get_class($slotlet) ?>" />

                                      <div class="title"><?php echo __('Configuración') ?></div>

                                      <div class="form_content">
                                        <?php echo SlotletManager::getConfigurationForm(get_class($slotlet), $slotlet->options) ?>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          <?php endforeach; ?>
                        </td>
                      <?php endforeach; ?>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>
<ul class="sf_admin_actions">
    <li><?php echo button_to(__('list'), 'layout/list?id='.$layout->getId(), array (
  'class' => 'sf_admin_action_list',
)) ?></li>
</ul>
</div>

<div id="curtain" style="display: none;">
  <?php echo image_tag('backend/ajax-loader.gif') ?> <?php echo __('Guardando los cambios efectuados...') ?>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery(function() {
  Layout.slotlet.makeDraggable(Layout.root.find('.slotlet'));

  Layout.column.makeDroppable(Layout.root.find('.column'));

  Layout.url = '<?php echo url_for('layout/sconfig', true) ?>';
});
//]]>
</script>