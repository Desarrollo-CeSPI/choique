<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardUserValidator.class.php 3109 2006-12-23 07:52:31Z fabien $
 */
class sfGuardUserValidator extends sfValidator
{
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->getParameterHolder()->set('username_error', 'Username or password is not valid.');
    $this->getParameterHolder()->set('password_field', 'password');
    $this->getParameterHolder()->set('remember_field', 'remember');

    $this->getParameterHolder()->add($parameters);

    return true;
  }

  public function execute(&$value, &$error)
  {
    $password_field = $this->getParameterHolder()->get('password_field');
    $password = $this->getContext()->getRequest()->getParameter($password_field);

    $remember = false;
    $remember_field = $this->getParameterHolder()->get('remember_field');
    $remember = $this->getContext()->getRequest()->getParameter($remember_field);

    $username = $value;

    $user = sfGuardUserPeer::retrieveByUsername($username);
    
    $session_user = $this->getContext()->getUser();


    // user exists?
    if ($user)
    {
      // password is ok?
      if ($user->checkPassword($password))
      {
            //check if captcha for forced attack es present
            if ($session_user->getAttribute('sf_guard_plugin_forced_attack_detected', 0))
            {
              $validator = new sfCaptchaGDValidator();
              $validator->initialize ($this->getContext());
              $captcha_request = $this->getContext()->getRequest()->getParameter('captcha','');
              if ($validator->execute($captcha_request, $error))
              {
                $session_user->signIn($user, $remember);
                return true;
              }
            }
            else
            {
                $session_user->signIn($user, $remember);
                return true;
            }
      }
    }

    // password is not ok, save login failure
    $failure = new sfGuardLoginFailure();
    $failure->setUsername($username);
    $failure->setAt(time());
    $failure->setCookieId($_SERVER['HTTP_COOKIE']);
    $failure->setIpAddress($_SERVER['REMOTE_ADDR']);
    $failure->save();

    $error = $this->getParameterHolder()->get('username_error');

    return false;
  }
}
