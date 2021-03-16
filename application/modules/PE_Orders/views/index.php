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
<?php $WorkflowModuleUID = $this->config->item("Workflows")["PE"]; ?>

<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<h4 class="card-title">PE Orders</h4>
<!-- 			<i class="icon-file-eye"></i> -->
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		
<!-- 		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">PE Orders</h4>
			</div>
		</div> -->

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
					<span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PE_Orders_Model->count_all(); ?></span>

				</a>
			</li>

			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PE_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#myorderslist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PE_Orders_Model->myorders_count_all(); ?></span>
					</a>
				</li>
				<?php 
				if(!empty($IsParking)){?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#parkingorderslist" role="tablist">
						Parking Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->PE_Orders_Model->parkingorders_count_all(); ?></span>
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

			<a class="excel-exportN-btn" > <i class="fa fa-download globalexceldownloadNew" title="Overall Queue Excel Export" aria-hidden="true" style=""></i> </a>	
		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>		

		<div class="tab-content tab-space customtabpane">

			<?php 
			$viewdata['WorkflowModuleUID'] = $WorkflowModuleUID;
			$viewdata['IsParking'] = $IsParking;

			$viewdata['NewOrdersTableName'] = "tblPE";
			$viewdata['WorkinProgressTableName'] = "workingprogresstable";
			$viewdata['myordertablename'] = "myorderstable";
			$viewdata['ParkingOrdersTableName'] = "parkingorderstable";
			$viewdata['CompletedOrdersTableName'] = "completedorderstable";
			?>

			<?php $this->load->view('DynamicColumns/DynamicColumnsView', $viewdata); ?>

		</div>

	</div>
</div>

<div id="md-cancelorder" tabindex="-1" role="dialog"  class="modal fade custommodal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center" style="background-color: #1d4870;">
				<h5 style="color: #fff;">Order Cancellation Request</h5>
			</div>
			<div class="modal-body">

				<div class="form-group">
					<div id="append_history"></div>
					<form id="OrderCancellation">
						<div class="col-md-12">
							<div class="form-group">
								<label for="Remarks" class="bmd-label-floating">Remarks <span class="mandatory"></span> </label>
								<input type="text" class="form-control"  id="Remarks" name="Remarks" value=""/>
							</div>
						</div>
					</form>
				</div>
				<div class="text-right">
					<button class="btn btn-space btn-social btn-color btn-success Proceed" disabled="true" id="Proceed" value="" >Proceed</button>
					<button class="btn btn-space btn-social btn-color btn-danger Close" data-dismiss="modal" style="" id="Close" value="" >Close</button>
				</div>
			</div>

		</div>
	</div>
</div>



<script type="text/javascript">
	var tblPE = false;
	$(function() {
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
		$('#tblPE').DataTable().destroy();
	});
	$(document).ready(function(){

		$('.datepicker').datetimepicker({
			icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-chevron-up",
				down: "fa fa-chevron-down",
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-screenshot',
				clear: 'fa fa-trash',
				close: 'fa fa-remove'
			},
			format: 'MM/DD/YYYY'
		});

		initialize('false');

		/*<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
			workinprogressinitialize('false');
			myordersinitialize('false');
			completedordersinitialize('false');
			<?php if(!empty($IsParking)) {?> parkingordersinitialize('false'); <?php }
		}	?>*/


		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
		});

		$(window).resize(function() {
			$($.fn.dataTable.tables( true ) ).css('width', '100%');
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
		});



		$(document).off('click','.search').on('click','.search',function()
		{
			// alert();
			var filterlist = $("#filter-bar .active").attr("href");

			var ProductUID = $('#adv_ProductUID option:selected').val();
			var ProjectUID = $('#adv_ProjectUID option:selected').val();
			var PackageUID = $('#adv_PackageUID option:selected').val();
			var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
			var CustomerUID = $('#adv_CustomerUID option:selected').val();
			//added Milestone,State, loan no.
			var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
					var StateUID = $('#adv_StateUID option:selected').val();
					var LoanNo = $('#adv_LoanNo').val();
			var FromDate = $('#adv_FromDate').val();
			var ToDate = $('#adv_ToDate').val();
			if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') &&  (PackageUID == '') && (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
			{


				$.notify(
				{
					icon:"icon-bell-check",
					message:'Please Choose Search Keywords'
				},
				{
					type:'danger',
					delay:1000 
				});
			} else {

				var filterlist = $("#filter-bar .active").attr("href");

				var formData = ({ 'ProductUID':ProductUID,'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 

				if(filterlist == '#orderslist')
				{
					initialize(formData);
				}
				else if(filterlist == '#workinprogresslist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						workinprogressinitialize(formData);
					<?php }	?>

				}
				else if(filterlist == '#myorderslist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						myordersinitialize(formData);
					<?php }	?>

				}//Parking Orders
				else if(filterlist == '#parkingorderslist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						parkingordersinitialize(formData);
					<?php }	?>

				}
				else if(filterlist == '#completedorderslist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						completedordersinitialize(formData);
					<?php }	?>

				}

			}
			return false;
		});

	});

	$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
		/*@Author Jainulabdeen <jainulabdeeen.b@avanzegroup.com> @Updated Mar 4 2020*/
		var filterlist = $("#filter-bar .active").attr("href");
		if(filterlist == '#orderslist')
		{
			var filter = 'orderslist';
		}
		else if(filterlist == '#workinprogresslist')
		{
			var filter= 'workinprogresslist';
		}
		else if(filterlist == '#myorderslist')
		{
			var filter= 'myorderslist';
		}
		else if(filterlist == '#parkingorderslist')
		{
			var filter= 'parkingorderslist';
		}
		else if(filterlist == '#completedorderslist')
		{
			var filter= 'completedorderslist';
		}
		/*End*/

		var ProductUID = $('#adv_ProductUID option:selected').val();
		var ProjectUID = $('#adv_ProjectUID option:selected').val();
		var PackageUID = $('#adv_PackageUID option:selected').val();
		var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		//Milestone, state, loanNo
		var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
		var StateUID = $('#adv_StateUID option:selected').val();
		var LoanNo = $('#adv_LoanNo').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (PackageUID == '') && (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
		{
			var formData = 'All';
		} 
		else 
		{
			var formData = ({ 'ProductUID':ProductUID,'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo, 'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'Status':filter}); 
		}

		// Check Completed Orders Tab
		var action_url;
		// if (filter == 'completedorderslist') {
		// 	action_url = '<?php echo base_url('CommonController/completedordersbasedonworkflow_ajax_listWriteExcel').'?controller='.$this->uri->segment(1); ?>';
		// } else {
		// 	action_url = '<?php echo base_url();?>PE_Orders/WriteExcel';
		// }
		action_url = '<?php echo base_url('CommonController/WriteGlobalExcelSheet').'?controller='.$this->uri->segment(1); ?>'+'&activesubqueue='+filter;

		$.ajax({
			type: "POST",
			url: action_url,
			xhrFields: {
				responseType: 'blob',
			},
			data: {'formData':formData},
			beforeSend: function(){


			},
			success: function(data)
			{

				/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
				var filename = $.trim($("ul.customtab .nav-link.active").clone().children().remove().end().text())+'.xlsx';
				
				if (typeof window.chrome !== 'undefined') {
					//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = "PE "+ filename;
					link.click();
				} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
					//IE version
					var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
					window.navigator.msSaveBlob(blob, filename);
				} else {
					//Firefox version
					var file = new File([data], filename, { type: 'application/octet-stream' });
					window.open(URL.createObjectURL(file));
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {

				console.log(jqXHR);


			},
			failure: function (jqXHR, textStatus, errorThrown) {

				console.log(errorThrown);

			},
		});

	});


	$(document).off('click','.reset').on('click','.reset',function(){
		// alert('init');
		$('#adv_ProjectUID').html('<option value = "All">All</option>');
		$('#adv_PackageUID').html('<option value = "All">All</option>');
		$('#adv_InputDocTypeUID').val('All').attr("selected", "selected");
		$('#adv_MilestoneUID').html('<option value = "All">All</option>');
					$('#adv_StateUID').html('<option value = "All">All</option>');
					$('#adv_LoanNo').val('All');
		$("#adv_CustomerUID").val('All');
		$("#adv_ProductUID").val('All');
		$("#adv_ProjectUID").val('All');
		$("#adv_InputDocTypeUID").val('All');
		$("#adv_PackageUID").val('All');
		$("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
		$("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
		callselect2();
		if(filterlist == '#orderslist')
		{
			initialize('false');
		}
		else if(filterlist == '#workinprogresslist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				workinprogressinitialize('false');
			<?php }	?>

		}
		else if(filterlist == '#myorderslist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				myordersinitialize(formData);
			<?php }	?>

		}
		else if(filterlist == '#parkingorderslist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				parkingordersinitialize(formData);
			<?php }	?>

		}
		else if(filterlist == '#completedorderslist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				completedordersinitialize(formData);
			<?php }	?>

		}

	});

	function initialize(formData)
	{

		tblPE = $('#tblPE').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY: '100vh',
			paging:  true,
			fixedColumns:   {
				leftColumns: 1,
				rightColumns: 1
			}, 
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			language: {
				sLengthMenu: "Show _MENU_ Orders",
				emptyTable:     "No Orders Found",
				info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
				infoEmpty:      "Showing 0 to 0 of 0 Orders",
				infoFiltered:   "(filtered from _MAX_ total Orders)",
				zeroRecords:    "No matching Orders found",
				processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
			},
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo base_url('PE_Orders/PEorders_ajax_list')?>",
				"type": "POST",
				"data" : {'formData':formData} 
			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]

		});

	}
