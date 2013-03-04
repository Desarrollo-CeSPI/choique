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
 * Navigation slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class NavigationSlotlet implements ISlotlet
{
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'        => __('Clase CSS'),
      'show_home'    => __('Link a inicio'),
      'show_contact' => __('Link de contacto'),
      'show_sitemap' => __('Link a mapa del sitio'),
      'show_rss'     => __('Link a RSS')
    );

    $form = strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    foreach (array('show_home', 'show_contact', 'show_sitemap', 'show_rss') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => checkbox_tag($key, true, $values[$key] != false, array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'         => 'slotlet_navigation',
      'show_home'     => true,
      'show_contact'  => true,
      'show_sitemap'  => true,
      'show_rss'      => true,
      'home_image'    => image_path(choiqueFlavors::getImagePath('home', 'gif')),
      'contact_image' => image_path(choiqueFlavors::getImagePath('envelope', 'gif')),
      'sitemap_image' => image_path(choiqueFlavors::getImagePath('sitemap', 'gif')),
      'rss_image'     => image_path(choiqueFlavors::getImagePath('rss', 'gif')),
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
      '%home%'    => $options['show_home'] ? $this->getHomeLink($options['home_image']) : '',
      '%contact%' => $options['show_contact'] ? $this->getContactLink($options['contact_image']) : '',
      '%sitemap%' => $options['show_sitemap'] ? $this->getSitemapLink($options['sitemap_image']) : '',
      '%rss%'     => $options['show_rss'] ? $this->getRssLink($options['rss_image']) : '',
    ));
  }
  
  protected function getLink($image, $alt, $href)
  {
    return strtr('<a href="%href%"><img src="%image%" alt="%alt%" title="%alt%" /></a>', array(
      '%image%' => $image,
      '%alt%'   => $alt,
      '%href%'  => url_for($href)
    ));
  }

  protected function getHomeLink($image)
  {
    return $this->getLink($image, __('Inicio'), '@homepage');
  }

  protected function getContactLink($image)
  {
    return $this->getLink($image, __('Contacto'), '@contact');
  }

  protected function getSitemapLink($image)
  {
    return $this->getLink($image, __('Mapa del sitio'), '@sitemap');
  }

  protected function getRssLink($image)
  {
    return $this->getLink($image, __('RSS'), '@feed');
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra elementos de navegación del sitio.';
  }

  public static function getName()
  {
    return 'Navegación';
  }
  
}