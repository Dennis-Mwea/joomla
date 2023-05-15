<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Joompush records.
 *
 * @since  1.6
 */
class JoompushModelDashboard extends JModelList
{
	public function __construct($config = array())
	{
		$this->db = JFactory::getDbo();
		parent::__construct($config);
	}
	
	/**
	 * Get todays subscribers count
	 *
	 * @return INT.
	 */
	public function getTodaySubsciberCount()
	{
		$query = $this->db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($this->db->quoteName('#__joompush_subscribers'));
		$query->where('created_on BETWEEN ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 23:59:59', true)));
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
	
	/**
	 * Get total subscribers count
	 *
	 * @return INT.
	 */
	public function getTotalSubsciberCount()
	{

		$query = $this->db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($this->db->quoteName('#__joompush_subscribers'));
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
	
	/**
	 * Get today subscribers count
	 *
	 * @return INT.
	 */
	public function getTodayNotifications($group='')
	{

		$query = $this->db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($this->db->quoteName('#__joompush_notifications'));
		$query->where('sent_on BETWEEN ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 23:59:59', true)));
		if ($group)
		{
			$query->where('group_id != 0');
		}
		else
		{
			$query->where('group_id = 0');
		}
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
	
	/**
	 * Get today open notification count
	 *
	 * @return INT.
	 */
	public function getTodayOpenNotifications($group='')
	{

		$query = $this->db->getQuery(true);
		
		if ($group)
		{
			$query->select('SUM(isread)');
		}
		else
		{
			$query->select('COUNT(isread)');
		}
		
		$query->from($this->db->quoteName('#__joompush_notifications'));
		$query->where('sent_on BETWEEN ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ('now', 'Y-m-d 23:59:59', true)));
		$query->where('isread != 0');
		
		if ($group)
		{
			$query->where('group_id != 0');
		}
		else
		{
			$query->where('group_id = 0');
		}
		
		$this->db->setQuery($query);

		$result = $this->db->loadResult();
		
		if ($result)
		{
			return $result;
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * Get total subscribers count
	 *
	 * @return INT.
	 */
	public function getTotalNotifications($group='')
	{

		$query = $this->db->getQuery(true);
		
		$query->select('COUNT(id)');
		$query->from($this->db->quoteName('#__joompush_notifications'));
		
		if ($group)
		{
			$query->where('group_id != 0');
		}
		else
		{
			$query->where('group_id = 0');
		}
		
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
	
	/**
	 * Get total open subscribers count
	 *
	 * @return INT.
	 */
	public function getTotalOpenNotifications($group='')
	{

		$query = $this->db->getQuery(true);
		
		if ($group)
		{
			$query->select('SUM(isread)');
		}
		else
		{
			$query->select('COUNT(isread)');
		}
		
		$query->from($this->db->quoteName('#__joompush_notifications'));
		$query->where('isread != 0');
		
		if ($group)
		{
			$query->where('group_id != 0');
		}
		else
		{
			$query->where('group_id = 0');
		}
		
		$this->db->setQuery($query);

		$result = $this->db->loadResult();
		
		if ($result)
		{
			return $result;
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * Get subscribers chart data
	 *
	 * @return INT.
	 */
	public function getSubscriberChart()
	{
		$last_dates = $this->LastDates(JHtml:: date ('now', 'Y-m-d', true), 7);
		
		$chart_data = array();
		
		$chart_data[0][] = 'Browser';
		$chart_data[0][] = 'Chrome';
		$chart_data[0][] = 'Mozilla';
		$chart_data[0][] = 'Others';
		
		$k = 1;
		foreach ($last_dates as $ndate)
		{
			$chart_data[$k][] = $ndate;  
			
			$query = $this->db->getQuery(true);
			$query->select('COUNT(id)');
			$query->from($this->db->quoteName('#__joompush_subscribers'));
			$query->where('browser LIKE ' . $this->db->quote('%chrome%'));
			$query->where('created_on BETWEEN ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 23:59:59', true)));
			
			$this->db->setQuery($query);

			$chrome_count = $this->db->loadResult();
			
			$chart_data[$k][] = $chrome_count;
			
			$query = $this->db->getQuery(true);
			$query->select('COUNT(id)');
			$query->from($this->db->quoteName('#__joompush_subscribers'));
			$query->where('browser LIKE ' . $this->db->quote('%mozilla%'));
			$query->where('created_on BETWEEN ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 23:59:59', true)));
			
			$this->db->setQuery($query);

			$mozila_count = $this->db->loadResult();
			
			$chart_data[$k][] = $mozila_count;
			
			$query = $this->db->getQuery(true);
			$query->select('COUNT(id)');
			$query->from($this->db->quoteName('#__joompush_subscribers'));
			$query->where('browser NOT LIKE ' . $this->db->quote('%mozilla%'));
			$query->where('browser NOT LIKE ' . $this->db->quote('%chrome%'));
			$query->where('created_on BETWEEN ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 23:59:59', true)));
			
			$this->db->setQuery($query);

			$others_count = $this->db->loadResult();
			
			$chart_data[$k][] = $others_count;
 			
 			 $k++;
		}
		
		return json_encode($chart_data, JSON_NUMERIC_CHECK);
	}
	
	/**
	 * Get Notifications chart data
	 *
	 * @return INT.
	 */
	public function getNotificationChart($group='')
	{
		$last_dates = $this->LastDates(JHtml:: date ('now', 'Y-m-d', true), 7);
		
		$chart_data = array();
		
		$chart_data[0][] = 'Date';
		$chart_data[0][] = 'Sent';
		$chart_data[0][] = 'Open';
		
		$k = 1;
		foreach ($last_dates as $ndate)
		{
			$chart_data[$k][] = $ndate;  
			
			$query = $this->db->getQuery(true);
			$query->select('COUNT(id)');
			$query->from($this->db->quoteName('#__joompush_notifications'));
			$query->where('sent_on BETWEEN ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 23:59:59', true)));
			
			if ($group)
			{
				$query->where('group_id != 0');
			}
			else
			{
				$query->where('group_id = 0');
			}
			
			$this->db->setQuery($query);

			$chrome_count = $this->db->loadResult();
			
			$chart_data[$k][] = $chrome_count;
			
			$query = $this->db->getQuery(true);
			
			if ($group)
			{
				$query->select('SUM(isread)');
			}
			else
			{
				$query->select('COUNT(isread)');
			}
			
			$query->from($this->db->quoteName('#__joompush_notifications'));
			$query->where('sent_on BETWEEN ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 00:00:01', true)) . ' AND ' . $this->db->quote(JHtml:: date ($ndate, 'Y-m-d 23:59:59', true)));

			if ($group)
			{
				$query->where('group_id != 0');
			}
			else
			{
				$query->where('group_id = 0');
			}
			
			$query->where('isread != 0');
			
			$this->db->setQuery($query);

			$chrome_count = $this->db->loadResult();
			
			if (empty($chrome_count))
			{
				$chrome_count = 0;
			}
			
			$chart_data[$k][] = $chrome_count;
			
			$k++;
		}
		
		return json_encode($chart_data, JSON_NUMERIC_CHECK);
	}
	
	/**
	 * Get last days according to param of given date
	 *
	 * @return INT.
	 */
	function LastDates($date, $day = 7)
	{
		$last_dates = array();
		
		$current_date = strtotime($date);
		
		for ($i=0 ; $i < $day; $i++)
		{
			$date = strtotime("-" . $i . " day", $current_date);
			$last_dates[] = date('Y-m-d', $date);
		}
		
		return array_reverse($last_dates);
	}
}
