<?php

/**
 * choiqueConfigHandler
 *
 * @author ncuesta
 */
class choiqueConfigHandler extends sfYamlConfigHandler
{
  public function execute($configFiles)
  {
    $this->initialize();

    $config = $this->parseYamls($configFiles);

    $data   = "<?php\n";
    $data  .= "sfConfig::set('choique_flavors_root_dir', '{$config['choique']['flavors']['root_dir']}');\n";
    $data  .= "sfConfig::set('choique_flavors_current',  '{$config['choique']['flavors']['current']}');\n";

    return $data;
  }
}