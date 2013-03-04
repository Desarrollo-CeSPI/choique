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
 * contact actions.
 *
 * @package    cms
 * @subpackage contact
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class contactActions extends sfActions
{
  private function setSectionNameIfNecessary()
  {
    if (!$this->getRequest()->isXmlHttpRequest())
    {
		  $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter("article_id"));

      if (!is_null($this->article))
      {
		    $section = $this->article->getSection();

        if (!$section)
        {
      		$section = SectionPeer::retrieveHomeSection();
        }
		  }
      else
      {
        $section = new Section();
        
        $section->setName('contact');
      }

      $this->getRequest()->getParameterHolder()->set('section_name', $section->getName());
    }
  }
  
  public function executeContactForm()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_CONTACT);
    }

    if ($this->article_id = $this->getRequestParameter('article_id'))
    {
    	$this->article = ArticlePeer::retrieveByPK($this->article_id);
    	$this->mail_to = $this->article->getObfuscatedContact();

      if (empty($this->public_mail))
      {
    	  $this->public_mail = CmsConfiguration::get('contact_mail');	
      }

    	$this->getUser()->setAttribute('article',$this->article);
    }
    else
    {
      if (CmsConfiguration::get('check_obfuscate_mail_addresses', true))
      {
        $this->mail_to = Article::obfuscate(CmsConfiguration::get('contact_mail'));
      }
      else
      {
        $this->mail_to = CmsConfiguration::get('contact_mail');
      }

    	$this->article_id = null;	
    }
    
    $this->setSectionNameIfNecessary();
  }
  
  public function validateSend()
  {
    $validate = true;
    
    if ($this->getRequest()->getMethod() == sfRequest::POST) {
      
      sfLoader::loadHelpers(array('I18N'));
      $subject = $this->getRequestParameter('subject');
      $body = $this->getRequestParameter('body');
      $sender_name = $this->getRequestParameter('sender_name');
      if(!empty($subject)){
        if(strcmp($subject, __("Ingrese el asunto")) == 0){
          $this->getRequest()->setError('subject', 'El asunto del mensaje no puede estar vacío');
          $validate = false;
        }
      }
      if(!empty($body)){
        if(strcmp($body, __("Contenido del mensaje")) == 0){
          $this->getRequest()->setError('body', 'El cuerpo del mensaje no puede estar vacío');
          $validate = false;
        }
      }
      if(!empty($sender_name)){
        if(strcmp($sender_name, __("Remitente")) == 0){
          $this->getRequest()->setError('sender_name', 'El nombre del remitente no puede estar vacío');
          $validate = false;
        }
      }
    }
    
    return $validate;
  }

  public function executeSend()
  {
    $mail_log=new MailLog();
    $mail_log->setMailFrom($this->getRequestParameter('sender_mail'));
    $mail_log->setSenderName($this->getRequestParameter('sender_name'));
    $article = ArticlePeer::retrieveByPK($this->getRequestParameter('article_id'));
    $this->article = $article;
    if ($article == null) {
      $mail_to = CmsConfiguration::get('contact_mail');
    } else {
      $mail_to = $article->getContact() ? $article->getContact() : CmsConfiguration::get('contact_mail');
    }
    $this->getRequest()->setParameter('mail_to', $mail_to);
  	$this->sendEmail('contact','sendContact');

    $mail_log->setMailTo($mail_to);
    $mail_log->setSubject($this->getRequestParameter('subject'));
    $mail_log->setBody($this->getRequestParameter('body'));
    if (!is_null($article)) {
    	$section_name=is_null($article->getSection())?null:$article->getSection()->getTitle();
    	$article_name=$article->getTitle();
    }else{
    	$section_name=null;
    	$article_name=null;
    }
    $mail_log->setSectionName($section_name);
    $mail_log->setArticleName($article_name);
    $mail_log->save();
    $this->setSectionNameIfNecessary();
  }

  public function executeSendContact()
  {
  	// class initialization
    $mail = new Mailer();
    $mail->setCharset('utf-8');
    
    // definition of the required parameters
    $mail->setSender($this->getRequestParameter('sender_mail'), $this->getRequestParameter('sender_name'));
    $mail->setFrom($this->getRequestParameter('sender_mail'), $this->getRequestParameter('sender_name'));
    $mail->addReplyTo($this->getRequestParameter('sender_mail'));
    $mail_to = $this->getRequestParameter('mail_to');
    $mail->addAddress($mail_to);
    $this->article = ArticlePeer::retrieveByPK($this->getRequestParameter('article_id'));
    $this->body = ($this->article != null? "<h2>". $this->article->getTitle()."</h2>":'').$this->getRequestParameter('body');

    $mail->setSubject($this->getRequestParameter('subject'));
    $mail->setContentType('text/html');
    $this->mail = $mail;
  }
  
  
  public function handleErrorSend()
  {
    $this->forward('contact', 'contactForm');
  }
}