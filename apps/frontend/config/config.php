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

// include project configuration
include(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// symfony bootstraping
require_once($sf_symfony_lib_dir.'/util/sfCore.class.php');
sfCore::bootstrap($sf_symfony_lib_dir, $sf_symfony_data_dir);


sfConfig::set('sf_web_dir', SF_ROOT_DIR.'/web-frontend');
sfConfig::set('sf_upload_dir', sfConfig::get('sf_web_dir').'/'.sfConfig::get('sf_upload_dir_name'));

// CMS' images dir.
sfConfig::set('cms_images_dir_name', 'assets');
sfConfig::set('cms_images_dir', sfConfig::get('sf_upload_dir').'/'.sfConfig::get('cms_images_dir_name'));

// CMS' images long description files dir
sfConfig::set('cms_longdesc_images_dir', sfConfig::get('cms_images_dir').'/longdesc');

// CMS' documents dir.
sfConfig::set('cms_docs_dir_name', 'docs');
sfConfig::set('cms_docs_path', sfConfig::get('sf_upload_dir_name').'/'.sfConfig::get('cms_docs_dir_name'));
sfConfig::set('cms_docs_dir', sfConfig::get('sf_upload_dir').'/'.sfConfig::get('cms_docs_dir_name'));

// CMS' links dir.
sfConfig::set('cms_links_dir_name', 'links');
sfConfig::set('cms_links_dir', sfConfig::get('sf_upload_dir').'/'.sfConfig::get('cms_links_dir_name'));