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
 * Subclass for representing a row from the 'rss_channel' table.
 *
 *
 *
 * @package lib.model
 */
class RssChannel extends BaseRssChannel implements SlotletInterface
{
  public function getCreatedByName()
  {
    return $this->getCreatedByAsGuardUser()->getName();
  }

  public function getCreatedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getCreatedBy());
  } 

  public function getUpdatedByName()
  {
    if (null !== $this->getUpdatedBy())
    {
      return sfGuardUserPeer::retrieveByPK($this->getUpdatedBy())->getName();
    }
    else
    {
      return '-';
    }
  }

  public function __toString()
  {
    return $this->getTitle();
  }

  public static function getSlotletMethods()
	{
		return array("getSlotlet");
	}

  public static function getSlotletName()
  {
    sfLoader::loadHelpers(array('I18N'));

    return __('Feeds');
  }

  public function getReader()
  {
    return choiqueFeedPeer::createFromWeb($this->getLink());
  }

  /**
   *  Returns a piece of HTML code holding the representation of the
   *  RSS feed. The elements to include will be onbtained through the option
   *  parameter 'rss_feeds'.
   *  If 'summary' is set in options, then the rss summary will be included
   *  The resulting HTML code should look like this:
   *
   *    <code>
   *        <div id="feeds">
   *            <div class="title">Rss Channel Title</div>
   *                <div class="content">
   *                    <div class="first">feed title feed link</div>
   *                </div>
   *            </div>
   *        </div>
   *    </code>
   *
   *  @param $options Array The options passed to the Slotlet.
   *
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
    $section = sfContext::getInstance()->getRequest()->getParameterHolder()->get("section_name", "NONE");

  	sfLoader::loadHelpers(array('I18N', 'Tag', 'Url'));

    $container_slotlet = ContainerSlotletPeer::retrieveByPK($options['container_slotlet_id']);
    $rss_channel       = RssChannelPeer::retrieveByPK($options['rss_channel_id']);

    $str = '';
    if (!is_null($rss_channel))
    {
      try
      {
        $reader = $this->getReader();
        $items  = $reader->getItems();

        $str .= ' <div id="slotlet_feed_'.sfInflector::underscore($rss_channel->getTitle()).'_'.$rss_channel->getId().'" class="slotlet_feed">';
        $str .= '<div class="title">';
        $str .=  $rss_channel->getTitle();
        $str .= '</div>';
        $str .= '<div class="content">';

        $count = min(count($items), $container_slotlet->getVisibleRss());
        for($i=0;$i < $count;$i++)
        {
          $str.= '<div class="content-child">';
          $str.= link_to($items[$i]->getTitle(), $items[$i]->getLink(), array('popup' => true));
          $str.= '</div>';
        }
        $str .= '</div><div class="footer"></div>';
        $str .= link_to(__('Ver mas'), '@rss_view_more?nombre='.sfInflector::underscore($rss_channel->getTitle()).'&id='. $rss_channel->getId().'&section='.$section,array('class'=>'feed_view_more'));
        $str .= '</div>';
      }
      catch (Exception $e)
      {
        $str = __('No hay contenidos para mostrar');
      }
    }

    return $str;
  }

  public static function getNullHTMLRepresentation($description)
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($description) ? __('Referencia a canal RSS inválida') : $description);
  }

  public function getHTMLRepresentation($count = 5, $show_more = true)
  {
    $section = sfContext::getInstance()->getRequest()->getParameterHolder()->get("section_name", "NONE");

  	sfLoader::loadHelpers(array('I18N','Url'));
  	$str = '';
  	try
    {
      $reader = $this->getReader();
      $items  = $reader->getItems();

      $str .= ' <div id="article_feed_'.sfInflector::underscore($this->getTitle()).'_'.$this->getId().'" class="article__feed">';
      $str .= '<div class="content">';

      $count = min(count($items), $count);
      for ($i=0; $i < $count; $i++)
      {
        $str.= '<div class="content-child">'.link_to($items[$i]->getTitle(), $items[$i]->getLink(), array('popup' => true)).'</div>';
      }
      $str .= '</div><div class="footer"></div>';
      if ($show_more)
        $str .= link_to(__('Ver mas'), '@rss_view_more?nombre='.sfInflector::underscore($this->getTitle()).'&id='. $this->getId().'&section='.$section, array('class'=>'feed_view_more'));
      $str .= '</div>';
      return $str;
    }
    catch (Exception $e)
    {
      $str = __('No hay contenidos para mostrar');
    }

    return $str;
  }

  public function canDelete()
  {
    if (!$this->canEdit()) return false;
    $criteria = new Criteria();
    $criteria->add(ArticleRssChannelPeer::RSS_CHANNEL_ID,$this->getId());
    
    return ArticleRssChannelPeer::doCount($criteria) == 0;
  }

  public function canEdit()
  {
    return ($context= sfContext::getInstance()) &&
          ((
          $context->getUser()->isSuperAdmin()
          ||
          $context->getUser()->hasCredential(array('designer', 'reporter_admin','reporter'),false) &&
          ($this->getCreatedByAsGuardUser()) && ($this->getCreatedByAsGuardUser()->getUsername() == $context->getUser()->getUsername())
          ) ||
          $context->getUser()->hasCredential('designer_admin')
          );
  }

  public function delete($con = null)
  {
    if ($this->canDelete())
    {
      return parent::delete($con);
    }
    else
    {
      return false;
    }
  }
}