<?php
class sfCaptchaGDValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
            $captcha = $value;
 
            $session_captcha = $this->getParameter('captcha_ref');
            
            if($captcha != $session_captcha)
            {
                sfContext::getInstance()->getUser()->setAttribute('captcha', NULL);
                $error = $this->getParameter('captcha_error');
                return false;
            }
        return true;
    }
 
    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);
 
        // Set default parameters value
        $this->setParameter('captcha_ref', $context->getUser()->getAttribute('captcha'));
 
        // Set parameters
        $this->getParameterHolder()->add($parameters);
 
 
        return true;
    }
}
?>
