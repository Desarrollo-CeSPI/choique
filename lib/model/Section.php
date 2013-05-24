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
 * Subclass for representing a row from the 'section' table.
 *
 *
 *
 * @package lib.model
 */
class Section extends BaseSection implements SlotletInterface
{
	const HORIZONTAL = 'horizontal';
	const VERTICAL 	 = 'vertical';
	const ALL 		   = 'all';
	const HOME		   = 'home';

  public static $MAX_DEPTH = 5;

  function __toString()
  {
    return $this->getPaddedToString();
    //return $this->getTitle();
  }

  function getPaddedToString()
  {
  	return str_repeat("&nbsp;&nbsp;",$this->getDepth()).$this->getTitle();
  }

  public function getDepth()
  {
    if ($this->isRoot())
    {
      return 0;
    }
    else
    {
      $parent_section = SectionPeer::retrieveByPk($this->getSectionId());

      return $parent_section->getDepth() + 1;
    }
  }

  public function getTreeToString($include_last = false)
  {
    $ancestors = $this->getAncestors($include_last);

    $str = '<span style="font-weight: bold">';

    if ($include_last || count($ancestors) > 0)
    {
      $str .= array_shift($ancestors)->getTitle();
    }
    else
    {
      $str .= $this->getTitle();
    }

    $str .= '</span>';

    if (count($ancestors) > 0)
    {
      $str .= ' (';
      foreach (array_reverse($ancestors) as $ancestor)
      {
        $str .= $ancestor->getTitle() . ' &gt; ';
      }
      $str = substr($str, 0, strlen($str) - 6);
      $str .= ')';
    }

    return $str;
  }

  public function hasMultimedia()
  {
    return (!is_null($this->getMultimediaId()));
  }

  public static function getSections($type)
  {
    $c = new Criteria();
    switch ($type)
    {
      case self::HORIZONTAL:
        $section = SectionPeer::retrieveHorizontalSection();
        break;

      case self::HOME:
        $section = SectionPeer::retrieveHomeSection();
        break;

      case self::ALL:
        $sections = SectionPeer::doSelect($c);
        $s = array();
        foreach ($sections as $sec) {
          switch ($sec->getName()) {
            case self::HORIZONTAL:
              break;
            default:
              $s[] = $sec;
              break;
          }
        }
        return $s;
        break;
    }

    if (!isset($section))
    {
      $section = SectionPeer::doSelectOne($c);
    }

    if (!$section)
    {
      return array();
    }

    $id = $section->getId();
    $c2 = new Criteria();
    $c2->add(SectionPeer::SECTION_ID, $id);
    $c2->add(SectionPeer::IS_PUBLISHED,true);
    $c2->addDescendingOrderByColumn(SectionPeer::PRIORITY);

    return SectionPeer::doSelect($c2);
  }

  public function getRoute()
  {
    return sprintf('@template_by_name?name=%s', $this->getName());
  }

  public function getHTMLRepresentation($selected, $htmlOptions = array(), $extra_content = null)
  {
    sfLoader::loadHelpers(array('Url', 'Tag', 'I18N'));

    $route = $this->getRoute();

    if (!array_key_exists("class", $htmlOptions))
    {
      $htmlOptions["class"] = '';
    }

    $htmlOptions["class"] .= ($selected == $this->getName() || $this->isAncestorOf($selected)) ? " selected" : "";
    $htmlOptions['title'] = $this->getDescription() ? $this->getDescription() : $this->getTitle();

    $use_color_block = true;

    return link_to($extra_content.__($this->getTitle()), $route, $htmlOptions);
  }

  public function getTemplateHTML()
  {
    $template = $this->getTemplate();

    //si la seccion no tiene una portada asociada, se busca el articulo para mostrar
    if (!$template)
    {
      $article = $this->getArticleForTemplate();
      //si tampoco tiene articulos, se muestran las subsecciones
      if (!$article)
      {
        $children = "<div class=\"default-template\">";
        foreach ($this->getPublishedChildren() as $child)
        {
          $children .= "<div class=\"default-template-child\">" . $child->getHTMLRepresentation('') . "</div>";
        }
        $children .= "</div>";

        return $children;
      }
      else
      {
        $with_navigation = CmsConfiguration::getUseNavigationInArticles();

        return $article->getActionToolbar(CmsConfiguration::getUseNavigationInArticles()).$article->getFullHTMLRepresentation().$article->getFooter();
      }
    }
    else
    {
      return $template->getHTML();
    }
  }

