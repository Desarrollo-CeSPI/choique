<?php

/**
 * choiqueImagesConfigHandler
 *
 * @author ncuesta
 */
class choiqueImagesConfigHandler extends sfYamlConfigHandler
{
  public function execute($configFiles)
  {
    $this->initialize();

    $config = $this->parseYamls($configFiles);

    $template = <<<PHP
  sfConfig::set('choique_image_%name%', '%path%');

PHP;

    $code = "<?php\n\n  // Choique images configuration\n\n";

    foreach ($config['images'] as $name => $path)
    {
      $code .= strtr($template, array('%name%' => $name, '%path%' => $path));
    }

    return $code;
  }
  
}