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
 * Class for representing the search slotlet
 *
 */
class Search implements SlotletInterface
{
	public static function getSlotletMethods()
	{
		return array("getSlotlet");
	}
	
  public static function getSlotletName()
  {
    sfLoader::loadHelpers(array('I18N'));

    return __('Buscador');
  }

  /**
   *  Return a piece of HTML code holding the representation
   *  of the Slotlet for Search class.
   *  The resulting HTML code should look like this:
   *  
   *  <code>
   *    <div id="search" class="sl_search">
   *      <div class="title">Buscador</div>
   *      <div class="content">
   *        <form action="sfLucene/search" id="sl_search_form" class="sl_search_form">
   *          <input type="text" value="Buscar..." id="search_text"
   *                 onClick="clearField('search_text', 'Buscar...'); return false;"
   *                 onBlur="fillField('search_text', 'Buscar...'); return false;" />
   *          <input type="submit" value="Buscar" class="submit" />
   *        </form>
   *      </div>
   *      <div class="footer"></div>
   *    </div>
   *  </code>
   *  
   *  @param $options Array The options passed to the Slotlet.
   *  
   *  @return string The HTML code of the Slotlet.
   */
	public static function getSlotlet($options)
	{
    sfLoader::loadHelpers(array('I18N'));
		sfContext::getInstance()->getResponse()->addStylesheet('frontend/slotlet/sl_search');
    
    return sprintf('
      <div id="search" class="sl_search">
        <div class="title">%s</div>
        <div class="content">
          %s
            %s
            %s
            %s
          </form>
        </div>
        <div class="footer"></div>
      </div>',
      __('Buscador'), 
      form_tag('sfLucene/search', array('id' => 'sl_search_form', 'class' => 'sl_search_form')),
      input_hidden_tag('cms_search', 1),
      input_tag('query', __('Buscar...'),
                array('id'      => 'search_text',
                      'onClick' => "clearField('search_text', '" . __('Buscar...') . "');",
                      'onBlur'  => "fillField('search_text', '" . __('Buscar...') . "');")),
      submit_tag(__('Buscar'),array('class'=>'submit')));
	}
}