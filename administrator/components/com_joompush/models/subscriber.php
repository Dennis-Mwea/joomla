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

require_once JPATH_ROOT . '/components/com_joompush/helpers/joompush.php';
/**
 * Joompush model.
 *
 * @since  1.6
 */
class JoompushModelSubscriber extends JModelAdmin
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
	public $typeAlias = 'com_joompush.subscriber';

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
	public function getTable($type = 'Subscriber', $prefix = 'JoompushTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$data = JFactory::getApplication()->getUserState('com_joompush.edit.subscriber.data', array());

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
	 * Method to duplicate an Subscriber
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
				$db->setQuery('SELECT MAX(ordering) FROM #__joompush_subscribers');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
	
	/**
	 * Prepare and send existing message template list
	 *
	 * @return String
	 *
	 * @since    1.6
	 */
	function getTemplates()
	{
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName('#__joompush_notification_templates'));
		$query->where($db->quoteName('state') . ' = 1');
		$db->setQuery($query);

		$data =  $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('COM_JOOMPUSH_SELECT_TEMPLATE'));

		foreach($data as $key=>$value) :
			$options[] = JHTML::_('select.option', $value->id, $value->title);
		endforeach;

		return $category_list = JHTML::_('select.genericlist', $options, 'jform[exstmsg]', 'class="inputbox"', 'value', 'text', 0);
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
			'com_joompush.notificationtemplate', 'notificationtemplate',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

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
		if($key)
		{
			$db = JFactory::getDbo();
 
			$query = $db->getQuery(true);
			$query->select($db->quoteName('key'));
			$query->from($db->quoteName('#__joompush_subscribers'));
			$query->where($db->quoteName('state') . ' = 1');
			$query->where($db->quoteName('id') . ' IN ('. $key . ')');
			$db->setQuery($query);

			return $db->loadColumn();
		}
	}
	
	/**
	 * Method to send push notification to selected subscriber.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	function sendNotification($data)
	{
		if ($data)
		{
			$db = JFactory::getDbo();
			
			if ($data['isnew'])
			{
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
				 
				$db->insertObject('#__joompush_notification_templates', $template);
			}
			else
			{
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from($db->quoteName('#__joompush_notification_templates'));
				$query->where($db->quoteName('state') . ' = 1');
				$query->where($db->quoteName('id') . ' = '. $data['exstmsg']);
				$db->setQuery($query);

				$template =  $db->loadObjectList();
				$template = $template[0];
			}
			
			require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
			
			$JoompushHelpersJpush = new JoompushHelpersJpush;
			$JoompushHelpersJoompush = new JoompushHelpersJoompushsite;
			
			$pushdata = new stdClass();
			$pushdata->template = $template;
			
			
			if ($data['gid'])
			{
				$track_code = $template->id . '_' . md5(uniqid(rand(), true)); // For track notification
				$pushdata->code = $track_code;
			
				$pushdata->gid	= $data['gid'];
				
				$ntype = 'group';
				
				$subscribers_data 	= new stdClass();
				
				$JoompushHelpersJoompush->saveNotification($subscribers_data, $data['gid'], $ntype, $template, 1, 'com_joompush', 0, $track_code);
				
				$results = $JoompushHelpersJpush::jtopicPush($pushdata);
			}
			else
			{
				$ntype = 'user';
				
				$subscribers_data 	= new stdClass();
				$results			= array();
				
				$subscribers_data->key 	= $data['key'];
				
				foreach ($subscribers_data->key as $key)
				{
					$pushdata->key	= array($key);
					$track_code= $key;
					$pushdata->code = $track_code;
					
					$JoompushHelpersJoompush->saveNotification($subscribers_data, 0, $ntype, $template, 1, 'com_joompush', 0, $track_code);
					
					$results[$key] = $JoompushHelpersJpush::jpush($pushdata);
				}
			}
			
			
			if ($data['gid'] == 0)
			{
				$new_result = new stdClass();
				$new_result->total = count($data['key']);
				
				$fail	 = 0;
				$success = 0;
				
				foreach($results as $k=>$result)
				{
					$result = json_decode($result);
					
					$archive_keys = array();
			
					if (isset($result->results[0]->error))
					{
						
						if ($result->results[0]->error == 'NotRegistered' || $result->results[0]->error == 'InvalidRegistration')
						{
							$query = $db->getQuery(true);
							
							$query->delete($db->quoteName('#__joompush_subscribers'));
							$query->where($db->quoteName('key') . ' = ' . $db->quote( $k ));
							 
							$db->setQuery($query);
							$db->execute();
						}
						
						$fail++;
					}
					else
					{
						$success++;
					}
				}
				
				$new_result->success = $success;
				$new_result->failure = $fail;
				
				echo json_encode($new_result);
			}
			else
			{
				echo $results;
			}
			
			jexit();
		}
	}
	
	/**
	 * Method to subscribers according to FCM Registered.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	 /*
	function syncSubscriber($keys, $results)
	{
		$archive_keys = array();
		
		foreach ($keys as $k=>$val)
		{
			if ($results[$k]->error == 'NotRegistered')
			{
				$archive_keys[] = $val;
			}
		}
		
		if ($archive_keys)
		{
			$db = JFactory::getDbo();
 
			$query = $db->getQuery(true);
			
			$conditions = array(
				$db->quoteName('key') . ' IN (' . $db->quote( implode(",", $archive_keys) ) . ')'
			);
			 
			$query->delete($db->quoteName('#__joompush_subscribers'));
			$query->where($conditions);
			 
			$db->setQuery($query);
			 
			$result = $db->execute();
		}
		
	}*/
}
