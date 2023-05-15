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

jimport('joomla.application.component.view');

/**
 * View class for a list of Joompush.
 *
 * @since  1.6
 */
class JoompushViewNotifications extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

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
		$JoompushHelpersJoompush = new JoompushHelpersJoompush();
		$this->status = $JoompushHelpersJoompush->getLicence();
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JoompushHelpersJoompush::addSubmenu('notifications');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = JoompushHelpersJoompush::getActions();

		JToolBarHelper::title(JText::_('COM_JOOMPUSH_TITLE_NOTIFICATIONS'), 'envelope');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/notifications';

		// Show trash and delete for components that uses the state field
			JToolBarHelper::deleteList('', 'notifications.delete', 'COM_JOOMPUSH_NOTIFICATIONS_DELETE');
			JToolBarHelper::divider();
			
			JToolBarHelper::custom('notifications.purge', 'purge', 'icon-32-unpublish.png', 'COM_JOOMPUSH_NOTIFICATIONS_PURGE', false);
			JToolBarHelper::divider();

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_joompush');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_joompush&view=notifications');

		$this->extra_sidebar = '';
		
		$options = array('group'=> JText::_('COM_JOOMPUSH_NOTIFICATIONS_FILTER_TYPE_GROUP'),
		'user'=> JText::_('COM_JOOMPUSH_NOTIFICATIONS_FILTER_TYPE_USER') );
		
		
		JHtmlSidebar::addFilter(
			JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TYPE'),
			'filter_type',
			JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.type'), true)
		);
		
		$options = array('read'=> JText::_('COM_JOOMPUSH_NOTIFICATIONS_READ'),
		'notread'=> JText::_('COM_JOOMPUSH_NOTIFICATIONS_NOT_READ') );
		
		
		JHtmlSidebar::addFilter(
			JText::_('COM_JOOMPUSH_NOTIFICATIONS_ISREAD_FILTER'),
			'filter_opened',
			JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.opened'), false)
		);
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`status`' => JText::_('JSTATUS'),
			'a.`title`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TITLE'),
			'a.`message`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_MESSAGE'),
			'a.`url`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_URL'),
			'a.`sent_on`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTS_SENT_ON'),
			'a.`type`' => JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TYPE'),
		);
	}
}
