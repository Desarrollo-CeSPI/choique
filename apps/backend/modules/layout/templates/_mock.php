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
<table class="mock">
  <tbody>
    <tr class="row">
      <td>
        <table>
          <tbody>
            <tr>
              <?php include_partial('layout/row_controls') ?>

              <td class="column empty">
                <?php include_partial('layout/column_controls', array('hide' => false)) ?>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>

    <tr>
      <td>
        <table class="slotlet">
          <thead>
            <tr>
              <td colspan="2" class="name">
                <span class="slotlet_name"></span>
                <a href="#" onclick="Layout.slotlet.edit(this); return false;"><?php echo image_tag('backend/layer--pencil.png', array('alt' => __('Propiedades'), 'title' => __('Mostrar u ocultar las propiedades de este slotlet'))) ?></a>
              </td>
            </tr>
          </thead>
          <tbody style="display: none;">
            <tr>
              <?php include_partial('layout/slotlet_controls') ?>

              <td class="content">
                <div class="form">
                  <input type="hidden" class="slotlet_class" value="" />

                  <div class="title"><?php echo __('Configuración') ?></div>
                  <div class="form_content"></div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>