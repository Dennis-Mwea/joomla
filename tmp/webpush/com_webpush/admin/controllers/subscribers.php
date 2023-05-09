<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class WebPusControllerSubscribers extends JControllerAdmin
{
	/**
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return bool|JModelLegacy
	 */
	public function getModel($name = 'Subscriber', $prefix = 'SubscriberModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
