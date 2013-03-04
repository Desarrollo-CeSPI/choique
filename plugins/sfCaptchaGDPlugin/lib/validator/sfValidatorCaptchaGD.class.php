<?php

/**
 * sfValidatorCaptchaGD validates a captcha for sfCaptchaGDPlugin.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Alex Kubyshkin <glint@techinfo.net.ru>
 */
class sfValidatorCaptchaGD extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * length: The length of the string
   *
   * Available error codes:
   *
   *  * captcha
   *  * length
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('captcha', 'Wrong code');
    $this->addMessage('length', 'Code length must be %length% characters');
    $this->addOption('length');

    $this->setOption('empty_value', '');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $clean = trim($value);

    $length = function_exists('mb_strlen') ? mb_strlen($clean, $this->getCharset()) : strlen($value);

    if ($this->hasOption('length') && $length != $this->getOption('length'))
    {
      sfContext::getInstance()->getUser()->setAttribute('captcha', '');
      throw new sfValidatorError($this, 'length', array('length' => $this->getOption('length')));
    }

    if (sfContext::getInstance()->getUser()->getAttribute('captcha') != $value){
      sfContext::getInstance()->getUser()->setAttribute('captcha', '');
      throw new sfValidatorError($this, 'captcha', array());
    }

    return $clean;
  }
}
