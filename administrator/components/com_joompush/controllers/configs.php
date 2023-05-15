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
 * Configs list controller class.
 *
 * @since  1.6
 */
class JoompushControllerConfigs extends JControllerAdmin
{
	/**
	 * Method to save the submitted configs values
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	 
	function save()
	{
		$jinput = JFactory::getApplication()->input;
		
		$post = $jinput->post->getArray(); 
		
		$model = $this->getModel();
		$result = $model->save($post['jform']);

		if ($result)
		{
			$this->setMessage(Jtext::_('COM_JOOMPUSH_CONFIG_SAVE_SUCCESS'));
			$this->setRedirect('index.php?option=com_joompush&view=dashboard');
		}
	}
	
	/**
	 * Method to save the submitted configs values 
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	function apply()
	{
		$jinput = JFactory::getApplication()->input;
		
		$post = $jinput->post->getArray(); 
		
		$model = $this->getModel();
		$result = $model->save($post['jform']);

		if ($result)
		{
			$this->setMessage(Jtext::_('COM_JOOMPUSH_CONFIG_SAVE_SUCCESS'));
			$this->setRedirect('index.php?option=com_joompush&view=configs');
		}
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
	public function getModel($name = 'configs', $prefix = 'JoompushModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	/**
	 * Method to save the submitted configs values 
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	function cancel()
	{
		$this->setRedirect('index.php?option=com_joompush&view=dashboard');
	}
}
