<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class WebPushViewNotification_Template extends JViewLegacy
{
	protected $state;

	protected $item;

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

		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out)) {
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		} else {
			$checkedOut = false;
		}

		$canDo = WebPushHelper::getActions();

		JToolBarHelper::title(JText::_('COM_WEBPUSH_TITLE_NOTIFICATION_TEMPLATE'), 'eye');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create')))) {
			JToolBarHelper::apply('notification_template.apply');
			JToolBarHelper::save('notification_template.save');
		}

		if (!$checkedOut && ($canDo->get('core.create'))) {
			JToolBarHelper::custom('notification_template.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('notification_template.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id)) {
			JToolBarHelper::cancel('notification_template.cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('notification_template.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
