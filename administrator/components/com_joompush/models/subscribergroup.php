<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Joompush model.
 *
 * @since  1.6
 */
class JoompushModelSubscribergroup extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_JOOMPUSH';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_joompush.subscribergroup';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Subscribergroup', $prefix = 'JoompushTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_joompush.subscribergroup', 'subscribergroup',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joompush.edit.subscribergroup.data', array());

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

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Method to duplicate an Subscribergroup
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_joompush'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REPORTER_ACCESS_WARNING'), 'warning');
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}
				

				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__joompush_subscriber_groups');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   gid  group id
	 * @param   pks  PK of subscribers table
	 * @param   kds  Key of subscriber
	 *
	 * @return void
	 *
	 * @since    1.0
	 */
	function setTopic($gid, $pks, $kds, $isDefault = 0)
	{
		$db = JFactory::getDbo();
		
		// Get existing suscriber group ids
		
		$query = $db->getQuery(true);
		
		$query->select($db->quoteName(array('subscriber_id')));
		$query->from($db->quoteName('#__joompush_subscriber_group_map'));
		$query->where($db->quoteName('group_id') . ' = '. $gid);
		$db->setQuery($query);
		$old_pks = $db->loadColumn();

		if ($isDefault == 0)
		{
			$removed = array_diff($old_pks, $pks);
		}

		$added = array_diff($pks, $old_pks);

		require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
		$JoompushHelpersJpush = new JoompushHelpersJpush;
		
		
		if ($added)
		{
			$keys = array();
			
			foreach ($added as $new)
			{
				$keys[] = $kds[$new];
			} 
		
			$result = $JoompushHelpersJpush::jaddTopicSubscription($gid, $keys);
				
			$result = json_decode($result);
			
			$result = $result->results;
			
			$k = 0;
			foreach ($result as $value)
			{
				if (isset($value->error))
				{
					unset($added[$k]);
				}
				
				$k++;
			}
			
			if ($added)
			{

				$query = $db->getQuery(true);
				
				$columns = array('subscriber_id', 'group_id');
				
				$query->insert($db->quoteName('#__joompush_subscriber_group_map'));
				$query->columns($db->quoteName($columns));
				
				foreach ($added as $pk)
				{
					$query->values($pk. ',' . $gid);
				}
				
				$db->setQuery($query);
				$db->execute();
			}
		}
		
				
		if (isset($removed))
		{
			$removed_keys = array();
			
			foreach ($removed as $rm)
			{
				$removed_keys[] = $kds[$rm];
			} 
			
			$result = $JoompushHelpersJpush::jremoveTopicSubscription($gid, $removed_keys);
				
			$result = json_decode($result);
			$result = $result->results;
			
			$k = 0;
			foreach ($result as $value)
			{
				if ($value->error)
				{
					unset($removed[$k]);
				}
				
				$k++;
			}

			if ($removed)
			{
				$query = $db->getQuery(true);
				
				$conditions = array(
					$db->quoteName('group_id') . ' = ' . $gid, 
					$db->quoteName('subscriber_id') . ' IN (' . $db->quote( implode(",", $removed) ) . ')'
				);
				
				$query->delete($db->quoteName('#__joompush_subscriber_group_map'));
				$query->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		if ($isDefault == 0)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOOMPUSH_SUCCESS_GROUP_ADD'), 'Message'); 
		}
	}
}
