<?php

  pake_desc('fix directories permissions including choique-specific ones');
  pake_task('choique-fix-perms');


  /**
   * Fixes permissions in a symfony project.
   * This task also takes into account choique-specific directories.
   *
   * @example symfony fix-perms
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_fix_perms($task, $args)
  {
    $default_user="www-data";
     if (count($args) < 1)
    {
      echo "\n".pakeColor::colorize( sprintf ("You don't provide user for chmod. Assumed %s", $default_user ), array('bg' => 'red', 'fg' => 'white', 'bold'=>true))."\n";
      $user=$default_user;
    }
    else
    {
      $user=$args[0];
    }

    $sf_root_dir = sfConfig::get('sf_root_dir');
    $lucene_index = sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'index';
    $choique_config = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'choique.yml';
    pake_chmod_recursive(sfConfig::get('sf_cache_dir_name'), $sf_root_dir, 0777, $user);
    pake_chmod_recursive(sfConfig::get('sf_log_dir_name'), $sf_root_dir, 0777, $user);
    pake_chmod_recursive('web-frontend'.DIRECTORY_SEPARATOR.'images', $sf_root_dir, 0777, $user);
    pake_chmod_recursive('web-frontend'.DIRECTORY_SEPARATOR.'css', $sf_root_dir, 0777, $user);
    pake_chmod_recursive('web-frontend'.DIRECTORY_SEPARATOR.'uploads', $sf_root_dir, 0777, $user);
    pake_chmod('symfony', $sf_root_dir, 0777);

    // Choique specific dirs
    pake_chmod_recursive(sfConfig::get('choique_flavors_root_dir', 'flavors'), $sf_root_dir, 0777, $user);

    // If indexes directory doesn't exist, create it
    if (!file_exists( $lucene_index))
    {
      pake_mkdirs( $lucene_index);
    }
    pake_chmod_recursive( $lucene_index, $sf_root_dir, 0777, $user);

    // If config/choique.yml file doesn't exist, create it
    if (!file_exists( $choique_config))
    {
      pake_touch( $choique_config);
    }

    // Change rw access permissions to 0777 for config/choique.yml
    pake_chmod('choique.yml', sfConfig::get('sf_config_dir'), 0777);
  }

  function pake_chmod_recursive($dir, $base, $mode, $user, $mode_subdirs=0777, $mode_subfiles=0666)
  {
    echo "\n".pakeColor::colorize( sprintf ("Change permissions to user %s", $user), array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";

    pake_chmod($dir, $base, $mode);
    @chown($base.DIRECTORY_SEPARATOR.$dir, $user);

    $dir_finder  = pakeFinder::type('dir')->ignore_version_control()->in($dir);
    $file_finder = pakeFinder::type('file')->ignore_version_control()->in($dir);
    
    pake_chmod($dir_finder, $dir, $mode_subdirs);
    pake_chmod($file_finder, $dir, $mode_subfiles);
    foreach ($dir_finder as $d)
    {
      @chown($d, $user);
    }
    foreach ($file_finder as $f)
    {
      @chown($f, $user);
    }
  } 

  pake_desc('rebuild indexed articles and documents and fixes index permissions');
  pake_task('choique-reindex');

  /**
   * Reindex Lucene index for every article and document in database.
   * Fixes index permissions 
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_reindex($task, $args)
  {
    echo "\n".pakeColor::colorize("Reindexing data", array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";
    run_lucene_rebuild($task, array('frontend'));
    echo "\n".pakeColor::colorize("Optimimzing index", array('bg' => 'green', 'fg' => 'white',  'bold'=>true))."\n";
    run_lucene_optimize($task, array('frontend'));
    echo "\n".pakeColor::colorize("Fixing permissions", array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";
    run_choique_fix_perms($task, array());  
  }

  pake_desc('List installed flavors');
  pake_task('choique-flavors-list');

  /**
   * List available flavors
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_flavors_list($task, $args)
  {
    echo "\n".pakeColor::colorize("Choique installed flavors", array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";
    $flavor_dir = sfConfig::get('choique_flavors_root_dir', 'flavors');
    $dir_finder  = pakeFinder::type('dir')->maxdepth(0)->ignore_version_control()->in($flavor_dir);
    foreach($dir_finder as $file)
    {
      echo ">> ".pakeColor::colorize(basename($file), array('fg' => 'green'))."\n"; 
    }
    if ( count($dir_finder) ==0)
    {
      echo pakeColor::colorize("There are no flavors installed. Please run symfony choique-flavors-initialize!!\n", array('fg'=>'red', 'bold'=>true));
    }
  }


  pake_desc('Select flavor');
  pake_task('choique-flavor-select');
  
  /**
   * Select flavor
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_flavor_select($task, $args)
  {
    if (count($args) < 1)
    {
      echo "\n".pakeColor::colorize("You don't provide a flavor name\n", array('fg'=>'red', 'bold'=>true));
    }
    else
    {
      $flavor=$args[0];

      $flavor_dir = sfConfig::get('choique_flavors_root_dir', 'flavors');
      $dir_finder  = pakeFinder::type('dir')->maxdepth(0)->ignore_version_control()->in($flavor_dir);
      $flavors = array_map('basename', $dir_finder);

      if (!in_array($flavor, $flavors))
      {
        echo "\n".pakeColor::colorize("Specified flavor: $flavor does not exists. Please run choique-flavors-list task to see available flavors\n", array('fg'=>'red', 'bold'=>true));
        return;
      }

      define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
      define('SF_APP',         'backend');
      define('SF_ENVIRONMENT', 'prod');
      define('SF_DEBUG',       true);
      // get configuration
      require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
      $databaseManager = new sfDatabaseManager();
      $databaseManager->initialize();
      $flavor_dir = sfConfig::get('choique_flavors_root_dir', 'flavors');
      $config = array('choique' => array('flavors' => array('current' => $flavor, 'root_dir' => $flavor_dir)));
      $yaml = sfYaml::dump($config);
      file_put_contents('config'.DIRECTORY_SEPARATOR.'choique.yml', $yaml);
      $images = array('images' => array());
      $yaml = sfYaml::dump($images);
      file_put_contents('config'.DIRECTORY_SEPARATOR.'images.yml', $yaml);
      choiqueFlavors::getInstance()->setCurrent($flavor, true);
      choiqueFlavors::publishResources($flavor);
      run_clear_cache($task, array());
    }
  }



  pake_desc('Initialize flavors');
  pake_task('choique-flavors-initialize');

  /**
   * List available flavors
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_flavors_initialize($task, $args)
  {
    echo "\n".pakeColor::colorize("Initializing default flavor", array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";
    $flavor_dir = sfConfig::get('choique_flavors_root_dir', 'flavors');
    $images_flavor =  'web-frontend'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'frontend';
    $styles_flavor =  'web-frontend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'frontend';
    $dir_finder  = pakeFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(array($flavor_dir, $images_flavor, $styles_flavor ));
    if ( count($dir_finder) ==0)
    {
      $config = array('choique' => array('flavors' => array('current' => 'default', 'root_dir' => $flavor_dir)));
      $yaml = sfYaml::dump($config);
      file_put_contents('config'.DIRECTORY_SEPARATOR.'choique.yml', $yaml);
      $default_flavor = sfConfig::get('app_choique_default_flavor_dir',sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'default-flavor');
      $finder_flavor = pakeFinder::type('any')->ignore_version_control()->in( $default_flavor );
      pake_mirror($finder_flavor, $default_flavor, $flavor_dir.DIRECTORY_SEPARATOR.'default', $options = array());
      $web_css_dir = $default_flavor.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'css';
      $finder_web_css = pakeFinder::type('any')->ignore_version_control()->in( $web_css_dir );
      pake_mirror($finder_web_css, $web_css_dir, $styles_flavor, $options = array());
      $web_images = $default_flavor.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'images';
      $finder_web_images = pakeFinder::type('any')->ignore_version_control()->in( $web_images );
      pake_mirror($finder_web_images, $web_images, $images_flavor, $options = array());
      run_choique_fix_perms($task, array());  
      run_clear_cache($task, array());
    }
    else
    {
      echo pakeColor::colorize("There is at least one flavors installed. Please run symfony choique-flavors-list to list available flavors!!\n", array('fg'=>'red', 'bold'=>true));
      echo pakeColor::colorize("If no flavor is listed, remove content of web-frontend/css/frontend and web-frontend/images/frontend. Be careful this can destroy your site style!!\n", array('fg'=>'white', 'bg' => 'red', 'bold'=>true));
      echo pakeColor::colorize("\trm -fr flavors/* config/choique.yml web-frontend/css/frontend/* web-frontend/images/frontend/*\n", array('fg'=>'white', 'bg' => 'red', 'bold'=>true));
    }
  }

  pake_desc('Fix image sizes');
  pake_task('choique-multimedia-fix-size');

  /**
   * List available flavors
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_multimedia_fix_size($task, $args)
  {
    define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
    define('SF_APP',         'backend');
    define('SF_ENVIRONMENT', 'prod');
    define('SF_DEBUG',       true);
    // get configuration
    require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
    $databaseManager = new sfDatabaseManager();
    $databaseManager->initialize();
    foreach (MultimediaPeer::doSelect(new Criteria()) as $m)
    {
        $im = new ImageResizer($m);
        if ($im->canResizeImages() && $m->getType() == 'image')
        {
          try
          {
            if (is_file($m->getMediumUri()))
            {
              unlink($m->getMediumUri());
            }

            $im->resize('m');

            if (!$m->isDefaultSmallUri() && is_file($m->getSmallUri()))
            {
              unlink($m->getSmallUri());
            }

            $im->resize('s');

            $m->save();
            echo "\n".pakeColor::colorize(sprintf("Fixed size for multimedia ID: %d", $m->getId()), array('bg' => 'green', 'fg' => 'white', 'bold'=>true))."\n";
          }catch(Exception $e)
          {
            
            echo "\n".pakeColor::colorize(sprintf("Problems fixing size for multimedia ID: %d error => %s", $m->getId(), $e->getMessage()), array('bg' => 'red', 'fg' => 'white', 'bold'=>true))."\n";
          }
        }
    }
  }

  pake_desc('Create/update user');
  pake_task('choique-user-update-or-create-admin');
  
  /**
   * Select flavor
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_user_update_or_create_admin($task, $args)
  {
    if (count($args) < 2)
    {
      echo "\n".pakeColor::colorize("You don't provide a username and password\n", array('fg'=>'red', 'bold'=>true));
    }
    else
    {
      $username=$args[0];
      $password=$args[1];

      define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
      define('SF_APP',         'backend');
      define('SF_ENVIRONMENT', 'prod');
      define('SF_DEBUG',       true);
      // get configuration
      require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
      $databaseManager = new sfDatabaseManager();
      $databaseManager->initialize();

      $user = sfGuardUserPeer::retrieveByUsername($username);
      $created = false;
      if (!$user) {
        $created = true;
        $user = new sfGuardUser();
        $user->setUsername($username);
      }
      $user->setPassword($password);
      $user->setIsActive(true);
      $user->setIsSuperAdmin(true);
      $user->save();

      echo "\n".pakeColor::colorize(sprintf("User %s successfully\n", $created? "created":"updated"), array('fg'=>'green', 'bold'=>true));

    }
  }

  pake_desc('Disable user');
  pake_task('choique-user-disable');
  
  /**
   * Select flavor
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_user_disable($task, $args)
  {
    if (count($args) < 1)
    {
      echo "\n".pakeColor::colorize("You don't provide a username\n", array('fg'=>'red', 'bold'=>true));
    }
    else
    {
      $username=$args[0];

      define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
      define('SF_APP',         'backend');
      define('SF_ENVIRONMENT', 'prod');
      define('SF_DEBUG',       true);
      // get configuration
      require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
      $databaseManager = new sfDatabaseManager();
      $databaseManager->initialize();

      $user = sfGuardUserPeer::retrieveByUsername($username, true);
      if (!$user) {
        echo "\n".pakeColor::colorize("Username $username is just disabled or username is unknown\n", array('fg'=>'red', 'bold'=>true));
         return;
      }
      $user->setIsActive(false);
      $user->save();

      echo "\n".pakeColor::colorize("User successfully disabled \n", array('fg'=>'green', 'bold'=>true));
    }
  }

  pake_desc('Enable user');
  pake_task('choique-user-enable');
  
  /**
   * Select flavor
   *
   *
   * @param object $task
   * @param array $args
   */
  function run_choique_user_enable($task, $args)
  {
    if (count($args) < 1)
    {
      echo "\n".pakeColor::colorize("You don't provide a username\n", array('fg'=>'red', 'bold'=>true));
    }
    else
    {
      $username=$args[0];

      define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
      define('SF_APP',         'backend');
      define('SF_ENVIRONMENT', 'prod');
      define('SF_DEBUG',       true);
      // get configuration
      require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
      $databaseManager = new sfDatabaseManager();
      $databaseManager->initialize();

      $user = sfGuardUserPeer::retrieveByUsername($username, false);
      if (!$user) {
        echo "\n".pakeColor::colorize("Username $username is just enabled or username is unknown\n", array('fg'=>'red', 'bold'=>true));
         return;
      }
      $user->setIsActive(true);
      $user->save();

      echo "\n".pakeColor::colorize("User successfully enabled \n", array('fg'=>'green', 'bold'=>true));
    }
  }

  
