<?php

defined('_JEXEC') or die('Restricted access');

// Set some global property
JFactory::getDocument()
	->addStyleDeclaration('.icon-webpush {background-image: url(../media/com_webpush/images/notifications.png)}');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'cm_webpush'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('WebPushHelper', JPATH_COMPONENT . '/helpers/webpush.php');

// Get instance of the controller prefixed by WebPush
// Perform the Request task
// Redirect if set by the controller
$controller = JControllerLegacy::getInstance('WebPush');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
