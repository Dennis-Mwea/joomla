<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Joompush records.
 *
 * @since  1.6
 */
class JoompushModelLicence extends JModelList
{
	/**
	 * Method to save an Licence
	 *
	 * @param   array  &$Licence  An array of post data.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	function verify($licence)
	{
		if ($licence)
		{ 
			$app = JFactory::getApplication();
			$config = JFactory::getConfig();
			
			$http = new JHttp;
			$options = new JRegistry;
			$transport = new JHttpTransportStream($options);

			$http = new JHttp($options, $transport);
			
			$data =  new stdClass();
			$data->key = $licence;
			$data->pid = 4;
			$data->url = juri::root();
			$data->site = $config->get( 'sitename' );
			$data->email = $config->get( 'mailfrom' );
			
			$api_url = 'https://weppsol.com?type=license&data='.base64_encode(json_encode($data)); 
			
		
			$response = $http->get($api_url);

			if ($response->code == 200)
			{
				$response_data = json_decode($response->body);
				
				if($response_data)
				{
					if ($response_data->success)
					{
						$chk = 0;
						
						$db = JFactory::getDbo();

						$query = $db->getQuery(true);
						$query
						->select('id')
						->from($db->quoteName('#__joompush_configs'))
						->where($db->quoteName('name') . ' = ' . $db->quote('licence'));
						$db->setQuery($query);
						
						$result = $db->loadResult();
						
						$values = new stdClass();
						$values->name = 'licence';
						$values->params = $response->body;
						
						if($result)
						{
							$values->id = $result;
							
							if ($db->updateObject('#__joompush_configs', $values, 'id'))
							{
								$chk = 1;
							}
						}
						else
						{
							$values->id = 0;
						
							if($db->insertObject('#__joompush_configs', $values ))
							{
								$chk = 1;
							}
						}
						
						if ($chk)
						{
							$app->enqueueMessage(Jtext::_('COM_JOOMPUSH_LICENCE_VERIFY_SUCCESS'), 'success');
						}
						else
						{
							
							$app->enqueueMessage(Jtext::_('COM_JOOMPUSH_LICENCE_VERIFY_ERROR'), 'error');
						}
						
						$app->redirect('index.php?option=com_joompush&view=licence');
					}
					else
					{
						$app->enqueueMessage(Jtext::_('COM_JOOMPUSH_LICENCE_VERIFY_VALIDATE'), 'error');
						$app->redirect('index.php?option=com_joompush&view=licence');
					}
				}
			}
			else
			{
				$app->enqueueMessage(Jtext::_('COM_JOOMPUSH_LICENCE_VERIFY_SERVER'), 'error');
				$app->redirect('index.php?option=com_joompush&view=licence');
			}
		}
	}
	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$current_date = JHtml::date('now', 'Y-m-d');
		
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__joompush_configs'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('licence'));
		$db->setQuery($query);

		$result = $db->loadObject();
		
		if($result)
		{
			$param = json_decode($result->params);
			
			$result->status = 0;
			
			if ($current_date <= $param->expires)
			{
				$result->status = 1;
			}
		}
		
		return $result;	
	}
}
