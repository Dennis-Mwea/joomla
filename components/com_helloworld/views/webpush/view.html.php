<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class WebPushViewWebPush extends JViewLegacy
{
	public function display($tpl = null)
	{
		echo json_encode([
			'success' => false,
		]);
	}
}
