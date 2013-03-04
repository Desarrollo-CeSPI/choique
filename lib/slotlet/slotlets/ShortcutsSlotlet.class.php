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
 * Shortcuts slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ShortcutsSlotlet implements ISlotlet
{
  protected function getCSSClassForType($type)
  {
    $classes = array(
      Shortcut::REFERENCE_TYPE_EXTERNAL        => "shortcut_external",
      Shortcut::REFERENCE_TYPE_EXTERNAL_POP_UP => "shortcut_external_pop_up",
      Shortcut::REFERENCE_TYPE_ARTICLE         => "shortuct_article",
      Shortcut::REFERENCE_TYPE_SECTION         => "shortcut_section",
      Shortcut::REFERENCE_TYPE_NONE            => "shortcut_none"
    );

    if (array_key_exists($type, $classes))
    {
      return $classes[$type];
    }
    else
    {
      return $classes[Shortcut::REFERENCE_TYPE_NONE];
    }
  }

  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_shortcut.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'class'           => 'shortcut_slotlet',
      'show_when_empty' => true,
      'shortcut_id'     => null
    );
  }

  public function render($options = array())
  {
    $shortcut = $this->getShortcut($options);

    $template = <<<SLOTLET
<div class="slotlet %class%">
  %content%
</div>
SLOTLET;

    return strtr($template, array(
      '%class%'   => $options['class'],
      '%content%' => $this->renderContent($shortcut, $options)
    ));
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra un atajo.';
  }

  public static function getName()
  {
    return 'Atajo';
  }
  
  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'           => __('Clase CSS'),
      'shortcut_id'     => __('Atajo'),
      'show_when_empty' => __('Mostrar si vacío')
    );

    $form = strtr($row, array(
      '%id%'    => 'shortcut_id',
      '%label%' => $labels['shortcut_id'],
      '%field%' => select_tag('shortcut_id', objects_for_select(ShortcutPeer::retrievePublished(), 'getId', '__toString', $values['shortcut_id'], array('include_blank' => true)), array('class' => 'slotlet_option'))
    ));
    
    $form .= strtr($row, array(
      '%id%'    => 'show_when_empty',
      '%label%' => $labels['show_when_empty'],
      '%field%' => checkbox_tag('show_when_empty', true, $values['show_when_empty'] != false, array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));

    return $form;
  }

  protected function getShortcut($options)
  {
    return ShortcutPeer::retrieveByPK($options['shortcut_id']);
  }

  protected function renderContent($shortcut, $options)
  {
    if (null === $shortcut || !$shortcut->getIsPublished())
    {
      if (false != $options['show_when_empty'])
      {
        return __('Sin contenido para mostrar');
      }
      else
      {
        return '';
      }
    }

    $template = '<div class="shortcut %type% %with_image% %is_external%">%content%</div>';

    return strtr($template, array(
      '%type%'        => $this->getCSSClassForType($shortcut->getReferenceType()),
      '%with_image%'  => $shortcut->hasMultimedia() ? 'with-image' : '',
      '%is_external%' => $shortcut->getOpenInNewWindow() || $shortcut->getReferenceType() == Shortcut::REFERENCE_TYPE_EXTERNAL_POP_UP ? 'external-shortcut' : '',
      '%content%'     => $shortcut->getHTMLRepresentation()
    ));
  }

}