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
class JoompushModelQuickpush extends JModelAdmin
{
	
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
		
		return $form;
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

			if ($data['jpgroup'])
			{
				foreach ($data['jpgroup'] as $gid)
				{
					$track_code = $template->id . '_' . md5(uniqid(rand(), true)); // For track notification
					$pushdata->code = $track_code;
				
					$pushdata->gid	= $gid;
					
					$ntype = 'group';
					
					$subscribers_data 	= new stdClass();
					
					$JoompushHelpersJoompush->saveNotification($subscribers_data, $gid, $ntype, $template, 1, 'com_joompush', 0, $track_code);
					
					$result = $JoompushHelpersJpush::jtopicPush($pushdata);
				}
				
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}
	
	/**
	 * Prepare and send existing JoomPush Group list
	 *
	 * @return String
	 *
	 * @since    1.6
	 */
	function getJoopushGroups()
	{
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName('#__joompush_subscriber_groups'));
		$query->where($db->quoteName('state') . ' = 1');
		$db->setQuery($query);

		$data =  $db->loadObjectList();

		$options = array();

		foreach($data as $key=>$value) :
			$options[] = JHTML::_('select.option', $value->id, $value->title);
		endforeach;

		return $group_list = JHTML::_('select.genericlist', $options, 'jform[jpgroup][]', 'class="inputbox" multiple="multiple"' , 'value', 'text', 0);
	}
}
