<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class WebPushControllerSubscribers extends JControllerAdmin
{
	/**
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return bool|JModelLegacy
	 */
	public function getModel($name = 'Subscriber', $prefix = 'WebPushModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to send push notification to selected subscriber.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.0
	 */
	function push()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');

		$pks   = implode(",", $pks);

		$this->setRedirect("index.php?option=com_webpush&view=subscribers&key=$pks");
	}
}
