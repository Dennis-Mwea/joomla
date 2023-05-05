<?php

defined('_JEXEC') or die('Restricted access');

class JFormRuleName extends JFormRule
{
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null): bool
	{
		return preg_match("/^.+$/", $value);
	}
}
