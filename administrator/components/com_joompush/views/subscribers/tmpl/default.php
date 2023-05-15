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
JHTML::_('behavior.modal');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_joompush/assets/css/joompush.css');
$document->addStyleSheet(JUri::root() . 'media/com_joompush/css/list.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_joompush');
$saveOrder = $listOrder == 'a.`ordering`';

$jinput = JFactory::getApplication()->input;
$gid = $jinput->get('gid', 0, 'INT');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joompush&task=subscribers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'subscriberList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

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
		
		jQuery('.chk input:checkbox').prop('checked', true);
		jQuery('.icon-jpush').html('<i class="fa fa-send fa-fw" aria-hidden="true"></i>');
		jQuery('#toolbar-jpush button').addClass('btn-success');
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
	
	function addToGroup()
	{
		if (document.adminForm.boxchecked.value==0)
		{
			alert('Please first make a selection or rejection from the list.');
		}
		else
		{ 
			Joomla.submitbutton('subscribergroup.addToGroup')
		}
	}
</script>
<?php

// Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}


// new post url

if ($gid)
{
	$url = JRoute::_('index.php?option=com_joompush&view=subscribers&tmpl=component&gid=' . $gid);
	echo '<div class="clearfix"></div>
			<div class="panel-heading custom-header-panel">
				<h3 class="panel-title jptitle_group">&nbsp;&nbsp;<i class="fa fa-users"></i> ' . JText::_('COM_JOOMPUSH_ADD_USER_TO_GROUP_HEAD_TITLE') . '</h3>
			</div>';
}
else
{
	$url = JRoute::_('index.php?option=com_joompush&view=subscribers');
}
?>

<form action="<?php echo $url; ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar) && $gid == 0): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else :  ?>
		<div id="">
			<?php endif; ?>
			
			<?php 
			if (empty($this->status))
			{?>
				<div id="message" class="notice notice-error" style= "border-left-color: #dc3232; background: #ebccd1;
    border-left-style: solid; padding: 11px 12px;" >
		 <p><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_VIEW_INTRO');?></p></div>
			<?php }
			?><br>	

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
			
			<table class="table table-striped" id="subscriberList">
				<thead>
					<tr>
						<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<?php endif; ?>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value=""
							   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_SUBSCRIBERS_ID', 'a.`id`', $listDirn, $listOrder); ?>
						</th>
						<?php if (isset($this->items[0]->state)): ?>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_SUBSCRIBERS_KEY', 'a.`key`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_SUBSCRIBERS_USER_ID', 'a.`user_id`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_SUBSCRIBERS_BROWSER', 'a.`browser`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_JOOMPUSH_SUBSCRIBERS_CREATED_ON', 'a.`created_on`', $listDirn, $listOrder); ?>
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
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_joompush');
						$canEdit    = $user->authorise('core.edit', 'com_joompush');
						$canCheckin = $user->authorise('core.manage', 'com_joompush');
						$canChange  = $user->authorise('core.edit.state', 'com_joompush');
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
											  title="<?php echo $disabledLabel ?>">
										<i class="icon-menu"></i>
									</span>
									<input type="text" style="display:none" name="order[]" size="5"
											   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
										<i class="icon-menu"></i>
									</span>
								<?php endif; ?>
							</td>
							<?php endif; 
							$cls= '';
							
							if (isset($item->gm))
							{
								$cls = 'chk';
							}
							?>
							<td class="hidden-phone <?php echo $cls;?>">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<?php echo $item->id; ?>
							</td>
							<?php if (isset($this->items[0]->state)): ?>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'subscribers.', $canChange, 'cb'); ?>
							</td>
							<?php endif; ?>

							<td>
								<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'subscribers.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<?php if ($gid == 0 && $item->state == 1) : ?>
										<a class="modal" rel="{handler: 'iframe', size: {x: 550, y: 400}}" href="<?php echo JRoute::_('index.php?option=com_joompush&tmpl=component&view=subscriber&key=' . $item->id );?>" title="<?php echo JText::_('COM_JOOMPUSH_SEND_NOTIFICATION_TITLE'); ?>">
											<?php echo substr($this->escape($item->key), 0, 50) . '...'; ?>
										</a>
									<?php else : ?>
										<?php echo substr($this->escape($item->key), 0, 50) . '...'; ?>
									<?php endif; ?>
								<?php else : ?>
									<?php echo substr($this->escape($item->key), 0, 50) . '...'; ?>
								<?php endif; ?>
								<input type="hidden" name="kid[<?php echo $item->id?>]" value="<?php echo $item->key; ?>"/>
							</td>	
							<td>
								<?php 	
									if($item->username)
									{
										echo $item->username;
									}	
									else
									{
										echo JText::_('COM_JOOMPUSH_GUEST_USER');
									}
								?>
							</td>
							<td>
								<?php echo ucfirst($item->browser); ?>
							</td>
							<td>
								<?php echo $item->created_on; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					
					<div>	
					<?php
						$key = $jinput->get('key','','string');
						if($key)
						{	
							echo '<a class="modal" id="newurl" rel="{handler: \'iframe\', size: {x: 550, y: 400}}" href="'.  JRoute::_("index.php?option=com_joompush&tmpl=component&view=subscriber&key=" . $key) . '"></a>';
						
							$js =  " jQuery(document).ready(function () { 
							var anchorObj = document.getElementById('newurl');
							 if (anchorObj.click) {
								anchorObj.click()
							}
							jQuery('#').click();  });";
							
							$document->addScriptDeclaration($js);
						}
						
						?>
					</div>
				</tbody>
			</table>
			<div class="center">
			<?php
			if ($gid)
			{
				?>
				<input type="hidden" name="gid" value="<?php echo $gid; ?>"/>
				<a onclick="addToGroup();" id="add_group" class="btn btn-success">
				<i class="fa fa-plus" aria-hidden="true"></i>
				<?php echo JText::_('COM_JOOMPUSH_ADD_USER_TO_GROUP'); ?> </a>
				<?php
			}
			?>
			</div>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
		
		
</form>        
<div class="alert alert-success" style="display:none";>
