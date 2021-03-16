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
			<h4 class="card-title">Priority Report</h4>
		</div>
<!-- 		<div class="row">
			<div class="col-md-6">
				<h4 class="card-title">Priority Report</h4>
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

						<th class="text-center"><b>Team Leader</b></th>
						<th class="text-center"><b>Total Pipeline</b></th>
						<?php foreach ($Customer_Prioritys as $key => $Priority) { ?>
							<th title="<?php echo $Priority->HelpText; ?>"><?php echo $Priority->PriorityName; ?></th>
						<?php } ?>
						<th class="text-center no-sort"><b>Total</b></th>
					</tr>

				</thead>
				<tbody id="OrderAge_Count">
						
					<?php foreach ($Processors as $ProcessorKey => $Processor) { ?>

						<tr class="text-center" data-id="<?php echo $value->SystemName; ?>">
							<td><?php echo $Processor->UserName; ?></td>
							<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo 'Pending'. $Processor->UserUID; ?>"  data-orderid=""  title="<?php echo 'Total Priority ( '.$Processor->UserName.')'; ?>" data-workflowmoduleuid="">0</a></td>

							<?php foreach ($Customer_Prioritys as $Priority) { ?>
								<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Processor->UserUID.'Priority'.$Priority->PriorityUID; ?>"  data-orderid=""  title="<?php echo $Processor->UserName.' ( '.$Priority->PriorityName.')'; ?>" data-workflowmoduleuid="<?php echo $Priority->WorkflowModuleUID; ?>">0</a></td>
							<?php } ?>
							<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Processor->UserUID.'Total'; ?>"  data-orderid=""  title="<?php echo $Processor->UserName.' (Total)'; ?>" data-workflowmoduleuid="">0</a></td>

						</tr>
					<?php } ?> 
						<tr class="text-center">
						<td><span class="bold">Total</span></td>
						<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="PendingTotal"  data-orderid=""  title="Total Priority" data-workflowmoduleuid="">0</a></td>

						<?php foreach ($Customer_Prioritys as $Priority) { ?>
							<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo 'Priority'.$Priority->PriorityUID.'Total'; ?>"  data-orderid=""  title="<?php echo $Priority->PriorityName .' (Total)'; ?>" data-workflowmoduleuid="<?php echo $Priority->WorkflowModuleUID; ?>">0</a></td>
						<?php } ?>
						<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="OverallTotal"  data-orderid=""  title="Total" data-workflowmoduleuid="">0</a></td>

					</tr>
				

				</tbody>  
			</table>
		</div>

	</div>
</div>

<!-- Orders Table -->
<?php $this->load->view('orderstable'); ?>
<!-- Orders Table -->









