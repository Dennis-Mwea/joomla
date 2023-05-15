<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Subscriber controller class.
 *
 * @since  1.6
 */
class JoompushControllerSync extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'sync';
		parent::__construct();
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'sync', $prefix = 'JoompushModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	/**
	 * Method to sync subscriber.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	function syncSubscribers()
	{
		$input = JFactory::getApplication()->input;
		$limit =  $input->get('limit');
		
		$session = JFactory::getSession();
		
		$last_id = 0;
		if ($session->get('joompush_sync_id'))
		{
			$last_id = $session->get('joompush_sync_id');
		}
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_subscribers'));
		$query->where($db->quoteName('id') . ' > '. $last_id);
		
		$db->setQuery($query, 0, $limit);

		$subscribers =  $db->loadObjectList();
		
		if ($subscribers)
		{
			end($subscribers);         
			$last_key = key($subscribers);

			$session_last_id = $subscribers[$last_key]->id;
			
			$session->set('joompush_sync_id', $session_last_id);
			
			require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
			
			$JoompushHelpersJpush = new JoompushHelpersJpush;
				
			$push_data	=  new stdClass();
			$template 	=  new stdClass();
			
			$config = JFactory::getConfig();
			
			$template->title = $config->get( 'sitename' );
			$template->message = '';
			$template->icon = '';
			$template->url = JURI::root();
			$template->dry_run = 1;
			
			
			$push_data->template = $template;
				
			$key = array();
			$key_ids = array();
			
			foreach($subscribers as $k=>$subscriber)
			{
				$key[]	= $subscriber->key;
				$key_ids[] 	= $subscriber->id;
			}
			
			$push_data->key = $key;
			
			$result = $JoompushHelpersJpush::jpush($push_data);
			
			$result = json_decode($result);
			
			$archive_key_ids = array();
			
			foreach ($push_data->key as $k=>$val)
			{	
				if (isset($result->results[$k]->error))
				{
					if ($result->results[$k]->error == 'NotRegistered' || $result->results[$k]->error == 'InvalidRegistration')
					{
						$archive_key_ids[] = $key_ids[$k];
					}
				}
			}
			
			if ($archive_key_ids)
			{
				$trash_keys = implode(",",$archive_key_ids);
				
				$query = $db->getQuery(true);
				
				$query->delete($db->quoteName('#__joompush_subscribers'));
				$query->where($db->quoteName('id') . ' IN (' . $trash_keys . ')');
				 
				$db->setQuery($query);
				$db->execute();
				
				$query = $db->getQuery(true);
				
				$query->delete($db->quoteName('#__joompush_subscriber_group_map'));
				$query->where($db->quoteName('subscriber_id') . ' IN (' . $trash_keys . ')');
				 
				$db->setQuery($query);
				$db->execute();
			}
			
			echo $session_last_id;
		}
		else
		{
			$session->clear('joompush_sync_id');
			echo 'done';
		}
		
		jexit();
	}	
}
