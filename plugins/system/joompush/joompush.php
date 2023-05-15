<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.JoomPush
 *
 * @copyright   Copyright (C) 2017 Weppsol Technologies, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.environment.browser');

require_once JPATH_ROOT . '/components/com_joompush/helpers/config.php';

/**
 * Plugin class for joompush handling.
 *
 * @since  1.6
 */
class PlgSystemJoompush extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @since today
	 * @throws Exception
	 */
	function onBeforeCompileHead()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		$browser = JBrowser::getInstance();
        $browserType = $browser->getBrowser();

		if (($browserType == 'chrome' || $browserType == 'mozilla' || $browserType == 'opera' || $browserType == 'firefox' || $browserType == 'safari'))
		{
			$document = JFactory::getDocument();

			$params = JComponentHelper::getParams('com_joompush');
			$allow_guest = $params->get('allow_guest', 1);
			$section 	 = $params->get('section', 3);

			$current_section = 0;
			$isClient = '';

			if ($app->isAdmin() && $user->id)
			{
				$current_section = 2;
				$isClient = 'admin';
			}
			elseif ($app->isSite())
			{
				$current_section = 1;
				$isClient = 'site';
			}

			$check = 0;

			if ($section == 3 && $app->isAdmin() && $user->id)
			{
				$check = 1;
			}
			elseif ($section == 3 && $app->isSite())
			{
				$check = 1;
			}

			// Start - To show allow notification Confirmation Alert on selected menu items

			if (($section == 3 || $section == 1) && $app->isSite())
			{
				$allow_notification 	 = $params->get('allow_notification');

				if ($allow_notification == 'selected')
				{
					$jpactivemenu 	 = $params->get('jpactivemenu');

					$itemId = $app->input->get('Itemid', 0,'int');

					if ($itemId == 0 || !in_array($itemId, $jpactivemenu))
					{
						return;
					}
				}
			}

			// End - To show allow notification Confirmation Alert on selected menu items

			if($section == $current_section || $check)
			{
				// get comonent config
				$JoompushHelpersConfig = new JoompushHelpersConfig;
				$configs = $JoompushHelpersConfig->getConfig();

				if (isset($configs['api_key'], $configs['server_key'], $configs['project_id'], $configs['sender_id'], $configs['app_id']))
				{
					$global_vars = 'var serverKey = "' . $configs['server_key']->value . '"; ';
					$global_vars .= 'var project_id = "' . $configs['project_id']->value . '"; ';
					$global_vars .= 'var messagingSenderId = "' . $configs['sender_id']->value . '"; ';
					$global_vars .= 'var appId = "' . $configs['app_id']->value . '"; ';
					$global_vars .= 'var vapidKey = "' . $configs['vapidKey']->value . '"; ';
					$global_vars .= 'var apiKey = "' . $configs['api_key']->value . '"; ';
					$global_vars .= 'var fbsw_url = "' . JURI::root() . 'firebase-messaging-sw.js' . '"; ';
					$global_vars .= 'var sw_url = "' . JURI::root() . 'joompush-sw.js' . '"; ';
					$global_vars .= 'var baseurl = "' . JURI::root() . '"; ';
					$global_vars .= 'var isClient = "' . $isClient . '"; ';
					$global_vars .= 'var userid = ' . $user->id . '; ';

					// Start GDPR seting
					$righttoinformed = $params->get( 'righttoinformed');
					$RightToInformedMessage = '';

					if ($righttoinformed)
					{
						$RightToInformedMessage = '<div class="jp-overlay-arrow"></div>' . $params->get( 'RightToInformedMessage');
					}

					$legalconsent = $params->get( 'legalconsent');
					$legalconsent_text = '';

					if ($legalconsent)
					{
						$legalconsent_text = $params->get( 'legal_consent');
						$legalconsent_text =  '<div id="legalconsent"><span>' . str_replace("{yourdomain}",JFactory::getConfig()->get( 'sitename' ),$legalconsent_text ) . '</span></div>' ;
					}


					$gdpr_setting =  0;
					$gdpr_show = '';

					if($righttoinformed || $legalconsent)
					{
						$gdpr_show .= '<div id="jp-overlay-backdrop" style="display:none;"><div id="jp-overlay-text">' . $RightToInformedMessage . '</div>' . $legalconsent_text . '</div>';
						$gdpr_setting =  1;
					}

					if ($app->input->cookie->get('jpsent'))
					{
						$btnText = JText::_('COM_JOOMPUSH_UNSUBSCRIBE_POPUP_BTN_TITLE_NO');
						$tooltip_text = JText::_('COM_JOOMPUSH_UNSUBSCRIBE_TOOLTIP');
					}
					else
					{
						$btnText = JText::_('COM_JOOMPUSH_UNSUBSCRIBE_POPUP_BTN_TITLE_YES');
						$tooltip_text = JText::_('COM_JOOMPUSH_SUBSCRIBE_TOOLTIP');
					}

					$gdpr_unsub_setting = 0;

					$unsubscriber = $params->get('unsubscriber');

					if ($unsubscriber)
					{
						$gdpr_unsub_setting =  1;

						$gdpr_show .= '<div class="jpbottomright"><span id="jpmyimg" title="'. $tooltip_text . '"><img id="jpmyimg" src="'. juri::root(). 'media/com_joompush/images/joompush.png" alt="JoomPush" style="width:50px;"></span></div><div id="jpmyModal" class="jpmodal"><div class="jpmodal-content"><span class="jpclose">&times;</span><div class="modal-header"><h4 class="jpmodal-title" id="pushModalLabel">' . JText::_('COM_JOOMPUSH_UNSUBSCRIBE_NOTIFICATION_TITLE') .'</h4></div><p id="jppopmsg">' . $params->get('unsubscriber_popup_message') . '</p><div class="jpmodal-footer"><span id="jpcallSW" class="btn btn-info">' . $btnText . '</span><img id="jploadimg" src="'. juri::root(). 'media/com_joompush/images/jploader.gif" alt="img" style="display:none;"></div></div></div>';
					}

					$jpgdpr_unsub_msg = str_replace("'","\'",$params->get('unsubscriber_message'));
					$gdpr_show = str_replace("'","\'",$gdpr_show);
					$global_vars .= "var jpgdpr_show = '" . $gdpr_show . "'; ";
					$global_vars .= 'var jpgdpr = ' . $gdpr_setting . '; ';
					$global_vars .= 'var jpgdpr_unsub = ' . $gdpr_unsub_setting . '; ';
					$global_vars .= "var jpgdpr_unsub_msg = '" . $jpgdpr_unsub_msg . "'; ";

					// End GDPR setting

					if ($allow_guest)
					{
						$document->addScriptDeclaration($global_vars);
						$document->addStyleSheet(Juri::root() . 'plugins/system/joompush/asset/css/joompush.css');

						JHtml::script('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
						JHtml::script('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
						JHtml::script(Juri::root() . 'plugins/system/joompush/asset/js/joompush.js');

						$manifest = JPATH_SITE . '/plugins/system/joompush/asset/manifest.json';
						if (JFile::exists($manifest))
						{
							$manifest_path = JUri::root() . '/plugins/system/joompush/asset/manifest.json';
							$document->addCustomTag('<link rel="manifest" href="' . $manifest_path . '">');
						}
					}
					else
					{
						if($user->id)
						{
							$document->addScriptDeclaration($global_vars);
							$document->addStyleSheet(Juri::root() . 'plugins/system/joompush/asset/css/joompush.css');

							JHtml::script('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
							JHtml::script('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
							JHtml::script(Juri::root() . 'plugins/system/joompush/asset/js/joompush.js');

							$manifest = JPATH_SITE . '/plugins/system/joompush/asset/manifest.json';
							if (JFile::exists($manifest))
							{
								$manifest_path = JUri::root() . '/plugins/system/joompush/asset/manifest.json';
								$document->addCustomTag('<link rel="manifest" href="' . $manifest_path . '">');
							}
						}
					}
				}

				return true;
			}
		}
	}

	function OnAfterConfigSave($config)
	{
		if ($config['sender_id'])
		{
			$path = JPATH_SITE;
			/*$path = explode('/',JPATH_SITE);
			array_pop($path);
			$path = implode('/',$path); */
			$fmsw_js = $path . '/firebase-messaging-sw.js';

			if (JFile::exists($fmsw_js))
			{
				JFile::delete($fmsw_js);
			}

			$sw_data = '';

			JFile::write($fmsw_js, $sw_data);

			$jpsw_js = $path . '/joompush-sw.js';

			if (JFile::exists($jpsw_js))
			{
				JFile::delete($jpsw_js);
			}

			$jpsw_data = 'self.addEventListener("push",function(i){
			  var t=i.data.json(),n=t.notification,o=n.title,
			  a=n.body,c=n.icon,l=n.title,t={url:{clickurl:n.click_action}};
			  i.waitUntil(self.registration.showNotification(o,{body:a,icon:c,tag:l,
			    data:t}))}),

			self.addEventListener("notificationclick",function(i){
			  i.notification.close();var t=i.notification.data.url,n=t.clickurl;
			  i.waitUntil(clients.openWindow(n))});

			  self.importScripts("/wkpwa_sw.js");';

			JFile::write($jpsw_js, $jpsw_data);

			$manifest = JPATH_SITE . '/plugins/system/joompush/asset/manifest.json';

			if (JFile::exists($manifest))
			{
				JFile::delete($manifest);
			}

			$jconfig = JFactory::getConfig();

			$mainfest_data = array();
			$mainfest_data['name']			= (string) $jconfig->get( 'sitename' );
			$mainfest_data['gcm_sender_id'] = (string) 103953800507;

			JFile::write($manifest,json_encode($mainfest_data));

			$this->saveDefaultGroup();
		}
	}

	/**
     * add suscriber to topic
     *
     * @return  string
     */
     function saveDefaultGroup()
     {
		require_once JPATH_ROOT . '/administrator/components/com_joompush/models/subscribergroup.php';

		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$current_datetime = JHtml:: date ('now', 'Y-m-d h:i:s', true);

		$JoompushModelSubscribergroup = new JoompushModelSubscribergroup;

		// For default admin group

		$query = $db->getQuery(true);
		$query
			->select('id')
			->from($db->quoteName('#__joompush_subscriber_groups'))
			->where($db->quoteName('is_default') . ' = 2');
		$db->setQuery($query);
		$admin_group_id = $db->loadResult();

		if (empty($admin_group_id))
		{
			$gropobj = new stdClass;
			$gropobj->id			= '';
			$gropobj->state 		= 1;
			$gropobj->title 		= 'Admin Subscribers Group';
			$gropobj->description	= 'Default group of all JoomPush admin subscribers';
			$gropobj->is_default 	= 2;
			$gropobj->usergroup_id 	= 0;
			$gropobj->created_by	= $user->id;
			$gropobj->created_on	= $current_datetime;
			$gropobj->modified_on	= $current_datetime;

			if ($db->insertObject('#__joompush_subscriber_groups',$gropobj,'id'))
			{
				$admin_group_id = $db->insertid();
			}


			$query = $db->getQuery(true);
			$query
				->select('id')
				->from($db->quoteName('#__joompush_subscribers'))
				->where($db->quoteName('type') . ' = ' . $db->quote('admin'))
				->order($db->quoteName('id'), 'ASC');
			$db->setQuery($query);
			$ids = $db->loadColumn();

			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->select($db->quoteName('key'))
				->from($db->quoteName('#__joompush_subscribers'))
				->where($db->quoteName('type') . ' = ' . $db->quote('admin'))
				->order($db->quoteName('id'), 'ASC');
			$db->setQuery($query);
			$keys = $db->loadAssocList('id','key');

			$JoompushModelSubscribergroup->setTopic($admin_group_id, $ids, $keys, 1);
		}

		// For all subscribers group

		$query = $db->getQuery(true);
		$query
			->select('id')
			->from($db->quoteName('#__joompush_subscriber_groups'))
			->where($db->quoteName('is_default') . ' = 1');

		$db->setQuery($query);
		$group_id = $db->loadResult();

		if (empty($group_id))
		{
			$gropobj = new stdClass;
			$gropobj->id			= '';
			$gropobj->state 		= 1;
			$gropobj->title 		= 'All Subscribers Group (Public)';
			$gropobj->description	= 'Default group of all JoomPush subscribers';
			$gropobj->is_default 	= 1;
			$gropobj->usergroup_id 	= 1;

			//LOGGED user id
			$user = JFactory::getUser();
			$gropobj->created_by		=	$user->id;

			$current_datetime = JHtml:: date ('now', 'Y-m-d h:i:s', true);
			$gropobj->created_on	=	$current_datetime;
			$gropobj->modified_on	=	$current_datetime;

			if ($db->insertObject('#__joompush_subscriber_groups',$gropobj,'id'))
			{
				$group_id = $db->insertid();
			}
		}

		$query = $db->getQuery(true);
		$query
			->select('id')
			->from($db->quoteName('#__joompush_subscribers'))
			->order($db->quoteName('id'), 'ASC');
		$db->setQuery($query);
		$ids = $db->loadColumn();

		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->select($db->quoteName('key'))
			->from($db->quoteName('#__joompush_subscribers'))
			->order($db->quoteName('id'), 'ASC');
		$db->setQuery($query);
		$keys = $db->loadAssocList('id','key');

		$JoompushModelSubscribergroup->setTopic($group_id, $ids, $keys, 1);
	 }

	/**
    * add new joomla group into JoomPush Group
    *
    * @return  string
    */
	public function onUserAfterSaveGroup($context, $table, $isNew, $data)
	{
	   if ($isNew)
	   {
			$db = JFactory::getDbo();
			$gropobj = new stdClass;
			$gropobj->id         = '';
			$gropobj->state       = 1;
			$gropobj->title       = $table->title;
			$gropobj->description  = 'Joomla ' . $table->title . ' User Group\'s JoomPush Subscribers Group';
			$gropobj->is_default   = 0;
			$gropobj->usergroup_id     = $table->id;
			$gropobj->created_by   = 0;
			$gropobj->created_on   = JHtml:: date ('now', 'Y-m-d h:i:s', true);;
			$gropobj->modified_on  = JHtml:: date ('now', 'Y-m-d h:i:s', true);;

			$db->insertObject('#__joompush_subscriber_groups',$gropobj,'id');
	   }
	}
}
