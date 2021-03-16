<style>
	.DTFC_RightBodyWrapper{
		z-index: 9;
	}

	.labelinfo{
		color: #3e3e3e;
		font-weight: 600;
	}

.card .card-header.card-header-icon .card-title,
.card .card-header.card-header-text .card-title {
 margin-top: 1px !important;
 color: #ffffff;
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

	.CompletedCounterDiv
	{
	  width: 100px;
	    height: 100px;
	    background: red;
	    -moz-border-radius: 50px;
	    -webkit-border-radius: 50px;
	    border-radius: 50px;
	  float:left;
	  margin:5px;
	}
</style>
<?php 	
$WorkflowModuleUID = $this->config->item("Workflows")["PreScreen"];
?>
 <div class="col-md-12 TableDiv">

          
 </div>
<div class="card mt-10 PreScreendiv" id="PreScreen">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<h4 class="card-title">Pre-Screen Orders</h4>
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
	</div>
	<div class="card-body" id="filter-bar">
			<?php //if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
		
			<?php //}?>
		
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
			<?php $this->load->view('common/commonsearch'); ?>


		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary newordercount" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->count_all(); ?></span>

				</a>
			</li>

			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary assignordercount" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#myorderslist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary myordercount" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->myorders_count_all(); ?></span>
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
						<span class="badge badge-pill badge-primary parkordercount" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->parkingorders_count_all(); ?></span>
					</a>
				</li>

			<?php }  ?> 

			<?php if(!empty($IsKickBack)){?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#KickBacklist" role="tablist">
					KickBack Orders
					<span class="badge badge-pill badge-primary kickbackorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->KickBackcount_all(); ?></span>

				</a>
			</li>
			<?php } ?> 

				<!-- Completed Orders -->
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#completedorderslist" role="tablist">
						Completed Orders
						<span class="badge badge-pill badge-primary completeordercount" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->completedordersBasedOnWorkflow_count_all(); ?></span>
					</a>
				</li>
			<?php } ?>

			<!-- Expiry Orders -->
			<?php if(!empty($IsExpiryOrders)){?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#Expiredorderslist" role="tablist">
					Expiry Orders
					<span class="badge badge-pill badge-primary Expiredorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PreScreen_Orders_Model->Expiredcount_all(); ?></span>

				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#ExpiredCompleteorderslist" role="tablist">
					Expiry Orders Complete
					<span class="badge badge-pill badge-primary ExpiredCompleteorder" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->ExpiredCompletecount_all(); ?></span>
				</a>
			</li>
			<?php } ?>

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
				$viewdata['ExpiredOrdersTableName'] = "Expiredorderstable";
		    	$viewdata['ExpiredCompleteOrdersTableName'] = "ExpiredCompleteOrdersTable";
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
<script type="text/javascript">

	$(document).ready(function(){
			$(document).off('click', '.nextorderclose').on('click', '.nextorderclose', function (e) {
				addcardspinner($('#PreScreen'));
				 $('.TableDiv').html("");
				 $('#PreScreen').show();
				 removecardspinner($('#PreScreen'));

			});
			
	});

	
</script>

