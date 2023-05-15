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
class JoompushViewSubscribers extends JViewLegacy
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

		JoompushHelpersJoompush::addSubmenu('subscribers');

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

		JToolBarHelper::title(JText::_('COM_JOOMPUSH_TITLE_SUBSCRIBERS'), 'users');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/subscriber';

		/*if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('subscriber.add', 'JTOOLBAR_NEW');
				JToolbarHelper::custom('subscribers.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('subscriber.edit', 'JTOOLBAR_EDIT');
			}
		}*/

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('subscribers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('subscribers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'subscribers.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('subscribers.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('subscribers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'subscribers.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('subscribers.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}
		
		if (isset($this->items[0]))
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('subscribers.push', 'jpush', 'jpush', 'COM_JOOMPUSH_SEND_NOTIFICATION', true);
		}
		if (isset($this->items[0]))
		{
			JToolBarHelper::divider();
			
			$toolbar = JToolBar::getInstance('toolbar');
			
			$button = "<a class='btn btn-warning'
			href='" . JRoute::_("index.php?option=com_joompush&view=sync") . "'>
			<span title='sync' class='icon-loop'>
			</span>  " . JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC') . "</a>";
			
			$toolbar->appendButton('Custom', $button);
		}
		
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_joompush');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_joompush&view=subscribers');

		$this->extra_sidebar = '';
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

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
			'a.`state`' => JText::_('JSTATUS'),
			'a.`key`' => JText::_('COM_JOOMPUSH_SUBSCRIBERS_KEY'),
			'a.`user_id`' => JText::_('COM_JOOMPUSH_SUBSCRIBERS_USER_ID'),
			'a.`browser`' => JText::_('COM_JOOMPUSH_SUBSCRIBERS_BROWSER'),
			'a.`created_on`' => JText::_('COM_JOOMPUSH_SUBSCRIBERS_CREATED_ON'),
		);
	}
}
