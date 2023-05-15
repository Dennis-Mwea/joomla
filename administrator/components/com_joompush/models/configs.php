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
class JoompushModelConfigs extends JModelList
{
	/**
	 * Method to save an Configs
	 *
	 * @param   array  &$config  An array of post data.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	function save($config)
	{
		if ($config)
		{
			$db =JFactory::getDBO();
			
			$db->truncateTable('#__joompush_configs');
			
			foreach($config as $name => $param) 
			{
				$configs = new stdClass();
				$configs->id 	 = '';
				$configs->name	 = $name;
				$configs->params = $param;
				
				$db->insertObject('#__joompush_configs', $configs);
			}
			
			 JPluginHelper::importPlugin('system');
			 $dispatcher = JDispatcher::getInstance();
			 $dispatcher->trigger('OnAfterConfigSave', array($config) );

			return 1;
		}
	}
	
	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_configs'));
		$db->setQuery($query);

		return $db->loadObjectList('name');
	}
}
