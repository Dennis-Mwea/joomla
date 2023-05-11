<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class WebPushControllerNotification_Template extends JControllerForm
{
	public function __construct()
	{
		$this->view_list = 'notification_templates';

		parent::__construct();
	}
}
