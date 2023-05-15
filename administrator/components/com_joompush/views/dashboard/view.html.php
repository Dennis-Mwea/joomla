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
class JoompushViewDashboard extends JViewLegacy
{

	protected $today_subsciber;
	protected $total_subsciber;
	protected $today_notifications;
	protected $total_notifications;
	protected $SubscriberChart;
	protected $NotificationChart;

	
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
		
		$model = $this->getModel();

		$this->today_subsciber = $model->getTodaySubsciberCount();
		$this->total_subsciber = $model->getTotalSubsciberCount();
		$this->today_notifications = $model->getTodayNotifications();
		$this->total_notifications = $model->getTotalNotifications();
		$this->SubscriberChart = $model->getSubscriberChart();
		$this->NotificationChart = $model->getNotificationChart();
		$this->today_open_notifications = $model->getTodayOpenNotifications();
		$this->total_open_notifications = $model->getTotalOpenNotifications();
		$this->today_open_group_notifications = $model->getTodayOpenNotifications('group');
		$this->total_open_group_notifications = $model->getTotalOpenNotifications('group');
		$this->today_group_notifications = $model->getTodayNotifications('group');
		$this->total_group_notifications = $model->getTotalNotifications('group');
		$this->GroupNotificationChart = $model->getNotificationChart('group');

		JoompushHelpersJoompush::addSubmenu('dashboard');
		
	

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

		JToolBarHelper::title(JText::_('COM_JOOMPUSH_TITLE_DASHBOARD'), 'dashboard.png');
		
		$toolbar = JToolBar::getInstance('toolbar');
		
		$button = "<a class='btn btn-success'
		href='" . JRoute::_("index.php?option=com_joompush&view=quickpush") . "'>
		<i class='fa fa-paper-plane' aria-hidden='true'></i>  " . JText::_('COM_JOOMPUSH_QUICK_PUSH') . "</a>";
		
		$toolbar->appendButton('Custom', $button);
			
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_joompush');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_joompush&view=dashboard');

		$this->extra_sidebar = '';
	}

}
