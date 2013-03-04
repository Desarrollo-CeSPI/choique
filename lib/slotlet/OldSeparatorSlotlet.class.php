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

class OldSeparatorSlotlet implements SlotletInterface
{
  /* Interface implementation */
  public static function getSlotletMethods()
  {
    return array('getSlotlet');
  }

  public static function getSlotletName() 
  {
    sfLoader::loadHelpers(array('I18N'));

    return __('Separador');
  }
  
 /**
   *  Returns a piece of HTML code holding the representation
   *  of the Slotlet for SeparatorSlotlet class.
   *  The resulting HTML code should look like this:
   *  
   *  <code>
   *   <div id"separator_PASSED_CONTAINER_SLOTLET_ID" class="slotlet-separator" style="height: PASSED_SEPARATORpx">&nbsp;</div>
   *  </code>
   *  
   *  @param $options Array The options passed to the Slotlet.
   *  
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
  	if (!(array_key_exists('separator', $options) && isset($options['separator'])))
    {
      $options['separator'] = CmsConfiguration::get('separator_default_height', 5);
    }

    return "<div id=\"separator_".$options['container_slotlet_id']."\" class=\"slotlet-separator\" style=\"height: ".$options['separator']."%\">&nbsp;</div>";
  }
  /* End Interface implementation */
}