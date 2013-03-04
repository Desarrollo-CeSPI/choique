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
 * FlashVideoPlayer
 *
 * Abstract super class for flash video (flv) players.
 *
 * @author ncuesta
 */
abstract class FlashVideoPlayer
{
  protected $multimedia;

  static public function getInstance(Multimedia $multimedia)
  {
    $subclass = FlashVideoPlayerOptions::getFlashVideoPlayerSubclass($multimedia->getPlayerId());

    return new $subclass($multimedia);
  }

  public function __construct(Multimedia $multimedia)
  {
    $this->multimedia = $multimedia;
  }

  /**
   * Return this instance's wrapped Multimedia object.
   *
   * @return Multimedia
   */
  public function getMultimedia()
  {
    return $this->multimedia;
  }

  /**
   * Return a snippet of HTML with the player.
   *
   * @param Array $options Additional options passed to the player.
   *
   * @return String
   */
  abstract public function render($options = array());
}