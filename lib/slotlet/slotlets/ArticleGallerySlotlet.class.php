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
 * Article gallery slotlet
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ArticleGallerySlotlet implements ISlotlet
{
  protected function getOptionsForGalleryId($selected_value = null)
  {
    return objects_for_select(ArticleGroupPeer::retrievePublished(),
      'getId',
      'getName',
      $selected_value,
      array('include_custom' => 'Contextual (Obtener del contenido principal)')
    );
  }

  protected function getRendererOptions($selected_value = null)
  {
    $options = array();

    foreach (SlotletRendererFactory::getFor($this) as $class_name => $renderer)
    {
      $options[$class_name] = strval($renderer);
    }
    
    return options_for_select($options, $selected_value);
  }

  public function getConfigurationForm($values = array())
  {
    $row    = '<div><label for="%id%">%label%</label> %field%</div><div style="clear:both;"></div>';
    $labels = array(
      'title'                        => __('Título'),
      'article_group_id'             => __('Galería de artículos'),
      'class'                        => __('Clase CSS'),
      'visibles'                     => __('Elementos por página'),
      'use_background_section_color' => __('Usar color de sección como fondo'),
    );

    $form = strtr($row, array(
      '%id%'    => 'article_group_id',
      '%label%' => $labels['article_group_id'],
      '%field%' => select_tag('article_group_id', $this->getOptionsForGalleryId($values['article_group_id']), array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'title',
      '%label%' => $labels['title'],
      '%field%' => input_tag('title', $values['title'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'class',
      '%label%' => $labels['class'],
      '%field%' => input_tag('class', $values['class'], array('class' => 'slotlet_option'))
    ));
    
    $form .= strtr($row, array(
      '%id%'    => 'visibles',
      '%label%' => $labels['visibles'],
      '%field%' => input_tag('visibles', $values['visibles'], array('class' => 'slotlet_option'))
    ));

    $form .= strtr($row, array(
      '%id%'    => 'use_background_section_color',
      '%label%' => $labels['use_background_section_color'],
      '%field%' => checkbox_tag('use_background_section_color', true, $values['use_background_section_color'] != false, array('class' => 'slotlet_option')) 
    ));
        
    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'            => 'rm_slotlet_article_gallery',
      'title'            => __('Galería de artículos'),
      'article_group_id' => null,
      'visibles'         => 4,
      'section_name'     => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'use_background_section_color' => false
    );
  }

  public function getJavascripts()
  {
    return array('scrollable.min.js', 'scrollable.autoscroll.min.js', 'slotlets/article_gallery_list.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/article_gallery_list.css');
  }

  public function render($options = array())
  {
    $class         = $options['class'];
    $title         = $options['title'];
    $article_group = $this->getArticleGallery($options['article_group_id'], $options['section_name']);
    $visibles      = intval($options['visibles']);

    if (null == $article_group)
    {
      $id  = time() % 50 + 1;
      $url = '#';

      $template = <<<SLOTLET
<div class="slotlet article_gallery %class%">
  <div class="article_gallery_container">
    <h2 class="article_gallery_title">%title%</h2>
    <div class="scrollable vertical">
      <div class="items">
        <div>
          <div class="item">
            %content%
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
SLOTLET;

      return strtr($template, array(
        '%content%' => __('Sin contenidos para mostrar'),
        '%class%'   => $options['class'],
        '%back_label%' => 'Anterior',
        '%next_label%' => 'Siguiente'
      ));
    }

    $template = <<<SLOTLET
<div class="slotlet article_gallery %class%">
  <div class="article_gallery_container">
    <h2 class="article_gallery_title">%title%</h2>
    <div id="actions">
      <a class="prev">&laquo; %back_label%</a>
      <a class="next">%next_label% &raquo;</a>
    </div>
    <div class="scrollable vertical">
      <div class="items">
        %articles%
      </div>
    </div>
  </div>
</div>
SLOTLET;

    $id = $article_group->getId();
    $template = strtr($template, array(
      '%title%'      => $title,
      '%id%'         => 'slotlet_article_gallery_'.sfInflector::underscore($title).'_'.$id,
      '%class%'      => $class,
      '%back_label%' => __('Anterior'),
      '%next_label%' => __('Siguiente')
    ));

    $criteria = new Criteria();
    $criteria->setLimit($article_group->getVisibleItems());

    $articles = $article_group->getArticlesByPriority($criteria);

    $count = count($articles);
    $articles_template = '';

   if($options['use_background_section_color'])
   {

    $article_tpl_i = <<<SLOTLET

     <style type="text/css">
        .%element_class%:hover { background-color: %section_color% !important; }
        .%element_class%:hover .section_name { color: #fff !important; }
      </style>

    <div class="item %element_class%">
      <a href="%url%" class="rm_article_gallery_view_more">
        <div class="section_name" style="color: %section_color%;">%section%</div>
        %title%
        <span class="go">&gt;</span>
      </a>
</div>

SLOTLET;

} 
else { // NO SE SETEO QUE USE EL COLOR
    $article_tpl_i = <<<SLOTLET

        <div class="item %element_class%">
          <a href="%url%" class="rm_article_gallery_view_more" target="%target%">
            <div class="section_name" style="color: %section_color%;">%section%</div>
            %title%
            <span class="go">&gt;</span>
          </a>
        </div>

SLOTLET;
}

    $subarticles_tpl = <<<SLOTLET
<div>
  %content%
</div>
SLOTLET;
    for ($i = 0; $i <  $count; $i++)
    {
      $article_tpl = '';

      for($j = 0; ($j < $visibles) && ($i < $count); $j++)
      {
        $article_tpl .= strtr($article_tpl_i, array(
          '%element_class%' => 'article_gallery_element_'.$i,
          '%title%'         => $articles[$i]->__toString(),
          '%description%'   => $articles[$i]->getDescription(),
          '%url%'           => url_for($articles[$i]->getURLReference()),
					'%target%'             =>  $articles[$i]->getTarget(),
          '%section%'       => ($articles[$i]->getSection()?$articles[$i]->getSection()->getTitle():''),
          '%section_color%' => ($articles[$i]->getSection()?$articles[$i]->getSection()->getColor():'')
        ));

        if(($j + 1) != $visibles)
        {
          $i++;
        }
      }

      $articles_template .= strtr($subarticles_tpl, array('%content%' => $article_tpl));
    }

    return strtr($template, array(
      '%articles%' => $articles_template
    ));
  }

  /**
   * @return ArticleGallery
   */
  public function getArticleGallery($id, $section_name = null)
  {
    $gallery = null;

    if (null != $id)
    {
      $gallery = ArticleGroupPeer::retrieveByPK($id);
    }
    else if (null != $section_name)
    {
      $section = SectionPeer::retrieveByName($section_name);

      if (null !== $section)
      {
        $gallery = $section->getOrInheritArticleGroup();
      }
    }

    return $gallery;
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra un listado de artículos publicados en distintas secciones.';
  }

  public static function getName()
  {
    return 'Listado de artículos sin imágenes';
  }

}