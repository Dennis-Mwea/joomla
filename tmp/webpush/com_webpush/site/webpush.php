<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

if (!class_exists('WebPushController'))
{
	require_once JPATH_COMPONENT . '/controllers/webpush.php';
}

if (!class_exists('WebPushHelperRoute'))
{
	require_once JPATH_COMPONENT . '/helpers/route.php';
}

$controller = JControllerLegacy::getInstance('Webpush');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
