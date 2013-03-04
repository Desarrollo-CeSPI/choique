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
 * FlashVideoPlayerJWFLV
 *
 * FlashVideoPlayer subclass for JWFLV media player (http://www.longtailvideo.com/players/jw-flv-player/).
 *
 * @author ncuesta
 */
class FlashVideoPlayerJWFLV extends FlashVideoPlayer
{
  /**
   * Render external player (multimedia's external_uri attribute).
   *
   * @param Array $options
   *
   * @return String
   */
  public function render($options = array())
  {
    if (!is_array($options))
    {
      $options = array('uri' => Multimedia::relativeUriFor($this->getMultimedia()->getLargeUri()));
    }

    sfLoader::loadHelpers(array('Asset', 'Tag', 'JWFLVMediaPlayer', 'UJS'));

    if (!isset($options['uri']) || empty($options['uri']))
    {
      throw new sfException('Missing required option: uri');
    }

    $uri = $options['uri'];

    unset($options['uri']);

    $default_options = array(
      'backcolor'  => '0xb2b2b2',
      'frontcolor' => '0x646464',
      'logo'       => image_path(Multimedia::FLV_LOGO)
    );

    return sprintf("%s<noscript><div class='no-javascript-error'>%s</div></noscript>",
      UJS_write(mediaplayer_video($uri, 300, 300, array_merge($default_options, $options))),
      __("Se requiere Javascript, y/o algunos agregados, para mostrar ciertos contenidos en su navegador.")
    );
  }
}