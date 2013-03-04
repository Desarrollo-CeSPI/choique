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
 * maillog actions.
 *
 * @package    cms
 * @subpackage maillog
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class maillogActions extends automaillogActions
{
	public function executeCreate()
	{
		$this->forward('maillog','list');
	}

	public function executeEdit()
	{
		$this->forward('maillog','list');
	}

	public function executeIndex()
	{
		$this->forward('maillog','list');
	}

  public function executeExport()
  {
    sfLoader::loadHelpers(array('I18N'));

    //List generation: input
    $this->processSort();
    $this->processFilters();
    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/mail_log/filters');
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);

    //Xls generation: output
    $this->setLayout(false);
    $filename = 'list.xls';
    $this->getResponse()->setHttpHeader('Content-Disposition', ' attachment; filename="'.$filename.'"');
    $full_filename = '/tmp/' . $filename; 
    $this->title = __('Listado de mails');
    $this->file = $full_filename;
    $excel_builder = MailLogPeer::getXls($c);
    @$excel_builder->toXLS($full_filename);
  }
}