<?php

/**
 * PJS actions.
 *
 * @package    sfPJS
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 3864 2007-04-24 15:27:52Z fabien $
 */
class sfPJSActions extends sfActions
{
  public function executeIndex()
  {
    // Change view to sfJavascript
    $this->getContext()->getRequest()->setAttribute($this->getRequestParameter('target_module').'_'.$this->getRequestParameter('target_action').'_view_name', 'sfJavascript', 'symfony/action/view');

    // Forward to the "real" javascript action
    $this->forward($this->getRequestParameter('target_module'), $this->getRequestParameter('target_action'));
  }
}
