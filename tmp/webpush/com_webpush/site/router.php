<?php

defined('_JEXEC') or die;

function webPushBuildRoute(&$query): array
{
	$segments = array();
	if (isset($query['option'], $query['task']) && $query['option'] == 'com_webpush')
	{
		unset($query['option']);
		unset($query['task']);
		if ($query['task'] == 'getUsers')
		{
			$segments[] = 'getUsers';
		}
		else if ($query['task'] == 'subscribe')
		{
			$segments[] = 'subscribe';
		}
	}

	return $segments;
}

function webPushParseRoute($segments): array
{
	$vars = array();
	if (count($segments) == 1)
	{
		$vars['option'] = 'com_webpush';
		if ($segments[0] == 'getUsers')
		{
			$vars['task'] = 'getUsers';
		}
		else if ($segments[0] == 'subscribe')
		{
			$vars['task'] = 'subscribe';
		}
	}

	return $vars;
}
