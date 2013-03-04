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
class ArticleWithMultimediaGallerySlotlet implements ISlotlet
{
  protected function getOptionsForGalleryId($selected_value = null)
  {
    return objects_for_select(ArticleGroupPeer::retrievePublished(), 'getId', 'getName', $selected_value, array('include_custom' => 'Contextual (Obtener del contenido principal)'));
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
      'title'            => __('Título'),
      'article_group_id' => __('Galería de artículos'),
      'class'            => __('Clase CSS')
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
        
    return $form;
  }

  public function getDefaultOptions()
  {
    return array(
      'class'            => 'rm_slotlet_article_gallery',
      'title'            => __('Galería de artículos'),
      'article_group_id' => ''
    );
  }

  public function getJavascripts()
  {
    return array('slotlets/article_multimedia_gallery.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/article_multimedia_gallery.css');
  }

  public function render($options = array())
  {
    $class            = $options['class'];
    $title            = preg_replace('/ /','_',$this->getName());
    $article_group_id = $options['article_group_id'];
    $article_group = $this->getArticleGallery($article_group_id);

    if (null == $article_group)
    {
      $id  = time() % 50 + 1;
      $url = '#';

      $template = <<<SLOTLET
<div class="slotlet article_multimedia_gallery %class%">
  <div class="image_thumb">
    <ul>
      <li>%content%</li>
    </ul>
  </div>
</div>
SLOTLET;

      return strtr($template, array(
        '%content%' => __('Sin contenidos para mostrar'),
        '%class%'   => $options['class']
      ));
    }

    

    $template = <<<SLOTLET
<div id="%id%" class="slotlet article_multimedia_gallery %class%">
  <div class="image_thumb">
    <ul class="articles_list">
      %articles%
    </ul>
  </div>
  <div class="articles_number_list_container">
    <ul class="articles_number_list">
      %articles_number_list%
    </ul>
  </div>
  <div class="main_image">
    <a href="%url%" ><img src="%image%" alt="%image_alt%" /></a>
    <div class="desc">
      <div class="block" style="background-color: %section_color%; background-color: %section_rgba_color%; *background-color: %section_color%;">
        <a class="rm_article_gallery_view_more" href="%url%">%title%</a>
        <p>%description%</p>
      </div>
    </div>
  </div>
  <script>
    var %id%_index=0;
  </script>
</div>
SLOTLET;

    $id =  $article_group->getId();
    $galery_id = 'slotlet_article_gallery_'.sfInflector::underscore($title).'_'.$id;
    $template = strtr($template, array(
      //'%title%'   => $title,
      '%id%'      => $galery_id,
      '%class%'   => $class
    ));

    $article_tpl = '';
    $criteria = new Criteria();
    $criteria->setLimit($article_group->getVisibleItems());
    $articles = $article_group->getArticlesByPriority($criteria);
    $count = count($articles);
    $articles_num_list_tpl = '';

    for ($i = 0; $i <  $count; $i++)
    {
      $article_tpl_i = <<<SLOTLET
<li class="%element_class%">
  <a href="%image%" style="display:none;" class="image_name"></a>
  <div class="block" style="background-color: %section_color%; background-color: %section_rgba_color%; *background-color: %section_color%;">
    <a href="%url%" class="rm_article_gallery_view_more">%title%</a>
    <p>%description%</p>
  </div>
</li>
SLOTLET;

      $articles_num_list_tpl_i = <<<SLOTLET
<li class="articles_number_list_element articles_number_list_element_%number% %active%" onclick="selectTabByNumber('%galery_id%',%number%);" style="background-color: %section_color%;">
  %number%
</li>
SLOTLET;
      
      $article_tpl .= strtr($article_tpl_i, array(
        '%element_class%' => 'article_gallery_element_'.$i,
        '%title%'         => $articles[$i]->getTitle(),
        '%image%'         => ($articles[$i]->getMultimediaId())? $articles[$i]->getMultimedia()->asRssFeedEnclosure()->getUrl(): 'no-image' ,
        '%image_alt%'     => ($articles[$i]->getMultimedia()?$articles[$i]->getMultimedia()->getDescription():'no-image'),
        '%description%'   => $articles[$i]->getHeading(),
        '%url%'           => url_for($articles[$i]->getURLReference()),
        '%section_color%' => $this->getSectionColor($articles[$i]),
        '%section_rgba_color%' => $this->getSectionColor($articles[$i], true),
        '%active%'        => $i == 0 ? 'active' : '',
      ));

      $articles_num_list_tpl .= strtr($articles_num_list_tpl_i, array(
                                    '%number%' => ($i + 1), 
                                    '%section_color%' => $this->getSectionColor($articles[$i]), 
                                    '%active%' => $i == 0 ? 'active' : '',
                                    '%galery_id%'     => $galery_id
      ));
    }

    if(count($articles))
    {
      $template = strtr($template, array(
        '%url%'           => url_for($articles[0]->getURLReference()),
        '%image%'         => ($articles[0]->getMultimediaId())? $articles[0]->getMultimedia()->asRssFeedEnclosure()->getUrl(): 'no-image',
        '%image_alt%'     => ($articles[0]->getMultimedia()?$articles[0]->getMultimedia()->getDescription():'no-image'),
        '%section_color%' => $this->getSectionColor($articles[0]),
        '%section_rgba_color%' => $this->getSectionColor($articles[0], true),
        '%title%'         => $articles[0]->getTitle(),
        '%description%'   => $articles[0]->getHeading()
      ));
    }

    return strtr($template, array(
      '%articles%'             => $article_tpl,
      '%articles_number_list%' => $articles_num_list_tpl
    ));
  }

  public function getSectionColor($article, $rgba = false)
  {
    if ($section = $article->getSection())
    {
      return $rgba ? $section->getRGBAColor() : $section->getColor();
    }

    return '';
  }

  /**
   * @return ArticleGallery
   */
  public function getArticleGallery($id, $main_content = null)
  {
    $gallery = null;

    if (null !== $id)
    {
      $gallery = ArticleGroupPeer::retrieveByPK($id);
    }

    /*

    if (null === $gallery && null !== $main_content)
    {
      try
      {
        $gallery = $main_content->getGallery();
      }
      catch (Exception $e)
      {
        $gallery = null;
      }
    }
     * 
     */

    return $gallery;
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra una galería de artículos publicados en distintas secciones.';
  }

  public static function getName()
  {
    return 'Galeria de articulos con imagen';
  }

}
