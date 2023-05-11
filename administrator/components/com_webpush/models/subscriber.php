<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

include_once JPATH_ROOT . "/components/com_webpush/vendor/autoload.php";
require_once JPATH_ROOT . '/components/com_webpush/helpers/webpush.php';

class WebPushModelSubscriber extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_WEBPUSH';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_webpush.subscriber';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	public function getTable($type = 'Subscriber', $prefix = 'WebPushTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * @return mixed|null
	 * @throws Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_webpush.edit.subscriber.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__webpush_subscribers');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	public function getTemplates()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName('#__webpush_notification_templates'));
		$query->where($db->quoteName('state') . ' = 1');
		$db->setQuery($query);

		$data =  $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('COM_WEBPUSH_SELECT_TEMPLATE'));

		foreach($data as $value) :
			$options[] = JHTML::_('select.option', $value->id, $value->title);
		endforeach;

		return JHTML::_('select.genericlist', $options, 'jform[exstmsg]', 'class="inputbox"', 'value', 'text', 0);
	}

	/**
	 * @param $data
	 * @param $loadData
	 *
	 * @return bool|JForm
	 * @throws Exception
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_webpush.notification_template', 'notification_template', [
			'control' => 'jform',
			'load_data' => $loadData,
		]);

		if (empty($form))
		{
			return false;
		}

		$input = JFactory::getApplication()->input;
		$key   = $input->get('key','','RAW');
		$gid   = $input->get('gid', 0, 'INT');

		$form->key = $this->getSubscriberKeys($key);
		$form->gid = $gid;
		$form->sid = $key;

		return $form;
	}

	function getSubscriberKeys($key)
	{
		if($key) {
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__webpush_subscribers'))
				->where("{$db->quoteName('id')} IN ($key)");

			return $db->setQuery($query)->loadColumn();
		}
	}

	/**
	 * @throws ErrorException
	 */
	public function sendNotification($data)
	{
		if ($data) {
			$db = JFactory::getDbo();
			if ($data['isnew']) {
				$template = new stdClass();
				$template->id 			= '';
				$template->state		= '1';
				$template->title 		= $data['title'];
				$template->message 		= $data['message'];
				$template->icon 		= $data['icon'];
				$template->url 			= $data['url'];
				$template->created_by 	= $data['created_by'];
				$template->modified_by 	= $data['modified_by'];
				$template->created_on 	= $data['created_on'];
				$template->modified_on	= $data['modified_on'];

				$db->insertObject('#__webpush_notification_templates', $template);
			} else {
				$query = $db->getQuery(true)
					->select('*')
					->from($db->quoteName('#__webpush_notification_templates'))
					->where("{$db->quoteName('id')} = {$data['exstmsg']}");
				$template = $db->setQuery($query)->loadObject();
			}

			$subscribers = $this->getSubscribers($data['key']);
			$results = WebPushHelper::sendMessages($subscribers, $template->title, $template->message, [
				'url' => $template->url,
				'icon' => $template->icon,
			]);

			echo json_encode($results);
		}

		jexit();
	}

	function getSubscribers(array $ids)
	{
		$db = JFactory::getDbo();

		$ids = implode(',', $ids);
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__webpush_subscribers'))
			->where("{$db->quoteName('id')} IN ($ids)");

		return $db->setQuery($query)->loadObjectList();
	}
}
