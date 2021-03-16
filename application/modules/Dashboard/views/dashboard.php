
<link href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/amchart/export.css" type="text/css" media="all" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/css/bootstrap-multiselect.css"   type="text/css" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/css/awesome-bootstrap-checkbox.css" type="text/css" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/css/dashboard.css"   type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/css/tooltipster.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/js/jquery.tooltipster.min.js"></script>
<style>
	.downloadbtndiv
	{
		position: absolute;
		z-index: 99;
		margin-left: 120px;
	}
	.downloadbtndiv1
	{
		position: absolute;
		z-index: 99;
		margin-left: 120px;
	}
	.pad-tb20
	{
		padding-top: 20px;
		padding-bottom: 20px;

	}
	.btn-download
	{
		background-color: #f44336;
	}
	.btn-download:active,.btn-download:focus,.btn-download:active:focus {
		color: #fff;
		background-color: #919191;
		border-color: #595959;
	}
	.btn-download:hover
	{
		background-color: #f44336;
	}
	.btn-download.focus, .btn-download:focus {
		color: #fff;
		background-color: #f44336;
		border-color: #f44336;
	}
	.dashboardview {
		margin-top: 15px;
	}

	.table-title-heading{
		padding: 10px;
		margin-top: -15px !important;
	}
	.pd-btm-0 {
		padding-bottom: 0px;
	}

	.margin-minus8 {
		margin: -8px;
	}

	.mt--15 {
		margin-top: -15px;
	}

	.mb-15 {
		margin-bottom: 15px !important;
	}

	.bulk-notes {
		list-style-type: none
	}

	.bulk-notes li:before {
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap {
		white-space: nowrap
	}

	.table-format>thead>tr>th {
		font-size: 12px;
	}

	.bmd-form-group .bmd-label-floating, .bmd-form-group .bmd-label-placeholder {
		top: -18px;
	}
	input {
		margin-bottom: 5px;
	}

	.dropdown-toggle:after{
		display: none;
	}

	.dropdown-menu-right{
		top: 0px ! important;
	}


       .ct-chart {
           position: relative;
       }
       .ct-legend {
           position: relative;
           z-index: 10;
           list-style: none;
           text-align: center;
       }
       .ct-legend li {
           position: relative;
           padding-left: 23px;
           margin-right: 10px;
           margin-bottom: 3px;
           cursor: pointer;
           display: inline-block;
       }
       .ct-legend li:before {
           width: 12px;
           height: 12px;
           position: absolute;
           left: 0;
           content: '';
           border: 3px solid transparent;
           border-radius: 2px;
       }
       .ct-legend li.inactive:before {
           background: transparent;
       }
       .ct-legend.ct-legend-inside {
           position: absolute;
           top: 0;
           right: 0;
       }
       .ct-legend.ct-legend-inside li{
           display: block;
           margin: 0;
       }
       .ct-legend .ct-series-0:before {
           background-color: #d70206;
           border-color: #d70206;
       }
       .ct-legend .ct-series-1:before {
           background-color: #f05b4f;
           border-color: #f05b4f;
       }
       .ct-legend .ct-series-2:before {
           background-color: #f4c63d;
           border-color: #f4c63d;
       }
       .ct-legend .ct-series-3:before {
           background-color: #d17905;
           border-color: #d17905;
       }
       .ct-legend .ct-series-4:before {
           background-color: #453d3f;
           border-color: #453d3f;
       }

.morris-hover.morris-default-style {
    font-size: 12px;
    text-align: center;
    border-radius: 5px;
    padding: 10px 12px;
    background: rgb(0 0 0 / 80%);
    color: #f3f3f3;
    font-family: Montserrat, sans-serif;
}

</style>

<div class="col-md-12 dashboardview">
	<div class="row">  
		<div class="col-md-12 text-right"> 
			<button class="btn btn-link btn-warning btn-xs filterbtn" id="orderfilter"><i class="icon-filter3"></i></button> 
		</div> 
	</div>
	<div id="advancedFilterForReport">
		<fieldset class="advancedsearchdiv">
			<form id="advancedsearchdata">
				<div class="col-md-12 pd-0">
					<div class="row ">
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
								<select class="select2picker form-control" data-toggle="select2" id="adv_WorkflowModuleUID" name="adv_WorkflowModuleUID">
									<option selected>Select</option>
									<?php 
									foreach ($WorkFlow as $Workflowkey => $row)
									{
										echo '<option value="'.$row->WorkflowModuleUID.'" '.($Workflowkey == 0 ? "selected" : "" ).'>'.$row->WorkflowModuleName.'</option>';
									}
									?>
								</select>
							</div>
						</div>

						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_Category"  class="bmd-label-floating">Period</label>
								<select class="select2picker form-control" data-toggle="select2" id="adv_period" name="adv_period">
									<option  value="today">Today</option>
									<option  value="week" selected>Current Week</option>                
									<option  value="month">Current Month</option>             
									<option  value="LYear">Current Year</option>             
									<option  value="Custom" hidden>Custom</option>             
								</select>
							</div>
						</div>
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_Groups"  class="bmd-label-floating">Date Range</label>
								<div class="input-daterange input-group" id="date-range">
									<input type="text" class="form-control checklistdatepicker" id="adv_fromDate" name="start" placeholder="From" value="<?php echo date("m/d/Y",strtotime("7 days ago")); ?>">
									<div class="col-md-2 text-center pd-0">	
										<button class="btn btn-white mt-0">TO</button>
									</div>
									<input type="text" class="form-control checklistdatepicker" id="adv_toDate" name="end" placeholder="To" value="<?php echo date('m/d/Y'); ?>">
									<input type="hidden" class="DateRange" value="<?php echo date("m/d/Y",strtotime("7 days ago")); ?>">
									<input type="hidden" class="DateRangeTo" value="<?php echo date('m/d/Y'); ?>">
								</div>
							</div>
						</div>
					</div>
				</div>

			</form>
		</fieldset>
	</div>



	<!-- INFLOW -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="Inflowcard">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Inflow Report
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						
						<div class="col-md-9"></div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker InflowType" id="InflowChart" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="ChartPeriodic" selected>Chart - Periodic</option>
								<option value="ChartIndividual">Chart - Individual</option>
								<option value="TablePeriodic">Table - Periodic</option>
								<option value="TableIndividual">Table - Individual</option>
							</select>
						</div>
						<div class="col-md-1 text-right" style="padding-left: 0px;">
								
							<div class="dropdown float-right" style="margin-left: 5px;">
								
								<input type='hidden' id="InflowType">
							</div>
							<button id="InflowRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>

					</div>
				</div>

				<div class="card-body pd-0 dd<?php echo $this->RoleUID;?>">
					<div class="col-md-12 InflowRefresh">
						<div class="col-md-12 TableProcessInflow scrool mb-15">


						</div>
						<div class="ChartDiv" style="height: 300px;">
							<canvas id="myChart" ></canvas>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<!--INFLOW END-->

<!-- SUCCESS QUEUE -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="Queueprogresscard">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Queue Progress
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10">
						<div class="col-md-9"></div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="form-control select2picker SuccessQueueType" id="SuccessQueueChart" name="SuccessQueueChart" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="SuccessChartPeriodic" selected>Chart - Periodic</option>
								<option value="SuccessChartIndividual">Chart - Individual</option>
								<option value="SuccessTablePeriodic">Table - Periodic</option>
								<option value="SuccessTableIndividual">Table - Individual</option>
							</select>
						</div>
						<div class="col-md-1 text-right" style="padding-left: 0px;">
								
							<div class="dropdown float-right" style="margin-left: 5px;">
								
								<input type='hidden' id="SuccessQueueType">
							</div>
							<button id="QueueRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>

							<div class="col-md-3" style="padding-right: 0px;margin-left: 500px;">
							
	
						</div>

						<div class="col-md-12 text-right">

							<div class="dropdown float-right" style="margin-left: 5px;">
								<!--
								<a href="#" class="dropdown-toggle card-drop arrow-none" data-toggle="dropdown" aria-expanded="false">
									<div><i class="fa fa-ellipsis-v arrow-none" aria-hidden="true"></i></div>
								</a>
								<div class="dropdown-menu dropdown-menu-right" style="top: 67%!important;">
									<a class="dropdown-item dropdown-view" id="queueprogress_periodwise" name="queueprogress_periodwise" href="javascript:void(0)" selected>Periodic</a>
									<a class="dropdown-item dropdown-view" id="queueprogress_individualwise" name="queueprogress_individualwise" href="javascript:void(0)">Individual</a>
								</div>
								-->
								<input type="hidden" id="SuccessType" value="Success">
							</div>
							<!--
							<button id="QueueRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
							-->

						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">

					<div class="col-md-12 QueueRefresh">

						<div class="col-md-12 TableDiv mb-15">


						</div>

						<div id="chart" name="chart"></div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<!--SUCCESS END-->


	<!-- PRODUCTIVITY -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="productivitycard">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Productivity 
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="col-md-8">
							<label for="" class="col-form-label" style="float: right;">Target </label>
						</div>
						<div class="col-md-1">
							<?php if(in_array($this->RoleType,$this->config->item('SuperAccess')))
							{?>
								<span class="bmd-form-group"><input type="text" class="form-control" id="adv_Target" name="adv_Target" value="<?php echo ($getTarget->ProductivityTarget) ? ($getTarget->ProductivityTarget) : 10; ?>" style="margin-top: -6px;"></span>
							<?php }else{ ?>
								<span class="bmd-form-group"><input type="text" class="form-control" id="adv_Target" name="adv_Target" value="<?php echo ($getTarget->ProductivityTarget) ? ($getTarget->ProductivityTarget) : 10; ?>" disabled style="margin-top: -6px;"></span>
							<?php } ?>
						</div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker productivitydropdown" id="ProductivityChartType" style="border: 1px solid #dedede !important;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="ChartPeriodic" selected>Chart - Periodic</option>
								<option value="ChartIndividual">Chart - Individual</option>
								<option value="TablePeriodic">Table - Periodic</option>
								<option value="TableIndividual">Table - Individual</option>
							</select>
						</div>
						<div class="col-md-1 text-right" style="padding-left: 0px;">
							<input type="hidden" class="inflowtype" id="productivitydropdown">
							<button id="productivityRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">

					<div class="col-md-12 productivityRefresh">
						<div class="col-md-12 TableInflow mb-15">

						</div>
						<div class="col-md-12 TableInflowindividual mb-15" id="Workflow-Userslist">

						</div>
						<div class="col-md-12 productivityChart mb-15" style="height: 300px;">
							<canvas id="myChartMixed"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--PRODUCTIVITY END-->

	<!-- AGING REPORT -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="Aging_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Aging Report
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10">

						<div class="col-md-8">
						</div>
						<div class="col-md-1">
						</div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker agingdropdown" id="AgingChartType" style="border: 1px solid #dedede !important;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="ChartAgingPeriodic" selected>Chart - Periodic</option>
								<option value="ChartAgingIndividual">Chart - Individual</option>
								<option value="ChartAgingCateory">Chart - Category</option>
								<option value="ChartAgingQueue">Chart - Queue</option>
								<option value="ChartAgingSubQueue">Chart - Sub Queue</option>

								<option value="TableAgingPeriodic">Table - Periodic</option>
								<option value="TableAgingIndividual">Table - Individual</option>
								<option value="TableAgingCateory">Table - Category</option>
								<option value="TableAgingQueue">Table - Queue</option>
								<option value="TableAgingSubQueue">Table - Sub Queue</option>
							</select>
						</div>
						<div class="col-md-1 text-right" style="padding-left: 0px;">
							<input type="hidden" class="agingtype" id="agingdropdown">
							<button id="AgingRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>


						<div class="col-md-12 text-right">
							<!--
							<div class="dropdown float-right" style="margin-left: 5px;">
								<a href="#" class="dropdown-toggle arrow-none" data-toggle="dropdown" aria-expanded="false">
									<div><i class="fa fa-ellipsis-v arrow-none" aria-hidden="true"></i></div>
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<a class="dropdown-item dropdown-view" id="agingreport_individualwise" name="agingreport_individualwise" href="javascript:void(0)" selected>Individual</a>
									<a class="dropdown-item dropdown-view" id="agingreport_periodwise" name="agingreport_periodwise" href="javascript:void(0)">Periodic</a>
									<a class="dropdown-item dropdown-view" id="agingreport_categorywise" name="agingreport_categorywise" href="javascript:void(0)">Category</a>
									<a class="dropdown-item dropdown-view" id="agingreport_queue" name="agingreport_queue" href="javascript:void(0)">Queue</a>
									<a class="dropdown-item dropdown-view" id="agingreport_subqueue" name="agingreport_subqueue" href="javascript:void(0)">Sub Queue</a>

								</div>
							</div>
						
							<button id="AgingRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						-->
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">

					<div class="col-md-12 AgingRefresh">

						<div class="col-md-12 AgingTableDiv mb-15">


						</div>
						
						<div class="col-md-12 agingChart mb-15">
						<div id="stack" name="stack"></div>
						</div>


					</div>
				</div>
			</div>
		</div>
	</div>
	<!--AGING END-->

	<!-- PIPELINE START -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="GateKeeping_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						GateKeeping 
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="row col-md-12" style="padding-right: 0px;">
							<div class="col-md-9" style="padding-right: 0px;"></div>
							<div class="col-md-2" style="padding-right: 0px;">
								<select class="select2picker GatekeepingType" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
									<option value="TotalReviewedChart" selected>Chart Total Reviewed</option>
									<option value="TotalReviewedTable">Table Total Reviewed</option>
									<option value="AgingChart">Chart Aging</option>
									<option value="AgingTable">Table Aging</option>
								</select>
								<input type="hidden" id="GatekeepingType">
							</div>
							<div class="col-md-1 text-right" style="padding-right: 0px;">
								
								<button id="PipeLineRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">

					<div class="col-md-12 PipeLineRefresh">

						<div class="col-md-12 PipeLineTableDiv mb-15">

						</div>
						<div class="col-md-12 PipeLineChart mb-15" style="height:320px" >
							<canvas id="doughnut-chart" style="height:320px" ></canvas>
						</div>
						<div class="col-md-12 PipeLineAgingChart mb-15" style="height:320px" >
							<div id="GatekeepingAgingChart" style="height:320px" ></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--PIPELINE END-->


	<!-- Quality report tab start -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="Quality_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Quality Report
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="row col-md-12" style="padding-right: 0px;">
							<div class="col-md-10">
								
							</div>
							<div class="col-md-1">
								
							</div>
							<div class="col-md-1 text-right" style="padding-right: 0px;">
								<div class="dropdown float-right" style="margin-left: 5px;">
									<a href="#" class="dropdown-toggle card-drop arrow-none" data-toggle="dropdown" aria-expanded="false">
										<div><i class="fa fa-ellipsis-v arrow-none" aria-hidden="true"></i></div>
									</a>
									<div class="dropdown-menu dropdown-menu-right" style="top: 67%!important;">
										<a class="dropdown-item QualityType" id="QualityTypeTeam" href="javascript:void(0)" data-type="Team" selected>Periodic</a>
										<a class="dropdown-item QualityType" id="QualityTypeIndividual" name="indPeriod" data-type="Individual"  href="javascript:void(0)">Individual</a>
									</div>
									<input type="hidden" id="QualityType">
								</div>
								<button id="QualityRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">
					<div class="col-md-12 QualityRefresh">
						<div class="col-md-12 TableQualityReport scrool  mb-15">


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--Quality report END-->


	<!-- Pending Files report tab start -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="Pending_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Gatekeeping Pending Files
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="row col-md-12" style="padding-right: 0px;">
						<div class="col-md-9" style="padding-right: 0px;"></div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker PendingFileType" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="PendingChart" selected>Chart View</option>
								<option value="PendingTable">Table View</option>
							</select>
							<input type="hidden" id="PendingFileType">
						</div>
							<div class="col-md-1 text-right" style="padding-right: 0px;">
								
								<button id="PendingRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">
					<div class="col-md-12 PendingRefresh">
						<div class="col-md-12 TablePendingReport scrool  mb-15">

						</div>
						<div class="col-md-12 PendingChart scrool  mb-15">
							<div class="" style="height:320px" id="chartdiv"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--Quality report END-->


	<!-- TAT Aging START -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="TATAging_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						TAT Report 
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10">

						<div class="row col-md-12" style="padding-right: 0px;">
						<div class="col-md-9" style="padding-right: 0px;"></div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker TATAgingDrop" id="TATAgingDrop" name="TATAgingDrop" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="TATAgingChart" selected>Chart View</option>
								<option value="TATAgingTable">Table View</option>
							</select>
							<input type="hidden" id="TATAgingDropType">
						</div>
							<div class="col-md-1 text-right" style="padding-right: 0px;">
								
							<button id="TATAgingRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
							</div>
						</div>

						<div class="col-md-12 text-right">
							<!--
							<div class="dropdown float-right" style="margin-left: 5px;">
								
								<a href="#" class="dropdown-toggle arrow-none" data-toggle="dropdown" aria-expanded="false">
									<div><i class="fa fa-ellipsis-v arrow-none" aria-hidden="true"></i></div>
								</a>

								<div class="dropdown-menu dropdown-menu-right">
								
									<a class="dropdown-item dropdown-view" id="pipeline_inflowsla" name="pipeline_inflowsla" href="javascript:void(0)" selected>Inflow SLA</a>
									<a class="dropdown-item dropdown-view" id="pipeline_pendingfilewise" name="pipeline_pendingfilewise" href="javascript:void(0)">Pending Files</a>
								
									<a class="dropdown-item dropdown-view" id="tat_individual" name="pipeline_agingfilewise" href="javascript:void(0)">Individual</a>

									<a class="dropdown-item dropdown-view" id="tat_periodic" name="pipeline_agingfilewise" href="javascript:void(0)">Periodic</a>

								</div>
							</div>
							-->
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd">

					<div class="col-md-12 TATAgingRefreshDIV">

						<div class="col-md-12 TATAgingTableDiv mb-15">


						</div>

						<div class="col-md-12 agingChart mb-15">
                       		<div id="multi-line-chart" class="ct-chart ct-golden-section"></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!--TAT Aging END-->

	

	<!-- FollowUp -->
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="FollowUpcard">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						FollowUp Report
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="col-md-9"></div>
						<div class="col-md-2" style="padding-right: 0px;">
							<select class="select2picker FollowUpType" id="FollowUpChart" style="border: 1px solid #dedede !important;margin-top: -10px;width: 100%;">
								<!-- <option value="">Select Chart</option> -->
								<option value="ChartPeriodic">Chart - Periodic</option>
								<option value="ChartIndividual">Chart - Individual</option>
								<option value="TablePeriodic" selected>Table - Periodic</option>
								<option value="TableIndividual">Table - Individual</option>
							</select>
						</div>
						<div class="col-md-1 text-right" style="padding-left: 0px;">
								
							<div class="dropdown float-right" style="margin-left: 5px;">
								
								<input type='hidden' id="FollowUpType">
							</div>
							<button id="FollowUpRefresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>
					</div>
				</div>

				<div class="card-body pd-0 dd<?php echo $this->RoleUID;?>">
					<div class="col-md-12 FollowUpRefresh">
						<div class="col-md-12 TableProcessFollowUp scrool mb-15">


						</div>
						<div class="FollowUpChartDiv" style="height: 300px;">
							<div id="FollowUpmyChart" style="height: 300px;" ></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--FollowUp END-->


</div>

<!-- Orders Modal Start -->
<?php $this->load->view('Dashboard/orderstable'); ?>
<!-- Orders Modal End -->

<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/multiselect/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/dashboard.js?reload=3.7"  type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/Chart.min.js"  type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utils.js" type="text/javascript" ></script>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

 <script src="https://d3js.org/d3.v5.min.js" charset="utf-8"></script>
 <script src="<?php echo base_url(); ?>assets/js/c3.js"></script>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
 
 <link rel="stylesheet" href="<?php echo base_url();?>assets/css/c3.css" type="text/css" />

<script src="<?php echo base_url(); ?>assets/js/morris.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.2/raphael-min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/morris.css" type="text/css" />

<script src="<?php echo base_url(); ?>assets/js/chartist.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/chartist-plugin-tooltip.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/chartist.min.css">
<link href="<?php echo base_url();?>assets/css/app.min.css" rel="stylesheet" type="text/css"  id="app-stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.1/chartist-plugin-legend.min.js"></script>

 <link rel="stylesheet" href="<?php echo base_url();?>assets/css/_email.scss" type="text/css" />


