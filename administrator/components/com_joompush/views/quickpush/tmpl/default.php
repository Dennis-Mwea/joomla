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
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

jQuery(function() {

    jQuery('#login-form-link').click(function(e) {
		jQuery("#exsting-form").delay(100).fadeIn(100);
 		jQuery("#new-form").fadeOut(100);
		jQuery('#register-form-link').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#isnew').val('0');
		e.preventDefault();
	});
	jQuery('#register-form-link').click(function(e) {
		jQuery("#new-form").delay(100).fadeIn(100);
 		jQuery("#exsting-form").fadeOut(100);
		jQuery('#login-form-link').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#isnew').val('1');
		e.preventDefault();
	});

});

function sendMessage()
{
	var form_validate = true;
	
	if ( jQuery('#register-form-link').hasClass( 'active' ))
	{
		if(jQuery('#jform_title').val().length === 0 )
		{
			jQuery('#jform_title').addClass('required');
			jQuery('#jform_title').addClass('invalid');
			jQuery('#jform_title-lbl').addClass('required');
			jQuery('#jform_title-lbl').addClass('invalid');
			jQuery('#jform_title').attr('required','required');
			
			form_validate = false;
		}
		if(jQuery('#jform_message').val().length === 0 )
		{
			jQuery('#jform_message').addClass('required');
			jQuery('#jform_message').addClass('invalid');
			jQuery('#jform_message-lbl').addClass('required');
			jQuery('#jform_message-lbl').addClass('invalid');
			jQuery('#jform_message').attr('required','required');

			form_validate = false;
		}
	}
	else
	{
		
		jQuery.each(jQuery("#subscriber-form").serializeArray(), function(i, field) {
			var field_value = jQuery.trim(field.value);

			switch(field.name) {
			case 'jform[exstmsg]':
				if(field_value == 0 && field.value != '')
				{
					jQuery('#jform_exstmsg-lbl').addClass('required');
					jQuery('#jform_exstmsg-lbl').addClass('invalid');
					jQuery('#jformexstmsg_chzn').addClass('required');
					jQuery('#jformexstmsg_chzn').addClass('invalid');
					
					form_validate = false;
				}
			break;
			}
		});
		
		if(jQuery('#jformjpgroup_chzn ul li').length < 2)
		{
			jQuery('#jform_jpgroup-lbl').addClass('required');
			jQuery('#jform_jpgroup-lbl').addClass('invalid');
			
			form_validate = false;
		}
	}
	
	
	if (form_validate === true)
	{
		jQuery('#send-submit').attr('disabled',true);
		jQuery('#send-submit').attr('onclick','');
		
		jQuery('form#subscriber-form').submit();
		//~ var datastring = jQuery("#subscriber-form").serialize();
		
		//~ jQuery.ajax({
				//~ type: "POST",
				//~ url: "index.php?option=com_joompush&task=quickpush.sendNotification",
				//~ data: datastring,
				//~ success: function(result) 
				//~ {
					//~ jQuery(".panel-body").hide();
					
					//~ var new_result = JSON.parse(result);
					
					//~ if (new_result.message_id)
					//~ {
						//~ var msg = "Group message sent successfully";
						
						//~ jQuery(".alert-success").html(msg);
						//~ jQuery(".alert-success").show();

					//~ }
					//~ else if (new_result.total) 
					//~ {
						//~ var msg = new_result.success + " messages sent successfully out of " + new_result.total + ", and " + new_result.failure +" got failed.";
						
						//~ jQuery(".alert-success").html(msg);
						//~ jQuery(".alert-success").show();
					//~ }
					//~ else
					//~ {
						//~ var msg = "Something went wrong please try again later";
						
						//~ jQuery(".alert-error").html(msg);
						//~ jQuery(".alert-error").show();
					//~ }
					
					//~ setTimeout(function(){ window.top.location.href = "index.php?option=com_joompush&view=subscribers"; window.parent.SqueezeBox.close(); }, 3500);		
				//~ }
		//~ });
	}
}
</script>
<form
	action="<?php echo JRoute::_('index.php?option=com_joompush&task=quickpush.sendNotification'); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="subscriber-form" class="form-validate">

	<div class="container">
    	<div class="row-fluid">
			<div class="span6 offset3">
				<div class="panel panel-login" style="border: 1px solid #2D2D90; padding: 18px;">
					<div class="panel-heading custom-header-panel">
						<h3 class="panel-title jptitle"> <i class="fa fa-paper-plane"></i> <?php echo JText::_('COM_JOOMPUSH_QUICK_PUSH'); ?></h3>
					</div>
					<div>&nbsp;</div>
					<div class="panel-body">
						<div class="row-fluid">
							<div class="span12">
								<div class="row-fluid">
									<div class="span12 center jpselecter">
										<a href="#" class="active jpalink" id="login-form-link"><i class="fa fa-envelope"></i> <?php echo JText::_('COM_JOOMPUSH_USE_EXISTING_TEMPLATE'); ?></a> &nbsp;
										<a href="#" id="register-form-link"><i class="fa fa-pencil"></i> <?php echo JText::_('COM_JOOMPUSH_NEW_TEMPLATE'); ?></a>
										<hr>
									</div>
								</div>
								
								<div id="exsting-form" style="display: block;">
									<div class="row-fluid">
										<div class="span10 form-horizontal newmsg">
											<div class="control-group">
												<div class="control-label">
													<label id="jform_exstmsg-lbl" for="jform_exstmsg" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_USE_EXISTING_TEMPLATE'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_USE_EXISTING_TEMPLATE'); ?>">	<?php echo JText::_('COM_JOOMPUSH_USE_EXISTING_TEMPLATE'); ?> 
													 </label>
													</div>
												<div class="controls">
													<?php echo $this->exsting_msg; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div id="new-form" style="display: none;">
											
									<div class="row-fluid">
										<div class="span10 form-horizontal newmsg">
											<fieldset class="adminform">
												<input type="hidden" name="jform[id]" value="" />
												<input type="hidden" name="jform[state]" value="1" />
												<?php echo $this->form->renderField('title'); ?>
												<?php echo $this->form->renderField('message'); ?>
												<?php echo $this->form->renderField('icon'); ?>
												<?php echo $this->form->renderField('url'); ?>
												<?php echo $this->form->renderField('created_by'); ?>
												<?php echo $this->form->renderField('modified_by'); ?>				
												<input type="hidden" name="jform[created_on]" value="<?php echo JHtml:: date ('now', 'Y-m-d h:i:s', true); ?>" />
												<input type="hidden" name="jform[modified_on]" value="<?php echo JHtml:: date ('now', 'Y-m-d h:i:s', true); ?>" />
												<input type="hidden" name="jform[isnew]" id="isnew" value="" />
												<?php
												if (isset($this->form->key))
												{
													foreach($this->form->key as $k=>$key)
													{
														echo '<input type="hidden" name="jform[key][' . $k .']" value="' . $key .'" />';
													}
												}
												?>
											</fieldset>
										</div>
									</div>
									
								</div>
								
								<div id="exsting-form" style="display: block;">
									<div class="row-fluid">
										<div class="span10 form-horizontal newmsg">
											<div class="control-group">
												<div class="control-label">
													<label id="jform_jpgroup-lbl" for="jform_jpgroup" class="hasPopover" title="<?php echo JText::_('COM_JOOMPUSH_TITLE_GROUPS'); ?>" data-original-title="<?php echo JText::_('COM_JOOMPUSH_TITLE_GROUPS'); ?>">	<?php echo JText::_('COM_JOOMPUSH_TITLE_GROUPS'); ?> 
													 </label>
													</div>
												<div class="controls">
													<?php echo $this->jpgroups; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>

						<div class="row">
							<div class="span12 center">
								<a id="send-submit" href="#" onClick="sendMessage();" class="btn btn-send btn-success"><i class="fa fa-paper-plane"></i> Send</a>
							</div>
						</div>
					</div>
				</div>
				
				<div class="alert alert-success" style="display:none";>
				<div class="alert alert-error" style="display:none";>
					
				</div>
			</div>
		</div>
	</div>
</form>
