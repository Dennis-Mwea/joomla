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

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Notificationtemplates list controller class.
 *
 * @since  1.6
 */
class JoompushControllerNotifications extends JControllerAdmin
{
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
	public function getModel($name = 'notifications', $prefix = 'JoompushModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	/**
	 * To delete notifications
	 */
	public function delete()
	{
		$input = JFactory::getApplication()->input;
		
		$data = $input->get('cid', array(), 'array');
		
		$model = $this->getModel();
		
		$result = $model->deleteNotifications($data);
		
		$this->setMessage(Jtext::_('COM_JOOMPUSH_NOTIFICATIONS_ITEMS_SUCCESS_DELETED'));
		$this->setRedirect('index.php?option=com_joompush&view=notifications');
	}
	
	
	/**
	 * To truncate notifications table
	 */
	public function purge()
	{
		$model = $this->getModel();
		
		$model->purgeNotifications();
		
		$this->setMessage(Jtext::_('COM_JOOMPUSH_NOTIFICATIONS_PURGED'));
		$this->setRedirect('index.php?option=com_joompush&view=notifications');
	}
}
