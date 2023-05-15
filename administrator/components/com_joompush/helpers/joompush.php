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

/**
 * Joompush helper.
 *
 * @since  1.6
 */
class JoompushHelpersJoompush
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			'<i class="fa fa-tachometer fa-fw" aria-hidden="true"></i> ' . JText::_('COM_JOOMPUSH_TITLE_DASHBOARD'),
			'index.php?option=com_joompush&view=dashboard',
			$vName == 'dashboard'
		);
		
		JHtmlSidebar::addEntry(
			'<i class="fa fa-user fa-fw" aria-hidden="true"></i> ' . JText::_('COM_JOOMPUSH_TITLE_SUBSCRIBERS'),
			'index.php?option=com_joompush&view=subscribers',
			$vName == 'subscribers'
		);
		
		JHtmlSidebar::addEntry(
			'<i class="fa fa-users fa-fw" aria-hidden="true"></i> ' .JText::_('COM_JOOMPUSH_TITLE_SUBSCRIBERGROUPS'),
			'index.php?option=com_joompush&view=subscribergroups',
			$vName == 'subscribergroups'
		);

		JHtmlSidebar::addEntry(
			'<i class="fa fa-eye fa-fw" aria-hidden="true"></i> ' . JText::_('COM_JOOMPUSH_TITLE_NOTIFICATIONTEMPLATES'),
			'index.php?option=com_joompush&view=notificationtemplates',
			$vName == 'notificationtemplates'
		);
		
		JHtmlSidebar::addEntry(
			'<i class="fa fa-envelope fa-fw" aria-hidden="true"></i> ' . JText::_('COM_JOOMPUSH_TITLE_NOTIFICATIONS'),
			'index.php?option=com_joompush&view=notifications',
			$vName == 'notifications'
		);
		
		JHtmlSidebar::addEntry(
			'<i class="fa fa-cogs fa-fw" aria-hidden="true"></i> ' . JText::_('COM_JOOMPUSH_TITLE_CONFIGS'),
			'index.php?option=com_joompush&view=configs',
			$vName == 'configs'
		);
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_joompush';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public function getLicence()
	{	
		$app = JFactory::getApplication();
		$current_date = JHtml::date('now', 'Y-m-d');
		
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_configs'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('licence'));
		$db->setQuery($query);

		$result = $db->loadObject();
		
		if($result)
		{
			$param = json_decode($result->value);
			
			$status = 0;
			
			if ($current_date <= $param->expires)
			{
				$status = 1;
			}
			
			return $status;
		}
		else
			{
			$app->enqueueMessage(Jtext::_('COM_JOOMPUSH_LICENCE_VIEW_INTRO'), 'error');
			$app->redirect('index.php?option=com_joompush&view=licence');
			}
			
	}


}
	
class JoompushHelper extends JoompushHelpersJoompush
{

}
