<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

require_once __DIR__ . '/vendor/autoload.php';

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class WebPushController extends JControllerLegacy
{
	/**
	 * @throws Exception
	 */
	public function display($cachable = false, $urlparams = array()): void
	{
		$app = JFactory::getApplication();

		$user = JFactory::getUser();
		if ($user->guest)
		{
			echo json_encode([
				'success' => false,
			]);
		}
		else
		{
			$jInput   = $app->input;
			$state    = $jInput->getString('state');
			$endpoint = $jInput->getString('endpoint');

			match ($state)
			{
				'delete' => $this->deleteSubscription($endpoint),
				'create' => $this->createSubscription($user, $endpoint),
				'update' => $this->updateSubscription($user, $endpoint),
			};

			echo json_encode([
				'success' => true,
//				'results' => $this->sendTestMessages($user),
			]);
		}

		$app->close();
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	private function createSubscription($user, string $endpoint): bool
	{
		$db       = JFactory::getDbo();
		$input    = JFactory::getApplication()->input;
		$key      = $input->getString('key');
		$token    = $input->getString('token');
		$result = $this->getSubscription($endpoint);
		if (!is_null($result)) {
			return $this->updateSubscription($user, $endpoint);
		}

		$subscription                    = new stdClass();
		$subscription->public_key        = $key;
		$subscription->auth_token        = $token;
		$subscription->content_encoding  = null;
		$subscription->subscribable_id   = $user->id;
		$subscription->subscribable_type = get_class($user);
		$subscription->endpoint          = $endpoint;

		return $db->insertObject('#__web_subscriptions', $subscription, 'id');
	}

	private function deleteSubscription($endpoint): bool
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__web_subscriptions')
			->where($db->quoteName('endpoint') . ' = ' . $db->quote($endpoint));

		return $db->setQuery($query)->execute();
	}

	/**
	 * @throws Exception
	 */
	private function updateSubscription($user, $endpoint): bool
	{
		$db    = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$key   = $input->getString('key');
		$token = $input->getString('token');
		$result = $this->getSubscription($endpoint);

		if (is_null($result))
		{
			return $this->createSubscription($user, $endpoint);
		}

		$subscription             = new stdClass();
		$subscription->id         = $result->id;
		$subscription->public_key = $key;
		$subscription->auth_token = $token;

		return $db->updateObject('#__web_subscriptions', $subscription, 'id');
	}

	private function getSubscription(string $endpoint): ?stdClass
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__web_subscriptions'))
			->where($db->quoteName('endpoint') . ' = ' . $db->quote($endpoint));

		return $db->setQuery($query)->loadObject();
	}

	/**
	 * @throws ErrorException
	 */
	private function sendTestMessages($user)
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__web_subscriptions'))
			->where($db->quoteName('subscribable_id') . ' = ' . $db->quote($user->id))
			->where($db->quoteName('subscribable_type') . ' = ' . $db->quote(get_class($user)));
		$results = $db->setQuery($query)->loadObjectList();
		$webPush = new WebPush([
			'VAPID' => [
				'subject'    => 'mailto:me@website.com',
				'privateKey' => 'ofB3Dgr_HTU0u1FnNNtzuz6dFnG8pY0Kdgt0HWZWUYQ',
				'publicKey'  => 'BO-M9OJnL6QSChU5UMvm-Zz5QvnCUIB0swJwUtmkZ5urdnaOItNkbo8-3q9uPzl5AIlhfV3dWGSQFmnClBLgxj0',
			],
		], [
			'TTL'       => 1000,
			'batchSize' => 200,
			'urgency'   => 'high',
			'topic'     => 'newEvent',
		]);
		foreach ($results as $subscription)
		{
			$sub = Subscription::create([
				'endpoint'  => $subscription->endpoint,
				'publicKey' => $subscription->public_key,
				'authToken' => $subscription->auth_token,
			]);
			$webPush->queueNotification($sub, json_encode([
				'title'   => 'Hey you',
				'icon'    => '/approved-icon.png',
				'message' => 'Your account was approved',
			]));
		}

		$messages = [];
		foreach ($webPush->flush() as $report)
		{
			$endpoint = $report->getEndpoint();

			if ($report->isSuccess())
			{
				$messages[] = "[v] Message sent successfully for subscription $endpoint.";
			}
			else
			{
				$messages[] = "[x] Message failed to sent for subscription $endpoint: {$report->getReason()}";
			}
		}

		return $messages;
	}
}
