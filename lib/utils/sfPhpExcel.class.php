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

class sfPhpExcel extends PHPExcel
{
  public function __construct()
  {
    parent::__construct();
    
    $this->getProperties()->setCreator(sfConfig::get('ex_meta_creator'));
    $this->getProperties()->setTitle(sfConfig::get('ex_meta_title'));
    $this->getProperties()->setSubject(sfConfig::get('ex_meta_subject'));
    $this->getProperties()->setDescription(sfConfig::get('ex_meta_description'));
    $this->getProperties()->setKeywords(sfConfig::get('ex_meta_keyword'));
    $this->getProperties()->setCategory(sfConfig::get('ex_meta_category'));
    $this->setActiveSheetIndex(0);
    
  }
}