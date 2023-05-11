<?php

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class WebPushTableSubscriber extends JTable
{
	public function __construct(&$db)
	{
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'WebPushTableSubscriber', [
			'typeAlias' => 'com_webpush.subscriber',
		]);
		parent::__construct('#__webpush_subscribers', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0)
		{
			$array['created_by'] = JFactory::getUser()->id;
		}

		if ($array['id'] == 0)
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!JFactory::getUser()->authorise('core.admin', 'com_webpush.subscriber.' . $array['id']))
		{
			$actions = JAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_webpush/access.xml', "/access/section[@name='subscriber']/");
			$default_actions = JAccess::getAssetRules('com_webpush.subscriber.' . $array['id'])->getData();
			$arrayJAccess   = array();

			foreach ($actions as $action)
			{
				$arrayJAccess[$action->name] = $default_actions[$action->name];
			}

			$array['rules'] = $this->jAccessRulesToArray($arrayJAccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	private function jAccessRulesToArray($jAccessRules): array
	{
		$rules = array();
		foreach ($jAccessRules as $action => $jAccess)
		{
			$actions = array();

			if ($jAccess)
			{
				foreach ($jAccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	public function check(): bool
	{
		if (property_exists($this, 'ordering') && $this->id == 0) {
			$this->ordering = self::getNextOrder();
		}

		return parent::check();
	}

	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;

		return 'com_webpush.subscriber.' . (int) $this->$k;
	}

	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_webpush');

		// Return the found asset-parent-id
		if ($assetParent->id) {
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	public function delete($pk = null): bool
	{
		$this->load($pk);

		return parent::delete($pk);
	}
}
