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
 * @version    SVN: $Id: BasesfGuardAuthActions.class.php 6352 2007-12-07 09:16:20Z fabien $
 */
class BasesfGuardAuthActions extends sfActions
{
  public function executeSignin()
  {
    $user = $this->getUser();
    $this->checkForcedAttack();
    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $referer = $user->getAttribute('referer', $this->getRequest()->getReferer());
      $user->getAttributeHolder()->remove('referer');

      $signin_url = sfConfig::get('app_sf_guard_plugin_success_signin_url', $referer);

      $this->redirect('' != $signin_url ? $signin_url : '@homepage');
    }
    elseif ($user->isAuthenticated())
    {
      $this->redirect('@homepage');
    }
    else
    {
      if ($this->getRequest()->isXmlHttpRequest())
      {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(401);

        return sfView::NONE;
      }

      if (!$user->hasAttribute('referer'))
      {
        $user->setAttribute('referer', $this->getRequest()->getReferer());
      }

      if ($this->getModuleName() != ($module = sfConfig::get('sf_login_module')))
      {
        return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
      }

      $this->getResponse()->setStatusCode(401);
    }
  }

  public function executeSignout()
  {
    $this->getUser()->signOut();

    $signout_url = sfConfig::get('app_sf_guard_plugin_success_signout_url', $this->getRequest()->getReferer());

    $this->redirect('' != $signout_url ? $signout_url : '@homepage');
  }

  public function executeSecure()
  {
    $this->getResponse()->setStatusCode(403);
  }

  public function executePassword()
  {
    throw new sfException('This method is not yet implemented.');
  }

  public function handleErrorSignin()
  {
    $user = $this->getUser();
    if (!$user->hasAttribute('referer'))
    {
      $user->setAttribute('referer', $this->getRequest()->getReferer());
    }

    $module = sfConfig::get('sf_login_module');
    if ($this->getModuleName() != $module)
    {
      $this->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));
    }
    $this->checkForcedAttack();
    return sfView::SUCCESS;
  }


  protected function checkForcedAttack()
  {
    $login_failure_max_attempts_same_user = sfConfig::get('app_sf_guard_plugin_login_failure_max_attempts_same_user', 5);
    $login_failure_max_attempts_same_ip   = sfConfig::get('app_sf_guard_plugin_login_failure_max_attempts_same_ip', 5); 
    $login_failure_check_range_time       = sfConfig::get('app_sf_guard_plugin_login_failure_check_range_time', 60); 
    
    $username = $this->getRequestParameter('username');
    $ip_addreess = $_SERVER['REMOTE_ADDR'];
    $cant_for_user = sfGuardLoginFailurePeer::doCountForUsernameInTimeRange($username, $login_failure_check_range_time);
    $cant_for_ip   = sfGuardLoginFailurePeer::doCountForIpInTimeRange($ip_addreess , $login_failure_check_range_time );

    $user = $this->getUser();
    $user->setAttribute('sf_guard_plugin_forced_attack_detected', 0);
    if(($cant_for_user > $login_failure_max_attempts_same_user ) || ($cant_for_ip > $login_failure_max_attempts_same_ip))
    {
      $user->setAttribute('sf_guard_plugin_forced_attack_detected', 1);
    }


  }
}
