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
 * RSS Channel Tabular slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class RSSChannelTabularSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_feed_tabular.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'            => 'slotlet_feed_tabular',
      'title'            => __('Canal RSS'),
      'show_title'       => true,
      'show_when_empty'  => true,
      'section_name'     => sfContext::getInstance()->getRequest()->getParameterHolder()->get('section_name', 'NONE'),
      'rss_channel_id'   => null,
      'visible_elements' => 4,
      'columns'          => 2
    );
  }

  protected function getRSSChannel( $options )
  {
    return RssChannelPeer::retrieveByPK($options['rss_channel_id']);
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N', 'Url'));

    $rss_channel = $this->getRSSChannel( $options);
    $title       = $options['title'];

    if (null == $rss_channel)
    {
      if (false === (bool) $options['show_when_empty'])
      {
        return null;
      }
      else
      {
        $id    = time() % 50 + 1;
        $url   = '#';
      }
    }
    else
    {
      $id  = $rss_channel->getId();
      $url = url_for('@rss_view_more?nombre='.sfInflector::underscore($title).'&id='.$id.'&section='.$options['section_name']);
    }
    
    $title_template = <<<TEMPLATE
  <div class="title">
    %title%
    <a href="%url%" class="feed_view_more">%more%</a>
  </div>
TEMPLATE;

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  %title%
  <div class="content">
    <table class="rt_content_table">
      <tbody>
        %content%
      </tbody>
    </table>
  </div>
  <div class="footer">
  </div>
</div>
SLOTLET;
    
    return strtr($template, array(
      '%id%'      => 'slotlet_feed_tabular_'.sfInflector::underscore($title).'_'.$id,
      '%class%'   => $options['class'],
      '%title%'   => false != $options['show_title'] ? strtr($title_template, array('%title%' => $title, '%url%' => $url)) : '',
      '%content%' => $this->getContent($rss_channel, $options),
      '%more%'    => __('Ver todas')
    ));
  }

  protected function getContent($rss_channel, $options)
  {
    if (null == $rss_channel)
    {
      return __('Sin contenidos para mostrar');
    }

    $visible = is_numeric($options['visible_elements']) ? intval($options['visible_elements']) : 5;
    $cols    = is_numeric($options['columns']) ? intval($options['columns']) : 2;

    try
    {
      $feed    = $rss_channel->getReader();
      $items   = $feed->getItems();
      $count   = min(count($items), $visible);
      $content = $this->renderItems($items, $visible, $cols);
    }
    catch (Exception $e)
    {
      $content = __('Sin contenidos para mostrar');
    }

    return $content;
  }

  protected function renderItems($items, $visible, $cols)
  {
    $row_template = <<<ROW
<tr class="rt_row">
  %cells%
</tr>
ROW;
    $cell_template = <<<CELL
<td class="rt_cell" id="rt_%id%">
  <div class="rt_title" style="color: %hex%;">%section%</div>
  <div class="rt_heading"><a href="%url%">%title%</a></div>
  <style type="text/css">
    #rt_%id%:hover { background-color: %hex%; }
    #rt_%id%:hover .rt_title { color: #fff !important; }
    #rt_%id%:hover .rt_heading, #rt_%id%:hover .rt_heading a { color: #444 !important; }
  </style>
</td>
CELL;

    if (0 == count($items))
    {
      return strtr($row_template, array(
        '%cells%' => '<td class="rt_cell">'.__('Sin contenidos para mostrar').'</td>'
      ));
    }

    $html = '';
    $row  = '';

    $default_section = SectionPeer::retrieveHomeSection();

    foreach ($items as $i => $item)
    {
      $related_section = SectionPeer::retrieveFirstLevelByColor(current($item->getCategories()));

      if (null === $related_section)
      {
        $related_section = $default_section;
      }

      $row .= strtr($cell_template, array(
        '%section%' => $related_section->getTitle(),
        '%hex%'     => $related_section->getColor(),
        '%title%'   => $item->getTitle(),
        '%url%'     => $item->getLink(),
        '%id%'      => md5($item->getLink())
      ));

      if (0 == ($i + 1) % $cols)
      {
        $html .= strtr($row_template, array('%cells%' => $row));

        $row = '';
      }
    }

    if (count($items) > 0 && count($items) % $cols > 0)
    {
      $html .= strtr($row_template, array('%cells%' => $row));
    }

    return $html;
  }

  static public function getDescription()
  {
    return 'Lector de canal de noticias RSS con disposición en forma de tabla.';
  }

  static public function getName()
  {
    return 'Canal RSS tabular';
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'            => __('Clase CSS'),
      'title'            => __('Título'),
      'show_when_empty'  => __('Mostrar si vacío'),
      'show_title'       => __('Mostrar título'),
      'rss_channel_id'   => __('Canal RSS'),
      'visible_elements' => __('Elementos visibles'),
      'columns'          => __('Columnas')
    );

    $form = strtr($row, array(
      '%id%'    => 'rss_channel_id',
      '%label%' => $labels['rss_channel_id'],
      '%field%' => select_tag('rss_channel_id', objects_for_select(RssChannelPeer::retrieveAll(), 'getId', '__toString', $values['rss_channel_id']), array('class' => 'slotlet_option'))
    ));

    foreach (array('title', 'class', 'visible_elements', 'columns') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    $form .= strtr($row, array(
      '%id%'    => 'show_when_empty',
      '%label%' => $labels['show_when_empty'],
      '%field%' => checkbox_tag('show_when_empty', true, $values['show_when_empty'] != false, array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'show_title',
      '%label%' => $labels['show_title'],
      '%field%' => checkbox_tag('show_title', true, $values['show_title'] != false, array('class' => 'slotlet_option'))
    ));

    return $form;
  }

}