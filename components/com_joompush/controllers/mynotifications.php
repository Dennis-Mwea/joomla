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

/**
 * Mynotifications list controller class.
 *
 * @since  1.6
 */
class JoompushControllerMynotifications extends JoompushController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Mynotifications', $prefix = 'JoompushModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	function setSubscriber()
	{
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$db = JFactory::getDbo();
		
		$params = JComponentHelper::getParams('com_joompush');
		
		$key 	  = $jinput->get('key','','RAW');
		$isClient = $jinput->get('IsClient','','RAW');
		$userid   = $jinput->get('Userid', 0, 'INT');
		
		$juser = JFactory::getUser($userid);
		$usergroups = implode(',',$juser->groups);
		
		if ($key)
		{
			$current_datetime = JHtml:: date ('now', 'Y-m-d h:i:s', true);
			
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__joompush_subscribers'));
			$query->where($db->quoteName('key') . ' LIKE '. $db->quote($key));
			$db->setQuery($query);
			$result = $db->loadResult();
			
			if ($result == 0)
			{
				// Create and populate an content object.
				$jpuser = new stdClass();
				$jpuser->id 			= '';
				$jpuser->state 			= 1;
				$jpuser->key 			= $key; 
				$jpuser->user_id 		= $userid;
				$jpuser->usergroup_id 	= $usergroups;
				$jpuser->browser 		= JBrowser::getInstance()->getBrowser();
				
				if ($isClient)
				{
					$jpuser->type 			= $isClient;
				}
			
				
				$jpuser->created_by 	= $userid;
				$jpuser->modified_by 	= $userid;
				$jpuser->created_on 	= $current_datetime;
				$jpuser->modified_on	= $current_datetime;

				// Insert the object into the user profile table.
				if ($db->insertObject('#__joompush_subscribers', $jpuser, 'id'))
				{
					
					// Start For Welcome Notification

					if ($params->get('notificationOnSubscribe'))
					{
						require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
						
						$JoompushHelpersJpush = new JoompushHelpersJpush;
						
						$welcomePushMsg  	= new stdClass();
						$welcomeTemplate	= new stdClass();
						$welcomeKey			= array();
						
						$welcomeKey[] 		= $key;
						
						$welcomeTemplate->title 	= $params->get('welcomeNotificationTitle');
						$welcomeTemplate->message	= $params->get('welcomeNotificationMessage');
						$welcomeTemplate->icon 		= $params->get('welcomeNotificationIcon');
						
						$welcomePushMsg->template	= $welcomeTemplate;
						$welcomePushMsg->key		= $welcomeKey;
						
						$JoompushHelpersJpush::jpush($welcomePushMsg);
					}
					
					// End For Welcome Notification
					
					$jp_user_id = $db->insertid();
					
					$query = $db->getQuery(true);
					$query
						->select('id')
						->from($db->quoteName('#__joompush_subscriber_groups'))
						->where($db->quoteName('is_default') . ' = 1');
						
					$db->setQuery($query);
					$group_id = $db->loadResult();
					
					require_once JPATH_ROOT . '/administrator/components/com_joompush/models/subscribergroup.php';
						
					$JoompushModelSubscribergroup = new JoompushModelSubscribergroup;
					
					if (!empty($group_id))
					{
						$key_array = array();
						$key_array[$jp_user_id] = $key;
						
						$JoompushModelSubscribergroup->setTopic($group_id, array($jp_user_id), $key_array, 1);
					}
					
					// admin subscribrs
					
					$section 	 = $params->get('section');
					
					if (($section == 2 || $section == 3) && $isClient == 'admin')
					{
						$query = $db->getQuery(true);
						$query
							->select('id')
							->from($db->quoteName('#__joompush_subscriber_groups'))
							->where($db->quoteName('is_default') . ' = 2');
						$db->setQuery($query);
						$admin_group_id = $db->loadResult();
						
						if (!empty($admin_group_id))
						{
							$key_array = array();
							$key_array[$jp_user_id] = $key;
							
							$JoompushModelSubscribergroup->setTopic($admin_group_id, array($jp_user_id), $key_array, 1);
						}
					}
					
					// To set group 
					
					if ($juser->groups)
					{
						$set_topic_array = array();
						
						foreach ($juser->groups as $jgid)
						{
							$query = $db->getQuery(true);
							$query->select('id');
							$query->from($db->quoteName('#__joompush_subscriber_groups'));
							$query->where($db->quoteName('usergroup_id') . ' = '. $jgid);
							
							$db->setQuery($query);
							$jpgid = $db->loadResult();
							
							if ($jpgid)
							{
								$set_topic_array[$jpgid][$jp_user_id] = $key;
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
					} 
					
				}
				
				echo $jp_user_id;
			}
			else
			{
				echo $result;
			}
		}
		
		jexit();
	}
	
	/**
     * to track push notification
     *
     * @return  string
     */
	function track()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		$code = $jinput->get('code','','RAW');
		$jpurl = $jinput->get('jpurl','','RAW');
		
		if ($code)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('isread') . ' = isread + 1'
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('code') . ' = ' . $db->quote($code)
			);
			 
		
			$query->update($db->quoteName('#__joompush_notifications'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		$app->redirect($jpurl);
	}

}
