<?php

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class WebPushControllerNotification_Templates extends JControllerAdmin
{
	/**
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return bool|JModelLegacy
	 */
	public function getModel($name = 'Notification_Template', $prefix = 'WebPushModel', $config = [])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function duplicate()
	{
		$app = JFactory::getApplication();
		JSession::checkToken() or $app->close(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');
		try {
			if (empty($pks)) {
				throw new Exception(JText::_('COM_WEBPUSH_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_WEBPUSH_ITEMS_SUCCESS_DUPLICATED'));
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_webpush&view=notification_templates');
	}
}
