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
 * @version    SVN: $Id$
 */
class sfGuardBasicSecurityFilter extends sfBasicSecurityFilter
{
  public function execute ($filterChain)
  {
    $first = $this->isFirstCall();
    if ( $first and !$this->getContext()->getUser()->isAuthenticated())
    {
      if ($cookie = $this->getContext()->getRequest()->getCookie(sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember')))
      {
        $c = new Criteria();
        $c->add(sfGuardRememberKeyPeer::REMEMBER_KEY, $cookie);
        $rk = sfGuardRememberKeyPeer::doSelectOne($c);
        if ($rk && $rk->getSfGuardUser())
        {
          $this->getContext()->getUser()->signIn($rk->getSfGuardUser());
        }
      }
    }

    if ($first && 
        $this->getContext()->getUser()->isAuthenticated() && 
        $this->getContext()->getUser()->getGuardUser()->getMustChangePassword())
    {
         $this->getContext()->getController()->forward('sfGuardUser','mustChangePassword');

    }

    parent::execute($filterChain);
  }
}
