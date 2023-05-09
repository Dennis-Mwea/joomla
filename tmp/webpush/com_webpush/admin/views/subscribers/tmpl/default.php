<?php

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleDeclaration('.table-responsive {width: 100%; overflow: auto}');

$listOrder = $this->escape($this->filter_order);
$listDirn  = $this->escape($this->filter_order_Dir);
?>

<form>
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
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<thead>
					<tr>
						<th width="1%"><?php echo JText::_('COM_WEBPUSH_NUM'); ?></th>

						<th width="2%">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>

						<th width="2%">
							<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_SUBSCRIBERS_SUBSCRIBER_ID', 'subscribable_id', $listDirn, $listOrder); ?>
						</th>

						<th width="50%">
							<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_SUBSCRIBERS_SUBSCRIPTION_ENDPOINT', 'endpoint', $listDirn, $listOrder); ?>
						</th>

						<th width="28%">
							<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_SUBSCRIBERS_SUBSCRIPTION_PUBLIC_KEY', 'public_key', $listDirn, $listOrder); ?>
						</th>

						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_SUBSCRIBERS_SUBSCRIPTION_AUTH_TOKEN', 'auth_token', $listDirn, $listOrder); ?>
						</th>

						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'COM_WEBPUSH_SUBSCRIBERS_SUBSCRIPTION_CONTENT_ENCODING', 'content_encoding', $listDirn, $listOrder); ?>
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
									<?php echo $row->subscribable_id; ?>
								</td>
								<td>
									<?php echo $row->endpoint; ?>
								</td>
								<td>
									<?php echo $row->public_key; ?>
								</td>
								<td>
									<?php echo $row->auth_token; ?>
								</td>
								<td align="center">
									<?php echo $row->content_encoding ?? '-'; ?>
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
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>