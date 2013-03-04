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
 * Poll slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class PollSlotlet implements ISlotlet
{
  public function getJavascripts()
  {
    return array();
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/sl_form.css');
  }

  public function getDefaultOptions()
  {
    return array(
      'id'              => 'poll_slotlet',
      'class'           => 'sl_poll',
      'title'           => __('Encuesta'),
      'show_when_empty' => true
    );
  }

  public function render($options = array())
  {
    sfLoader::loadHelpers(array('I18N'));

    $form = FormPeer::retrieveLastActivePoll();

    if (false === $options['show_when_empty'] && null === $form)
    {
      return null;
    }

    $template = <<<SLOTLET
<div id="%id%" class="slotlet %class%">
  <div class="title">%title%</div>
  %content%
  <div class="footer">
  </div>
</div>
SLOTLET;

    return strtr($template, array(
      '%id%'      => $options['id'],
      '%class%'   => $options['class'],
      '%title%'   => $options['title'],
      '%content%' => $this->getContent($form, $options)
    ));
  }

  protected function getContent($form, $options)
  {
    if (null === $form)
    {
      return strtr('<div class="rectangle">%content%</div>', array('%content%' => __('En este momento no hay encuestas activas')));
    }
    else if (!sfContext::getInstance()->getRequest()->getCookie($form->getName()))
    {
      return $form->getHTMLRepresentation();
    }
    else
    {
      $template = <<<CONTENT
<div class="rectangle">
  <div class="form-description">
    <h1 class="form-description-title">%title%</h1>
  </div>
  <div id="%id%_results">
    %content%
  </div>
</div>
CONTENT;

      $row_template = <<<ROW
<div class="results-row">
  <div class="results-first-td">%label%</div>
  <div class="results-second-td">%value%</div>
</div>
ROW;

      $content = '';
      if ($form->getRows() > 0)
      {
        foreach ($form->getFieldsBySort() as $field)
        {
          $content .= strtr($row_template, array(
            '%label%' => $field->getLabel() ? $field->getLabel() : __('Etiqueta'),
            '%value%' => sprintf("%.2f%%", $field->getPercentage())
          ));
        }
      }

      return strtr($template, array(
        '%title%'   => $form->getDescription(),
        '%id%'      => $options['id'],
        '%content%' => $content
      ));
    }
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra la última encuesta activa (si hay alguna) en el sitio.';
  }

  public static function getName()
  {
    return 'Encuesta';
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'class'           => __('Clase CSS'),
      'title'           => __('Título'),
    );

    $form = '';

    foreach (array('title', 'class') as $key)
    {
      $form .= strtr($row, array(
        '%id%'    => $key,
        '%label%' => $labels[$key],
        '%field%' => input_tag($key, $values[$key], array('class' => 'slotlet_option'))
      ));
    }

    return $form;
  }

}