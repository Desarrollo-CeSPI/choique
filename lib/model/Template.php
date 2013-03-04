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
 * Subclass for representing a row from the 'template' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Template extends BaseTemplate
{
	/**
	 *	Create a new instance of NewsSpace with the information
	 *	provided as parameter, and return it. Make it belong to
	 *	this Template.
	 *
	 *	@return NewsSpace
	 */
	public function createNewsSpace($row, $col, $type, $articleId, $width = null)
	{
		$newsSpace = new NewsSpace();
		$newsSpace->setTemplateId($this->getId());
		$newsSpace->setRowNumber($row);
		$newsSpace->setColumnNumber($col);
		$newsSpace->setType($type);
		$newsSpace->setArticleId($articleId);
		$newsSpace->setWidth($width);
		
		return $newsSpace;
	}
	
	/**
	 *	Retrieve all the NewsSpaces associated to this Template,
	 *	and return them in an associative array of arrays
	 *	using row numbers as keys.
	 *
	 *	@return Array
	 */
	public function getNewsSpacesGroupedByRow()
	{
		$array = array();
		
		$c = new Criteria();
		$c->add(NewsSpacePeer::TEMPLATE_ID, $this->getId());
		$c->addAscendingOrderByColumn(NewsSpacePeer::ROW_NUMBER);
		foreach (NewsSpacePeer::doSelect($c) as $ns)
    {
			if (!array_key_exists($ns->getRowNumber(), $array))
      {
				$array[$ns->getRowNumber()] = array();
      }

			$array[$ns->getRowNumber()][$ns->getColumnNumber()] = $ns;
		}
		
		return $array;
	}
	
	/**
	 *	Return the HTML Representation for this Template.
	 *
	 *	@return string
	 */	
	public function getHTML()
	{
    sfLoader::loadHelpers(array('I18N', 'Url', 'Tag'));

		$html = '<div id="breaking-line">&nbsp;</div>';
    
    if(CmsConfiguration::get('check_show_action_toolbar_in_sections', false))
    {
      $with_navigation = CmsConfiguration::getUseNavigationInSections();

      $html .= $this->getActionToolbar($with_navigation);
     }
    
    
		foreach ($this->getNewsSpacesGroupedByRow() as $row)
    {
			$html .= "\n<div class=\"line\">\n\t<div class=\"front-content\">\n";
      $default_width = 100 / sizeof($row);
      $class = 'first';
			foreach ($row as $ns)
      {
        $ns_width = $ns->getWidth() ? $ns->getWidth() : $default_width;

				$html .= sprintf("\n\t\t\t<div style=\"width: %d%%\" class=\"template-container %s\">%s</div>", $ns_width, $class, $ns->getDisplayableArticle(true,100));// $ns_width));

        $class = '';
      }
			$html .= "\n\t</div>\n</div>";
		}
		
    return $html;
  }

  public function getArticles()
  {
    $c = new Criteria();
    $c->add(NewsSpacePeer::TEMPLATE_ID, $this->getId());
    $c->addJoin(NewsSpacePeer::ARTICLE_ID, ArticlePeer::ID);

    return ArticlePeer::doSelect($c);
  }

  public function deleteRelatedNewsSpaces()
  {
    if ($this->isNew())
    {
      return false;
    }
    else
    {
      $criteria = new Criteria();
      $criteria->add(NewsSpacePeer::TEMPLATE_ID, $this->getId());
      
      return NewsSpacePeer::doDelete($criteria);
    }
  }

  function __toString()
  {
    return $this->getName();
  }

  public function canDelete()
  {
    if ( !$this->canEdit() ) return false;
    if ( !(($context= sfContext::getInstance()) &&
          (
            $context->getUser()->hasCredential('reporter_admin')
            && $context->getUser()->getGuardUser() !== null && $context->getUser()->getGuardUser()->getId() == $this->getCreatedBy()
          ) ) ) return false;
    $criteria = new Criteria();
    $criteria->add(SectionPeer::TEMPLATE_ID,$this->getId());
    return SectionPeer::doCount($criteria) == 0;
  }


  public function canEdit()
  {
    return
        ($context= sfContext::getInstance()) &&
          (
          $context->getUser()->isSuperAdmin()
          ||
          $context->getUser()->hasCredential(array('designer_admin','reporter_admin'), false)
          ||
          (
            $context->getUser()->hasCredential('reporter_admin')
            && $context->getUser()->getGuardUser() !== null && $context->getUser()->getGuardUser()->getId() == $this->getCreatedBy()
          ));
  }


  public function setName($v)
  {
    parent::setName($v);

    $this->setPublicName($v);
  }

  /**
   * Create and return a dynamic Gallery object made up of the Multimedia
   * objects related to every Article in this Template. The resulting
   * Gallery is not saved.
   * 
   * @return Gallery
   */
  public function getGallery()
  {
    $gallery = new Gallery();

    foreach ($this->getArticles() as $article)
    {
      $article->buildGallery($gallery);
    }

    return $gallery;
  }

  public function getPublicNameAsRssUrl()
  {
    sfLoader::loadHelpers(array('Url','Tag'));
    return choiqueUtil::generateUrl('frontend', 'rss/list/'.urlencode($this->getPublicName()));
  }
  
  
  /**
   *  Return a snippet which is the action toolbar for a Section
   *
   *  @return string
   */
  public function getActionToolbar($with_navigation = false)
  {
    sfLoader::loadHelpers(array('UJS', 'Asset', 'Url', 'Tag', 'I18N', 'Lightview', 'Javascript'));

    $separator = '<span class="separator">|</span>';

    if (CmsConfiguration::get('check_use_social_share_toolbar_in_section', false))
    {
      try
      {
        $social_tool = SocialTools::get(CmsConfiguration::get('custom_social_sharing_tool_in_section'));

        $social_tool->register(sfContext::getInstance()->getResponse());
        
        $social = $social_tool->render().$separator;
      }
      catch (RuntimeException $e)
      {
        $social = '';
      }
    }
    else
    {
      $social = '';
    }

    $toolbar = <<<TOOLBAR
<div id="break-line">
  <div id="top-article-actions" class="article-actions">
    %social%
    <div class="cq-article-actions">
      %nav_back%
      %nav_forward%
      %print%
      %send_by_email%
      %enlarge_text%
      %shrink_text%
      <noscript>%noscript%</noscript>
    </div>
  </div>
</div>
TOOLBAR;
    
    return strtr($toolbar, array(
      '%nav_back%'      => $with_navigation ? UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('navigation_back', 'gif'), array('alt' => __('Volver'), 'title' => __('Volver'))), 'history.back()').UJS_write(" ".$separator) : '',
      '%nav_forward%'   => $with_navigation ? UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('navigation_forward', 'gif'), array('alt' => __('Adelante'), 'title' => __('Adelante'))), 'history.go(1)').UJS_write(" ".$separator) : '',
      '%print%'         => '',//UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('print', 'gif'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))), 'popup_window("'.url_for('article/printPreview?id='.$this->getId()).'","print_window")').UJS_write(" ".$separator),
      '%send_by_email%' => '',//UJS_lightview_ajax(url_for('article/sendByEmail?id='.$this->getId()), image_tag(choiqueFlavors::getImagePath('article_contact', 'gif'), array('alt' => __('Enviar por mail'), 'title' => __('Enviar por mail'))), __('Envio de email'), __('Enviar por email'), array('fullscreen' => 'false', 'height' => 150, 'width'=>200)).UJS_write(" ".$separator),
      '%enlarge_text%'  => UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('zoom_plus', 'gif'), array('alt' => __('aumentar'), 'title' => __('aumentar'))),'enlargeText("full-html", 5)').UJS_write(" ".$separator),
      '%shrink_text%'   => UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('zoom_minus', 'gif'), array('alt' => __('disminuir'), 'title' => __('disminuir'))),'enlargeText("full-html", -5)').UJS_write(" ".$separator),
      '%social%'        => $social,
      '%noscript%'      => '',//link_to(image_tag(choiqueFlavors::getImagePath('print', 'gif'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))), 'article/printPreview?id='.$this->getId())
    ));
  }

  public function getAuthor()
  {
    $author = sfGuardUserPeer::retrieveByPK($this->getCreatedBy());

    return ($author) ? $author->getName() : '';
  }

  public function getAuthorUpdated()
  {
    $author = sfGuardUserPeer::retrieveByPK($this->getUpdatedBy());

    return ($author) ? $author->getName() : '';
  }

}