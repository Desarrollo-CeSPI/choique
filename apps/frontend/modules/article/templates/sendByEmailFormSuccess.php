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
<?php use_helper('Javascript', 'Validation') ?>

<div id="mail-body">
  <?php echo form_remote_tag(array(
    'update' => 'mail-body',
    'url'    => 'article/sendByEmail?id='.$article->getId(),
    'method' => 'get'
  )) ?>
    <fieldset id="fieldset-mail">
      <div class="form-row-email">
        <?php echo label_for('mailto', __('Enviar a:'), array('class' => 'required')) ?>
        <div class="content<?php $sf_request->hasError('mailto') and print ' form-error' ?>">
          <?php if ($sf_request->hasError('mailto')): ?>
            <?php echo form_error('mailto', array('class' => 'form-error-msg')) ?>
          <?php endif; ?>

          <?php echo input_tag('mailto', $mailto, array('placeholder'=>__('Ingrese el email'))) ?>
        </div>
      </div>

      <div class="form-row-email">
        <?php echo label_for('from', __('Remitente (su nombre):'), array('class' => 'required')) ?>
        <div class="content<?php $sf_request->hasError('from') and print ' form-error' ?>">
          <?php if ($sf_request->hasError('from')): ?>
            <?php echo form_error('from', array('class' => 'form-error-msg')) ?>
          <?php endif; ?>

          <?php echo input_tag('from', $from, array('placeholder'=>__('Ingrese su nombre'))) ?>
        </div>
      </div>
    </fieldset>
    <div id="form-actions">
      <input type="reset" value="<?php echo __('Cancelar') ?>" onclick="Lightview.hide();" />
      <?php echo submit_tag('Enviar') ?>
    </div>
  </form>
</div>
