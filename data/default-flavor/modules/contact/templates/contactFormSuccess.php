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

<?php if (CmsConfiguration::get('check_show_contact_email_address', true)): ?>
	<div class="contact_form_title">
	  <?php echo __('CONTACTO CON:') ?>&nbsp;
	  <?php echo $mail_to ?> 
	</div>
<?php endif ?>

<?php echo form_remote_tag(array(
        'update'   => 'contact_form',
        'url'      => 'contact/send',
    ), array('class' => 'contact-form')) ?>
  <?php echo input_hidden_tag('article_id', $article_id) ?>
  <div class="form-cols-left">
	  <div class="form-row">
		  <?php echo form_error('subject',array('class' => 'form-error-msg')) ?>
		  <?php echo input_tag('subject',(!is_null($sf_params->get('subject')))?$sf_params->get('subject'):__("Ingrese el asunto")) ?>
	  </div>
	  <div class="form-row">
		<?php echo form_error('sender_name',array('class' => 'form-error-msg')) ?>
		<?php echo input_tag('sender_name', (!is_null($sf_params->get('sender_name')))?$sf_params->get('sender_name'):__("Remitente")) ?>
	  </div>
	  <div class="form-row">
		<?php echo form_error('sender_mail',array('class' => 'form-error-msg')) ?>
		<?php echo input_tag('sender_mail', (!is_null($sf_params->get('sender_mail')))?$sf_params->get('sender_mail'):__("Email del remitente")) ?>
	  </div>
  </div>
  <div class="form-cols-right">
	  <div class="form-row-right">
		<?php echo form_error('body',array('class' => 'form-error-msg')) ?>
		<?php echo textarea_tag('body', (!is_null($sf_params->get('body')))?$sf_params->get('body'):__("Contenido del mensaje")) ?>
	  </div>

	  <div class="form-actions">
		<?php echo submit_tag(__('Enviar')) ?>
		<?php echo reset_tag(__('Limpiar')) ?>
	  </div>
  </div>
</form>
<?php if(!$sf_request->isXmlHttpRequest()) : ?>
	</div>	
<?php endif; ?>
</div>
