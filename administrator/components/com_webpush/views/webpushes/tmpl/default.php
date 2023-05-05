<?php

defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->filter_order);
$listDirn  = $this->escape($this->filter_order_Dir);
?>

<form action="index.php?option=com_webpush&view=webpushes" method="post" id="adminForm" name="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<div class="row-fluid">
				<div class="span6">
					<?php echo JText::_('COM_WEBPUSH_WEBPUSHES_FILTER'); ?>
					<?php
					echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
					?>
				</div>
			</div>

			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th width="1%"><?php echo JText::_('COM_WEBPUSH_NUM'); ?></th>
					<th width="2%">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="35%">
						<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_WEBPUSHES_NAME', 'name', $listDirn, $listOrder); ?>
					</th>
					<th width="60%">
						<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_WEBPUSHES_VALUE', 'value', $listDirn, $listOrder); ?>
					</th>
					<th width="2%">
						<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_ID', 'id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->items)) : ?>
					<?php foreach ($this->items as $i => $row) : ?>
						<tr>
							<td>
								<?php echo $this->pagination->getRowOffset($i); ?>
							</td>
							<td>
								<?php echo JHtml::_('grid.id', $i, $row->id); ?>
							</td>
							<td>
								<a href="<?php echo JRoute::_("index.php?option=com_webpush&task=webpush.edit&id=$row->id"); ?>"
								   title="<?php echo JText::_('COM_WEBPUSH_EDIT_WEBPUSH'); ?>">
									<?php echo $row->name; ?>
								</a>
							</td>
							<td>
								<?php echo $row->value; ?>
							</td>
							<td align="center">
								<?php echo $row->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
