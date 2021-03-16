<style>
	th, td { text-align: center; }
	.bold{ font-weight: bold;  }

	.card .card-header.card-header-icon .card-title,
	.card .card-header.card-header-text .card-title {
	 margin-top: 1px !important;
	 color: #ffffff;
	}
</style>
<div class="card mt-10" id="prioritycount-view">

	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<!-- <i class="icon-database-time2"></i> -->
			<h4 class="card-title">Pipeline Report</h4>
		</div>
<!-- 		<div class="row">
			<div class="col-md-6">
				<h4 class="card-title">Pipeline Report</h4>
			</div>
			<div class="col-md-6 text-right"> 
				<button class="btn btn-default btn-xs btn-link refresh-btn" style="font-size: 13px;color:#900C3F;cursor: pointer;"><i class="fa fa-filter"></i></button>
			</div>
		</div> -->
	</div>

	<div class="card-body">
	
		<!-- HEADER -->
		<?php $this->load->view('priorityheader'); ?>
		<!-- HEADER -->

		<!-- Filter  -->
		<?php $this->load->view('priorityfilter'); ?>
		<!-- Filter  -->
		
		<div class="col-md-12 material-datatable">

			<table class="table table-hover" id="priority-data" style="">
				<thead>
					<tr>

						<th class="text-center no-sort"><b>Team Leader</b></th>
						<th class="text-center no-sort"><b>Total Pipeline</b></th>
						<?php foreach ($Workflows as $key => $Workflow) { ?>
							<th><?php echo $Workflow->WorkflowModuleName.' Completion'; ?></th>
						<?php } ?>
					</tr>

				</thead>
				<tbody id="OrderAge_Count">
					<tr class="text-center">
						<td><span class="bold">Total Result</span></td>
						<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="PendingTotal"  data-orderid=""  title="Total Pipeline" data-workflowmoduleuid="">0</a></td>

						<?php foreach ($Workflows as $Workflow) { ?>
							<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Workflow->SystemName.'Total'; ?>"  data-orderid=""  title="<?php echo $Workflow->WorkflowModuleName .' (Total)'; ?>" data-workflowmoduleuid="<?php echo $Workflow->WorkflowModuleUID; ?>">0</a></td>
						<?php } ?>

					</tr>
					<?php foreach ($Processors as $ProcessorKey => $Processor) { ?>

						<tr class="text-center">
							<td><?php echo $Processor->UserName; ?></td>
							<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo 'Pending'. $Processor->UserUID; ?>"  data-orderid=""  title="<?php echo 'Total Pipeline ( '.$Processor->UserName.')'; ?>" data-workflowmoduleuid="">0</a></td>
		
							<?php foreach ($Workflows as $Workflow) { ?>
								<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Workflow->SystemName. $Processor->UserUID; ?>"  data-orderid=""  title="<?php echo $Processor->UserName.' ( '.$Workflow->WorkflowModuleName.')'; ?>" data-workflowmoduleuid="">0</a></td>
							<?php } ?>

						</tr>
					<?php } ?> 

					

				</tbody>  
			</table>
		</div>

	</div>
</div>

<!-- Orders Table -->
<?php $this->load->view('orderstable'); ?>
<!-- Orders Table -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.counterup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="assets/js/app/pipelinereport.js"></script>







