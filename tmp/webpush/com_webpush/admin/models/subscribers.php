<?php

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class WebPushModelSubscribers extends JModelList
{
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'subscribable_id', 'endpoint', 'public_key', 'auth_token', 'content_encoding',
			];
		}

		parent::__construct($config, $factory);
	}

	protected function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__webpush_subscribers'));
//		if (!empty($search = $this->getState('filter.search'))) {
//			$query->where("name LIKE %$search%")
//				->orWhere("name LIKE %$search%");
//		}
		$orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'asc');

		return $query->order("{$db->escape($orderCol)} {$db->escape($orderDirn)}");
	}
}
