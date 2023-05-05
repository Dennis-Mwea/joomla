<?php

defined('_JEXEC') or die;

function webPushBuildRoute(&$query): array
{
	$segments = array();
	if (isset($query['option'], $query['task']) && $query['option'] == 'com_webpush')
	{
		unset($query['option']);
		unset($query['task']);
		if ($query['task'] == 'subscribe')
		{
			$segments[] = 'subscribe';
		} else if ($query['task'] == 'sendMessages')
		{
			$segments[] = 'sendMessages';
		} else if ($query['task'] == 'getSubscribers')
		{
			$segments[] = 'getSubscribers';
		}
	}

	return $segments;
}

function webPushParseRoute($segments): array
{
	$vars = [];
	if (count($segments) == 1)
	{
		$vars['option'] = 'com_webpush';
		if ($segments[0] == 'subscribe')
		{
			$vars['task'] = 'subscribe';
		} else if ($segments[0] == 'sendMessages')
		{
			$vars['task'] = 'sendMessages';
		} else if ($segments[0] == 'getSubscribers')
		{
			$vars['task'] = 'getSubscribers';
		}
	}

	return $vars;
}
