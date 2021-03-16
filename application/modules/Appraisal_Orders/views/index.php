<style>
	.DTFC_RightBodyWrapper{
		z-index: 9;
	}

	.labelinfo{
		color: #3e3e3e;
		font-weight: 600;
	}

	.notification-right{
		position: absolute;
		top: 10px;
		border: 1px solid #FFF;
		right: 46px;
		font-size: 9px;
		background: #f44336;
		color: #FFFFFF;
		min-width: 20px;
		padding: 0px 5px;
		height: 20px;
		border-radius: 10px;
		text-align: center;
		line-height: 19px;
		vertical-align: middle;
		display: block;
	}
	
	/*overall excel export*/
	.nav-pills-rose.customtab{
		position:relative;
	}
	.excel-expo-btn{
		position:absolute;
		right:13px;
	}
	.excel-expo-btn i{
		font-size:15px;
		color:#0B781C;
		cursor: pointer;
		margin-top: 13px;
	}
</style>
<?php 
$WorkflowModuleUID = $this->config->item("Workflows")["Appraisal"];
?>
<div class="card mt-20">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-file-eye"></i>
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Appraisal Orders</h4>
			</div>
		</div>

	</div>
	<div class="card-body" id="filter-bar">
		<!-- GET NEXT ORDER INCLUDED -->
		<?php $this->load->view('GetNextOrder/get_next_order'); ?>
		<!-- GET NEXT ORDER INCLUDED -->
		
		<!-- Workflow Documents View -->
		<?php $this->load->view('common/Workflow_Documents_View'); ?>
		<!-- Workflow Documents View -->
		<!-- QUEUE STATUS REPORT -->
		<div class="pull-right"> 
			<a href="javascript:;" class="viewqueuereport" title="View Status Report"><i class="fa fa-list-alt"  aria-hidden="true"></i></a>&nbsp;&nbsp;
		</div>
		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Appraisal_Orders_Model->count_all(); ?></span>

				</a>
			</li>

			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Appraisal_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#myorderslist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Appraisal_Orders_Model->myorders_count_all(); ?></span>
					</a>
				</li>


				<?php 
				$this->load->view('ExceptionQueue/ExceptionQueueList', ['WorkflowModuleUID'=>$WorkflowModuleUID]); 
				?>

				<?php 
				if(!empty($IsParking)){?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#parkingorderslist" role="tablist">
						Parking Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Appraisal_Orders_Model->parkingorders_count_all(); ?></span>
					</a>
				</li>

			<?php } ?> 
				<!-- Completed Orders -->
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#completedorderslist" role="tablist">
						Completed Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->completedordersBasedOnWorkflow_count_all(); ?></span>
					</a>
				</li>
			<?php } ?>

			 <a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalExcelSheet?controller='.$this->uri->segment(1) ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a>
			
		
		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>


		

		<div class="tab-content tab-space customtabpane">


			<?php 
				$viewdata['WorkflowModuleUID'] = $WorkflowModuleUID;
				$viewdata['IsParking'] = $IsParking;

				$viewdata['NewOrdersTableName'] = "tblNewOrders";
				$viewdata['WorkinProgressTableName'] = "workingprogresstable";
				$viewdata['myordertablename'] = "myorderstable";
				$viewdata['ParkingOrdersTableName'] = "parkingorderstable";
				$viewdata['CompletedOrdersTableName'] = "completedorderstable";
			?>
			<?php $this->load->view('DynamicColumns/DynamicColumnsView', $viewdata); ?>

		</div>

	</div>
</div>



<script type="text/javascript">
	
	var WorkflowModuleUID = '<?php echo $WorkflowModuleUID; ?>';
	var ModuleController = '<?php echo $this->uri->segment(1); ?>';
</script>

<!-- common queue -->
<?php $this->load->view('orderinfoheader/commonqueue'); ?>