  public function getArticleForTemplate()
  {
    if ($this->hasArticle())
    {
      return $this->getArticle();
    }
    else
    {
      $c = new Criteria();
      $c->add(ArticlePeer::SECTION_ID, $this->getId());
      $c->add(ArticlePeer::IS_PUBLISHED, true);
      $c->addDescendingOrderByColumn(ArticlePeer::PUBLISHED_AT);

      return ArticlePeer::doSelectOne($c);
    }
  }

  public function getPublishedArticles()
  {
    $c = new Criteria();
    $c->add(ArticlePeer::SECTION_ID, $this->getId());
    $c->add(ArticlePeer::IS_PUBLISHED, true);

    return ArticlePeer::doSelect($c);
  }

  public function isEditable()
  {
    return ($this->isNew() || $this->getId() != SectionPeer::retrieveHorizontalSection()->getId());
  }

  public function setName($name)
  {
    sfLoader::loadHelpers(array('CmsEscaping'));

    if ($this->isEditable())
    {
      return parent::setName(escape_string($name));
    }
  }

  protected function canBeModifiedWhenPublished()
  {

    return
        ($context= sfContext::getInstance()) &&
          ((
          $context->getUser()->hasCredential(array('designer','reporter'),false) &&
          !$this->getIsPublished()
          )
          ||
          $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
          );
  }

