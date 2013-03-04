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
 * FlashVideoPlayerOptions
 *
 * Options holder for flash video players.
 *
 * To declare a new FlashVideoPlayer subclass one must:
 *
 *   - add a new constant in the 'Subclasses constants section'
 *   - append it to self::$_subclasses as self::MY_NEW_PLAYER_CONSTANT => 'MyFlashVideoPlayerSubclass'
 *   - append it to self::$_subclasses_names as self::MY_NEW_PLAYER_CONSTANT => 'My beautiful player'
 *   - declare its capabilities (audio, video or both) by appending it to
 *       self::$_subclasses_capabilities as self::MY_NEW_PLAYER_CONSTANT => self::CAPABILITY_CONSTANT
 *      (the latter could be any of 0Capabilities constants secion').
 *
 * @author ncuesta
 */
class FlashVideoPlayerOptions
{
  // Subclasses constants section
  const
    PLAYER_JWFLV    = 1,
    PLAYER_OSFLV    = 2,
    PLAYER_EXTERNAL = 100;

  // Capabilities constants section
  const
    CAPABILITY_EXTERNAL    = 0,
    CAPABILITY_ONLY_AUDIO  = 1,
    CAPABILITY_ONLY_VIDEO  = 2,
    CAPABILITY_AUDIO_VIDEO = 3;

  static protected
    $_subclasses = array(
        self::PLAYER_EXTERNAL => 'FlashVideoPlayerExternal',
        self::PLAYER_OSFLV    => 'FlashVideoPlayerOSFLV',
        self::PLAYER_JWFLV    => 'FlashVideoPlayerJWFLV'
      ),
    $_subclasses_names = array(
        self::PLAYER_EXTERNAL => 'Reproductor externo',
        self::PLAYER_OSFLV    => 'Reproductor interno: OSFLV',
        self::PLAYER_JWFLV    => 'Reproductor interno: JWFLV'
      ),
    $_subclasses_capabilities = array(
        self::PLAYER_OSFLV    => self::CAPABILITY_ONLY_VIDEO,
        self::PLAYER_EXTERNAL => self::CAPABILITY_EXTERNAL,
        self::PLAYER_JWFLV    => self::CAPABILITY_ONLY_VIDEO
      ),
    $_capabilities = array(
        self::CAPABILITY_EXTERNAL    => '',
        self::CAPABILITY_ONLY_AUDIO  => 'Audio',
        self::CAPABILITY_ONLY_VIDEO  => 'Video',
        self::CAPABILITY_AUDIO_VIDEO => 'Audio + Video'
      );

  /**
   * Pretty print subclasses options (add their capabilities to the string name).
   *
   * @param mixed $option
   * @param mixed $index
   */
  static protected function prettyPrintOption(&$option, $index)
  {
    if (self::$_subclasses_capabilities[$index] !== self::CAPABILITY_EXTERNAL)
    {
      $option = $option . ' (' . self::$_capabilities[self::$_subclasses_capabilities[$index]] . ')';
    }
  }

  /**
   * Return an instance of a FlashVideoPlayer subclass, according to $id.
   *
   * @param integer $id The player id (a constant defined in this class)
   *
   * @return FlashVideoPlayer
   */
  static public function getFlashVideoPlayerSubclass($id)
  {
    if (in_array($id, array_keys(self::$_subclasses)))
    {
      return self::$_subclasses[$id];
    }
    else
    {
      throw new sfException("Unable to find FlashVideoPlayer subclass for id: $id");
    }
  }

  /**
   * (Base method for common logic)
   * Return an associative array holding available subclasses of flash video player.
   *
   * @param mixed $include_blank If true, a blank option will be appended.
   *              If it is a non-null string, an option with null value and
   *              $include_blank as description will be appended.
   *
   * @return Array The array of flas video player subclasses.
   */
  static protected function getOptions($include_blank, $options)
  {
    if ($include_blank !== false && !is_null($include_blank))
    {
      if (is_string($include_blank))
      {
        return array('' => $include_blank) + $options;
      }
      else
      {
        return array('' => '') + $options;
      }
    }

    return $options;
  }

  /**
   * Return an associative array holding available subclasses of flash video player.
   *
   * @param mixed $include_blank If true, a blank option will be appended.
   *              If it is a non-null string, an option with null value and
   *              $include_blank as description will be appended.
   *
   * @return Array The array of flas video player subclasses.
   */
  static public function getSubclasses($include_blank = false)
  {
    return self::getOptions($include_blank, self::$_subclasses);
  }

  /**
   * Return an associative array holding available subclasses of flash video player,
   * along with their string representations (More suitable as <select> options than
   * self::getSubclasses()).
   *
   * @param mixed $include_blank If true, a blank option will be appended.
   *              If it is a non-null string, an option with null value and
   *              $include_blank as description will be appended.
   *
   * @return Array The array of flas video player subclasses.
   */
  static public function getSubclassesOptions($include_blank = false)
  {
    $options = self::$_subclasses_names;
    array_walk($options, array('FlashVideoPlayerOptions', 'prettyPrintOption'));
    
    return self::getOptions($include_blank, $options);
  }

  /**
   * Return capabilities declared for player $id.
   *
   * @param integer $id The player id (a constant defined in this class)
   *
   * @return integer
   */
  static public function getCapabilitiesFor($id)
  {
    if (in_array($id, array_keys(self::$_subclasses_capabilities)))
    {
      return self::$_subclasses_capabilities[$id];
    }

    return null;
  }
}