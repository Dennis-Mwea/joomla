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
 * Licence list controller class.
 *
 * @since  1.6
 */
class JoompushControllerLicence extends JControllerAdmin
{
	/**
	 * Method to save the submitted Licence values
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	 
	function verify()
	{
		$jinput = JFactory::getApplication()->input;
		
		$license_key = $jinput->post->get('license_key'); 

		$model = $this->getModel();
		$result = $model->verify($license_key);
	}
		
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $Licence  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'Licence', $prefix = 'JoompushModel', $Licence = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
}

