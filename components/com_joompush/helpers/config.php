<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
defined('_JEXEC') or die;

/**
 * Class JoompushHelpersConfig
 *
 * @since  1.0
 */
class JoompushHelpersConfig
{
    /**
     * Gets the config params
     *
     * @return  Array
     */
    public static function getConfig()
    {
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_configs'));
		$db->setQuery($query);

		return $db->loadObjectList('name');
	}
}
