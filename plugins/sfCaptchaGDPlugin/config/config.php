<?php

if (sfConfig::get('app_sfCaptchaGDPlugin_routes_register', true) && in_array('sfCaptchaGD', sfConfig::get('sf_enabled_modules')))
{
  $r = sfRouting::getInstance();
  // preprend our routes
  $r->prependRoute('sf_captchagd', '/captcha', array('module' => 'sfCaptchaGD', 'action' => 'GetImage'));
}
