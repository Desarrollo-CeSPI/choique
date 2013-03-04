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

<?php foreach ($slotlets as $slotlet): ?>
    <div id="<?php echo $slotlet->getId() ?>" class="slotlets">
	    <div class="sl_name">
        <?php echo $slotlet->getName()?>
        <?php echo link_to_remote(image_tag('backend/bullet_delete.png', array('title'=>__('Borrar Slotlet'),'alt'=>__('Borrar Slotlet'))),array(
          'update'   => 'slotlet_list',
          'url'      => 'slotlet/deleteSlotlet?id='.$slotlet->getId(),
          'loading'  => "Element.show('indicator')",
          'complete' => "Element.hide('indicator')",
          'script'   =>  true,
          'confirm'  => __('¿Está seguro?'),
        )) ?>
	    </div>
	    <div class="sl_type">
        <?php echo $slotlet->getType()?>
	    </div>
	    <?php echo draggable_element($slotlet->getId(), array('revert' => true, 'script' => true)) ?>
    </div>    
<?php endforeach ?>