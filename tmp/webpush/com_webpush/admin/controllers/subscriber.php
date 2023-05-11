<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Subscriber controller class.
 *
 * @since  1.6
 */
class WebPushControllerSubscriber extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'subscribers';

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
	public function getModel($name = 'Subscriber', $prefix = 'WebPushModel', $config = array())
	{
		return parent::getModel($name, $prefix, ['ignore_request' => true]);
	}

	/**
	 * Method to send push notification to selected subscriber.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.0
	 */
	public function sendNotification()
	{
		$input = JFactory::getApplication()->input;

		$data = $input->get('jform', array(), 'array');

		$model = $this->getModel();

		$model->sendNotification($data);
	}
}
