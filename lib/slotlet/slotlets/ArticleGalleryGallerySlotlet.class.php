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
 * @author Juan Pablo Perez <jpablop@cespi.unlp.edu.ar>
 */
class ArticleGalleryGallerySlotlet implements ISlotlet
{
  protected function getOptionsForGalleryId($selected_value = null)
  {
    return objects_for_select(ArticleGroupPeer::retrievePublished(),
      'getId',
      'getName',
      $selected_value
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
      'article_groups'               => __('Galerías de artículos'),
      'class'                        => __('Clase CSS'),
      'visibles'                     => __('Cant. Max. Elementos por Galeria'),
      'use_background_section_color' => __('Usar color de sección  como fondo'),
    );

    $form = strtr($row, array(
      '%id%'    => 'article_groups',
      '%label%' => $labels['article_groups'],
      '%field%' => textarea_tag('article_groups',  $values['article_groups'] , array('class' => 'slotlet_option'))
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
      'article_groups' => '',
      'visibles'         => 4,
      'section_name'     => sfContext::getInstance()->getRequest()->getParameter('section_name'),
      'use_background_section_color' => false
    );
  }

  public function getJavascripts()
  {
    return array( 'slotlets/article_group_gallery_list.js');
  }

  public function getStylesheets()
  {
    return array('frontend/slotlet/article_group_gallery_list.css');
  }

  public function render($options = array())
  {
    $class          = $options['class'];
    $title          = $options['title'];
		$visibles       = intval($options['visibles']);
    $article_groups = $this->getArticleGalleries($options['article_groups']);

		$id_general  = time() % 50 + 1;

    if (count($article_groups) == 0)
    {
      $url = '#';

      $template = <<<SLOTLET
<div class="slotlet article_group_gallery %class%" id="%id%" >
  <div class="article_group_gallery_container">
    <h2 class="article_group_gallery_title">%title%</h2>
      <div class="items">
          <div class="item">
            %content%
          </div>
      </div>
  </div>
</div>

SLOTLET;

      return strtr($template, array(
        '%content%' => __('Sin contenidos para mostrar'),
        '%class%'   => $options['class'],

      ));
    }

    $template = <<<SLOTLET
<div class="slotlet article_group_gallery %class%">
  <div class="article_group_gallery_container">
    <h2 class="article_group_gallery_title">%title%</h2>

      <div class="galleries">
					<div class="article_group_gallery_filters">
						%galleries_labels%
					</div>
      </div>

      <div class="content">
      	<ul>
      		%articles%
      	</ul>
      </div>

  </div>
</div>
SLOTLET;


		$article_gallery_label = <<<SLOTLET
			 <a href='#' id='%id%'>%label%</a>
SLOTLET;


		if($options['use_background_section_color'])
		{

			$article_gallery_article = <<<SLOTLET

     <style type="text/css">
        .%element_class%:hover { background-color: %section_color% !important; }
        .%element_class%:hover .section_name { color: #fff !important; }
      </style>

			<li class='%article_gallery_id%'>
        <div class="item %element_class%">
          <a href="%url%" class="rm_article_gallery_view_more" target="%target%">
            <div class="section_name" style="color: %section_color%;">%section%</div>
            %title%
            <span class="go">&gt;</span>
          </a>
        </div>
			</li>
SLOTLET;

		}
		else { // NO SE SETEO QUE USE EL COLOR
			$article_gallery_article = <<<SLOTLET

			<li class='%article_gallery_id%'>
        <div class="item %element_class%">
          <a href="%url%" class="rm_article_gallery_view_more" target="%target%">
            <div class="section_name" style="color: %section_color%;">%section%</div>
            %title%
            <span class="go">&gt;</span>
          </a>
        </div>
			</li>

SLOTLET;

		}




		$compose_article_groups_labels='';

		$compose_article_groups_articles = '';


		foreach($article_groups as $text => $article_group)
		{

			$id = $article_group->getId();

			$id_elemento = $id_general.'_'.$id;

		  $compose_article_groups_labels .= strtr($article_gallery_label, array(
					'%label%'      => $text,
					'%id%'         => $id_elemento
			));

			$criteria = new Criteria();
			$criteria->setLimit($article_group->getVisibleItems());
	  	$articles = $article_group->getArticlesByPriority($criteria);
			$count = count($articles);

			for ($i = 0; $i <  $count; $i++)
			{
				for($j = 0; ($j < $visibles) && ($i < $count); $j++)
				{
					$compose_article_groups_articles .= strtr($article_gallery_article, array(
						'%article_gallery_id%' => $id_elemento,
						'%element_class%'      => 'article_gallery_element_'.$i,
						'%title%'              => $articles[$i]->__toString(),
						'%description%'        => $articles[$i]->getDescription(),
						'%url%'                => url_for($articles[$i]->getURLReference()),
						'%target%'             =>  $articles[$i]->getTarget(),
						'%section%'            => ($articles[$i]->getSection()?$articles[$i]->getSection()->getTitle():''),
						'%section_color%'      => ($articles[$i]->getSection()?$articles[$i]->getSection()->getColor():'')
					));

					if(($j + 1) != $visibles)
					{
						$i++;
					}
				}
			}
		}

	return strtr($template, array(
      '%galleries_labels%' => $compose_article_groups_labels,
		  '%articles%'         => $compose_article_groups_articles,
		  '%class%'            => $class,
		  '%title%'            => $title
    ));
  }

  /**
   * @return ArticleGallery[]
   */
  public function getArticleGalleries($article_groups)
  {
    $galleries = explode("\n",$article_groups);

		$ret  = array();

		foreach($galleries as $gallery)
		{
			$gal = explode(":",$gallery);
			$text = $gal[0];
			$id = $gal[1];
			if ((null != $id)  && (null != $obj = ArticleGroupPeer::retrieveByPK($id)))
			{
				$ret[$text] = $obj;
			}
    }
    return $ret;
  }

  public static function getDescription()
  {
    return 'Slotlet que muestra un  listado de galerias de articulos y sus  artículos publicados en distintas secciones.';
  }

  public static function getName()
  {
    return 'Listado galerias de artículos sin imágenes';
  }

}