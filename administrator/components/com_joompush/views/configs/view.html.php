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
class JoompushViewConfigs extends JViewLegacy
{
	protected $items;

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
		$this->items = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JoompushHelpersJoompush::addSubmenu('configs');

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
		$canDo = JoompushHelpersJoompush::getActions();

		JToolBarHelper::title(JText::_('COM_JOOMPUSH_TITLE_CONFIGS'), 'equalizer');

		// If not checked out, can save the item.
		if (($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('configs.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('configs.save', 'JTOOLBAR_SAVE');
		}
		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('configs.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('configs.cancel', 'JTOOLBAR_CLOSE');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_joompush&view=configs');

		$this->extra_sidebar = '';
	}
}
