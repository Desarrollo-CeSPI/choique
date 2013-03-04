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
 * template actions.
 *
 * @package    cms
 * @subpackage template
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class templateActions extends sfActions
{
  protected function getTemplateOrCreate($id = 'id')
  {
    if (is_null($this->getRequestParameter($id)))
    {
      $template = new Template();
    }
    else
    {
      $template = TemplatePeer::retrieveByPK($this->getRequestParameter($id));
      if (is_null($template))
      {
        $template = new Template();
      }
    }

    return $template;
  }

  public function executePreview()
  {
    $this->setLayout('layout');

    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_PREVIEW);
    }

    $template = $this->getTemplateOrCreate('template[id]');
    $params = $this->getRequestParameter('template');

    unset($params['id']);

    $news_spaces = array();
    foreach ($params as $row_number => $row_contents)
    {
      $row = array();
      foreach ($row_contents as $cell_number => $cell_content)
      {
        $row[intval($cell_content['order'])] = $template->createNewsSpace(
          $row_number,
          $cell_content['order'],
          $cell_content['type'],
          $cell_content['article_id']
        );
      }

      if (!empty($row))
      {
        $news_spaces[] = $row;
      }
    }

    $this->news_spaces = $news_spaces;
  }
}