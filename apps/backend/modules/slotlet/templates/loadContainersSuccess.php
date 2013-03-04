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
<?php use_helper('Javascript', 'Validation', 'Object') ?>

<?php foreach ($containers as $container):?>
<div id="cont_<?php echo $container ?>" class="container">
    <div id="cont_name_<?php echo $container?>" class="container_name"><?php echo($container)?>
	    <?php echo input_in_place_editor_tag(
	                'cont_name_'.$container,
	                'slotlet/editContainerName?id='.$container->getId(),
	                array('cols'        => 10)
	    )?>
    </div>
    <div>
    <?php echo link_to_remote(image_tag('backend/layout_delete.png',array('title'=>__('Borrar Contenedor'),'alt'=>__('Borrar Contenedor'))),array(
            'update'   => 'slotletlayout',
            'url'      => 'slotlet/deleteContainer?id='.$container->getId(),
            'loading'    => "Element.show('indicator')",
            'complete'   => "Element.hide('indicator')",
            'script'     =>  true,
            'confirm'   => __('¿Está seguro?'),
    )) ?>
    </div>
    <ul id="cont_slotlets_ul_<?php echo $container->getId() ?>" class="ul_slotlets">
    <?php foreach($container->getContainerSlotletsByPriority() as $container_slotlet) : ?>
        <li class="sortable" id="item_<?php echo $container_slotlet->getId() ?>">
            <div id="sl_<?php echo $container_slotlet->getId() ?>" class="container_sl"><?php echo $container_slotlet ?></div>
            <?php echo input_in_place_editor_tag(
                            'sl_'.$container_slotlet->getId(),
                            'slotlet/editContainerSlName?id='.$container_slotlet->getId(),
                            array('cols'        => 10)
                )?>
            <?php echo link_to_remote(image_tag('backend/bullet_delete.png',array('title'=>__('Borrar Slotlet'),'alt'=>__('Borrar Slotlet'))),array(
                             'update'   => 'slotletlayout',
                             'url'      => 'slotlet/deleteContainerSlotlet?id='.$container_slotlet->getId(),
                             'loading'    => "Element.show('indicator')",
                             'complete'   => "Element.hide('indicator')",
                             'script'     =>  true,
                             'confirm'   => __('¿Está seguro?'),
             ))?>
             <br/>
            <div class="sl_type">
                <?php echo __("Tipo: ").$container_slotlet->getSlotlet()->getType() ?>
	        </div>
          <?php if ($container_slotlet->getSlotlet()->getTYpe() == 'Feeds'):?>
            <?php echo select_tag('sl_rss_'.$container_slotlet->getId(), _get_options_from_objects($container_slotlet->getRssChannelActive()), array());?>
            
            <?php echo observe_field('sl_rss_'.$container_slotlet->getId() ,array(
                                  'script'    =>  true,
                                  'url'       =>  'slotlet/editRssChannel',
                          'with'    =>  "'rss_id=' + $('sl_rss_" . $container_slotlet->getId() . "').getValue() + '&id=" . $container_slotlet->getId() . "&_csrf_token=".csrf_token()."'"))?>

            <?php echo javascript_tag(remote_function(array(
                                  'script'    =>  true,
                                  'url'       =>  'slotlet/editRssChannel',
                                  'with'    =>  "'rss_id=' + $('sl_rss_" . $container_slotlet->getId() . "').getValue() + '&id=" . $container_slotlet->getId() ."&_csrf_token=".csrf_token()."'" )));
                                  ?>
          <div id="sl_visible_rss_<?php echo $container_slotlet->getId() ?>" class="container_sl"><?php 
          $visible= $container_slotlet->getVisibleRss();
          echo (isset($visible))?$visible:'Ingrese Cantidad'?></div>
            <?php echo input_in_place_editor_tag(
                            'sl_visible_rss_'.$container_slotlet->getId(),
                            'slotlet/editContainerSlVisibleRss?id='.$container_slotlet->getId(),
                            array('cols'        => 10)
                )?>
          <?php endif;?>
	   </li>
    <?php endforeach ?>
    </ul>
    <div id="feedback_<?php echo $container->getId() ?>" style="float: left;"></div>
</div>

<?php echo drop_receiving_element('cont_'.$container, array(
  'update'     => 'slotletlayout',
  'url'        => 'slotlet/addSlotlet',
  'with'       => "'container_id=".$container->getId()."&slotlet_id=' + encodeURIComponent(element.id)+'&_csrf_token=".csrf_token()."'",
  'accept'     => 'slotlets',
  'script'     =>  true,
  'hoverclass' => 'layout-active',
  'loading'    => "Element.show('indicator')",
  'complete'   => "Element.hide('indicator')"
)) ?>  
  
<?php echo sortable_element('cont_slotlets_ul_'.$container->getId(), array(
                            'url'     => 'slotlet/sort?container_id='.$container->getId().'&_csrf_token='.csrf_token(),
                            'update'  => 'feedback_'.$container->getId(),
                            'script'  => true,
                            'loading' => "Element.show('indicator')",
                            'complete'=> visual_effect('switch_off','indicator').
                                         visual_effect('highlight','feedback_'.$container->getId())
)) ?>	
<?php endforeach?>
<div id="empty">
</div>