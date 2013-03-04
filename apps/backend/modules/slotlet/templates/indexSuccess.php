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
<?php use_helper('Javascript') ?>

<?php if ($sf_flash->has('notice')): ?>
<div class="save-ok" style="background-color: #a5f986; border: 1px solid #73b65a; text-align: right; margin: 3px; padding: 4px;">
  <h2 style="font-size: 11px; color: #275d12;"><?php echo __($sf_flash->get('notice')) ?></h2>
</div>
<?php endif; ?>


<div id="slotlet_renderer">
    <h2><?php echo __("Slotlets")?>:</h2>
    
    <?php echo form_remote_tag(array(
        'update'   => 'slotlet_list',
        'url'      => 'slotlet/createSlotlet',
        'loading'  => "Element.show('indicator')",
        'complete' => "Element.hide('indicator')",
        'script'   =>  true,
    )) ?>
        <?php echo label_for('slotlet_name',__('Nombre Slotlet:')) ?>
        <?php echo input_tag('slotlet_name') ?>
        <?php echo label_for('cls',__('Tipo:')) ?>
        <?php echo select_tag('cls',SlotletRenderer::getSlotletsClases()) ?>
        <?php echo submit_tag(__('Agregar Slotlet'),array('id'=>'add_slotlet'))?>
    </form>
    <div id="slotlet_list">
        <?php include_partial('slotlets',array('slotlets'=>$slotlets)); ?>
    </div>
    <?php echo javascript_tag(remote_function(array(
        'update'   => 'slotletlayout',
        'url'      => 'slotlet/loadContainers',
        'loading'    => "Element.show('indicator')",
        'complete'   => "Element.hide('indicator')",
        'script'     =>  true,
         
    ))) ?>
    <div style="height:20px;float: right;">
        <p id="indicator" style="display:none">
          <?php echo image_tag('common/indicator.gif') ?>Actualizando Layout...
        </p>
    </div>
    <h2><?php echo __("Contenedores")?>:</h2>
    <?php echo form_remote_tag(array(
        'update'   => 'slotletlayout',
        'url'      => 'slotlet/addContainer',
        'loading'    => "Element.show('indicator')",
        'complete'   => "Element.hide('indicator')",
        'script'     =>  true,
    )) ?>
    <?php echo label_for('container_name',__('Nombre Contenedor:')) ?>
    <?php echo input_tag('container_name') ?>
    <?php echo submit_tag(__('Agregar Contenedor'),array('id'=>'add_container'))?>
    </form>
    <div><?php echo image_tag('/sf/sf_admin/images/help.png')?><?php echo __("Haga click sobre el nombre de un contenedor para editarlo")?></div>
    <p><?php echo image_tag('/sf/sf_admin/images/help.png')?><?php echo __("Para modificar el orden dentro de los contenedores, arrastre los elementos")?></p>
    <div id="slotletlayout">
    </div>
</div>