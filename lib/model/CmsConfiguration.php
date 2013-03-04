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
 * Subclass for representing a row from the 'cms_configuration' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CmsConfiguration extends BaseCmsConfiguration
{
  const FIELD_TYPE_TEXT     = '';
  const FIELD_TYPE_CHECKBOX = 'check';
  const FIELD_TYPE_HIDDEN   = 'hidden';
  const FIELD_TYPE_CUSTOM   = 'custom';

  static protected $_visited = array();

  public static function has($key)
  {
    $param = self::getConfigurationParameterByKey($key);

    return (!(empty($param) || is_null($param)));
  }

  public static function addToCache($cms_configuration)
  {
    if (null === $cms_configuration)
    {
      return;
    }

    self::$_visited[$cms_configuration->getConfigurationKey()] = $cms_configuration;
  }

  public static function getFromCache($key)
  {
    if (self::hasCached($key))
    {
      return self::$_visited[$key];
    }

    return null;
  }

  public static function hasCached($key)
  {
    return array_key_exists($key, self::$_visited);
  }

  public static function get($key, $default = null)
  {
    $param = self::getConfigurationParameterByKey($key);

    if (empty($param))
    {
      $param = self::createKey($key, $default);
    }

    self::addToCache($param);
    
    return $param->getConfigurationValue();
  }

  public static function createKey($key, $default = null)
  {
    $configuration = new CmsConfiguration();
    $configuration->setConfigurationKey($key);
    $configuration->setConfigurationValue($default);
    $configuration->setName(self::getNameFromYaml($key));
    $configuration->save();

    return $configuration;
  }

  private static function getFromYaml()
  {
    $values = sfYaml::load(sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'configuration_names.yml');
    return is_array($values) ? $values['names'] : $values;
  }

  public static function getNameFromYaml($key)
  {
    $values = self::getFromYaml();
    $real_values = array();
    foreach (array_values($values) as $k=>$v) $real_values += $v; 
    $values = $real_values;
    return ((is_array($values) && array_key_exists($key, $values)) ? $values[$key] : $key);
  }

  public static function set($key, $value)
  {
    $param = self::getParameter($key);
    $param->setConfigurationValue($value);
    $param->save();

    self::addToCache($param);
    return $param;
  }

  //If the parameter $key doesn't exist, it will be created
  public static function getParameter($key)
  {
    $param = self::getConfigurationParameterByKey($key);
    if (!$param)
    {
      $param = new CmsConfiguration();
      $param->setConfigurationKey($key);
      $param->setName($key);
      $param->save();
    }

    return $param;
  }

  public static function getAll()
  {
    return CmsConfigurationPeer::doSelect(new Criteria()); 
  }

  public static function getAllOptions()
  {
    $values = self::getFromYaml();
    $objects = array();
    foreach($values as $category => $options)
    {
      $objects[$category] = array();
      foreach($options as $key => $description)
      {
        $config = self::getConfigurationParameterByKey($key);
        if ( $config == null) 
        {
          $config = self::set($key, null);
        }
        $config->setName($description);
        $objects[$category][$key] = $config;
      } 
    }
    return $objects; 
  }

  public static function getConfigurationParameterByKey($key)
  {
    if (null !== $param = self::getFromCache($key))
    {
      return $param;
    }

    $c = new Criteria();
    $c->add(CmsConfigurationPeer::CONFIGURATION_KEY, $key);

    $param = CmsConfigurationPeer::doSelectOne($c);

    self::addToCache($param);

    return $param;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function getConfigurationValue()
  {
    if ($this->_parse_key_name() == self::FIELD_TYPE_CHECKBOX)
    {
      return (parent::getConfigurationValue() === '1');
    }
    else
    {
      return parent::getConfigurationValue();
    }
  }

  public function setConfigurationValue($v)
  {
    if ($this->_parse_key_name() == self::FIELD_TYPE_CHECKBOX)
    {
      $v = (isset($v) && !is_null($v) && $v != '0') ? '1' : '0';
    }

    return parent::setConfigurationValue($v);
  }

  protected function _parse_key_name()
  {
    if (preg_match('/^' .self::FIELD_TYPE_HIDDEN . '_\w/', $this->getConfigurationKey()) > 0)
    {
      return self::FIELD_TYPE_HIDDEN;
    }
    elseif (preg_match('/^' .self::FIELD_TYPE_CHECKBOX . '_\w/', $this->getConfigurationKey()) > 0)
    {
      return self::FIELD_TYPE_CHECKBOX;
    }
    elseif (preg_match('/^' .self::FIELD_TYPE_CUSTOM . '_\w/', $this->getConfigurationKey()) > 0)
    {
      return self::FIELD_TYPE_CUSTOM;
    }
    else
    {
      return self::FIELD_TYPE_TEXT;
    }
  }

  protected function getKeyWithoutType($key)
  {
    return substr($key,strpos($key,'_')+1);
  }

  public function getFormRow()
  {
    sfLoader::loadHelpers(array('Tag', 'Form', 'I18N'));

    if ($this->_parse_key_name() === self::FIELD_TYPE_HIDDEN)
    {
      return input_hidden_tag('cms_configuration[' . $this->getConfigurationKey() . ']', $this->getConfigurationValue());
    }

    $html  = "<div class=\"form-row\">";
    $html .= label_for('cms_configuration[' . $this->getConfigurationKey() . ']', __($this->getName()), 'class=required');
    $html .= "<div class=\"content\">";

    switch ($this->_parse_key_name())
    {
      case self::FIELD_TYPE_CHECKBOX:
        $html .= checkbox_tag('cms_configuration[' . $this->getConfigurationKey() . ']', 1, $this->getConfigurationValue());
        break;
      case self::FIELD_TYPE_CUSTOM:
        $key = $this->getKeyWithoutType($this->getConfigurationKey());
        $html .= get_partial('administration/'.$key);
        break;
      case self::FIELD_TYPE_TEXT:
      default:
        $html .= input_tag('cms_configuration[' . $this->getConfigurationKey() . ']', $this->getConfigurationValue(), 'size=80');
    }

    $html .= "</div>";
    $html .= "<div style=\"clear:both; height: 1px\">&nbsp;</div>";
    $html .= "</div>";

    return $html;
  }

  public static function getVersion()
  {
    return sfConfig::get('app_choique_version', '2.0.0');
  }


  static public function getUseNavigationInArticles()
  {
    return CmsConfiguration::get('check_use_navigation_in_articles', true);
  }
  
  static public function getUseNavigationInSections()
  {
    return CmsConfiguration::get('check_use_navigation_in_sections', true);
  }

}