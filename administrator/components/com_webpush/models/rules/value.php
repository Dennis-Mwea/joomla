<?php

defined('_JEXEC') or die('Restricted access');

class JFormRuleValue extends JFormRule
{
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null): bool
	{
		$valid = preg_match('/^[A-Za-z0-9_-]+$/i', $value);

		if (!$valid) {
			$element->attributes()->message = "The value $value is not a valid api key.";
		}

		return $valid;
	}
}
