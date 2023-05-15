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
 * View to edit
 *
 * @since  1.6
 */
class JoompushViewSync extends JViewLegacy
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
		if (count($errors = $this->get('Errors')))
		{
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
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = JoompushHelpersJoompush::getActions();

		JToolBarHelper::title(JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC'), 'loop');
		
		$toolbar = JToolBar::getInstance('toolbar');
		
		$button = "<a class='btn'
		href='" . JRoute::_("index.php?option=com_joompush&view=subscribers") . "'>
		<span title='sync' class='icon-back'>
		</span>  " . JText::_('COM_JOOMPUSH_TITLE_SUBSCRIBERS') . "</a>";
		$toolbar->appendButton('Custom', $button);
			
	}
}
