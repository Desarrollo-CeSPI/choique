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
 * DefaultNewsSlotletRenderer
 *
 * Default renderer for News Slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class DefaultNewsSlotletRenderer extends BaseSlotletRenderer
{
  protected function doRender()
  {
    $section = $this->getOption('section');
    $news    = $this->getOption('news', array());

    $template = <<<SLOTLET
<div class="slotlet %class%">
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
    $row_template = '<div class="content-child %class%">%content%</div>';

    if (null !== $section)
    {
      if (count($news) > 0)
      {
        $content  = '';
        $news     = array_slice($news, 0, $this->getOption('maximum_elements'));

        foreach ($news as $i => $article)
        {
          $content .= strtr($row_template, array(
            '%class%'   => ($i == 0 ? 'first' : '').' child-'.($i+1),
            '%content%' => $article->getHTMLReference()
          ));
        }

        return $content;
      }
    }

    return strtr($row_template, array('%class%' => 'first', '%content%' => __('Sin contenidos para mostrar')));
  }

  protected function renderableClasses()
  {
    return array('NewsSlotlet');
  }

  public function getDescription()
  {
    return 'Listado';
  }

  public function getName()
  {
    return 'Por defecto';
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_news.css');
  }

}