/***********************
/**
			*Function Description: Parking Orders 
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
		
**Parking Orders start************** */
function parkingordersinitialize(formData){

<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

	parkingorderstable = $('#parkingorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 1,
			rightColumns: 1
		}, 
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": "<?php echo base_url('PE_Orders/parkingorders_ajax_list')?>",
			"type": "POST",
			"data" : {'formData':formData} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]

	});

<?php } ?>

}
/*************************Parking Orders End****************************/


	function workinprogressinitialize(formData){

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			workingprogresstable = $('#workingprogresstable').DataTable( {
				scrollX:        true,
				scrollCollapse: true,
				fixedHeader: false,
				scrollY: '100vh',
				paging:  true,
				fixedColumns:   {
					leftColumns: 1,
					rightColumns: 1
				}, 
				"bDestroy": true,
				"autoWidth": true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				"order": [], //Initial no order.
				"pageLength": 10, // Set Page Length
				"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
				language: {
					sLengthMenu: "Show _MENU_ Orders",
					emptyTable:     "No Orders Found",
					info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
					infoEmpty:      "Showing 0 to 0 of 0 Orders",
					infoFiltered:   "(filtered from _MAX_ total Orders)",
					zeroRecords:    "No matching Orders found",
					processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


				},
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?php echo base_url('PE_Orders/workinginprogress_ajax_list')?>",
					"type": "POST",
					"data" : {'formData':formData} 
				},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ]

			});

		<?php } ?>

	}
	function myordersinitialize(formData){

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			myorderstable = $('#myorderstable').DataTable( {
				scrollX:        true,
				scrollCollapse: true,
				fixedHeader: false,
				scrollY: '100vh',
				paging:  true,
				fixedColumns:   {
					leftColumns: 1,
					rightColumns: 1
				}, 
				"bDestroy": true,
				"autoWidth": true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				"order": [], //Initial no order.
				"pageLength": 10, // Set Page Length
				"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
				language: {
					sLengthMenu: "Show _MENU_ Orders",
					emptyTable:     "No Orders Found",
					info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
					infoEmpty:      "Showing 0 to 0 of 0 Orders",
					infoFiltered:   "(filtered from _MAX_ total Orders)",
					zeroRecords:    "No Orders found",
					processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


				},
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?php echo base_url('PE_Orders/myorders_ajax_list')?>",
					"type": "POST",
					"data" : {'formData':formData} 
				},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ]

			});

		<?php } ?>

	}

	// Completed Orders
	function completedordersinitialize(formData){

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			completedorderstable = $('#completedorderstable').DataTable( {
				scrollX:        true,
				scrollCollapse: true,
				fixedHeader: false,
				scrollY: '100vh',
				paging:  true,
				fixedColumns:   {
					leftColumns: 1,
					rightColumns: 1
				}, 
				"bDestroy": true,
				"autoWidth": true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				"order": [], //Initial no order.
				"pageLength": 10, // Set Page Length
				"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
				language: {
					sLengthMenu: "Show _MENU_ Orders",
					emptyTable:     "No Orders Found",
					info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
					infoEmpty:      "Showing 0 to 0 of 0 Orders",
					infoFiltered:   "(filtered from _MAX_ total Orders)",
					zeroRecords:    "No matching Orders found",
					processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


				},
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?php echo base_url('CommonController/completedordersbasedonworkflow_ajax_list').'?controller='.$this->uri->segment(1); ?>",
					"type": "POST",
					"data" : {'formData':formData} 
				},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ]

			});

		<?php } ?>

	}

</script>


<!-- Madhuri 26/09/2019 -->

<script type="text/javascript">
	$(document).off('click','.PEPickNewOrder').on('click','.PEPickNewOrder',function(){

		var OrderUID = $(this).attr('data-orderuid');

		var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


		$.ajax({
			url: 'CommonController/PickExistingOrderCheck',
			type: 'POST',
			dataType: 'json',
			data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
			beforeSend: function(){

			},
		})
		.done(function(response) {

			if(response.validation_error == 2)
			{
				$.notify(
				{
					icon:"icon-bell-check",
					message:response.message
				},
				{
					type:response.color,
					delay:1000 
				});

				setTimeout(function(){ 

					triggerpage('PE/index/'+OrderUID);

				}, 2000);

			}else if(response.validation_error == 1){
				$('.AssignmentName').html(response.message.UserName);
				$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
				$('#ChangeOrderAssignment').modal('show');

			}


		})
		.fail(function(jqXHR) {
			console.error("error", jqXHR);
			$.notify(
			{
				icon:"icon-bell-check",
				message:"unable to assign"
			},
			{
				type:"danger",
				delay:1000 
			});
		})
		.always(function() {
			console.log("complete");
		});

	});
</script>
