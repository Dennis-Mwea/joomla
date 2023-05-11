<?php

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
JFactory::getDocument()->addStyleSheet(JUri::root() . 'media/com_webpush/css/form.css');
?>

<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task === 'notification_template.cancel') {
            Joomla.submitform(task, document.getElementById('notification_template-form'));
        } else {
            if (task !== 'notification_template.cancel' && document.formvalidator.isValid(document.id('notification_template-form'))) {

                Joomla.submitform(task, document.getElementById('notification_template-form'));
            } else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_("index.php?option=com_webpush&layout=edit&id={$this->item->id}"); ?>"
      method="post" enctype="multipart/form-data" name="adminForm" id="notification_template-form"
      class="form-validate">
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_WEBPUSH_TITLE_NOTIFICATION_TEMPLATE', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">

				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"/>
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('message'); ?>
					<?php echo $this->form->renderField('icon'); ?>
					<?php echo $this->form->renderField('url'); ?>

					<?php echo $this->form->renderField('created_by'); ?>
					<?php echo $this->form->renderField('modified_by'); ?>

					<input type="hidden" name="jform[created_on]" value="<?php if ($this->item->created_on) {
						echo $this->item->created_on;
					} else {
						echo JHtml::date('now', 'Y-m-d h:i:s');
					} ?>"/>

					<input type="hidden" name="jform[modified_on]"
					       value="<?php echo JHtml::date('now', 'Y-m-d h:i:s'); ?>"/>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
