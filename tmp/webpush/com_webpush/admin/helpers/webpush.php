<?php

defined('_JEXEC') or die('Restricted access');

abstract class WebPushHelper
{
	public static function addSubMenu(string $submenu): void
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_WEBPUSH_SUBMENU_CONFIG'),
			'index.php?option=com_webpush',
			$submenu == 'webpushes'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_WEBPUSH_SUBMENU_SUBSCRIBERS'),
			'index.php?option=com_webpush&view=subscribers',
			$submenu == 'subscribers'
		);

		JHtmlSidebar::addEntry(
			'<i class="fa fa-eye fa-fw" aria-hidden="true"></i>' . JText::_('COM_WEBPUSH_SUBMENU_NOTIFICATION_TEMPLATES'),
			'index.php?option=com_webpush&view=notification_templates',
			$submenu == 'notification_templates'
		);

		// Set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-webpush {background-image: url(../media/com_webpush/images/notifications.png);}');
		if ($submenu == 'subscribers')
		{
			$title = 'COM_WEBPUSH_ADMINISTRATOR_SUBSCRIBERS';
		} else if ($submenu == 'notification_templates') {
			$title = 'COM_WEBPUSH_ADMINISTRATOR_NOTIFICATION_TEMPLATES';
		} else {
			$title = 'COM_WEBPUSH_ADMINISTRATOR';
		}

		$document->setTitle(JText::_($title));
	}

	public static function getActions(?int $messageId = 0): JObject
	{
		$results   = new JObject();
		$assetName = empty($messageId) ? 'com_webpush' : "com_webpush.message.$messageId";
		$actions   = JAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_webpush/access.xml');
		if (!empty($actions))
		{
			foreach ($actions as $action)
			{
				$results->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
			}
		}

		return $results;
	}
}
