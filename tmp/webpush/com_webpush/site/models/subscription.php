<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webpush
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\User\User;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SubscriptionModel extends JModelLegacy
{
	private $db;

	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		$this->db = JFactory::getDbo();
		parent::__construct($config, $factory);
	}

	public function create(User $user, string $endpoint, string $token, string $key): stdClass
	{
		if (!is_null($this->find($endpoint)))
		{
			return $this->update($user, $endpoint, $token, $key);
		}

		$subscription                    = new stdClass();
		$subscription->public_key        = $key;
		$subscription->auth_token        = $token;
		$subscription->content_encoding  = null;
		$subscription->subscribable_id   = $user->id;
		$subscription->subscribable_type = get_class($user);
		$subscription->endpoint          = $endpoint;

		$this->db->insertObject('#__webpush_subscribers', $subscription, 'id');

		return $this->find($endpoint);
	}

	private function find(string $endpoint): ?stdClass
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__webpush_subscribers'))
			->where($this->db->quoteName('endpoint') . ' = ' . $this->db->quote($endpoint));

		return $this->db->setQuery($query)->loadObject();
	}

	public function delete($endpoint): bool
	{
		$query = $this->db->getQuery(true)
			->delete('#__webpush_subscribers')
			->where($this->db->quoteName('endpoint') . ' = ' . $this->db->quote($endpoint));

		return $this->db->setQuery($query)->execute();
	}

	public function update(User $user, string $endpoint, string $token, string $key): stdClass
	{
		$result = $this->find($endpoint);

		if (is_null($result))
		{
			return $this->create($user, $endpoint, $token, $key);
		}

		$subscription             = new stdClass();
		$subscription->public_key = $key;
		$subscription->auth_token = $token;
		$subscription->id         = $result->id;
		$subscription->subscribable_id   = $user->id;

		$this->db->updateObject('#__webpush_subscribers', $subscription, 'id');

		return $this->find($endpoint);
	}

	public function getSubscribers(JUser $user): array
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__webpush_subscribers'))
			->where($db->quoteName('subscribable_id') . ' = ' . $db->quote($user->id))
			->where($db->quoteName('subscribable_type') . ' = ' . $db->quote(get_class($user)));

		return $db->setQuery($query)->loadObjectList();
	}
}
