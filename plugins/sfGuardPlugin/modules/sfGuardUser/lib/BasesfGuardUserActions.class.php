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
 * @version    SVN: $Id: BasesfGuardUserActions.class.php 3384 2007-02-01 09:05:19Z fabien $
 */
class BasesfGuardUserActions extends autosfGuardUserActions
{
  public function validateEdit()
  {
    if ($this->getRequest()->getMethod() == sfRequest::POST && !$this->getRequestParameter('id'))
    {
      if ($this->getRequestParameter('sf_guard_user[password]') == '')
      {
        $this->getRequest()->setError('sf_guard_user{password}', 'Password is mandatory');

        return false;
      }
    }

    return true;
  }

  public function executeChangePassword()
  {
    $this->labels = $this->getLabels(); 
    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $user = sfGuardUserPeer::retrieveByUsername($this->getUser()->getUsername());
      $user->setPassword($this->getRequestParameter('sf_guard_user[new_password]'));
      $user->setChangePasswordAt(time());
      $user->setMustChangePassword(false);
      $user->save();
      $this->getUser()->signOut();
      $this->setFlash('changes','La contraseña fue modificada correctamente. Por favor ingrese nuevamente');
      $this->redirect('@homepage');
    }
 
  }

  public function executeMustChangePassword()
  {
     $this->setFlash('notice','Debes actualizar la contraseña para poder continuar');
     $this->forward('sfGuardUser','changePassword');
  }

  public function handleErrorChangePassword()
  {
    $this->preExecute();
    $this->labels = $this->getLabels();
    return sfView::SUCCESS;
  }

  public function validateChangePassword()
  {
    if ($this->getRequest()->getMethod() == sfRequest::POST && !$this->getRequestParameter('id'))
    {
      $user = sfGuardUserPeer::retrieveByUsername($this->getUser()->getUsername());
      if (!$user->checkPassword($this->getRequestParameter('sf_guard_user[password]')  ))
      {
        $this->getRequest()->setError('sf_guard_user{password}', 'Contraseña actual no es valida o la nueva contraseña no es valida.');
        return false;
      }

      if ($this->getRequestParameter('sf_guard_user[password]') == $this->getRequestParameter('sf_guard_user[new_password]'))
      {
        $this->getRequest()->setError('sf_guard_user{new_password}', 'La nueva contraseña debe ser diferente a la actual');
        return false;
      }
    }
    return true;

  }


  protected function getLabels()
  {
    switch ($this->getActionName()) {
      case 'changePassword':
        return array(
          'sf_guard_user{password}' => 'Contraseña Actual:',
          'sf_guard_user{new_password}' => 'Nueva Contraseña:',
          'sf_guard_user{password_bis}' => 'Confirmación contraseña:',
        );
    }
    return parent::getLabels();
  }
}