  public function canEdit()
  {
    return $this->canBeModifiedWhenPublished() &&
        ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this);
  }

  public function canDelete()
  {
    if ( !$this->canEdit()) return false;
    return ($this->getId() != SectionPeer::retrieveHomeSection()->getId() && $this->getId() != SectionPeer::retrieveHorizontalSection()->getId());
  }

  public function canPublish()
  {
    return $this->isUnpublish() && ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this);
  }

  public function canUnpublish()
  {
    return $this->isPublish() && ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this);
  }


  public function delete($con = null)
  {
    if ($this->canDelete())
    {
      return parent::delete($con);
    }
  }

  /**
   *  Return the associated Multimedia for the section whose name is $name.
   *  If that section doesn't have an associated Multimedia element, try
   *  to get it from any of its parent sections.
   *
   *  @param $name String The name of the Section whose Multimedia is to be retrieved.
   *  @return String The Banner representation of the Multimedia.
   */
  public static function getBannerByName($name)
  {
    $section = SectionPeer::retrieveByName($name);
    if (!$section)
    {
      $section = SectionPeer::retrieveHomeSection();
    }

    return $section->getBanner();
  }

  /**
   *  Return a Banner representation of the Multimedia associated either to this section
   *  or any of its ancestors. If no Multimedia is found, $null_text will be returned.
   *
   *  @param $null_text String The text to be returned if no Multimedia is found.
   *  @return String The Banner representation of the Multimedia found or $null_text.
   */
  public function getBanner($null_text = '')
  {
    $multimedia = $this->getMultimedia();
    if (!$multimedia)
    {
      if (!$this->isRoot() && !$this->isHomeSection())
      {
        $multimedia = $this->getParentSection()->getBanner($null_text);
      }
      elseif ($this->isHomeSection())
      {
        $multimedia = $null_text;
      }
      else
      {
        $home_section = SectionPeer::retrieveHomeSection();

        if ($home_section->getId() != $this->getId())
        {
          $multimedia = $home_section->getBanner($null_text);
        }
      }
    }
    else
    {
      $multimedia = $multimedia->getBanner();
    }

    return (empty($multimedia) ? $null_text : $multimedia);
  }

  public function isHomeSection()
  {
    return $this->getId() == SectionPeer::retrieveHomeSection()->getId();
  }

  public function hasTemplate()
  {
    return (!is_null($this->getTemplateId()));
  }

  public function hasArticle()
  {
    return (!is_null($this->getArticleId()));
  }

  /**
   *  Return a string holding the path to the section whose name is $name.
   *
   *  @param  $name       string The name of the last section to appear in the path.
   *  @param  $force_home bool   If true, HOME section will be forced into the path.
   *
   *  @return string The HTML of the path to display.
   */
  public static function getPath($name, $force_home = true)
  {
    $section = SectionPeer::retrieveByName($name);
    $txt = "<div>";
    $home_section = SectionPeer::retrieveHomeSection();
    if ($section)
    {
      $ancestors = array_reverse($section->getAncestors(true));

      if ($force_home && !in_array($home_section, $ancestors))
      {
        $txt .= link_to($home_section->getTitle(), '@homepage', array('title' => $home_section->getTitle())) . " &gt; ";
      }

      foreach ($ancestors as $section)
      {
        if (0 != strcasecmp($section->getName(), self::HORIZONTAL))
        {
          $txt .= link_to(__($section->getTitle()), sprintf('@template_by_name?name=%s', $section->getName()), array('title' => $section->getTitle())) . " &gt; ";
        }
      }
      //Remove trailing ' > '
			$txt = substr($txt, 0, strlen($txt) - 6);
    }
    else
    {
      $txt .= link_to($home_section->getTitle(), '@homepage');
    }
    $txt .= "</div>";

    return $txt;
  }

  public function getFirstLevelSection()
  {
    if ($this->isRoot())
    {
      return $this;
    }

    $ancestors = $this->getAncestors(true);
    if (0 == strcasecmp($ancestors[count($ancestors) - 1]->getName(), self::HORIZONTAL))
    {
      return $ancestors[count($ancestors) - 2];
    }
    else
    {
      return $ancestors[count($ancestors) - 1];
    }
  }

  public function getNthLevelSections($depth)
  {
    $depth    = intval($depth);
    $my_depth = $this->getDepth();

    if ($my_depth == $depth) // Nth-level (n): return children
    {
      return $this->getPublishedChildren();
    }
    else if ($my_depth > $depth) // Greater (> n) level section: ask parent
    {
      return $this->getParentSection()->getNthLevelSections($depth);
    }

    return array();
  }

  public function getSiblings()
  {
    return $this->isRoot() ? array() : $this->getParentSection()->getChildren();
  }

  /**
   *  Answer whether the receiver is an ancestor of the section named $sectionName.
   *
   *  @param $sectionName String The name of the Section to test if the receiver is an ancestor.
   *  @return boolean True if the receiver is an ancestor of the section named $sectionName.
   */
  public function isAncestorOf($sectionName)
  {
    $section = SectionPeer::retrieveByName($sectionName);

    if (!$section)
    {
      return false;
    }

    return (in_array($this->getName(), array_map(create_function('$section', 'return $section->getName();'), $section->getAncestors())));
  }

  public function getChildren($criteria = null)
  {
    if (null === $criteria)
    {
      $c = new Criteria();
    }
    else
    {
      $c = clone $criteria;
    }

    $c->addDescendingOrderByColumn(SectionPeer::PRIORITY);

    return $this->getSectionsRelatedBySectionId($c);
  }

  public function getPublishedChildren($criteria = null)
  {
    if (null !== $criteria)
    {
      $c = $criteria;
    }
    else
    {
      $c = new Criteria();
    }

    $c->add(SectionPeer::IS_PUBLISHED, true);
    $c->addDescendingOrderByColumn(SectionPeer::PRIORITY);

    return $this->getSectionsRelatedBySectionId($c);
  }

  /**
   *  Retrieve all the sections that have the receiver's
   *  parent as their parent.
   *
   *  @return Array of Section
   **/
  public function getBrothers()
  {
    $c = new Criteria();
    $c->add(SectionPeer::SECTION_ID, $this->getSectionId());

    return $sectionsChildren = SectionPeer::doSelect($c);
  }

  /**
   *  Return the sections that are ancestors of the receiver.
   *  The last Section in the tree will be included only if
   *  the parameter $include_last is true.
   *
   *  @param $include_last Boolean Whether to include the last section in the tree or not.
   *  @return Array The ancestor Sections.
   */
  public function getAncestors($include_last = false)
  {
    $ancestors = array();
    $section = $this;
    while(!$section->isRoot())
    {
      $ancestors[] = $section;
      $section = $section->getParentSection();
    }

    if ($include_last)
    {
      $ancestors[] = $section;
    }

    return $ancestors;
  }

  /**
   *  Return the first root section in the ancestors tree of the
   *  section named $sectionName.
   *
   *  @param $sectionName String The name of the section whose first root section will
   *                               be returned.
   *  @return Section The first root section or null if the section named $secionName does not exist.
   */
  public static function getSectionRoot($sectionName)
  {
    $section = SectionPeer::retrieveByName($sectionName);
    if (!$section)
    {
      return null;
    }

    while (!$section->isRoot())
    {
      $section = $section->getParentSection();
    }

    return $section;
  }

  public function hasChildren()
  {
    $c = new Criteria();
    $c->add(SectionPeer::SECTION_ID, $this->getId());

    return (SectionPeer::doCount($c) > 0);
  }

  public function hasPublishedChildren()
  {
    $c = new Criteria();
    $c->add(SectionPeer::SECTION_ID, $this->getId());
    $c->add(SectionPeer::IS_PUBLISHED, true);

    return (SectionPeer::doCount($c) > 0);
  }

  public function isRoot()
  {
    return (!$this->getSectionId());
  }

  public function getParentSection()
  {
    return $this->getSectionRelatedBySectionId();
  }

  public static function getAllMetas()
  {
    $metas = '';
    foreach (SectionPeer::retrievePublished() as $section)
    {
      $metas .= $section->getMetas();
    }

    //remove the extra comma (',') from the end of the metas string, if any word has been added to it.
    if (strlen($metas) > 0)
    {
      $metas = substr($metas, 0, strlen($metas) - 2);
    }

    return $metas;
  }

  public function getMetas()
  {
    //split the title and description of this section into words, and for each of those words
    //if the length of the word is greater than 3 characters, add it to the metas string.
    $metas = "";
    $string = $this->getTitle() . " " . $this->getDescription();
    $sources = array('/á/','/é/','/í/','/ó/','/ú/','/ñ/','/:/','/\'/','/\"/','/\s/');
    $replacements = array('a','e','i','o','u','n',' ',' ',' ',' ');
    $string = preg_replace($sources, $replacements, $string);
    foreach (explode(" ", $string) as $word)
    {
      if (strlen($word) > 4)
      {
        $metas .= "$word, ";
      }
    }

    return $metas;
  }

  public function getSearchResultRepresentation()
  {
    $str  = '<div class="result">';
    $str .= '<div class="result-title">' . link_to($this->getTitle(), '@template_by_name?name='.$this->getName()) . '</div> ';
    $str .= '<div class="result-body">' . $this->getDescription() . '</div>';
    $str .= '</div>';

    return $str;
  }

  public function isIndexable()
  {
    return $this->getIsPublished();
  }

  public function isPublish()
  {
    return $this->getIsPublished();
  }

  public function isUnpublish()
  {
    return !$this->isPublish();
  }

  /**
   * Deprectated: It returns an array of array, the way symfony likes the OR credentials
   *
   * @return unknown
   */
  public function getCredentials()
  {
    return NULL;
  }

  public function getLineage()
  {
  	$ret = array($this);
  	foreach ($this->getChildren() as $child)
    {
      $ret = array_merge($ret, $child->getLineage());
    }

    return $ret;
  }

  public function getAllSectionsTree($user=null)
  {
  	$ret = SectionPeer::getSectionsTree($user);
  	if (!$this->isNew())
    {
  	 	$ret = array_diff($ret, $this->getLineage());
  	}

  	return $ret;
  }

  public function hasCredential($user)
  {
    $credentials = $this->getCredentials();

    return (($user==null) ? true : (is_null($credentials) ? true : $user->hasCredential($credentials)));
  }

  /**
   *  Used in the frontend app, to retrieve all the
   *  published sections in a sorted fashion. This is
   *  useful in the search form of article/showAll.
   **/
	public function getPublishedSectionsTree(&$ids)
	{
    if ($this->getIsPublished())
  	{
      $ids[] = $this->getId();
    }

	  foreach ($this->getChildren() as $child)
    {
	    if ($child->getIsPublished())
	    {
        $child->getPublishedSectionsTree($ids);
      }
    }
	}

	public function getSectionTree(&$ids, $user=null)
	{
	  $ids[] = $this->getId();
	  foreach ($this->getChildren() as $child)
	  {
      if ($child->hasCredential($user))
	    {
        $child->getSectionTree($ids, $user);
      }
    }
	}

  public static function getSlotletName()
  {
    sfLoader::loadHelpers(array('I18N'));

    return __('Menú contextual');
  }

  public static function getSlotletMethods()
  {
    return array('getSlotlet');
  }

  /**
   *  Returns a piece of HTML code holding the representation
   *  of the (Contextual Menu) Slotlet for Section class.
   *  The current section will be obtained through the option
   *  parameter 'section_name'.
   *  The resulting HTML code should look like this:
   *
   *  <code>
   *    <div id="contextual_menu" class="sl_shortcut">
   *      <div id="section_name" class="first-level with-arrow">
   *        <a href="#" onClick="function showOrCollapseSection('section_name'); return false;"><img src="arrow-white.png" /></a>
   *        //Actual representation of a Section
   *        <div class="second-level" id="section_name-children">
   *          <div>
   *            //Actual representation of a sub-Section
   *          </div>
   *          <div>
   *            //Actual representation of another sub-Section
   *          </div>
   *        </div>
   *      </div>
   *      <div id="another_section_name" class="first-level">
   *        //Actual representation of another Section
   *      </div>
   *      // ...
   *    </ul>
   *  </code>
   *
   *  @param $options Array The options passed to the Slotlet.
   *
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
    sfLoader::loadHelpers(array('PJS'));

  	//Mandatory parameters check and initialization
    if (!(array_key_exists('section_name', $options) && isset($options['section_name'])))
    {
      $options['section_name'] = sfContext::getInstance()->getRequest()->getParameter("section_name", self::HOME);
    }

    if (!(array_key_exists('include_ancestor_sections', $options) && isset($options['include_ancestor_sections'])))
    {
      $options['include_ancestor_sections'] = CmsConfiguration::get('check_include_ancestor_sections', false);
    }

    $section = SectionPeer::retrieveByName($options['section_name']);
    if (!($section && $section->getIsPublished()))
    {
      return '';
    }

    $open_section = (!is_null($section->getParentSection())) ? $section->getParentSection()->getName() : $options['section_name'];

    use_pjs(sprintf("pjs/sections?currentSection=%s&openSection=%s&version=%s", $options['section_name'], $open_section, time()));

    $root = $section->getFirstLevelSection();

    $txt = "<div id=\"contextual_menu\" class=\"sl_section\">";
    if (CmsConfiguration::get('check_include_home_section_in_section_slotlet', false))
    {
      // Hard-code home section first
      $home_section = SectionPeer::retrieveHomeSection();
      $txt .= '<div id="'.$home_section->getName().'" class="first-level">'.$home_section->getHTMLRepresentation($options['section_name'], array('id' => $home_section->getName().'-anchor')).'</div>';
      // End hard-coded section
    }

    foreach ($root->getPublishedChildren() as $children)
    {
      $txt .= $children->getSectionsTree($options['section_name']);
    }
    $txt .= "<div class=\"footer\"></div>";
    $txt .= "</div>";

    return $txt;
  }

  private function getChildrensHTMLRepresentation($section_name, $hidden)
  {
    $txt = sprintf("<div class=\"second-level %s\" id=\"%s-children\">",
                    (!$hidden) ? "" : "hidden",
                    $this->getName());
    foreach ($this->getPublishedChildren() as $child)
    {
      $txt .= sprintf("<div class=\"second-level-child\">%s</div>", $child->getHTMLRepresentation($section_name));
    }
    $txt .= "</div>";
    return $txt;
  }

  public function getSectionsTree($section_name)
  {
    sfLoader::loadHelpers(array("Javascript", "I18N"));

    $hasChildren = $this->hasChildren();

    $txt  = sprintf("<div id=\"%s\" class=\"first-level %s\">",
                    $this->getName(),
                    ($hasChildren) ? "with-arrow" : "");

    $is_ancestor = $this->isAncestorOf($section_name);
    if ($hasChildren)
    {
      //do not allow closure of this section if it is the parent of the current section ($sectionName)
      $fn = ($is_ancestor) ? "" : "showOrCollapseSection('" . $this->getName() . "');";
      $arrow = ($this->getName() == $section_name || $is_ancestor) ? "arrow_black.png" : "arrow_white.png";
      $txt .= link_to_function(image_tag('frontend/'.$arrow, array("alt" => __("Mostrar submenú"), 'title' => __('Mostrar submenú'), "id" => $this->getName() . "-arrow")),
                               $fn,
                               array("class" => "arrow"));
    }
    $txt .= $this->getHTMLRepresentation($section_name, array("id" => $this->getName() . "-anchor")) . "</div>";

    if ($hasChildren)
    {
      $txt .= $this->getChildrensHTMLRepresentation($section_name, !$is_ancestor);
      if(!$is_ancestor){
        $txt .= sprintf("<noscript>%s</noscript>", $this->getChildrensHTMLRepresentation($section_name, false));
      }
    }

    return $txt;
  }

  /**
   *  Return all the Article's whose type is NEWS and
   *  which have been assigned a priority on me. Sort them
   *  by that priority.
   *
   *  @param  Boolean $include_ancestor_sections Whether or not to include ancestor sections to retrieve the news.
   *  @param  Boolean $include_children_sections Whether or not to include children sections to retrieve the news.
   *  @param  Integer $max_count The maximum number of News' to return.
   *  @param  Boolean $sort_by_priority If True, sort Articles by priority; otherwise, sort them using `published_at'
   *                  column in descending order.
   *
   *  @return Array The Array of sorted news.
   */
  public function getSortedNews($include_ancestor_sections = false, $include_children_sections = true, $max_count = null, $sort_by_priority = true, $include_institutional = false)
  {
    if (is_null($max_count))
    {
      $max_count = CmsConfiguration::get('max_news', 5);
    }

    //Retrieve all my news, sorted by priority
    $section_ids = array($this->getId());
    $c = ArticlePeer::getSortedNewsCriteriaBase($section_ids, $sort_by_priority, $include_institutional);
    $c->setLimit($max_count * $max_count);
    $articles = ArticlePeer::doSelect($c);
    $articles_array = array_slice(array_unique($articles), 0, $max_count);
    if (count($articles_array) < $max_count)
    {
      $section_ids = array();
      //If desired, include my ancestor sections' news in the result set
      if ($include_ancestor_sections)
      {
        $section_ids = array_merge($section_ids, array_map(create_function('$section', 'return $section->getId();'), $this->getAncestors()));
      }

      //If desired, include my children sections' news in the result set
      if ($include_children_sections)
      {
        $section_ids = array_merge($section_ids, array_map(create_function('$section', 'return $section->getId();'), $this->getChildren()));
      }

      $c = ArticlePeer::getSortedNewsCriteriaBase($section_ids, $sort_by_priority, $include_institutional);
      $c->add(ArticlePeer::ID, array_map(create_function('$article', 'return $article->getId();'), $articles_array), Criteria::NOT_IN);
      $c->setLimit($max_count * $max_count);
      $articles = ArticlePeer::doSelect($c);
      $count_articles_array = count($articles_array);
      for ($i = 0; $i < ($max_count - $count_articles_array) && $i < count($articles); $i++)
      {
        $articles_array[] = $articles[$i];
      }
    }
    return $articles_array;
  }

  public function getArticleSectionsByPriority($only_news = false)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(ArticleSectionPeer::PRIORITY);

    if ($only_news)
    {
      $c->add(ArticlePeer::TYPE, Article::NEWS);
    }

    return $this->getArticleSectionsJoinArticle($c);
  }

  /**
   * Get the layout for this section.
   *
   * If this section has not been assigned a specific Layout, get it
   * recursively from this section's ancestors. If this is a root section,
   * return the default layout.
   *
   * @param  PDO $con Database connection (optional)
   *
   * @return Layout The layout
   */
  public function getLayout($con = null)
  {
    if ($layout = parent::getLayout($con))
    {
      return $layout;
    }
    elseif (!$this->isRoot())
    {
      return $this->getParentSection()->getLayout($con);
    }
    else
    {
      return LayoutPeer::retrieveDefault($con);
    }
  }

  /**
   * Return TRUE if this Section has a color defined for itself
   * (Not taking into account color inheritance).
   *
   * @return boolean
   */
  public function hasOwnColor()
  {
    return ('' != trim($this->getOwnColor()));
  }

  /**
   * Return TRUE if this Section has a color defined for itself or inherited.
   *
   * @return boolean
   */
  public function hasColor()
  {
    return ('' != trim($this->getColor()));
  }

  /**
   * Get the color for this section.
   * This method will inherit the color from a parent section
   * if none has been defined for this one and it has a parent section.
   *
   * @return string The color
   */
  public function getColor()
  {
    if ($this->hasOwnColor())
    {
      return $this->getOwnColor();
    }
    else
    {
      return $this->inheritColor();
    }
  }

  public function getRGBAColor($alpha = 0.75)
  {
    // Clean up this section's color
    $hex_color = preg_replace('/[^0-9A-Fa-f]/', '', $this->getColor());

    if (3 == strlen($hex_color))
    {
      $hex_color = $hex_color[0].$hex_color[0].$hex_color[1].$hex_color[1].$hex_color[2].$hex_color[2];
    }

    $red   = hexdec(substr($hex_color, 0, 2));
    $green = hexdec(substr($hex_color, 2, 2));
    $blue  = hexdec(substr($hex_color, 4, 2));

    return sprintf('rgba(%d, %d, %d, %.2f)', $red, $green, $blue, $alpha);
  }

  /**
   * Return the color of the parent secion, if any.
   *
   * @return string or null
   */
  public function inheritColor()
  {
    if (!$this->isRoot())
    {
      return $this->getParentSection()->getColor();
    }
  }

  public function getOwnColor()
  {
    return parent::getColor();
  }

  public function getAllSectionDocuments()
  {
    if ($this->isRoot())
    {
      return $this->getSectionDocuments();
    }

    return array_merge($this->getSectionDocuments(), $this->getParentSection()->getAllSectionDocuments());
  }

  public function getDocuments($inherit = true)
  {
    if ($inherit)
    {
      $section_documents = $this->getAllSectionDocuments();
    }
    else
    {
      $section_documents = $this->getSectionDocuments();
    }

    return array_unique(array_map(create_function('$sd', 'return $sd->getDocument();'), $section_documents));
  }

  public function getAllSectionLinks()
  {
    if ($this->isRoot())
    {
      return $this->getSectionLinks();
    }

    return array_merge($this->getSectionLinks(), $this->getParentSection()->getAllSectionLinks());
  }

  public function getLinks($inherit = true)
  {
    if ($inherit)
    {
      $section_links = $this->getAllSectionLinks();
    }
    else
    {
      $section_links = $this->getSectionLinks();
    }

    return array_unique(array_map(create_function('$sd', 'return $sd->getLink();'), $section_links));
  }

  public function hasSectionDocuments()
  {
    return (0 < $this->countSectionDocuments());
  }

  public function hasSectionLinks()
  {
    return (0 < $this->countSectionLinks());
  }

  /**
   * Answer whether this Section has an ArticleGroup set.
   *
   * @return bool
   */
  public function hasArticleGroup()
  {
    return $this->getArticleGroupId() !== null;
  }

  public function getOrInheritArticleGroup($con = null)
  {
    if ($this->hasArticleGroup())
    {
      return $this->getArticleGroup($con);
    }

    if (!$this->isRoot())
    {
      return $this->getParentSection()->getOrInheritArticleGroup($con);
    }
  }

}
sfLucenePropelBehavior::getInitializer()->setupModel('Section');
