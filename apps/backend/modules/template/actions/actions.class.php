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
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class templateActions extends autotemplateActions
{
  public function executeNewEditor()
  {
    $this->template = $this->getTemplateOrCreate();
    $this->used_articles = $this->template->getArticles();
    $this->rows = $this->template->getNewsSpacesGroupedByRow();
  }

  public function executeChangeOrder()
  {
    sfLoader::loadHelpers(array('Javascript', 'Tag'));

    $js = '';
    $row = $this->getRequestParameter('row');
    foreach ($this->getRequestParameter('data') as $sort => $cell)
    {
      $js .= "$('template_${row}_${cell}_order').value = '${sort}';";
    }
    $this->renderText(javascript_tag($js));

    return sfView::NONE;
  }

  public function executeAddRow()
  {
    $this->index =$this->getRequestParameter('row');
    $this->row = array();
    $this->representations = Article::getAvailableRepresentations();
  }

  public function executeAddArticle()
  {
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('id'));
  }

  public function executeAddCell()
  {
    $id = substr($this->getRequestParameter('id'), strrpos($this->getRequestParameter('id'), '_') + 1);
    $this->article = ArticlePeer::retrieveByPK($id);

    $this->representations = Article::getAvailableRepresentations();
    $news_space = new NewsSpace();
    $news_space->setType(current(array_keys($this->representations)));
    $news_space->setArticle($this->article);
    $this->news_space = $news_space;
    $this->row = $this->getRequestParameter('row');
    $this->cell = $this->getRequestParameter('cell');
  }

  public function executeNewSave()
  {
    $template = $this->getTemplateOrCreate('template[id]');
    $params = $this->getRequestParameter('template');

    unset($params['id']);

    $rows = array();
    // Use $r to indicate row order, as the number of the rows may skip some values or even be negative
    $r = 0;
    foreach ($params as $row_number => $row_contents)
    {
      $row = array();
      foreach ($row_contents as $cell_number => $cell_content)
      {
        $row[intval($cell_content['order'])] = $template->createNewsSpace(
          $r,
          $cell_content['order'],
          $cell_content['type'],
          $cell_content['article_id'],
          $cell_content['width']
        );
      }

      if (!empty($row))
      {
        $rows[] = $row;
        $r++;
      }
    }

    if (!empty($rows))
    {
      $template->deleteRelatedNewsSpaces();
      // Sanitize inner-row order for every row
      foreach ($rows as $row)
      {
        $index = 0;
        foreach ($row as $news_space)
        {
          $news_space->setColumnNumber($index);
          $news_space->save();
          $index++;
        }
      }
      $this->setFlash('notice', 'El diseño de la portada fue guardado satisfactoriamente');
    }
    else
    {
      $this->setFlash('notice', 'No se guardaron los cambios ya que la portada no contenía elementos');
    }

    $this->redirect('template/newEditor?id='.$template->getId());
  }

  public function handlePost()
  {
    $this->updateTemplateFromRequest();

   if($this->template->isNew())
      $this->template->setCreatedBy($this->getUser()->getGuardUser()->getId());
    else
      $this->template->setUpdatedBy($this->getUser()->getGuardUser()->getId());

    $this->saveTemplate($this->template);

    $this->setFlash('notice', 'Your modifications have been saved');

    if ($this->getRequestParameter('save_and_add'))
    {
      return $this->redirect('template/create');
    }
    else if ($this->getRequestParameter('save_and_list'))
    {
      return $this->redirect('template/newEditor?id='.$this->template->getId());
    }
    else
    {
      return $this->redirect('template/edit?id='.$this->template->getId());
    }
  }

  public function executeDelete()
  {
    $this->template = $this->getTemplateOrCreate();
    if ($this->template->canDelete())
    {
      try
      {
        $this->template->delete();
        
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('delete', 'La portada ' . $this->template->getName() .' no se puede borrar, debido a que esta referenciada en una sección ');
      }
      $this->setFlash('notice', 'The selected element has been successfully deleted'); 
    }
    else
    {
      $this->getRequest()->setError('delete', 'La portada ' . $this->template->getName() .' no se puede borrar, debido a que esta referenciada en una sección ');
    }
    return $this->forward('template','list');
  }
}