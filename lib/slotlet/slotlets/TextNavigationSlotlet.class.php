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
 * Text navigation slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class TextNavigationSlotlet extends NavigationSlotlet
{
  public function getDefaultOptions()
  {
    return array(
      'class'         => 'slotlet_navigation',
      'show_home'     => true,
      'show_contact'  => true,
      'show_sitemap'  => true,
      'show_rss'      => true
    );
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_navigation.css');
  }

  public function render($options = array())
  {
    $template = <<<SLOTLET
<div class="slotlet %class%">
  %home%
  %contact%
  %sitemap%
  %rss%
</div>
SLOTLET;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%home%'    => $options['show_home'] ? $this->getHomeLink() : '',
      '%contact%' => $options['show_contact'] ? $this->getContactLink() : '',
      '%sitemap%' => $options['show_sitemap'] ? $this->getSitemapLink() : '',
      '%rss%'     => $options['show_rss'] ? $this->getRssLink() : '',
    ));
  }
  
  protected function getLink($alt, $href, $compat)
  {
    return strtr('<a href="%href%">%alt%</a>', array(
      '%alt%'   => $alt,
      '%href%'  => url_for($href)
    ));
  }

  protected function getHomeLink($image)
  {
    return $this->getLink(__('Inicio'), '@homepage');
  }

  protected function getContactLink($image)
  {
    return $this->getLink(__('Contacto'), '@contact');
  }

  protected function getSitemapLink($image)
  {
    return $this->getLink(__('Mapa del sitio'), '@sitemap');
  }

  protected function getRssLink($image)
  {
    return $this->getLink(__('RSS'), '@feed');
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra elementos de navegación del sitio en modo texto.';
  }

  public static function getName()
  {
    return 'Navegación (texto)';
  }
  
}