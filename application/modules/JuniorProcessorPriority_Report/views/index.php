<style>
	th, td { text-align: center; }
	.bold{ font-weight: bold;  }
	.btn-workup{
		color: #35465c !important;
	}
	.datepickertable td{
		position:relative;
	}

		body {
			position:relative;
		}
		.bootstrap-datetimepicker-widget {
			z-index: 9999 !important;
			position: fixed !important;
			margin: 250px 0px 10px 0px;
		}

/*	.dropdown-menu.bootstrap-datetimepicker-widget.open{
		height: auto;
		padding-right: 0;
    	padding-left: 0;
		z-index: 9999 !important;
	}
	
	.checklisttable .checklistdatepicker{
		position: relative; z-index: 100000;
	}*/
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
			<h4 class="card-title">Junior Processor Priority Report</h4>
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

						<th class="text-center no-sort"><b>LoanStatus</b></th>
						<th class="text-center"><b>Total Pipeline</b></th>
						<?php foreach ($Customer_Prioritys as $Priority) { ?>
							<th title="<?php echo $Priority->HelpText; ?>"><?php echo $Priority->PriorityName; ?></th>
						<?php } ?>
						<th class="text-center no-sort"><b>Total</b></th>
					</tr>

				</thead>
				<tbody id="OrderAge_Count">
					
					<?php foreach ($Modules as $Milestonekey => $Milestone) { ?>
						<tr class="text-center">
							<td><?php echo '00'.$Milestone->MilestoneName; ?></td>

							<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo 'Pending'. $Milestone->MilestoneUID; ?>"  data-orderid=""  title="<?php echo 'Total Pipeline ( '.$Milestone->MilestoneName.')'; ?>" data-workflowmoduleuid="">0</a></td>

							<?php foreach ($Customer_Prioritys as $Priority) { ?>
								<td class="text-center"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Milestone->MilestoneUID.'Priority'.$Priority->PriorityUID; ?>"  data-orderid=""  title="<?php echo $Milestone->MilestoneName.' ( '.$Priority->PriorityName.')'; ?>" data-workflowmoduleuid="">0</a></td>
							<?php } ?>
							<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Milestone->MilestoneUID.'Total'; ?>"  data-orderid=""  title="<?php echo $Milestone->MilestoneName.' (Total)'; ?>" data-workflowmoduleuid="">0</a></td>

						</tr>
					<?php } ?> 

					<tr class="text-center">
						<td><span class="bold">Total</span></td>
						
						<td class="text-center bold"><a href="javascript:void(0);" class="text-primary listorders" data-count="PendingTotal"  data-orderid=""  title="Total Pipeline" data-workflowmoduleuid="">0</a></td>

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


