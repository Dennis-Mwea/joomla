<?php

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

defined('_JEXEC') or die('Restricted access');

class WebPushModelWebPushes extends JModelList
{
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = ['id', 'name', 'value'];
		}

		parent::__construct($config, $factory);
	}

	protected function getListQuery(): JDatabaseQuery|string
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__webpush_configs'));
		if (!empty($search = $this->getState('filter.search'))) {
			$query->where("name LIKE %$search%")
				->orWhere("name LIKE %$search%");
		}
		$orderCol = $this->state->get('list.ordering', 'name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		return $query->order("{$db->escape($orderCol)} {$db->escape($orderDirn)}");
	}
}