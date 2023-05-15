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

if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}

$config_data = $this->items;
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'configs.cancel') {
			Joomla.submitform(task, document.getElementById('configs-form'));
		}
		else {
			
			if (task != 'configs.cancel' && document.formvalidator.isValid(document.id('configs-form'))) {
				
				Joomla.submitform(task, document.getElementById('configs-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action=""
	method="post" enctype="multipart/form-data" name="adminForm" id="configs-form" class="form-validate">
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
			
				<div class="row-fluid">
					<div class="span10 form-horizontal">
						<h3><i class="fa fa-fire" aria-hidden="true"></i> <?php echo JText::_('COM_JOOMPUSH_SECTION_FCM'); ?></h3>
					<hr>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_server_key-lbl" for="jform_server_key" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_SERVER_KEY'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SERVER_KEY'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SERVER_KEY'); ?> 
							 </label>
							</div>
						<div class="controls">
							<input type="text" name="jform[server_key]" id="jform_server_key" value="<?php echo ($config_data['server_key']->params) ?  $config_data['server_key']->params : ''; ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SERVER_KEY');?>">
						</div>
					</div>
					
					<div class="control-group">
						<div class="control-label">
							<label id="jform_api_key-lbl" for="jform_api_key" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_API_KEY'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_API_KEY'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_API_KEY'); ?> 
							 </label>
							</div>
						<div class="controls">
							<input type="text" name="jform[api_key]" id="jform_api_key" value="<?php echo ($config_data['api_key']->params) ?  $config_data['api_key']->params : ''; ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_API_KEY');?>">
						</div>
					</div>
					
					<div class="control-group">
						<div class="control-label">
							<label id="jform_sender_id-lbl" for="jform_sender_id" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_SENDER_ID'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SENDER_ID'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SENDER_ID'); ?> 
							 </label>
							</div>
						<div class="controls">
							<input type="text" name="jform[sender_id]" id="jform_sender_id" value="<?php echo ($config_data['sender_id']->params) ?  $config_data['sender_id']->params : ''; ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_SENDER_ID');?>">
						</div>
					</div>
					
					<div class="control-group">
						<div class="control-label">
							<label id="jform_project_id-lbl" for="jform_project_id" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_PROJECT_ID'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_PROJECT_ID'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_PROJECT_ID'); ?> 
							 </label>
							</div>
						<div class="controls">
							<input type="text" name="jform[project_id]" id="jform_project_id" value="<?php echo ($config_data['project_id']->params) ?  $config_data['project_id']->params : ''; ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_PROJECT_ID');?>">
						</div>
					</div>	
				
					<div class="control-group">
						<div class="control-label">
							<label id="jform_icon-lbl" for="jform_icon" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_ICON'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_ICON'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_ICON'); ?> 
							 </label>
							</div>
						<div class="controls">
							<input type="text" name="jform[icon]" id="jform_project_id" value="<?php echo ($config_data['icon']->params) ?  $config_data['icon']->params : 'media/com_joompush/images/joompush.png'; ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_ICON');?>">
						</div>
					</div>	
					
					<div class="control-group">
						<div class="control-label">
							<label id="jform_url-lbl" for="jform_url_id" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_FORM_DESC_URL'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_URL'); ?>">	<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_URL'); ?> 
							 </label>
							</div>
						<div class="controls">
						<input type="text" name="jform[url]" id="jform_project_id" value="<?php echo ($config_data['url']->params) ?  $config_data['url']->params : JURI::root(); ?>" placeholder="<?php echo JText::_('COM_JOOMPUSH_FORM_LBL_URL');?>">
						</div>
					</div>	
					
					</div>
				</div>

				<input type="hidden" name="task" value=""/>
				<?php echo JHtml::_('form.token'); ?>

		</div>
	</div>
</form>
