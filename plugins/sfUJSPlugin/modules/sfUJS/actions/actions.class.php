<?php


class sfUJSActions extends sfActions
{
	public function executeScript()
	{
		$this->getResponse()->setContentType('application/x-javascript');
		$this->setLayout(false);
		$key = substr($this->getRequestParameter('key'), 0, 32);
		$script = $this->getUser()->getAttribute('UJS_'.$key, '', 'symfony/UJS');
		$this->getUser()->getAttributeHolder()->remove('UJS_'.$key, 'symfony/UJS');
		
		return $this->renderText($script);
	}
}
