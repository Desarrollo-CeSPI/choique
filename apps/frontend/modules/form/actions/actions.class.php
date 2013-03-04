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
 * form actions.
 *
 * @package    choique
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class formActions extends sfActions
{

  public function executeSubmit()
  {
    sfLoader::loadHelpers(array('I18N'));

    $this->form = FormPeer::retrieveByPK($this->getRequestParameter('form_id'));
    $this->forward404Unless($this->form);

    $fields = $this->form->getFields();

    $rows = $this->form->getRows() + 1;

    foreach ($fields as $field) {

      $name = $this->form->getNameForHTML().'_'.$field->getId();

      $data = new Data();

      if ($field->getType() == Field::INPUT_TYPE_RADIO) {
        if ($value = $this->getRequestParameter($field->getDefaultValue())) {
          if ($field->getLabel() == $value) {
            $data->setData(__('Sí'));
            $data->setField($field);
            $data->setRow($rows);
          }
        }
      } else if ($field->getType() == Field::INPUT_TYPE_CHECKBOX) {
        if ($this->hasRequestParameter($name)) {
          $data->setData(__('Sí'));
          $data->setField($field);
          $data->setRow($rows);
        }
      } else {
        if ($value = $this->getRequestParameter($name)) {
          $data->setData($value);
          $data->setField($field);
          $data->setRow($rows);
        } else if (($field->getType()!=Field::LABEL)&&$field->getIsRequired()) {
          $data->delete();
          $this->setFlash('error', __('El campo "'.$field->getLabel().'" es requerido.'));
          if($this->getRequest()->isXmlHttpRequest()){
            $this->getUser()->setAttribute('javascript', true);
          }
          return $this->redirect('form/error');
        }
      }

      $data->save();

    }

    $this->form->setRows($rows);

    $this->form->save();

    if (CmsConfiguration::get('check_send_form_by_mail', false) && !$this->form->getIsPoll())
    {
      $this->getRequest()->setParameter('row',$rows);
      try
      {
        $this->sendEmail('form','sendFormByMail');
      }
      catch(Exception $e)
      {
      }
    }
    $this->getContext()->getResponse()->setCookie($this->form->getNameForHTML(), true);
    if(!$this->getRequest()->isXmlHttpRequest()){
      $this->no_javascript = true;
    }
  }

  public function executeSendFormByMail()
  {
    $this->form = FormPeer::retrieveByPK($this->getRequestParameter('form_id'));
    $this->row = $this->getRequestParameter('row');
    // class initialization
    $mail = new Mailer();
    $mail->setCharset('utf-8');
    $m_from= CmsConfiguration::get('form_mail_from','no-reply@example.com');
    $m_from_text = CmsConfiguration::get('form_mail_from_text', 'Portal');
    $mail->setSender($m_from, $m_from_text);
    $mail->setFrom($m_from, $m_from_text);
    $mail->addReplyTo($m_from);

    $mail_to = $this->form->getEmail();
    
    if (null === $mail_to)
    {
      $mail_to = CmsConfiguration::get('form_mail',CmsConfiguration::get('contact_mail'));
    }

    $mail->addAddress($mail_to);

    $mail->setSubject('Nuevo Formulario: '.$this->form->getTitle());
    $mail->setContentType('text/html');
    $this->mail = $mail;
  }

  public function executeError()
  {
    if($this->getUser()->hasAttribute('javascript')){
      $this->getUser()->getAttributeHolder()->remove('javascript');
      $this->setLayout(false);
    }
    else{
      $this->no_javascript = true;
    }
  }
    
  public function executeShowResults()
  {
    $this->form = $this->getFormOrCreate();
  }
    

}