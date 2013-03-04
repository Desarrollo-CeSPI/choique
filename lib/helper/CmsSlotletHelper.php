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
<?php

  /**
   *  Returns all the slotlets contained in $container_name ordered by priority
   *  @param  string container_name
   *  @param  array options parameters for each type of slotlet
   *  @return string <div>'s containing each slotlet representation 
   *  Options must be passed in the following way
   *  $options('sl_type'=>$parameter) 
   *  <b>Examples:</b>
   *  <code>
   *      include_container('right', array('separator'=>30));
   *  </code>
   */
	function include_container($container_name, $options = array())
	{
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      sfLoader::loadHelpers(array('Layout'));

      $section = sfContext::getInstance()->getRequest()->getParameter('section_name');

      return include_layout_for_section($section, $sf_content, $options);
    }

		sfLoader::loadHelpers(array('CmsEscaping'));

		$container_name = strtolower($container_name);

		$c = new Criteria();
		$c->add(ContainerPeer::NAME,$container_name);
		$container=ContainerPeer::doSelectOne($c);

		$html = '';
		if ($container)
    {
			$c->clear();
			$c->addDescendingOrderByColumn(ContainerSlotletPeer::PRIORITY);

			foreach ($container->getContainerSlotlets($c) as $container_slotlet)
      {
				$options_for_slotlet = array(
            'id'                   => escape_string($container_slotlet->getName()),
            'container_slotlet_id' => $container_slotlet->getId(),
            'section_name'         => sfContext::getInstance()->getRequest()->getParameter("section_name", Section::HOME),
            'title'                => $container_slotlet->getName()
          );

				$options_for_slotlet = array_merge($options, $options_for_slotlet);
          
        if ($container_slotlet->getSlotlet()->getType() == 'Feeds')
        {
          $options_for_rss = array(
              'rss_channel_id' => $container_slotlet->getRssChannelId(),
              'visible_rss'    => $container_slotlet->getVisibleRss()
            );
          $options_for_slotlet = array_merge($options_for_rss, $options_for_slotlet);
        }

				$sl_representation = call_user_func(array($container_slotlet->getSlotlet()->getCls(), 'getSlotlet'), $options_for_slotlet);
				$html .= $sl_representation;
			}
		}

		return $html;
  }