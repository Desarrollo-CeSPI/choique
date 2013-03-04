<?php

require_once(dirname(__FILE__).'/../../../lib/sfCaptchaGD.class.php');

/**
 * Captcha actions.
 *
 * @package    sfCaptchaGD
 * @subpackage sfCaptchaGDActions
 * @author     Alex Kubyshkin <glint@techinfo.net.ru>
 * @version    
 */
class sfCaptchaGDActions extends sfActions
{
  /**
   * Output captcha image
   *
   */
  public function executeGetImage()
  {
    $captcha = new sfCaptchaGD();
    $captcha->generateImage(); 
    return sfView::NONE;
  }
}
