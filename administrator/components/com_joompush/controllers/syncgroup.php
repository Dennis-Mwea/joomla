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

require_once JPATH_ROOT . '/administrator/components/com_joompush/models/subscribergroup.php';

/**
 * Subscriber controller class.
 *
 * @since  1.6
 */
class JoompushControllerSyncgroup extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'syncgroup';
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
	public function getModel($name = 'syncgroup', $prefix = 'JoompushModel', $config = array())
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
	function syncGroups()
	{
		$input = JFactory::getApplication()->input;
		$limit =  $input->get('limit');
		
		$session = JFactory::getSession();
		
		$last_id = 0;
		if ($session->get('joompush_gsync_id'))
		{
			$last_id = $session->get('joompush_gsync_id');
		}
		
		$current_datetime = JHtml:: date ('now', 'Y-m-d h:i:s', true);
		
		$JoompushModelSubscribergroup = new JoompushModelSubscribergroup;
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('b.*');
		$query->select('a.id as gid, a.title as gtitle');
		$query->from($db->quoteName('#__usergroups', 'a'));
		$query->join('LEFT', $db->quoteName('#__joompush_subscriber_groups', 'b') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.usergroup_id') . ')');
		$query->order($db->quoteName('a.id') . ' ASC');
		$db->setQuery($query, 0, $limit);

		$groups =  $db->loadObjectList();
		
		if ($last_id == 0)
		{
			foreach ($groups as $jgroup)
			{
				if (empty($jgroup->id))
				{
					
					$gropobj = new stdClass;
					$gropobj->id			= '';
					$gropobj->state 		= 1;
					$gropobj->title 		= $jgroup->gtitle;
					$gropobj->description	= 'Joomla ' . $jgroup->gtitle . ' User Group\'s JoomPush Subscribers Group';
					$gropobj->is_default 	= 0;
					$gropobj->usergroup_id 	= $jgroup->gid;
					$gropobj->created_by	= 0;
					$gropobj->created_on	= $current_datetime;
					$gropobj->modified_on	= $current_datetime;

					$db->insertObject('#__joompush_subscriber_groups',$gropobj,'id');
				}
			}
		}
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_subscribers'));
		$query->where($db->quoteName('id') . ' > '. $last_id);
		$query->order($db->quoteName('id') . ' ASC');
		
		$db->setQuery($query, 0, $limit);

		$subscribers =  $db->loadObjectList();
		
		if ($subscribers)
		{
			end($subscribers);         
			$last_key = key($subscribers);

			$session_last_id = $subscribers[$last_key]->id;
			
			$session->set('joompush_gsync_id', $session_last_id);
			
			$set_topic_array = array();
			
			foreach ($subscribers as $subscriber)
			{
				$sub_jgroup = explode(',', 	$subscriber->usergroup_id);
				
				foreach ($sub_jgroup as $jgid)
				{
					if ($jgid)
					{
						$query = $db->getQuery(true);
						$query->select('id');
						$query->from($db->quoteName('#__joompush_subscriber_groups'));
						$query->where($db->quoteName('usergroup_id') . ' = '. $jgid);
						
						$db->setQuery($query);
						$jpgid = $db->loadResult();
			
						$set_topic_array[$jpgid][$subscriber->id] = $subscriber->key;
					}
				}
			}	
			
			if ($set_topic_array)
			{
				foreach ($set_topic_array as $ky=>$topic)
				{
					
					$ids = array();
					$keys = array();
					
					foreach($topic as $j=>$key)
					{
						$ids[] = $j; 
						$keys[$j] = $key; 
					}
						
					$JoompushModelSubscribergroup->setTopic($ky, $ids, $keys, 1);
				}
			}
			
			echo $session_last_id;
		}
		else
		{
			$session->clear('joompush_gsync_id');
			echo 'done';
		}
		
		jexit();
	}	
}
