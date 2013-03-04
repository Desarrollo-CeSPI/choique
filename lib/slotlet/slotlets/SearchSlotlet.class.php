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
 * Search slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SearchSlotlet implements ISlotlet
{
  static public function getName()
  {
    return 'Buscador';
  }

  static public function getDescription()
  {
    return 'Slotlet para realizar búsquedas dentro del sitio.';
  }

  public function getJavascripts()
  {
    return array('slotlets/choique.search.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_search.css');
  }

  public function getDefaultOptions()
  {
    sfLoader::loadHelpers(array('I18N', 'Url','Asset'));
    return array(
      'id'           => 'search_slotlet',
      'class'        => 'sl_search',
      'title'        => __('Buscador'),
      'query'        => __('Buscar'),
      'mini'         => false,
      'submit_image' => image_path(choiqueFlavors::getImagePath('search_arrow'))
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Url','Asset'));

    $template = $this->getTemplate($options['mini']);

    return strtr($template, array(
      '%id%'           => $options['id'],
      '%class%'        => $options['class'],
      '%title%'        => $options['title'],
      '%url%'          => url_for('sfLucene/search'),
      '%query%'        => $options['query'],
      '%submit%'       => __('Buscar'),
      '%submit_image%' => $options['submit_image']
    ));
  }

  protected function getTemplate($mini_interface)
  {
    if ($mini_interface)
    {
      $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%_mini">
  <div class="content">
    <form action="%url%" method="post" id="%id%_form" class="%class%_form">
      <input type="text" name="query" value="%query%" id="%id%_query" class="%class%_query" />
      <input type="image" src="%submit_image%" name="commit" class="%class%_submit" alt="%submit%" id="%id%_arrow" />
    </form>
  </div>
</div>
SLOTLET;
    }
    else
    {
      $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  <div class="title">%title%</div>
  <div class="content">
    <form action="%url%" method="post" id="%id%_form" class="%class%_form">
      <input type="text" name="query" value="%query%" id="%id%_query" class="%class%_query" />
      <input type="submit" value="%submit%" class="%class%_submit" />
    </form>
  </div>
  <div class="footer"></div>
</div>
SLOTLET;
    }

    $template .= <<<SLOTLET
<script type="text/javascript">
//<![CDATA[
  Choique.placeholder('#%id% .%class%_query', '%query%');
//]]>
</script>
SLOTLET;

    return $template;
  }
  
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class' => __('Clase CSS'),
      'title' => __('Título'),
      'query' => __('Texto por defecto'),
      'mini'  => __('Mini buscador')
    );

    $form = '';

    foreach (array('title', 'query', 'class') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => 'so_'.$key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option', 'id' => 'so_'.$key))
      ));
    }

    $form .= strtr($row, array(
      '%id%'    => 'mini',
      '%label%' => $labels['mini'],
      '%field%' => checkbox_tag('mini', true, $values['mini'] != false, array('class' => 'slotlet_option'))
    ));

    return $form;
  }

}
