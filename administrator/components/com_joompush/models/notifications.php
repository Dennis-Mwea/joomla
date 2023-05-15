<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Joompush records.
 *
 * @since  1.6
 */
class JoompushModelNotifications extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'status', 'a.`status`',
				'title', 'a.`title`',
				'message', 'a.`message`',
				'icon', 'a.`icon`',
				'url', 'a.`url`',
				'sent_by', 'a.`sent_by`',
				'type', 'a.`type`',
				'sent_on', 'a.`sent_on`',
				'isread', 'a.`isread`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$type = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $type);
		
		$opened = $app->getUserStateFromRequest($this->context . '.filter.opened', 'filter_opened', '', 'string');
		$this->setState('filter.opened', $opened);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_joompush');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		//	$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__joompush_notifications` AS a');


		// Join over the user field 'sent_by'
		$query->select('`created_by`.name AS `sent_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`sent_by`');
		
		// Join over the user field 'group_id'
		$query->select('`g`.title AS `group_name`');
		$query->join('LEFT', '#__joompush_subscriber_groups AS `g` ON `g`.id = a.`group_id`');

		
		

		// Filter by type
		$type = $this->getState('filter.type');

		if ($type)
		{
			$query->where('a.type LIKE ' . $db->Quote($type));
		}
		
		// Filter by opened
		$opened = $this->getState('filter.opened');

		if ($opened == 'notread')
		{
			$query->where('a.isread = 0');
		}
		elseif($opened == 'read')
		{
			$query->where('a.isread > 0');
		}
		else
		{
			$query->where('a.isread != -1');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('( a.title LIKE ' . $search . '  OR  a.message LIKE ' . $search . ' )');
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
	
	/**
	 * To delete an items
	 *
	 */
	function deleteNotifications($ids)
	{
		if ($ids)
		{
			$cids = implode(',', $ids);
			
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			
			$conditions = array(
				$db->quoteName('id') . ' IN (' . $cids . ')',
			);

			$query->delete($db->quoteName('#__joompush_notifications'));
			$query->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();
			
			return $result;
		}
	}
	
	/**
	 * To truncate notifications table
	 *
	 */
	function purgeNotifications()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query = 'TRUNCATE TABLE ' .($db->quoteName('#__joompush_notifications'));

		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
	}
}
