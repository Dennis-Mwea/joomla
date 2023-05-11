<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class webpushViewnotification_templates extends JViewLegacy
{
	protected $items;
	protected $state;
	protected $sidebar;
	protected $pagination;

	/**
	 * @param   string|null  $tpl
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display($tpl = null): void
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		WebPushHelper::addSubMenu('notification_templates');

		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * @return void
	 */
	protected function addToolBar()
	{
		$state = $this->get('State');
		$canDo = WebPushHelper::getActions();

		$title = JText::_('COM_WEBPUSH_MANAGER_WEBPUSHES');
		if ($this->pagination->total) {
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>({$this->pagination->total})</span>";
		}
		JToolbarHelper::title($title, 'webpush');

		// Check if the form exists before showing the add/edit buttons
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/views/notification_template')) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::addNew('notification_template.add');
				JToolbarHelper::custom('notification_templates.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE');
			}

			if ($canDo->get('core.edit')) {
				JToolBarHelper::editList('notification_template.edit');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('notification_templates.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH');
				JToolBarHelper::custom('notification_templates.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH');
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'notification_templates.delete');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('notification_templates.archive');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('notification_templates.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN');
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'notification_templates.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('notification_templates.trash');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_webpush');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_webpush&view=notification_templates');

		$this->extra_sidebar = '';
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);
	}

	protected function getSortFields(): array
	{
		return [
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`title`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TITLE'),
			'a.`message`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_MESSAGE'),
			'a.`url`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_URL'),
			'a.`created_on`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_CREATED_ON'),
		];
	}
}
