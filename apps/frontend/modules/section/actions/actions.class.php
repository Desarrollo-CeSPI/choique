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
 * section actions.
 *
 * @package    new_cms
 * @subpackage section
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class sectionActions extends sfActions
{
  public function executeTemplateByName()
  {
    $home_section = SectionPeer::retrieveHomeSection();

  	$this->section_name = $this->getRequestParameter('name', $home_section->getName());
  	$this->getRequest()->setParameter('section_name', $this->section_name);
    if ($this->section_name != $home_section->getName())
    {
      $this->section = SectionPeer::retrieveByName($this->section_name);
    }
    else
    {
      $this->section = $home_section;
    }

    if ($this->section && $this->section->getIsPublished())
    {
      $this->template        = $this->section->getTemplateHTML();
      $this->template_object = $this->section->getTemplate();

      if (null !== $this->template_object)
      {
        $main_content = $this->template_object;
      }
      else
      {
        $main_content = $this->section->getArticleForTemplate();
      }

      SlotletManager::setMainContent($main_content);
    }
    else
    {
      sfLoader::loadHelpers(array('I18N'));

      $this->getRequest()->setParameter('query', $this->section_name);
      $this->getRequest()->setParameter('cms_search', true);
      $this->getRequest()->setParameter('referer_msg',
                                        __('La sección solicitada: "%%name%%" actualmente no está disponible. A continuación, los resultados de la búsqueda con esos términos.',
                                           array('%%name%%' => $this->section_name)));
      $this->forward('sfLucene', 'search');
    }
  }

  public function executeAutocomplete()
  {
    sfLoader::loadHelpers(array('I18N'));

    $text = $this->getRequestParameter('section_autocomplete_query');
    if (is_null($text))
      $text = $this->getRequestParameter('section_autocomplete_query_bis');

    $query = '%'.$text.'%';

    $c = new Criteria();
    $c->setIgnoreCase(true);
    $c->addAscendingOrderByColumn(SectionPeer::TITLE);
    $c->add(SectionPeer::TITLE, $query, Criteria::LIKE);
    $c->add(SectionPeer::IS_PUBLISHED, true);
    $c->setLimit(12);

    $txt  = '<ul style="font-size: 9px">';
    $txt .= '<li id="">' . __('Cualquiera') . '</li>';
    foreach (SectionPeer::doSelect($c) as $section)
      $txt .= '<li id="' . $section->getId() . '">' . $section->getTreeToString() . '</li>';
    $txt .= '</ul>';

    $this->renderText($txt);

    return sfView::NONE;
  }

  public function executeLayout()
  {
    $this->getResponse()->setTitle('Previsualización de distribución');
    $this->getResponse()->addJavascript('layout_preview.js', 'last');

    $layout = new Layout();

    $this->section = $this->getRequestParameter('name');
    $temporary_layout = TemporaryLayoutPeer::retrieve($this->getRequestParameter('layout'));

    if (null === $temporary_layout)
    {
      return $this->renderText('<html><body>La previsualizaci&oacute;n expir&oacute;. Por favor, vuelva a generarla desde el editor de distribuciones. Esta ventana se cerrar&aacute; autom&aacute;ticamente en 5 segundos...<script type="text/javascript">setTimeout("window.close();", 5000);</script></body></html>');
    }

    $configuration = (array) $layout->decode($temporary_layout->getLayout());
    $layout->setArticleLayout($configuration);

    LayoutPeer::setActive($layout);

    $this->setTemplate('templateByName');

    return $this->executeTemplateByName();
  }

  public function executeStylesheet()
  {
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);
    $this->getResponse()->setContentType('text/css');

    $this->name    = $this->getRequestParameter('name');
    $this->section = SectionPeer::retrieveByName($this->name);

    if (null === $this->section)
    {
      $this->color = CmsConfiguration::get('section_default_color');
      $this->rgba =  CmsConfiguration::get('section_default_rgbacolor');
    }
    else
    {
      $this->color = $this->section->getColor();
      $this->rgba  = $this->section->getRGBAColor();
    }
  }

}