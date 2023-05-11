<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class WebPushModelNotification_Templates extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id', 'a.`id`',
				'state', 'a.`state`',
				'title', 'a.`title`',
				'message', 'a.`message`',
				'icon', 'a.`icon`',
				'url', 'a.`url`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'created_on', 'a.`created_on`',
				'modified_on', 'a.`modified_on`',
			];
		}

		parent::__construct($config);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($this->getState('list.select', 'DISTINCT a.*'))
			->from('`#__webpush_notification_templates` as a')
			->select('`created_by`.name AS `created_by`')
			->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`')
			->select('`modified_by`.name AS `modified_by`')
			->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		$published = $this->getState('filter.state');
		if (is_numeric($published)){
			$query->where('state = ' . (int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		$search = $this->getState('filter.search');
		if (!empty($search)){
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.title LIKE ' . $search . '  OR  a.message LIKE ' . $search . ' )');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}
}
