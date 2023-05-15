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

if (isset($this->items->id))
{
	$param = json_decode($this->items->params);
}	
?>

<div class="container">  	
  <table class="table table-bordered">
    <thead style="background: lightgray;">
      <tr>
		  
        <th><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_KEY');?></th>
        <?php if (isset($this->items->id))
        {
		?>
        <th><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_RENEWS_ON');?></th>
        <th><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_STATUS');?></th>
       <?php 
		}
		?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><form action="index.php?option=com_joompush&task=licence.verify" method="post">
			<?php 
			if (isset($this->items->id) && $this->items->status)
			{
				echo  '<label>' . $param->key .' </label>';
			}
			else
			{?>
			<input name="license_key" id="license_key" type="text" size="37" aria-required="true" value="<?php if (isset($this->items->id) && $param->key) { echo $param->key; } ?>">	
			<?php } ?>
			</td>
		<?php if (isset($this->items->id))
		{
		?>	
			<td><?php echo $param->expires; ?>  </td>
			
			<td><?php 
			if($this->items->status)
			{
				echo Jtext::_('COM_JOOMPUSH_LICENCE_STATUS_ACTIVE');
			}
			else
			{
				echo Jtext::_('COM_JOOMPUSH_LICENCE_STATUS_EXPIRE');
			}
			?>  </td>
			</tr>
        <?php
		}
		?>
    </tbody>
  </table>
  <?php if(!isset($this->items) || !$this->items->status) 
  { ?>
	   <input type="submit" class="btn btn-info" value="<?php echo Jtext::_('COM_JOOMPUSH_LICENCE_BTN_ACTIVATE_LICENCE');?>">
  <?php } ?>
	 <br><br><br>
	  <p style="font-size: 12px;"><?php echo Jtext::_('COM_JOOMPUSH_LICENCE_FOOTER_NOTE');?>.</p>

</div>
</form>
