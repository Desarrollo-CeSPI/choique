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
<?php use_helper('Validation', 'Javascript') ?>

<?php if(!$sf_request->isXmlHttpRequest()) : ?>
  <?php $article = ArticlePeer::retrieveByPk($article_id); ?>
  <?php if(!is_null($article)): ?>
  <div id="contact_return_link">
    <?php echo link_to(__("Volver al artículo"), $article->getURLReference(), array('title' => __("Volver al artículo"), 'accesskey' => 'r')); ?>
  </div>
  <?php endif; ?>
	<div id="contact_form">
<?php endif; ?>

<div id="contact_form_container">
<h1 class="contact_form">
  <?php echo __('Formulario de contacto') ?>
</h1>

<?php if (CmsConfiguration::get('check_show_contact_email_address', true)): ?>
	<h2 class="contact_form_subtitle">
	  <?php echo __('Contacto con:') ?>
	 <span class="<?php CmsConfiguration::get('check_obfuscate_mail_addresses', true) and print 'obfuscated' ?>"> <?php echo $mail_to ?> </span>
	</h2>
<?php endif ?>

<?php echo form_remote_tag(array(
        'update'   => 'contact_form',
        'url'      => 'contact/send',
    ), array('class' => 'contact-form')) ?>
  <?php echo input_hidden_tag('article_id', $article_id) ?>
	
  <div class="form-row">
  	  <?php echo form_error('subject',array('class' => 'form-error-msg')) ?>
      <?php echo label_for('subject', __('Asunto')) ?>
      <?php echo input_tag('subject',
                           (!is_null($sf_params->get('subject')))?$sf_params->get('subject'):'',
                           array('placeholder' => __("Ingrese el asunto"))
                           ) ?>
  </div>
  <div class="form-row">
  	<?php echo form_error('sender_name',array('class' => 'form-error-msg')) ?>
    <?php echo label_for('sender_name', __('De')) ?>
    <?php echo input_tag('sender_name', 
                         (!is_null($sf_params->get('sender_name')))?$sf_params->get('sender_name'):'',
                         array('placeholder' => __("Remitente"))
                        ) ?>
  </div>
  <div class="form-row">
  	<?php echo form_error('sender_mail',array('class' => 'form-error-msg')) ?>
    <?php echo label_for('sender_mail', __('Mail')) ?>
    <?php echo input_tag('sender_mail', 
                         (!is_null($sf_params->get('sender_mail')))?$sf_params->get('sender_mail'):'', 
                         array('placeholder' => __("Email del remitente"))

                      )?>
  </div>
  <div class="form-row form-body">
  	<?php echo form_error('body',array('class' => 'form-error-msg')) ?>
    <?php echo label_for('body', __('Comentario')) ?>
    <?php echo textarea_tag('body', 
                            (!is_null($sf_params->get('body')))?$sf_params->get('body'):'',
                            array('placeholder' => __("Contenido del mensaje")) 
                          )?>
  </div>

  <div class="form-actions">
    <?php echo submit_tag(__('Enviar')) ?>
    <?php echo reset_tag(__('Limpiar')) ?>
  </div>
</form>
<?php if(!$sf_request->isXmlHttpRequest()) : ?>
	</div>	
<?php endif; ?>
</div>