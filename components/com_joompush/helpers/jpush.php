<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_joompush/helpers/config.php';

/**
 * Class JoompushHelpersJpush
 *
 * @since  1.0
 */
class JoompushHelpersJpush
{
    /**
     * send push message to suscriber
     *
     * @return  string
     */
    public static function jpush($pushMsg)
    {
		if ($pushMsg)
		{
			$JoompushHelpersConfig = new JoompushHelpersConfig;
			$configs = $JoompushHelpersConfig->getConfig();
			
			$registrationIds = $pushMsg->key; 
			
			$icon = ''; 
			
			if ($pushMsg->template->icon)
			{
				$icon = JURI::root() . $pushMsg->template->icon;
			}
			else
			{
				$icon = JURI::root() .  $configs['icon']->params;
			}
			
			$url = '';
			
			if ($pushMsg->template->url)
			{
				$url = $pushMsg->template->url;
			}
			else
			{
				$url = $configs['url']->params;
			}
			
			if ($pushMsg->code)
			{
				$url =  JURI::root() . 'index.php?option=com_joompush&task=mynotifications.track&code='. $pushMsg->code . '&jpurl=' . urlencode($url);
			}
			
			$dry_run = false;
			
			if (isset($pushMsg->template->dry_run))
			{
				$dry_run = true;
			}
			
			// prep the bundle
			$msg = array
			(
				'body' 			=> $pushMsg->template->message,
				'title'			=> $pushMsg->template->title,
				'icon'			=> $icon,
				'click_action'	=> $url,
				'vibrate'		=> 1,
				'sound'			=> 1
			);
			
			$fields = array
			(
				'registration_ids' 	=> $registrationIds,
				'notification'		=> $msg,
				'dry_run' 			=> $dry_run
			);
			 
			return self::sendToClient('https://fcm.googleapis.com/fcm/send', $fields);
		}
	}
	
    /**
     * send push message to topic
     *
     * @return  string
     */
    public static function jtopicPush($pushMsg)
    {
		if ($pushMsg)
		{
			$JoompushHelpersConfig = new JoompushHelpersConfig;
			$configs = $JoompushHelpersConfig->getConfig();
			
			$icon = ''; 
			
			if ($pushMsg->template->icon)
			{
				$icon = JURI::root() . $pushMsg->template->icon;
			}
			else
			{
				$icon = JURI::root() .  $configs['icon']->params;
			}
			
			$url = '';
			
			if ($pushMsg->template->url)
			{
				$url = $pushMsg->template->url;
			}
			else
			{
				$url = $configs['url']->params;
			}
			
			if ($pushMsg->code)
			{
				$url =  JURI::root() . 'index.php?option=com_joompush&task=mynotifications.track&code='. $pushMsg->code . '&jpurl=' . urlencode($url);
			}
			
			// prep the bundle
			$msg = array
			(
				'body' 			=> $pushMsg->template->message,
				'title'			=> $pushMsg->template->title,
				'icon'			=> $icon,
				'click_action'	=> $url,
				'vibrate'		=> 1,
				'sound'			=> 1
			);
			
			$fields = array
			(
				 'to' => '/topics/' . $pushMsg->gid,
				'notification'		=> $msg
			);
			
			return self::sendToClient('https://fcm.googleapis.com/fcm/send', $fields);
		}
	}
	
	 /**
     * add suscriber to topic
     *
     * @return  string
     */
    public static function jaddTopicSubscription($topic_id, $recipients_tokens)
    {
		if ($topic_id)
		{
			$fields =  array(
                    'to' => '/topics/' . $topic_id,
                    'registration_tokens' => $recipients_tokens
			);
			
			return self::sendToClient('https://iid.googleapis.com/iid/v1:batchAdd', $fields);
		}
	}
	
	 /**
     * Remove suscriber to topic
     *
     * @return  string
     */
    public static function jremoveTopicSubscription($topic_id, $recipients_tokens)
    {

		if ($topic_id)
		{
			$fields =  array(
                    'to' => '/topics/' . $topic_id,
                    'registration_tokens' => $recipients_tokens
			);
			
			return self::sendToClient('https://iid.googleapis.com/iid/v1:batchRemove', $fields);
		}
	}
	
	/**
     * Send notification
     *
     * @return  string
     */
	public static function sendToClient($url, $fields)
	{
		$JoompushHelpersConfig = new JoompushHelpersConfig;
		$configs = $JoompushHelpersConfig->getConfig();
		
		$api_key = $configs['server_key']->value;
		
		$headers = array
		(
			'Authorization: key=' . $api_key,
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		
		return $result;
	}
}
