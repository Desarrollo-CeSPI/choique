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
 * PathHelper
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class PathHelper
{
  private static $root;

  /**
   * Get the root path of the current flavor.
   *
   * @return string
   */
  static public function getRoot()
  {
    if (null === self::$root)
    {
      self::$root = choiqueFlavors::getGlobalPath();

      if (!is_writable(self::$root))
      {
        throw new DomainException('El directorio de estilos visuales no puede ser modificado por falta de permisos.');
      }
    }

    return self::$root;
  }

  /**
   * Get an absolute path inside the current flavor for $relative_path,
   * optionally checking if the generated path is writable (if $check_write is
   * true).
   *
   * @throws DomainException          if the generated path is not valid.
   * @throws InvalidArgumentException if the generated path is not writable and
   *                                  $check_write is true.
   *
   * @param  string $relative_path
   * @param  bool   $check_write
   *
   * @return string
   */
  static public function getPath($relative_path, $check_write = false)
  {
    $path = realpath(self::getRoot().'/'.ltrim($relative_path, '/'));

    $root = realpath(self::getRoot());

    if (strlen($path) < strlen($root) || !is_readable($path))
    {
      throw new DomainException('La ruta ingresada es inválida.');
    }

    if ($check_write && !is_writable($path))
    {
      throw new InvalidArgumentException('El archivo seleccionado no puede ser modificado.');
    }

    return $path;
  }

  /**
   * Find any files and/or directories (as specified by $type) in $base and
   * return them as an associative array with their relative paths keys and
   * their filenames as values.
   * 
   * @param string $type The type of files to find: 'dir', 'file' or 'any'.
   * @param string $base The base path to search in. Defaults to the root path.
   *
   * @return array
   */
  static public function find($type, $base = null)
  {
    if (null === $base)
    {
      $base = self::getRoot();
    }

    $path = realpath($base);

    $matches = sfFinder::type($type)
      ->ignore_version_control()
      ->discard('.*')
      ->maxdepth(0)
      ->in($path);

    natsort($matches);


    $pad   = strlen(realpath(self::getRoot()));
    $start = strlen($path);

    $response = array();
    
    foreach ($matches as $match)
    {
      $response[ltrim(substr($match, $pad), '/')] = ltrim(substr($match, $start), '/');
    }

    return $response;
  }

  static public function emptyDirectories(array $directories)
  {
    $response = true;
    
    foreach ($directories as $directory)
    {
      $reponse = $response && self::emptyDirectory($directory);
    }
    
    return $response;
  }

  /**
   * Empty $directory - that is, remove all of its contents.
   *
   * @param  string $directory The path to the directory to be emptied.
   * 
   * @return bool
   */
  static public function emptyDirectory($directory)
  {
    if (!$directory_handler = @opendir($directory))
    {
      return false;
    }
    else
    {
      while (false !== ($name = readdir($directory_handler)))
      {
        if ($name == '.' || $name =='..')
        {
          // Skip .* files
          continue;
        }

        $current = $directory.'/'.$name;

        if (is_dir($current))
        {
          self::emptyDirectory($current);
          
          $result = rmdir($current);
        }
        else
        {
          $result = @unlink($current);
        }

        if (!$result)
        {
          return false;
        }
      }
    }

    return true;
  }

  static public function deepCopy($from, $to)
  {
    $directory = opendir($from);

    @mkdir($to);

    while (false !== ($name = readdir($directory)))
    {
      if ($name[0] == '.')
      {
        // Skip .* files
        continue;
      }

      $current        = $from.'/'.$name;
      $current_target = $to.'/'.$name;

      if (is_dir($current))
      {
        $result = self::deepCopy($current, $current_target);
      }
      else
      {
        $result = copy($current, $current_target);
      }
      
      if (!$result)
      {
        return false;
      }
    }

    closedir($directory);

    return true;
  }
  
}