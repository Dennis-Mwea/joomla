<?php

defined('_JEXEC') or die('Restricted access');

class WebPushControllerWebPush extends JControllerForm
{
	protected function allowEdit($data = [], $key = 'id'): bool
	{
		$id = $data[$key] ?? 0;
		if (!empty($id)) {
			return JFactory::getUser()->authorise('core.edit', "com_webpush.message.$id");
		}

		return parent::allowEdit($data, $key);
	}
}
