<?php

defined('_JEXEC') or die('Restricted access');

class WebPushViewWebPushes extends JViewLegacy
{
	public function display($tpl = null): void
	{
		// Get application
		$app= JFactory::getApplication();
		$context = 'webpush.list.admin.webpush';

		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filter_order = $app->getUserStateFromRequest($context, 'filter_order', 'filter_order', 'name');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context, 'filter_order_Dir', 'filter_order_Dir', 'asc');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// What access permissions does this user have? What can (s)he do?
		$this->canDo = WebPushHelper::getActions();
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br>', $errors));
			return;
		}

		// Display the tool bar and number of found items
		$this->addToolBar();

		parent::display($tpl);

		JFactory::getDocument()->setTitle(JText::_('COM_WEBPUSH_ADMINISTRATOR'));
	}

	protected function addToolBar(): void
	{
		$title = JText::_('COM_WEBPUSH_MANAGER_WEBPUSHES');
		if ($this->pagination->total) {
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>({$this->pagination->total})</span>";
		}
		JToolbarHelper::title($title, 'webpush');

		if ($this->canDo->get('core.create')) {
			JToolbarHelper::addNew('webpush.add');
		}
		if ($this->canDo->get('core.edit')) {
			JToolbarHelper::editList('webpush.edit');
		}
		if ($this->canDo->get('core.delete')) {
			JToolbarHelper::deleteList('', 'webpushes.delete');
		}
		if ($this->canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolbarHelper::preferences('com_webpush');
		}
	}
}