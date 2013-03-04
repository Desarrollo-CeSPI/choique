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

class Mailer extends sfMail
{
  public function __construct()
  {
    parent::__construct();
    
    //Getting the configuration variables
    $type     = CmsConfiguration::get('text_mail_type', 'text/html');
    $hostname = CmsConfiguration::get('text_mail_hostname', 'localhost');
    $username = CmsConfiguration::get('text_mail_host_username');
    $password = CmsConfiguration::get('text_mail_host_password');
    
    //Setting the configuration
    $this->setMailer($type);
    $this->setHostname($hostname);
    $this->setUsername($username);
    $this->setPassword($password);
  }
  
  public function send()
  {
    if (!$this->mailer->Send())
    {
      throw new sfException($this->mailer->ErrorInfo);
    }
  }

}