<?php

defined('_JEXEC') or die('Restricted access');

class WebPushTableWebPush extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__webpush_configs', 'id', $db);
	}

	public function bind($src, $ignore = array()): bool
	{
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($src, $ignore);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_webpush.message.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->name;
	}

	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// Find the parent-asset
		// The item has the component as asset-parent
		$assetParent->loadByName('com_webpush');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}
}