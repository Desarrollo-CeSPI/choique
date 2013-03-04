<?php

/**
 * choiqueViewConfigHandler
 *
 * @author ncuesta
 */
class choiqueViewConfigHandler extends sfViewConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws <b>sfConfigurationException</b> If a requested configuration file does not exist or is not readable
   * @throws <b>sfParseException</b> If a requested configuration file is improperly formatted
   * @throws <b>sfInitializationException</b> If a view.yml key check fails
   */
  public function execute($configFiles)
  {
    $configFiles = array_merge($configFiles, $this->currentFlavorConfigFiles());

    return parent::execute($configFiles);
  }

  protected function currentFlavorConfigFiles()
  {
    $current_flavor_path = choiqueFlavors::getInstance()->getGlobalPath(false);

    return array(
        $current_flavor_path.'/config/view.yml'
      );
  }
}