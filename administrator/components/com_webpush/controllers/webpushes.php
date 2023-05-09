<?php

defined('_JEXEC') or die('Restricted access');

class WebPushControllerWebPushes extends JControllerAdmin
{
	/**
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return bool|JModelLegacy
	 */
	public function getModel($name = 'WebPush', $prefix = 'WebPushModel', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
