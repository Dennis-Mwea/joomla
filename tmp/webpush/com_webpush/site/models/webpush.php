<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webpush
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class WebPushModel extends JModelLegacy
{
	private JDatabaseDriver $db;

	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		$this->db = JFactory::getDbo();
		parent::__construct($config, $factory);
	}

	public function getConfig()
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__webpush_configs'));
		$this->db->setQuery($query);

		return $this->db->loadObjectList('name');
	}
}
