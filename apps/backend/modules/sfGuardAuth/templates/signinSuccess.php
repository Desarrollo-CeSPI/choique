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
<?php use_helper('Validation', 'I18N', 'Javascript') ?>

<?php if ($sf_flash->has('changes')): ?>
<div class="save-ok">
<h2><?php echo __($sf_flash->get('changes')) ?></h2>
</div>
<?php endif; ?>

<div id="login">
  <div class="right">
      <div class="title_login">
        <?php echo __('login | %name%', array("%name%" => sfConfig::get('app_choique_instance_name', 'Choique CMS'))) ?>
      </div>

      <?php echo form_tag('@sf_guard_signin') ?>
          <div class="form-row" id="sf_guard_auth_username">
            <?php echo form_error('username') ?>
            <?php echo label_for('username', __('Usuario:')) ?>
            <?php echo input_tag('username', $sf_data->get('sf_params')->get('username')) ?>
          </div>

          <div class="form-row" id="sf_guard_auth_password">
            <?php echo form_error('password') ?>
            <?php echo label_for('password', __('Contraseña:')) ?>
            <?php echo input_password_tag('password') ?>
          </div>

<?php if($sf_user->getAttribute('sf_guard_plugin_forced_attack_detected',0)):?>

          <div class="form-row" id="sf_guard_auth_captcha">
            <?php echo form_error('captcha') ?>
            <?php echo label_for('captcha',  __('Ingrese los dígitos mostrados en la imagen') ) ?>
            <?php  
            echo link_to_function(image_tag(url_for('/captcha/', true).'?key='.rand(1, 10), array('id' => 'captcha_img', 'alt' => __('Haga click aquí para ver la imagen'))),
                                      "document.getElementById('captcha_img').src = '".url_for('/captcha/')."?reload=1&key='+Math.round(Math.random(0)*1000)+1;") 
            ?>
            <br />
            <?php echo input_tag('captcha') ?>
          </div>
<?php endif; ?>





          <div class="form-actions">
            <?php echo submit_tag(__('Ingresar'), 'class="button"') ?>
          </div>

        </form>

  </div>
</div>




<?php echo javascript_tag("$('username').focus()") ?>