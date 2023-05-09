<?php

defined('_JEXEC') or die('Restricted access');

class WebPushModelWebPush extends JModelAdmin
{
	/**
	 * @param string $name
	 * @param string $prefix
	 * @param array $options
	 *
	 * @return bool|JTable
	 * @throws Exception
	 */
	public function getTable($name = 'WebPush', $prefix = 'WebPushTable', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * @param array $data
	 * @param bool $loadData
	 *
	 * @return bool|JForm
	 */
	public function getForm($data = [], $loadData = true)
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
	protected function loadFormData()
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
