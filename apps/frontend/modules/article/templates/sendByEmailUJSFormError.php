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
<?php use_helper('Javascript','Validation') ?>
  <div id="contact_return_link">
    <?php echo link_to(__("Volver al artículo"), $article->getURLReference(), array('title' => __("Volver al artículo"), 'accesskey' => 'r')); ?>
  </div>
<?php $article_id=$article->getId() ?>
<div id="mail-body-acc">
<?php echo form_tag('article/sendByEmailUJS?id='.$article_id, array("method" => 'get')) ?>
<fieldset id="fieldset-mail" class="">
  <div class="form-row-email">
    <?php echo label_for('mailto', __('Enviar a:'), 'class="required" ') ?>
    <div class="content<?php if ($sf_request->hasError('mailto')): ?> form-error<?php endif; ?>">
      <?php if ($sf_request->hasError('mailto')): ?>
        <?php echo form_error('mailto', array('class' => 'form-error-msg')) ?>
      <?php endif; ?>

      <?php echo input_tag('mailto',$mailto) ?>
    </div>
  </div>

  <div class="form-row-email">
    <?php echo label_for('from', __('Remitente (su nombre):'), 'class="required" ') ?>
    <div class="content<?php if ($sf_request->hasError('from')): ?> form-error<?php endif; ?>">
      <?php if ($sf_request->hasError('from')): ?>
        <?php echo form_error('from', array('class' => 'form-error-msg')) ?>
      <?php endif; ?>

      <?php echo input_tag('from',$from) ?>
    </div>
  </div>
</fieldset>
<div id="form-actions-email">
  <?php echo submit_tag('Enviar') ?>
</div>
</form>
</div>