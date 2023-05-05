<?php

defined('_JEXEC') or die;

jimport('joomla.environment.browser');

require_once JPATH_ROOT . "/components/com_webpush/models/webpush.php";

class plgSystemWebpush extends JPlugin
{
	/**
	 * On after route.
	 *
	 * @throws Exception
	 */
	public function onBeforeCompileHead(): void
	{
		$app = JFactory::getApplication();
		$browser = JBrowser::getInstance()->getBrowser();
		if (in_array($browser, ['chrome', 'mozilla', 'opera', 'firefox', 'safari']) && $app->isClient('site')) {
			$config = (new WebPushModel())->getConfig();
			if (isset($config['web_push_public_key'], $config['web_push_private_key'])) {
				$document = JFactory::getDocument();
				$jsVars = "var webPushPublicKey = '{$config['web_push_public_key']->value}';";

				$document->addScriptDeclaration($jsVars);
				JHtml::script(JUri::root() . 'plugins/system/webpush/assets/webpush.js');
			}
		}
	}

	public function onAfterRoute(): void
	{
	}
}
