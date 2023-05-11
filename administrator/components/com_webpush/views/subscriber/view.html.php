<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class WebPushViewSubscriber extends JViewLegacy
{
	protected $exsting_msg;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->exsting_msg  = $this->get('Templates');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
