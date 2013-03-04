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
 * AddThisSocialTool
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class AddThisSocialTool implements SocialToolInterface
{
  /**
   * Register the needed Javascripts and Stylesheets for the Social Tool.
   *
   * @param  sfWebResponse $response
   *
   * @return void
   */
  public function register(sfWebResponse $response)
  {
    $response->addJavascript('http://s7.addthis.com/js/250/addthis_widget.js', 'last');
    $response->addStylesheet('common/addthis');
    $response->addStylesheet('common/social', 'last');
  }

  /**
   * Render the HTML of the Social Tool.
   *
   * @return string The HTML snippet.
   */
  public function render()
  {
    $toolbar = <<<HTML
<div class="social_toolbox addthis_toolbox addthis_pill_combo">
  <a class="addthis_button_compact"></a>
</div>
HTML;

    return strtr($toolbar, array('%share%' => 'Compartir'));
  }

}