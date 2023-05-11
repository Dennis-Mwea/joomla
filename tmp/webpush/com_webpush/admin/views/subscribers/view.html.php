<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class WebPushViewSubscribers extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * @param $tpl
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app= JFactory::getApplication();
		$context = 'webpush.list.admin.webpush';

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filter_order = $app->getUserStateFromRequest($context, 'filter_order', 'filter_order', 'id');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context, 'filter_order_Dir', 'filter_order_Dir', 'asc');

		// Check for errors
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		WebPushHelper::addSubMenu('subscribers');
		$this->sidebar = JHtmlSidebar::render();

		$this->addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar()
	{
		$title = JText::_('COM_WEBPUSH_MANAGER_WEBPUSHES');
		if ($this->pagination->total) {
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>({$this->pagination->total})</span>";
		}
		JToolbarHelper::title($title, 'webpush');

		$canDo = WebPushHelper::getActions();
		if (isset($this->items[0])) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('subscribers.push', 'jpush', 'jpush', 'COM_WEBPUSH_SEND_NOTIFICATION');
		}

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_webpush');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_webpush&view=subscribers');

		$this->extra_sidebar = '';
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);
	}
}
