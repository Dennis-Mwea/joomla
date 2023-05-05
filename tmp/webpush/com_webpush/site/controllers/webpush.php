<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT . '/models/webpush.php';
require_once JPATH_COMPONENT . '/models/subscription.php';

class WebpushController extends JControllerLegacy
{
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

	public function sendMessages()
	{

	}
}
