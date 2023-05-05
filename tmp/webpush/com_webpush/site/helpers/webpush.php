<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/../vendor/autoload.php';

abstract class WebPushHelper
{
	/**
	 * @throws ErrorException
	 */
	public static function sendMessages(string $title, ?string $message, array $payload): array
	{
		$user          = JFactory::getUser();
		$subscriptions = (new SubscriptionModel)->getSubscribers($user);
		$webPush       = self::getWebPush();

		foreach ($subscriptions as $subscription)
		{
			$payload = $payload ?? [];
			$payload['msg'] = $message;
			$webPush->queueNotification(self::createSubscription($subscription), json_encode([
				'title'   => $title,
				'message' => $payload,
				'icon'    => '/approved-icon.png',
			]));
		}

		return self::checkForResponse($webPush->flush());
	}

	/**
	 * @throws ErrorException
	 */
	protected static function getWebPush(): WebPush
	{
		$config = (new WebPushModel)->getConfig();

		return new WebPush([
			'VAPID' => [
				'subject'    => 'sanify@sanify24.de',
				'publicKey'  => $config['web_push_public_key']->value,
				'privateKey' => $config['web_push_private_key']->value,
			],
		], [
			'TTL'       => 1000,
			'batchSize' => 200,
			'urgency'   => 'high',
			'topic'     => 'newEvent',
		]);
	}

	/**
	 * @throws ErrorException
	 */
	protected static function createSubscription(stdClass $subscription): Subscription
	{
		return Subscription::create([
			'endpoint'  => $subscription->endpoint,
			'publicKey' => $subscription->public_key,
			'authToken' => $subscription->auth_token,
		]);
	}

	protected static function checkForResponse(array $reports): array
	{
		$messages = [];
		foreach ($reports as $report)
		{
			$messages[] = $report->isSuccess()
				? "[v] Message sent successfully for subscription {$report->getEndpoint()}."
				: "[x] Message failed to sent for subscription {$report->getEndpoint()}: {$report->getReason()}";
		}

		return $messages;
	}
}
