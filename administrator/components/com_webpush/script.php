<?php

use Joomla\CMS\Installer\InstallerScript;
use Minishlink\WebPush\VAPID;

defined('_JEXEC') or die('Restricted access');

class com_webPushInstallerScript extends InstallerScript
{
	/**
	 * Runs right after any installation action is performed on the component.
	 *
	 * @param  string    $type   - Type of PostFlight action. Possible values are:
	 *                           - * install
	 *                           - * update
	 *                           - * discover_install
	 * @param  \stdClass $parent - Parent object calling object.
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		$path = JPATH_ROOT . '/components/com_webpush';

		include_once "$path/vendor/autoload.php";
		require_once "$path/models/webpush.php";

		$config = VAPID::createVapidKeys();
		$model = new WebPushModel;
		$values[] = $model->create('web_push_public_key', $config['publicKey']);
		$values[] = $model->create('web_push_private_key', $config['privateKey']);

		JText::_("Configuration keys have been generated.");
		JLog::add("Configuration keys have been generated: " . json_encode($values));
	}
}
