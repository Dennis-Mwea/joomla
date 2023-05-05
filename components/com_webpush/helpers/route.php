<?php


defined('_JEXEC') or die('Restricted access');

class WebPushHelperRoute
{
	public static function getUsersRoute(): string
	{
		return 'index.php?option=com_webpush&task=getUsers';
	}

	public static function getUserRoute($userId): string
	{
		return "index.php?option=com_webpush&task=getUser&id=$userId";
	}

	public static function subscribeRoute(): string
	{
		return 'index.php?option=com_webpush&task=subscribe';
	}
}
