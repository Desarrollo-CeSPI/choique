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

class choiqueSettingsFilter extends sfFilter
{
  public function execute($filterChain)
  {
    if ($this->getContext()->getUser()->isAuthenticated())
    {
      $urls = sfConfig::get('app_choique_url', null);

      $config_errors = array();

      if (!isset($urls['frontend']) || is_null($urls['frontend']))
      {
        $config_errors[] = 'You must set the frontend url (in app.yml).';
      }

      if (!isset($urls['backend']) || is_null($urls['backend']))
      {
        $config_errors[] = 'You must set the backend url (in app.yml).';
      }
      else
      {
        if ($this->getContext()->getModuleName() != 'ajax' && $this->getContext()->getActionName() != 'test')
        {
          sfLoader::loadHelpers(array('CmsCSRFToken'));
          
          $url = $urls['backend'].'/ajax/test';
          $url .= '?_csrf_token='.csrf_token();
          
          if (file_get_contents($url) === FALSE)
          {
            $config_errors[] = 'Backend url is not correctly set (in app.yml).';
          }
        }
      }

      if (count($config_errors))
      {
        $this->getContext()->getUser()->setAttribute('choique_settings', $config_errors, 'symfony/flash');
      }
    }
    
    $filterChain->execute();
  }
}