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

		// Set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-webpush {background-image: url(../media/com_webpush/images/notifications.png);}');
		if ($submenu == 'subscribers')
		{
			$document->setTitle(JText::_('COM_WEBPUSH_ADMINISTRATOR_SUBSCRIBERS'));
		}
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
