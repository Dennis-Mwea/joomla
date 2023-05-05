<?php

defined('_JEXEC') or die('Restricted access');

abstract class WebPushHelper
{
	public static function getActions(?int $messageId = 0): JObject
	{
		$results = new JObject();
		$assetName = empty($messageId) ? 'com_webpush' : "com_webpush.message.$messageId";
		$actions = JAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_webpush/access.xml');
		if (!empty($actions)) {
			foreach ($actions as $action) {
				$results->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
			}
		}

		return $results;
	}
}
