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
 * choiqueView
 *
 * @author ncuesta
 */
class choiqueView extends sfPHPView
{
  protected function getTemplateDir($module_name, $template)
  {
    $directory = choiqueFlavors::getModulePath($module_name).'/'.sfConfig::get('sf_app_module_template_dir_name');

    if (is_readable($directory.'/'.$template))
    {
      return $directory;
    }

    return null;
  }

  protected function getGlobalTemplateDir($template)
  {
    $directory = choiqueFlavors::getGlobalPath().'/templates';

    if (is_readable($directory.'/'.$template))
    {
      return $directory;
    }

    return null;
  }

  public function configure()
  {
    parent::configure();

    if (!is_readable($this->getDirectory().'/'.$this->getTemplate()) || !$this->directory)
    {
      $this->setDirectory($this->getTemplateDir($this->moduleName, $this->getTemplate()));

      // require our configuration
      $viewConfigFile = choiqueFlavors::getModulePath($this->moduleName).'/'.sfConfig::get('sf_app_module_config_dir_name').'/view.yml';
      if ($config = sfConfigCache::getInstance()->checkConfig($viewConfigFile, true))
      {
        require($config);
      }
    }

    if (!is_readable($this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate()))
    {
      $this->decoratorDirectory = $this->getGlobalTemplateDir($this->getDecoratorTemplate());
    }
  }

  /**
   * Loop through all template slots and fill them in with the results of
   * presentation data.
   *
   * @override
   * 
   * @param string A chunk of decorator content
   *
   * @return string A decorated template
   */
  protected function decorate($content)
  {
    $template = $this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate();
    if (!is_readable($template))
    {
      $template = $this->getGlobalTemplateDir($template);
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfView} decorate content with "'.$template.'"');
    }

    // set the decorator content as an attribute
    $this->attributeHolder->set('sf_content', $content);

    // for backwards compatibility with old layouts; remove at 0.8.0?
    $this->attributeHolder->set('content', $content);

    // render the decorator template and return the result
    $retval = $this->renderFile($template);

    return $retval;
  }
}