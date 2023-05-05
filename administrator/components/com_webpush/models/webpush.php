<?php

defined('_JEXEC') or die('Restricted access');

class WebPushModelWebPush extends JModelAdmin
{
	public function getTable($name = 'WebPush', $prefix = 'WebPushTable', $options = []): JTable|bool
	{
		return parent::getTable($name, $prefix, $options);
	}

	public function getForm($data = [], $loadData = true): JForm|bool
	{
		$form = $this->loadForm('com_webpush.webpush', 'webpush', [
			'control' => 'jform',
			'load_data' => $loadData,
		]);

		return empty($form) ? false : $form;
	}

	public function getScript(): string
	{
		return 'administrator/components/com_webpush/models/forms/webpush.js';
	}

	/**
	 * @return array|bool|JObject|mixed
	 * @throws Exception
	 */
	protected function loadFormData(): mixed
	{
		$data = JFactory::getApplication()->getUserState('com_webpush.edit.webpush.data', []);

		return empty($data) ? $this->getItem() : $data;
	}

	protected function canDelete($record): bool
	{
		if (!empty($record->id)) {
			return JFactory::getUser()->authorise('core.delete', "com_webpush.message.$record->id");
		}

		return parent::canDelete($record);
	}
}
