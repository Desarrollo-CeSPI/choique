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
 * choiqueFlavors
 *
 * @author ncuesta
 */
class choiqueFlavors
{
  static protected $_instance;

  static private $_checked_images = false;

  /**
   * Return the instance of choiqueFlavors, and optionally force a re-check
   * of configuration files.
   * 
   * @param  Boolean $force_check_config True if configuration must be checked
   * 
   * @return choiqueFlavors The object instance
   */
  static public function getInstance($force_check_config = false)
  {
    if (is_null(self::$_instance) || $force_check_config)
    {
      self::checkConfig();

      self::$_instance = new self();

      self::checkImagesConfig();
    }

    return self::$_instance;
  }

  /**
   * Check configuration files.
   */
  static public function checkConfig()
  {
    include(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_config_dir').'/choique.yml'));
  }

  /**
   * Check images configuration file.
   */
  static public function checkImagesConfig()
  {
    if (self::$_checked_images)
    {
      return;
    }
    
    include(sfConfigCache::getInstance()->checkConfig(self::getGlobalPath().'/config/images.yml'));

    self::$_checked_images = true;
  }
  
  /**
   * Return an array holding the names of every choique flavor that is
   * currently available (installed).
   *
   * @param  Boolean $include_keys Whether to also use the flavor names as keys
   *                               of the resulting array, or just as values
   *
   * @param  Boolean $include_actual Whethe to also include or not the actual flavor
   *
   * @return Array   The array of available (installed) flavors
   */
  public function getAll($include_keys = false, $include_current = true)
  {
    $dirnames = $this->getDirnames();

    if ($include_keys !== false)
    {
      $flavors = array();
      foreach ($dirnames as $dirname)
      {
        if ($include_current) 
        {
          $flavors[basename($dirname)] = basename($dirname);
        }
        else
        {
          if (basename($dirname) !== self::current())
            $flavors[basename($dirname)] = basename($dirname);
        }
      }
    }
    else
    {
      $flavors = ($include_current)?array_map('basename', $dirnames):$this->getDirnames(false);
    }
    
    natsort($flavors);

    return $flavors;
  }

  private function getDirnames($include_current = true)
  {
    $dirnames = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(self::getPath());

    if (!$include_current)
    {
      $flavors = array();

      foreach ($dirnames as $dirname)
      {
        if (basename($dirname) !== self::current())
        {
          $flavors[] = $dirname;
        }
      }
      return $flavors;
    }
    else
    {
      return $dirnames;
    }
  }

  /**
   * Return the name of the currently selected choique flavor.
   * 
   * @return string The name of the currently selected choique flavor
   */
  public function current()
  {
    return sfConfig::get('choique_flavors_current', 'default');
  }

  /**
   * Set the current flavor to $current_flavor.
   *
   * @throws sfConfigurationException If unable to write to configuration file
   *
   * @param  string $current_flavor The current flavor to be set
   *
   * @return string The current flavor
   */
  public function setCurrent($current_flavor = 'choique', $throw_exception = false)
  {
    $config = sfYaml::load(self::getConfigFilePath());
    
    if (empty($config) || !isset($config['choique']) || !isset($config['choique']['flavors']))
    {
      $config = array('choique' => array('flavors' => array('current' => $current_flavor, 'root_dir' => 'flavors')));
    }
    else
    {
      $config['choique']['flavors']['current'] = $current_flavor;
    }

    self::dump($config, $throw_exception);

    $this->clearCache('all');

    return $current_flavor;
  }

  /**
   * Clears symfony cache.
   */
  public function clearCache($application = 'frontend')
  {
    if ($application == 'all')
    {
      $cache_dir = sfConfig::get('sf_cache_dir').'/../../'; // Global cache dir
    }
    else
    {
      $cache_dir = sfConfig::get('sf_cache_dir').'/../../'.$application; // Application-specific cache dir
    }

    if (!empty($cache_dir))
    {
      foreach (sfFinder::type('file')->prune('.svn')->discard('.svn')->in($cache_dir) as $file)
      {
        @unlink($file);
      }

      foreach (array_reverse(sfFinder::type('dir')->prune('.svn')->discard('.svn')->in($cache_dir)) as $file)
      {
        @rmdir($file);
      }

      @rmdir($cache_dir);
    }
  }

  /**
   * Dump $config to the configuration file.
   * If $throw_exception is true, an exception will be thrown if configuration
   * file is not writable.
   * This method already re-checks config.
   *
   * @throws sfConfigurationException If a write error occurs and $throw_exception
   *                                  is true
   *
   * @param Array   $config         The array of configuration values
   * @param Boolean $throw_exeption True if an exception must be throw on write error
   */
  static public function dump($config, $throw_exception)
  {
    $yaml = sfYaml::dump($config);

    if (is_writable(self::getConfigFilePath()))
    {
      file_put_contents(self::getConfigFilePath(), $yaml);

      // Force config re-check
      self::checkConfig();
    }
    elseif ($throw_exception)
    {
      throw new sfConfigurationException('Unable to write to choique flavors configuration file: '.self::getConfigFilePath());
    }
  }

  /**
   * Return the path to the flavors' root dir.
   *
   * @param  Boolean $relative Whether the path should be relative (false by default)
   *
   * @return string  The route to the flavors root dir
   */
  static public function getPath($relative = false)
  {
    $path  = ($relative ? '' : sfConfig::get('sf_root_dir').'/');
    $path .= sfConfig::get('choique_flavors_root_dir', 'flavors');
    
    return $path;
  }

  static public function getModulePath($module_name = null, $relative = false)
  {
    $path = self::getPath($relative).'/'.self::getInstance()->current().'/modules';
    if (!is_null($module_name))
    {
      $path .= '/'.$module_name;
    }

    return $path;
  }

  static public function getGlobalPath($relative = false)
  {
    return self::getPath($relative).'/'.self::getInstance()->current();
  }

  /**
   * Return the path to the config file (choique.yml).
   * 
   * @return string The path
   */
  static protected function getConfigFilePath()
  {
    return sfConfig::get('sf_config_dir').'/choique.yml';
  }

  /**
   * Get the public path of a flavor-dependant image named $image_name -which
   * is the key used to set it in the images.yml configuration file. If no such
   * image has been defined, the $image_name with the $default_extension appended
   * will be returned.
   *
   * @param type $image_name
   * @param type $default_extension
   * @return type
   */
  static public function getImagePath($image_name, $default_extension = 'png')
  {
    // Force check before looking for the image path
    self::checkImagesConfig();
    
    return sprintf('frontend/%s', sfConfig::get('choique_image_'.$image_name, $image_name.'.'.ltrim($default_extension, '.')));
  }

  /**
   * Get the path of a flavor named $flavor_name and try to copy resources
   * into public frontend directories
   *
   * @param type $flavor_name
   */
  static public function publishResources($flavor_name)
  {
    $source_css = choiqueFlavors::getPath()."/$flavor_name/web/css";
    $source_images = choiqueFlavors::getPath()."/$flavor_name/web/images";

    $web_base_dir = sfConfig::get('choique_frontend_web_dir',SF_ROOT_DIR.'/web-frontend');

    if ( !is_dir($source_css ) || !is_dir($source_images) || !is_dir($web_base_dir) ) throw new LogicException("Alguno de los directorios: $source_css o $source_images o $web_base_dir no existen!");
    
    $target_css    = realpath("$web_base_dir/css/frontend");
    $target_images = realpath("$web_base_dir/images/frontend");

    $r = PathHelper::emptyDirectories(array(
      $target_css,
      $target_images
    ));
    if (!$r) throw newLogicException("No se pudo eliminar el directorio: $target_css o $target_images!");
    
    $r = PathHelper::deepCopy($source_images, $target_images);

    if (!$r) throw newLogicException("No se pudo copiar el directorio: $source_images en $target_images!");

    $r = PathHelper::deepCopy($source_css, $target_css);

    if (!$r) throw newLogicException("No se pudo copiar el directorio: $source_css en $target_css!");
  
    return true;
 
  }

}