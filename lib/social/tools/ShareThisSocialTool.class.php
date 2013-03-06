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
 * ShareThisSocialTool
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ShareThisSocialTool implements SocialToolInterface
{
  /**
   * Register the needed Javascripts and Stylesheets for the Social Tool.
   *
   * @param sfWebResponse $response
   *
   * @return void
   */
  public function register(sfWebResponse $response)
  {
    $response->addJavascript('http://w.sharethis.com/button/buttons.js', 'last');
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
<div class="social_toolbox">
  <span class="st_facebook"></span>
  <span class="st_twitter"></span>
  <span class="st_linkedin"></span>
  <span class="st_googleplus"></span>
</div>
<script type="text/javascript">
  var switchTo5x=false;
  stLight.options({ publisher: '0d36269a-b262-4e43-adac-024a884de904' });
</script>
HTML;

    return $toolbar;
  }

}
