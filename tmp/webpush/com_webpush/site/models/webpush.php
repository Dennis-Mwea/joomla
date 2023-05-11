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
	private $db;

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

	public function create(string $key, string $value): stdClass
	{
		if (!is_null($config = $this->find($key, $value)))
		{
			return $this->update($config, $value);
		}

		$config                    = new stdClass();
		$config->name        = $key;
		$config->value        = $value;

		$this->db->insertObject('#__webpush_configs', $config, 'id');

		return $this->find($key, $value);
	}

	private function find(string $key, string $value): ?stdClass
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__webpush_configs'))
			->where($this->db->quoteName('name') . ' = ' . $this->db->quote($key))
			->where($this->db->quoteName('value') . ' = ' . $this->db->quote($value));

		return $this->db->setQuery($query)->loadObject();
	}

	public function update(stdClass $config, string $value): stdClass
	{
		$newConfig             = new stdClass();
		$newConfig->value = $value;
		$newConfig->id         = $config->id;

		$this->db->updateObject('#__webpush_configs', $newConfig, 'id');

		return $this->find($config->name, $value);
	}
}
