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

	.card .card-header.card-header-icon .card-title,
	.card .card-header.card-header-text .card-title {
 	margin-top: 1px !important;
 	color: #ffffff;
	}
</style>
<?php $WorkflowModuleUID = $this->config->item("Workflows")["HOI"]; ?>
<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<h4 class="card-title">HOI Orders</h4>
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		
<!-- 		<div class="row">
			<div class="col-md-10">
				
			</div>
		</div>
 -->
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
		  <!---Common Search--->
          <?php $this->load->view('common/commonsearch'); ?>
          <!---Common Search--->


		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item HOIQueue">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary newordercount" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->count_all(); ?></span>

				</a>
			</li>
			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item HOIQueue">
					<a class="nav-link" data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary assignordercount" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item HOIQueue">
					<a class="nav-link " data-toggle="tab" href="#myorderslist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary myordercount" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->myorders_count_all(); ?></span>
					</a>
				</li>

				<!-- Exception Queue Order List -->
				<?php 
				
				$this->load->view('ExceptionQueue/ExceptionQueueList', ['WorkflowModuleUID'=>$WorkflowModuleUID]); 
				?>
				<input type="hidden" value="<?php echo $WorkflowModuleUID ?>" name="WorkflowModuleUID" id="WorkflowModuleUID">

			<?php if(!empty($IsParking)){?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#parkingorderslist" role="tablist">
						Parking Orders
						<span class="badge badge-pill badge-primary parkordercount" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->parkingorders_count_all(); ?></span>
					</a>
				</li>
			<?php } ?> 

				<!-- <li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#hoiwaitingorderstablelist" role="tablist">
						HOI Waiting
						<span class="badge badge-pill badge-primary hoiwaitingorder" style="background-color: #fff;color: #000;"><?php $postdata = ['queue_status'=>'Waiting']; echo $this->HOI_Orders_Model->hoiloan_count_all($postdata); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#hoiresponsedorderstablelist" role="tablist">
						HOI Response Received
						<span class="badge badge-pill badge-primary hoiresponceorder" style="background-color: #fff;color: #000;"><?php $postdata = ['queue_status'=>'Responsed']; echo $this->HOI_Orders_Model->hoiloan_count_all($postdata); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#hoireceivedorderstablelist" role="tablist">
						HOI Doc Received
						<span class="badge badge-pill badge-primary hoireceived" style="background-color: #fff;color: #000;"><?php $postdata = ['queue_status'=>'Received']; echo $this->HOI_Orders_Model->hoiloan_count_all($postdata); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#hoiexceptionorderstablelist" role="tablist">
						HOI Exception
						<span class="badge badge-pill badge-primary hoiexception" style="background-color: #fff;color: #000;"><?php $postdata = ['queue_status'=>'Exceptional']; echo $this->HOI_Orders_Model->hoiloan_count_all($postdata); ?></span>
					</a>
				</li> -->

				<?php if(!empty($IsKickBack)){ ?>
				<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#KickBacklist" role="tablist">
					KickBack Orders
					<span class="badge badge-pill badge-primary kickbackorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->KickBackcount_all(); ?></span>

				</a>
			</li>
				<?php } ?>
			
				<!-- Completed Orders -->
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#completedorderslist" role="tablist">
						Completed Orders
						<span class="badge badge-pill badge-primary completeordercount" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->completedordersBasedOnWorkflow_count_all(); ?></span>
					</a>
				</li>
			<?php  } ?>
			<!-- Expiry Orders -->
			<?php if(!empty($IsExpiryOrders)){?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#Expiredorderslist" role="tablist">
					Expiry Orders
					<span class="badge badge-pill badge-primary Expiredorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->Expiredcount_all(); ?></span>

				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#ExpiredCompleteorderslist" role="tablist">
					Expiry Orders Complete
					<span class="badge badge-pill badge-primary ExpiredCompleteorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->ExpiredCompletecount_all(); ?></span>
				</a>
			</li>
			<?php } ?>

			<!-- HOI Rework -->
			<!-- <li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#HOIReworkOrderList" role="tablist">
					HOI Rework Orders
					<span class="badge badge-pill badge-primary HOIReworkOrder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->HOI_Orders_Model->HOIReworkOrders_CountAll(); ?></span>

				</a>
			</li> -->
			<!-- HOI Rework end -->

			<a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalExcelSheet?controller='.$this->uri->segment(1) ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a>

			<a class="excel-exportN-btn" href="" > <i class="fa fa-download globalexceldownloadNew" title="Overall Queue Excel Export" aria-hidden="true" style=""></i> </a>
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
				$viewdata['KickBackOrdersTableName'] = "KickBackorderstable";
				$viewdata['CompletedOrdersTableName'] = "completedorderstable";
				$viewdata['hoiwaitingorderstablename'] = "hoiwaitingorderstable";
				$viewdata['hoiresponsedorderstablename'] = "hoiresponsedorderstable";
				$viewdata['hoireceivedorderstablename'] = "hoireceivedorderstable";
				$viewdata['hoiexceptionorderstablename'] = "hoiexceptionorderstable";
				$viewdata['ExpiredOrdersTableName'] = "Expiredorderstable";
	    		$viewdata['ExpiredCompleteOrdersTableName'] = "ExpiredCompleteOrdersTable";
				/* $viewdata['HOIReworkOrdersTableName'] = "HOIReworkOrdersTable";
				$viewdata['IsHOIReworkOrders'] = true; */
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


