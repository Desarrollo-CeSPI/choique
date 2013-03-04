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

class AssetHTMLRepresentationFlash extends AssetHTMLRepresentation
{
  protected function renderMedium()
  {
    $tag=content_tag("object",
        tag("param", array("value" => $this->getLargeUri(true), "name" => "movie_".$this->getMultimedia()->getName())).$this->getMultimedia()->getDescription(),
        array(
          "width"   => $this->getMultimedia()->getWidth(),
          "height"  => $this->getMultimedia()->getHeight(),
          "quality" => "high",
          "pluginspage" => "http://www.adobe.com/go/getflashplayer",
          "type"    => "application/x-shockwave-flash",
          "wmode"   => 'transparent',
          "data"    => $this->getLargeUri(true)
        ));
    return $tag. sprintf("<noscript><div class='no-javascript-error'>%s</div></noscript>", __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador."));
  }

  protected function renderClickable()
  {
    return UJS_lightview_media(
        $this->getLargeUri(),
        image_tag($this->getMultimedia()->getMediumUri() ? $this->getMediumUri() : Multimedia::DEFAULT_VIDEO_ICON_URI),
        $this->getMultimedia()->getTitle(),
        $this->getMultimedia()->getDescription()
      );
  }
}