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
 * Subclass for representing a row from the 'field' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Field extends BaseField
{
  const INPUT_TYPE_CHECKBOX = 0;
  const INPUT_TYPE_DATE     = 1;
//  const INPUT_TYPE_FILE     = 2;
  const INPUT_TYPE_RADIO    = 3;
  const INPUT_TYPE_TEXT     = 4;
  const LABEL               = 5;
  const SELECT              = 6;
  const TEXTAREA            = 7;

  public static function getFieldTypes()
  {
    return array(self::INPUT_TYPE_CHECKBOX,
                 self::INPUT_TYPE_DATE,
//                 self::INPUT_TYPE_FILE,
                 self::INPUT_TYPE_RADIO,
                 self::INPUT_TYPE_TEXT,
                 self::LABEL,
                 self::SELECT,
                 self::TEXTAREA);
  }

  public static function getRepresentativeImage($field)
  {
    switch($field)
    {
      case self::INPUT_TYPE_CHECKBOX:
        return content_tag('div',
                           label_for('checkbox_demo', __('Checkbox de demostración')).checkbox_tag('checkbox_demo', 'checkbox_demo', false, array('id' => 'demo')),
                           array('id' => self::INPUT_TYPE_CHECKBOX.'_demo', 'class' => 'field'));
      break;
      case self::INPUT_TYPE_DATE:
        return content_tag('div',
                           label_for('input_date_tag_demo', __('Demostración de fechas')).input_date_tag('input_date_tag_demo', '', array('id' => 'demo', 'rich' => true)),
                           array('id' => self::INPUT_TYPE_DATE.'_demo', 'class' => 'field'));
      break;
      /*
      case self::INPUT_TYPE_FILE:
        return content_tag('div',
                           label_for('input_file_tag_demo', __('Demostración de archivos')).input_file_tag('input_file_tag_demo', '', array('id' => 'demo')),
                           array('id' => self::INPUT_TYPE_FILE.'_demo', 'class' => 'field'));
      break;
      */
      case self::INPUT_TYPE_RADIO:
        return content_tag('div',
                           label_for('radiobutton_demo', __('Radiobutton de demostración')).radiobutton_tag('radiobutton_demo', 'radiobutton_demo', false, array('id' => 'demo')),
                           array('id' => self::INPUT_TYPE_RADIO.'_demo', 'class' => 'field'));
      break;
      case self::INPUT_TYPE_TEXT:
        return content_tag('div',
                           label_for('input_tag_demo', __('Demostración de textarea')).input_tag('input_tag_demo', '', array('id' => 'demo')),
                           array('id' => self::INPUT_TYPE_TEXT.'_demo', 'class' => 'field'));
      break;
      case self::LABEL:
        return content_tag('div',
                           label_for('label_demo', __('Etiqueta de demostración')),
                           array('id' => self::LABEL.'_demo', 'class' => 'field'));
      break;
      case self::SELECT:
        return content_tag('div',
                           label_for('select_tag_demo', __('Demostración de multiple selección')).select_tag('select_tag_demo', options_for_select(self::getDefaultOptions()), array('id' => 'demo')),
                           array('id' => self::SELECT.'_demo', 'class' => 'field'));
      break;
      case self::TEXTAREA:
        return content_tag('div',
                           label_for('textarea_tag_demo', __('Demostración de texto libre')).textarea_tag('textarea_tag_demo', '', array('id' => 'demo')),
                           array('id' => self::TEXTAREA.'_demo', 'class' => 'field'));
      break;
    }
  }

  public function getHTMLRepresentation()
  {
    sfLoader::loadHelpers(array('Form', 'I18N'));

    $name  = $this->getForm()->getNameForHTML().'_'.$this->getId();
    $label = ($this->getLabel())?$this->getLabel():__('Etiqueta');

    if ($this->getIsRequired())
    {
      $str = label_for($this->getId(), $label, array('class' => 'required'));
    }
    else
    {
      $str = label_for($this->getId(), $label);
    }

    switch($this->getType())
    {
      case self::INPUT_TYPE_CHECKBOX:
        return $str.checkbox_tag($name, $this->getDefaultValue(), false, array('id' => $this->getId(), 'class' => 'checkbox'));
      break;
      case self::INPUT_TYPE_DATE:
        return $str.input_date_tag($name, '', array('id' => $this->getId(), 'rich' => true));
      break;
      /*
      case self::INPUT_TYPE_FILE:
        return $str.input_file_tag($name, '', array('id' => $this->getId()));
      break;
      */
      case self::INPUT_TYPE_RADIO:
        return $str.radiobutton_tag($this->getDefaultValue(), $this->getLabel(), true, array('id' => $this->getId(), 'class' => 'radio-button'));
      break;
      case self::INPUT_TYPE_TEXT:
        return $str.input_tag($name, $this->getDefaultValue(), array('id' => $this->getId(), 'class' => 'text-input'));
      break;
      case self::LABEL:
        return content_tag('div', ($this->getLabel())?$this->getLabel():__('Etiqueta'), array('id' => $this->getId(), 'class' => 'label'.($this->getIsRequired()?' required':'')));
      break;
      case self::SELECT:
        if ($this->getDefaultValue())
        {
          $options = $this->getOptionsArray();
        }
        else
        {
          $options = self::getDefaultOptions();
        }
        return $str.select_tag($name, options_for_select($options), array('id' => $this->getId(), 'class' => 'select'));
      break;
      case self::TEXTAREA:
        return $str.textarea_tag($name, $this->getDefaultValue(), array('id' => $this->getId(), 'class' => 'textarea'));
      break;
    }
  }

  public static function getDefaultOptions()
  {
    sfLoader::loadHelpers(array('I18N'));
    
    return array(1 => __('Primera opción'), 2 => __('Segunda opción'), 3 => __('Tercera opción'));
  }

  public function getOptionsArray()
  {
    $dv = explode("\n", $this->getDefaultValue());

    $options = array('' => '');

    foreach ($dv as $op)
    {
      $options[$op] = $op;
    }

    return $options;
  }

  public function getCount()
  {
    $c = new Criteria();
    $c->add(DataPeer::FIELD_ID, $this->getId());

    return DataPeer::doCount($c);
  }

  public function getPercentage()
  {
    return ($this->getCount() * 100) / $this->getForm()->getRows();
  }
}