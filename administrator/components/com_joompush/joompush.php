<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_joompush'))
{
	JFactory::getApplication()->enqueueMessage(JText::_('COM_REPORTER_ACCESS_WARNING'), 'warning');
	return;
}
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_joompush/css/font-awesome/css/font-awesome.min.css');
// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Joompush', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Joompush');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
