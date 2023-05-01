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
 * Class JoompushFrontendHelpersite
 *
 * @since  1.6
 */
class JoompushHelpersJoompushsite
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_joompush/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_joompush/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'JoompushModel');
		}

		return $model;
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
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_joompush'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_joompush') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
    
    /**
     * save the notification
     *
     * @return  Array
     */
    public function saveNotification($subscribers_data, $gid = 0, $ntype, $template, $sent = 0, $client, $client_id = 0, $track_code)
    {
		$sent_by = JFactory::getUser()->id;
		$sent_on = JHtml:: date ('now', 'Y-m-d h:i:s', true);
		
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		
		$columns = array('subscriber_id', 'key', 'group_id', 'type', 'title', 'message', 'icon', 'url', 'sent', 'isread', 'sent_by', 'sent_on', 'client', 'client_id', 'code');
				
		$query->insert($db->quoteName('#__joompush_notifications'));
		$query->columns($db->quoteName($columns));
		
		if (isset($subscribers_data->key))
		{
			foreach ($subscribers_data->key as $key)
			{
				$query->values('0, ' . $db->quote($key) . ', ' . $gid . ', ' . $db->quote($ntype) . ', ' .  $db->quote($template->title) . ', ' . $db->quote($template->message) . ', ' . $db->quote($template->icon) . ', ' . $db->quote($template->url) . ', ' . $sent . ', 0, ' . $sent_by . ', '. $db->quote($sent_on) . ', '. $db->quote($client) . ', '. $client_id . ', ' . $db->quote($track_code));
			}
		}
		else
		{
			$query->values('0, "" , ' . $gid . ', ' . $db->quote($ntype) . ', ' .  $db->quote($template->title) . ', ' . $db->quote($template->message) . ', ' . $db->quote($template->icon) . ', ' . $db->quote($template->url) . ', ' . $sent . ', 0, ' . $sent_by . ', '. $db->quote($sent_on) . ', '. $db->quote($client) . ', '. $client_id . ', ' . $db->quote($track_code));
		}
		
		$db->setQuery($query);
		$db->execute();
	}
}
