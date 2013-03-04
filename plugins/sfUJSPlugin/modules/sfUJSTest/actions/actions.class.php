<?php


class sfUJSTestActions extends sfActions
{
	public function executeIndex()
	{
	  $this->static = $this->getRequestParameter('static', 1);
	}
}
