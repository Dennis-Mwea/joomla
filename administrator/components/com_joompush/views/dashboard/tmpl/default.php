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

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_joompush/assets/css/dashboard.css');
$document->addScript('https://www.gstatic.com/charts/loader.js');
?>
<style>
.chart {
  width: 100%; 
  min-height: 350px;
}
</style>
<script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable(<?php echo $this->SubscriberChart; ?>);

      var options = {
		vAxis: {format: '0'},
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
      };

      var chart = new google.visualization.ColumnChart(document.getElementById("reportcolumnchart"));
      chart.draw(data, options);
  }

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

      function drawChart1() {
        var data = google.visualization.arrayToDataTable(<?php echo $this->NotificationChart; ?>);

        var options = {
          title: '<?php echo JText::_('COM_JOOMPUSH_DASHBOARD_NOTIFICATION_CHART_TITLE'); ?>',
          curveType: 'function',
          vAxis: {viewWindowMode: "explicit", viewWindow:{ min: 0 }}, 
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('reportlinechart'));

        chart.draw(data, options);
      }
      
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart2);
      
       function drawChart2() {
        var data = google.visualization.arrayToDataTable(<?php echo $this->GroupNotificationChart; ?>);

        var options = {
          title: '<?php echo JText::_('COM_JOOMPUSH_DASHBOARD_GROUP_NOTIFICATION_CHART_TITLE'); ?>',
          curveType: 'function',
          vAxis: {viewWindowMode: "explicit", viewWindow:{ min: 0 }}, 
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('groupreportlinechart'));

        chart.draw(data, options);
      }
      
      jQuery(window).resize(function(){
  drawChart();
  drawChart1();
  drawChart2();
});
    </script>
    	
<?php
// Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}

