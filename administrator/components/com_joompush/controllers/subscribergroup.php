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

/**
 * Subscribergroup controller class.
 *
 * @since  1.6
 */
class JoompushControllerSubscribergroup extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'subscribergroups';
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
	public function getModel($name = 'subscribergroup', $prefix = 'JoompushModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	function addToGroup()
	{
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$kds   = $input->post->get('kid', array(), 'array');
		$gid   = $input->get('gid', '', 'INT');
		
		$model = $this->getModel();
		$model->setTopic($gid, $pks, $kds);
		
		$this->setRedirect('index.php?option=com_joompush&view=subscribers&tmpl=component&gid=' . $gid);
	}
}
