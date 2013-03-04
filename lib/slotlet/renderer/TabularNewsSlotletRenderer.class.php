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
 * TabularNewsSlotletRenderer
 *
 * Slotlet renderer that shows the news in a tabular way.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class TabularNewsSlotletRenderer extends BaseSlotletRenderer
{
  protected function doRender()
  {
    $section = $this->getOption('section');
    $news    = array_slice($this->getOption('news', array()), 0, $this->getOption('maximum_elements'));

    $template = <<<SLOTLET
<div class="slotlet %class% tns_tabular">
  <div class="title">
    <a class="rss_icon" href="%rss_url%"><img src="%rss_icon%" alt="%rss_alt%" title="%rss_title%" /></a>
    %title%
  </div>
  <div class="content">
    %content%
  </div>
  <div class="footer">
  </div>
</div>
SLOTLET;

    return strtr($template, array(
      '%title%'     => $this->getOption('title'),
      '%class%'     => $this->getOption('class'),
      '%rss_url%'   => url_for('feed/news?section='.$section->getName()),
      '%rss_icon%'  => image_path('common/rss.png'),
      '%rss_title%' => __('Canal RSS de novedades'),
      '%rss_alt%'   => __('RSS'),
      '%content%'   => $this->renderContent($section, $news)
    ));
  }

  protected function renderContent($section, $news)
  {
    $template = <<<CONTENT
<table class="tns_container">
  <tbody>
    %news%
  </tbody>
</table>
CONTENT;

    return strtr($template, array(
      '%news%' => $this->renderNews($section, $news)
    ));
  }

  protected function renderNews($section, $news)
  {
    $row_template = <<<ROW
<tr class="tns_row">
  %cells%
</tr>
ROW;
    $cell_template = <<<CELL
<td class="tns_cell" id="tns_%id%">
  <h2 class="tns_title" style="color: %hex%;">%section%</h2>
  <p class="tns_heading">%title%</p>
  <style type="text/css">
    #tns_%id%:hover { background-color: %hex%; }
    #tns_%id%:hover .tns_title { color: #fff !important; }
    #tns_%id%:hover .tns_heading, #tns_%id%:hover .tns_heading a { color: %title_hover_color% !important; }
  </style>
</td>
CELL;

    if (0 == count($news))
    {
      return strtr($row_template, array(
        '%cells%' => '<td class="tns_cell">'.__('Sin contenidos para mostrar').'</td>'
      ));
    }

    $html = '';
    $row  = '';
    $cols = intval($this->getOption('columns', 2));

    foreach ($news as $i => $article)
    {
      $related_section = (null !== $article->getSectionId() ? $article->getSection() : $section);
      $row .= strtr($cell_template, array(
        '%section%' => $related_section->getTitle(),
        '%hex%'     => $related_section->getColor(),
        '%title%'   => $article->getLinkedTitle(),
        '%id%'      => $article->getId(),
        '%title_hover_color%' => $this->getOption('title_hover_color','#444')
      ));

      if (0 == ($i + 1) % $cols)
      {
        $html .= strtr($row_template, array('%cells%' => $row));

        $row = '';
      }
    }

    if (count($news) > 0 && count($news) % $cols > 0)
    {
      $html .= strtr($row_template, array('%cells%' => $row));
    }

    return $html;
  }

  protected function renderableClasses()
  {
    return array('NewsSlotlet');
  }

  public function getDescription()
  {
    return 'Por filas/columnas';
  }

  public function getName()
  {
    return 'Tabular';
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_tabular_news.css');
  }
  
}