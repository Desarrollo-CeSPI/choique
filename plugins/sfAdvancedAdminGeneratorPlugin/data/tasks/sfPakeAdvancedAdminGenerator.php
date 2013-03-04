<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('initialize a new advanced admin module');
pake_task('propel-init-advanced-admin', 'app_exists');

function run_propel_init_advanced_admin($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide your module name.');
  }

  if (count($args) < 3)
  {
    throw new Exception('You must provide your model class name.');
  }

  $app         = $args[0];
  $module      = $args[1];
  $model_class = $args[2];
  $theme       = isset($args[3]) ? $args[3] : 'default';

  try
  {
    $author_name = $task->get_property('author', 'symfony');
  }
  catch (pakeException $e)
  {
    $author_name = 'Your name here';
  }

  $constants = array(
    'PROJECT_NAME' => $task->get_property('name', 'symfony'),
    'APP_NAME'     => $app,
    'MODULE_NAME'  => $module,
    'MODEL_CLASS'  => $model_class,
    'AUTHOR_NAME'  => $author_name,
    'THEME'        => $theme,
  );

  $moduleDir = sfConfig::get('sf_root_dir').'/'.sfConfig::get('sf_apps_dir_name').'/'.$app.'/'.sfConfig::get('sf_app_module_dir_name').'/'.$module;

  // create module structure
  $finder = pakeFinder::type('any')->ignore_version_control()->discard('.sf');
  $dirs = sfLoader::getGeneratorSkeletonDirs('sfAdvancedAdmin', $theme);
  foreach ($dirs as $dir)
  {
    if (is_dir($dir))
    {
      pake_mirror($finder, $dir, $moduleDir);
      break;
    }
  }

  // customize php and yml files
  $finder = pakeFinder::type('file')->ignore_version_control()->name('*.php', '*.yml');
  pake_replace_tokens($finder, $moduleDir, '##', '##', $constants);
}
