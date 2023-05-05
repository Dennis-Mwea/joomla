<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT . '/models/webpush.php';
require_once JPATH_COMPONENT . '/models/subscription.php';
require_once JPATH_COMPONENT . '/helpers/webpush.php';

class WebpushController extends JControllerLegacy
{
	/**
	 * @return void
	 * @throws Exception
	 */
	public function subscribe(): void
	{
		$app = JFactory::getApplication();

		$user     = JFactory::getUser();
		$model    = new SubscriptionModel;
		$key      = $app->input->getString('key');
		$token    = $app->input->getString('token');
		$state    = $app->input->getString('state');
		$endpoint = $app->input->getString('endpoint');

		$subscription           = match ($state)
		{
			'delete' => $model->delete($endpoint),
			'create' => $model->create($user, $endpoint, $token, $key),
			'update' => $model->update($user, $endpoint, $token, $key),
		};
		$response               = new stdClass();
		$response->success      = true;
		$response->subscription = $subscription;

		echo json_encode($response);
		$app->close();
	}

	/**
	 * @throws ErrorException
	 * @throws Exception
	 */
	public function sendMessages(): void
	{
		$user          = JFactory::getUser();
		$app           = JFactory::getApplication();
		$title         = $app->input->getString('title');
		$message       = $app->input->getString('message');
		$payload       = $app->input->get('payload');
		$subscriptions = (new SubscriptionModel)->getSubscribers($user);

		$response          = new stdClass();
		$response->success = true;
		$response->data    = WebPushHelper::sendMessages($subscriptions, $title, $message, $payload);

		echo json_encode($response);
		$app->close();
	}

	/**
	 * @throws Exception
	 */
	public function getSubscribers(): void
	{
		$user              = JFactory::getUser();
		$response          = new stdClass();
		$response->success = true;
		$response->users   = (new SubscriptionModel)->getSubscribers($user);

		echo json_encode($response);
		JFactory::getApplication()->close();
	}
}
