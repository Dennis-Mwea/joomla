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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_joompush/css/form.css');
?>

<script>
jQuery(document).ready(function () {
	
});

function syncSubscribers()
{
	jQuery('#sync_header').hide();
	jQuery('#sync_notice').show();
	jQuery('#sync_body').show();
	syncKeys();
}

function syncKeys()
{
	jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_joompush&task=syncgroup.syncGroups",
			data: {'limit': 30},
			success: function(result) 
			{
				if (result == 'done')
				{
					jQuery('#sync_notice').hide();
					jQuery('#sync_body').hide();
					jQuery('#sync_result').show();
				}
				else if (!isNaN(result))
				{
					syncKeys();
				}
				else
				{
					jQuery('#sync_notice').hide();
					jQuery('#sync_body').hide();
					jQuery('#sync_result_fail').show();
				}
			}
	});
}
</script>
<div class="row">
  <div class="span4"></div>
  <div class="span4">
		<div id="sync_header" class="alert alert-warning alert-block">
			<h3 class="alert-heading"><?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC'); ?> </h3>
			<span><?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC_DESC'); ?></span><br /><br />
			<div class="center">
				<span><a href="#" onClick="syncSubscribers()" class="btn btn-success"><span class="icon-loop icon-white"></span> <?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC_SYCNBTN'); ?></a></span>
				<span><a href="<?php echo JRoute::_("index.php?option=com_joompush&view=subscribers"); ?>" class="btn btn-warning"><span class="icon-cancel icon-white"></span> <?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_CANCEL'); ?></a></span>
			</div>
		</div>

		<div class="alert" id="sync_notice" style="display: none;">
			<span class="icon-warning-circle"></span>
			<?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_WARNING'); ?>
		</div>
				
		<div id="sync_body" class="center" style="display: none;">
		<i class="fa fa-refresh fa-spin fa-5x fa-fw sync_loader" aria-hidden="true"></i><br />
		<span><strong><?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC_SUBTITLE'); ?></strong></span>
		</div>		
				
				
		<div id="sync_result" style="display: none;">
			<div class="alert alert-success alert-block">
				<h2 class="alert-heading"><?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_MESAGGE'); ?></h2>
				<div id="finishedframe center">
					<p><?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC__MESSAGE_1'); ?></p>

					<a class="btn btn-primary btn-large" href="<?php echo JRoute::_("index.php?option=com_joompush&view=subscribergroups"); ?>">
						<span class="icon-users icon-white"></span>
						<?php echo JText::_('COM_JOOMPUSH_GROUP_SYNC_MANGE_BTN'); ?>			
					</a>
					<!-- <a class="btn" id="ab-viewlog-success" href="http://localhost/new_cco/administrator/index.php?option=com_akeeba&amp;view=Log&amp;tag=backend.id46">
						<span class="icon-list"></span>
						<?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_LOG_BTN'); ?>			
					</a> -->
				</div>
			</div>
		</div>
		
		<div id="sync_result_fail" style="display: none;">
			<div class="alert alert-danger alert-block">
				<h2 class="alert-heading"><?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_MESAGGE_ERROR'); ?></h2>
				<div id="finishedframe center">
					<p><?php echo JText::_('COM_JOOMPUSH_SUBSCRIBER_SYNC_MESSAGE_ERROR_1'); ?></p>
				</div>
			</div>
		</div>
		
	</div>
	<div class="span4"></div>
</div>
