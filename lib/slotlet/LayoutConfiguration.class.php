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
 * Layout Configuration wrapper classes.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */

/**
 * Global configuration.
 */
class LayoutConfiguration
{
  protected
    $options = array(),
    $rows    = array();

  public function __construct(stdClass $configuration)
  {
    $this->load($configuration);
  }

  private function load(stdClass $configuration)
  {
    return $this
      ->loadOptions($configuration->options)
      ->loadRows($configuration->rows)
    ;
  }

  private function loadOptions($options)
  {
    foreach ($options as $key => $value)
    {
      $this->options[$key] = $value;
    }

    return $this;
  }

  private function loadRows($rows)
  {
    foreach ($rows as $row)
    {
      $this->rows[] = new LayoutConfigurationRow($row);
    }

    return $this;
  }

  public function render($options = array())
  {
    $html = '';
    $options['number_of_rows'] = count($this->rows);

    foreach ($this->rows as $row)
    {
      $html .= $row->render($options);
    }

    return $html;
  }

  public function getSlotlets($options)
  {
    $slotlets = array();

    foreach ($this->rows as $row)
    {
      $slotlets = array_merge($slotlets, $row->getSlotlets($options));
    }

    return $slotlets;
  }

  public function getRows()
  {
    return $this->rows;
  }

  public function isEmpty()
  {
    return count($this->rows) == 0;
  }

}

/**
 * Row configuration.
 */
class LayoutConfigurationRow
{
  protected
    $columns = array(),
    $options = array('class' => '');

  protected
    $template = '
<div class="%class%">
  %content%
  <div style="clear: both; font-size: 0px; height: 0.1px;"> </div>
</div>';

  public function __construct(stdClass $configuration)
  {
    $this->load($configuration);
  }

  public function getOption($option, $default = null)
  {
    return array_key_exists($option, $this->options) ? $this->options[$option] : $default;
  }

  private function load(stdClass $configuration)
  {
    return $this
      ->loadOptions($configuration->options)
      ->loadColumns($configuration->columns)
    ;
  }

  private function loadOptions($options)
  {
    foreach ($options as $key => $value)
    {
      $this->options[$key] = $value;
    }

    return $this;
  }

  private function loadColumns($columns)
  {
    foreach ($columns as $column)
    {
      $this->columns[] = new LayoutConfigurationColumn($column);
    }

    return $this;
  }

  public function isEmpty()
  {
    return (0 == count($this->columns));
  }

  public function render($options)
  {
    if ($this->isEmpty())
    {
      return;
    }

    $options['row_class']     = $this->options['class'];
    $options['columns_count'] = count($this->columns);

    return strtr($this->template, array(
      '%class%'   => 'layout_container '.$options['row_class'],
      '%content%' => $this->renderColumns($options)
    ));
  }

  public function getSlotlets($options = array())
  {
    $slotlets = array();

    foreach ($this->columns as $column)
    {
      $slotlets = array_merge($slotlets, $column->getSlotlets($options));
    }

    return $slotlets;
  }

  public function renderColumns($options)
  {
    $html = '';

    foreach ($this->columns as $column)
    {
      $html .= $column->render($options);
    }

    return $html;
  }

  public function getColumns()
  {
    return $this->columns;
  }

}

/**
 * Column configuration.
 */
class LayoutConfigurationColumn
{
  protected
    $options  = array('width' => false),
    $slotlets = array();

  protected
    $template = '
<div class="%class%" style="width: %width%%measure_unit%; float: left;">
  %content%
</div>';

  public function __construct($configuration)
  {
    $this->load($configuration);
  }

  public function getOption($option, $default = null)
  {
    return array_key_exists($option, $this->options) ? $this->options[$option] : $default;
  }

  private function load($configuration)
  {
    return $this
      ->loadOptions($configuration->options)
      ->loadSlotlets($configuration->slotlets)
    ;
  }

  private function loadOptions($options)
  {
    foreach ($options as $key => $value)
    {
      $this->options[$key] = $value;
    }

    return $this;
  }

  private function loadSlotlets($slotlets)
  {
    foreach ($slotlets as $slotlet)
    {
      $this->slotlets[] = new LayoutConfigurationSlotlet($slotlet);
    }

    return $this;
  }

  public function isEmpty()
  {
    return (0 == count($this->slotlets));
  }

  public function render($options)
  {
    $html = '';

    if (null == $this->options['width'] || '' == trim($this->options['width']))
    {
      unset($this->options['width']);
    }
    else
    {
      if (0 < preg_match('/(\d+)(\D*)/', $this->options['width'], $matches))
      {
        $this->options['width'] = trim($matches[1]);

        if (isset($matches[2]) && trim($matches[2]) != '')
        {
          $this->options['measure_unit'] = trim($matches[2]);
        }
        else
        {
          $this->options['measure_unit'] = 'px';
        }
      }
    }

    $options = array_merge(array(
      'width'        => 99.5 / $options['columns_count'],
      'content'      => $options['content'],
      'measure_unit' => '%',
      'main_content' => $options['main_content']
    ), $this->options, $options);

    $content = $this->renderSlotlets($options);

    if (!isset($options['class']))
    {
      $options['class'] = '';
    }

    $options['class'] .= ' slotlet_container';

    return strtr($this->template, array(
      '%class%'        => $options['class'],
      '%width%'        => $options['width'],
      '%measure_unit%' => $options['measure_unit'],
      '%content%'      => trim($content) === '' ? '&nbsp;' : $content
    ));
  }

  public function renderSlotlets($options)
  {
    $content  = '';

    foreach ($this->slotlets as $slotlet)
    {
      $content .= $slotlet->render($options);
    }

    return $content;
  }

  public function getSlotlets($options = array())
  {
    $slotlets = array();

    foreach ($this->slotlets as $slotlet)
    {
      $slotlets[] = $slotlet->getSlotlet($options);
    }

    return $slotlets;
  }

}

/**
 * Slotlet configuration.
 */
class LayoutConfigurationSlotlet
{
  protected
    $name,
    $options = array();

  public function __construct($slotlet)
  {
    $this->name = $slotlet->name;

    $this->loadOptions($slotlet->options);
  }

  private function loadOptions($options)
  {
    foreach ($options as $key => $value)
    {
      $this->options[$key] = $value;
    }

    return $this;
  }

  public function render($options)
  {
    return SlotletManager::render($this->name, array_merge($options, $this->options));
  }

  public function getSlotlet($options)
  {
    $slotlet = SlotletManager::get($this->name);

    $slotlet->options = array_merge($slotlet->getDefaultOptions(), $options, $this->options);

    return $slotlet;
  }

}