<?php

defined('JPATH_BASE') or die('Restricted access');

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldCreatedby extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'createdby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput(): string
	{
		// Initialize variables.
		$html = [];

		// Load user
		$user_id = $this->value;

		if ($user_id) {
			$user = JFactory::getUser($user_id);
		} else {
			$user   = JFactory::getUser();
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		}

		if (!$this->hidden) {
			$html[] = "<div>$user->name ($user->username)</div>";
		}

		return implode($html);
	}
}
