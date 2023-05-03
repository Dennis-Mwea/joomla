<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

JLoader::registerPrefix('WebPush', JPATH_COMPONENT);
JLoader::register('WebPushController', JPATH_COMPONENT . '/controller.php');

// Execute the task
$controller = JControllerLegacy::getInstance('WebPush');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