if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	<!-- product info -->
	
	<div class="wepinfo-cover">
		<div class="wepinfo-container">
				
				<div class="row">
					<div class="span12">
						<div class="center"><h3>STAY UPDATED </h3></div>
						<div class="list-group">
							<a href="https://extensions.joomla.org/write-review/review/add/?extension_id=12890" target="_blank" class="list-group-item visitor">
								<span class="pull-right sicon">
									<i class="fa fa-eye"></i>
								</span>
								<p class="list-group-item-text">
									Give us a Review</p>
							</a>
							<a href="https://www.facebook.com/Weppsol/" target="_blank" class="list-group-item facebook-like">
								<span class="pull-right sicon">
									<i class="fa fa-facebook-square"></i>
								</span>
								<p class="list-group-item-text">
									Like us on Facebook</p>
							</a>
							<a href="https://plus.google.com/+weppsol" target="_blank" class="list-group-item google-plus">
								<span class="pull-right sicon">
									<i class="fa fa-google-plus-square"></i>
								</span>
								<p class="list-group-item-text">
									Like us on Google+</p>
							</a>
						
							<a href="https://twitter.com/weppsol" target="_blank" class="list-group-item twitter">
								<span class="pull-right sicon">
									<i class="fa fa-twitter-square"></i>
								</span>
								<p class="list-group-item-text">
									Follow us on Twitter</p>
							</a>
							<a href="https://weppsol.com/support/documentation/joompush-documentation" target="_blank" class="list-group-item document">
								<span class="pull-right sicon">
									<i class="fa fa-file-text"></i>
								</span>
								<p class="list-group-item-text">
									Documentation</p>	
							<a href="https://weppsol.com/support/forum/forums/joompush/general-questions-issues" target="_blank" class="list-group-item suppoet">
								<span class="pull-right sicon">
									<i class="fa fa-life-ring"></i>
								</span>
								<p class="list-group-item-text">
									Support Forums</p>
							</a>
							</a>
						</div>
					</div>
				</div>
	
		</div>
	</div>
	<!-- end product info -->
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
			<!--- Panel Subscribers -->
					<div class="panel panel-info">
						
						<div class="panel-heading">
							
							<h3 class="panel-title"><i class="fa fa-users fa-fw" aria-hidden="true"></i> <?php echo JText::_('COM_JOOMPUSH_TITLE_SUBSCRIBERS'); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row-fluid">
							  <div class="span6"><div id="reportcolumnchart" class="chart"></div></div>
							  <div class="span6">
									<div class="row-fluid">
										<div class="span2"></div>
										<div class="span3 tiles orange-tiles">
											<div class="tile-icon"><i class="fa fa-user" aria-hidden="true"></i> </div>
											<div class="tile-count"><?php echo $this->today_subsciber; ?></div>	
											<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_SUBSCRIBED_TODAY'); ?></div>	
										</div>
										<div class="span2"></div>
										<div class="span3 tiles green-tiles">
											<div class="tile-icon"><i class="fa fa-user-plus" aria-hidden="true"></i> </div>
											<div class="tile-count"><?php echo $this->total_subsciber; ?></div>	
											<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_SUBSCRIBED_TOTAL'); ?></div>	
										</div>
										<div class="span2"></div>
									</div>
							  </div>
							</div>
						</div>
					</div>
					
			<!--- Panel Notifications -->		
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fa fa-bullhorn fa-fw" aria-hidden="true"></i> <?php echo JText::_('COM_JOOMPUSH_TITLE_PUSH_NOTIFICATIONS'); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row-fluid">
							  <div class="span6"><div id="reportlinechart" class="chart"></div></div>
							  <div class="span6">
									<div class="row-fluid">
										<div class="span2"></div>
										<div class="span3 tiles green-tiles">
												<div class="tile-icon"><i class="fa fa-send-o" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->today_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_TODAY'); ?></div>	
										</div>
										<div class="span2"></div>
										<div class="span3 tiles orange-tiles">
												<div class="tile-icon"><i class="fa fa-calendar" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->total_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_ALL_TIME'); ?></div>	
										</div>
										<div class="span2"></div>
									</div>						  
									<div class="row-fluid">
										<div class="span12"></div>
									</div>
									<div class="row-fluid">
										<div class="span2"></div>
										<div class="span3 tiles open-tiles">
												<div class="tile-icon"><i class="fa fa-envelope-open-o " aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->today_open_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_OPEN_TODAY'); ?></div>	
										</div>
										<div class="span2"></div>
										<div class="span3 tiles allopen-tiles">
												<div class="tile-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->total_open_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_OPEN_ALL_TIME'); ?></div>	
										</div>
										<div class="span2"></div>
									</div>						  
							  </div>
							</div>
						</div>
					</div>
			<!-- Pannel end -->	
			<!--- Panel group Notifications -->		
					<div class="panel panel-warning">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fa fa-group fa-fw" aria-hidden="true"></i> <?php echo JText::_('COM_JOOMPUSH_TITLE_GROUP_PUSH_NOTIFICATIONS'); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row-fluid">
							  <div class="span6"><div id="groupreportlinechart" class="chart"></div></div>
							  <div class="span6">
									<div class="row-fluid">
										<div class="span2"></div>
										<div class="span3 tiles green-tiles">
												<div class="tile-icon"><i class="fa fa-send-o" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->today_group_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_TODAY'); ?></div>	
										</div>
										<div class="span2"></div>
										<div class="span3 tiles orange-tiles">
												<div class="tile-icon"><i class="fa fa-calendar" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->total_group_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_ALL_TIME'); ?></div>	
										</div>
										<div class="span2"></div>
									</div>						  
									<div class="row-fluid">
										<div class="span12"></div>
									</div>
									<div class="row-fluid">
										<div class="span2"></div>
										<div class="span3 tiles open-tiles">
												<div class="tile-icon"><i class="fa fa-envelope-open-o " aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->today_open_group_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_OPEN_TODAY'); ?></div>	
										</div>
										<div class="span2"></div>
										<div class="span3 tiles allopen-tiles">
												<div class="tile-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> </div>
												<div class="tile-count"><?php echo $this->total_open_group_notifications; ?></div>	
												<div class="tile-title"><?php echo JText::_('COM_JOOMPUSH_DASHBOARD_OPEN_ALL_TIME'); ?></div>	
										</div>
										<div class="span2"></div>
									</div>						  
							  </div>
							</div>
						</div>
					</div>
					
			<!-- Pannel end -->		
			
					
		</div>
					<!-- end dashboard-->
			<div class="clearfix"></div>
			
	</div>
	
