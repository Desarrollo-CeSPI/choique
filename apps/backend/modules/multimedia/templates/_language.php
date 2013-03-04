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
<?php $value_param = ($sf_request->hasParameter('multimedia[language]'))? $sf_request->getParameter('multimedia[language]'): $multimedia->getLanguage(); ?>
<?php $value = select_tag('multimedia[language]', 
                           options_for_select(array ('es_AR' => __('Spanish'), 
                                                     'en_US' => __('English'), 
                                                     'pt_BR' => __('Portugues')), 
                           $multimedia->getLanguage()), 
                           array('id'=>'languageSelect')); ?>
<?php echo $value ? $value : '&nbsp;' ?>
