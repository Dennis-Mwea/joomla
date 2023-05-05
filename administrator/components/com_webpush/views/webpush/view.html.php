<?php

defined('_JEXEC') or die('Restricted access');

class WebPushViewWebPush extends JViewLegacy
{
	protected $form = null;

	protected $item;

	protected $script;

	protected $canDo;

	/**
	 * @param $tpl
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display($tpl = null): void
	{
		$this->form   = $this->get('Form');
		$this->item   = $this->get('Item');
		$this->script = $this->get('Script');

		$this->canDo = WebPushHelper::getActions($this->item->id);
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br>', $errors));

			return;
		}

		// Set the toolbar
		$this->addToolBar();

		parent::display($tpl);

		$this->setDocument();
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	protected function addToolBar(): void
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
		JToolbarHelper::title(
			JText::_($isNew ? 'COM_WEBPUSH_MANAGER_WEBPUSH_NEW' : 'COM_WEBPUSH_MANAGER_WEBPUSH_EDIT'),
			'helloworld'
		);
		if ($isNew)
		{
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::apply('webpush.apply');
				JToolBarHelper::save('webpush.save');
				JToolBarHelper::custom(
					'webpush.save2new',
					'save-new.png',
					'save-new_f2.png',
					'JTOOLBAR_SAVE_AND_NEW',
					false
				);
			}
			JToolBarHelper::cancel('webpush.cancel');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('webpush.apply');
				JToolBarHelper::save('webpush.save');

				if ($this->canDo->get('core.create'))
				{
					JToolBarHelper::custom(
						'webpush.save2new',
						'save-new.png',
						'save-new_f2.png',
						'JTOOLBAR_SAVE_AND_NEW',
						false
					);
				}
			}
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::custom(
					'webpush.save2copy',
					'save-copy.png',
					'save-copy_f2.png',
					'JTOOLBAR_SAVE_AS_COPY',
					false
				);
			}
			JToolBarHelper::cancel('webpush.cancel', 'JTOOLBAR_CLOSE');

		}
	}

	protected function setDocument(): void
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle(JText::_($isNew ? 'COM_WEBPUSH_WEBPUSH_CREATING' : 'COM_WEBPUSH_WEBPUSH_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_webpush/views/webpush/submit-button.js");
		JText::script('COM_WEBPUSH_WEBPUSH_ERROR_UNACCEPTABLE');
	}
}
