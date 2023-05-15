<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_joompush/assets/css/joompush.css');
$document->addStyleSheet(JUri::root() . 'media/com_joompush/css/list.css');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$sortFields = $this->getSortFields();

?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	};

	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
	});
	
	window.toggleField = function (id, task, field) {

		var f = document.adminForm,
			i = 0, cbx,
			cb = f[ id ];

		if (!cb) return false;

		while (true) {
			cbx = f[ 'cb' + i ];

			if (!cbx) break;

			cbx.checked = false;
			i++;
		}

		var inputField   = document.createElement('input');
		inputField.type  = 'hidden';
		inputField.name  = 'field';
		inputField.value = field;
		f.appendChild(inputField);

		cb.checked = true;
		f.boxchecked.value = 1;
		window.submitform(task);

		return false;
	};
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'notifications.purge')
		{
			if (confirm('Do you really want to purge all notifications? It will affect the dashboard summary.')) {
				Joomla.submitform(task);
			} else {
				return false;
			}
		}
		else
		{
			Joomla.submitform(task);
		}
	}

</script>

<?php

// Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_joompush&view=notifications'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>
			<?php 
			if (empty($this->status))
			{?>
				<div id="message" class="notice notice-error" style= "border-left-color: #dc3232; background: #ebccd1;
    border-left-style: solid; padding: 11px 12px;" >
		 <p><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_VIEW_INTRO');?></p></div>
			<?php }
			?>	
			<br>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search"
						   class="element-invisible">
						<?php echo JText::_('JSEARCH_FILTER'); ?>
					</label>
					<input type="text" name="filter_search" id="filter_search"
						   placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
						   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
						   title="<?php echo JText::_('JSEARCH_FILTER'); ?>"/>
				</div>
				<div class="btn-group pull-left">
					<button class="btn hasTooltip" type="submit"
							title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i></button>
					<button class="btn hasTooltip" id="clear-search-button" type="button"
							title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
						<i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit"
						   class="element-invisible">
						<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable"
						   class="element-invisible">
						<?php echo JText::_('JFIELD_ORDERING_DESC'); ?>
					</label>
					<select name="directionTable" id="directionTable" class="input-medium"
							onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
						<option value="asc" <?php echo $listDirn == 'asc' ? 'selected="selected"' : ''; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
						</option>
						<option value="desc" <?php echo $listDirn == 'desc' ? 'selected="selected"' : ''; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
						</option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<table class="table table-striped" id="notificationsList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTEMPLATES_ID', 'a.`id`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TYPE', 'a.`type`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JText::_('COM_JOOMPUSH_NOTIFICATIONTEMPLATES_KEY_GROUP'); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTEMPLATES_TITLE', 'a.`title`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTEMPLATES_MESSAGE', 'a.`message`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTEMPLATES_URL', 'a.`url`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONS_STATUS', 'a.`status`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONS_ISREAD', 'a.`isread`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONS_ISREAD_COUNT', 'a.`isread`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONES_SENT_BY', 'a.`sent_by`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_NOTIFICATIONTS_SENT_ON', 'a.`sent_on`', $listDirn, $listOrder); ?>
						</th>

						
					</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
				?>
					<tr class="row<?php echo $i % 2; ?>">

						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>

						<?php echo $item->id; ?>
						</td>		
						<td>
							<?php echo ucwords($item->type); ?>
						</td>
						<td>
							<?php if ($item->group_id) { echo ucwords($item->group_name); } else { echo substr($this->escape($item->key), 0, 30) . '...'; }?>
						</td>		
						<td>
							<?php echo $this->escape($item->title); ?>
						</td>				
						<td>
							<?php echo $item->message; ?>
						</td>				
						<td>
							<?php echo $item->url; ?>
						</td>
						
						<td>
							<?php if ($item->sent) { echo JText::_('COM_JOOMPUSH_NOTIFICATIONS_SENT'); } else { echo JText::_('COM_JOOMPUSH_NOTIFICATIONS_NOT_SENT'); } ?>
						</td>				
						<td>
							<?php if ($item->isread) { echo JText::_('COM_JOOMPUSH_NOTIFICATIONS_READ'); } else { echo JText::_('COM_JOOMPUSH_NOTIFICATIONS_NOT_READ'); } ?>
						</td>	
						<td class="center">
							<?php echo $item->isread;?>
						</td>				
						<td>
							<?php echo $item->sent_by; ?>
						</td>
						<td>
							<?php echo $item->sent_on; ?>
						</td>

					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>        
