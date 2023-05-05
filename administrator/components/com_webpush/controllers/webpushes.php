<?php

defined('_JEXEC') or die('Restricted access');

class WebPushControllerWebPushes extends JControllerAdmin
{
	public function getModel($name = 'WebPush', $prefix = 'WebPushModel', $config = ['ignore_request' => true]): JModelLegacy|bool
	{
		return parent::getModel($name, $prefix, $config);
	}
}
