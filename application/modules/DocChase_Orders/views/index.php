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
<?php 
	$WorkflowModuleUID = $this->config->item("Workflows")["DocChase"];
?>
<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<!-- <i class="icon-cart2"></i> -->
			<h4 class="card-title">Doc Chase Orders</h4>
		</div>
		<?php //$this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		
<!-- 		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Doc Chase Orders</h4>
			</div>
		</div> -->
	</div>
	
	<div class="card-body" id="filter-bar">

		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->DocChase_Orders_Model->count_all(); ?></span>

				</a>
			</li>
			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->DocChase_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#myorderlist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary" id="workinprogressdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->DocChase_Orders_Model->myorders_count_all(); ?></span>
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#parkingorderlist" role="tablist">
						Parking Orders
						<span class="badge badge-pill badge-primary" id="parkingordersdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->DocChase_Orders_Model->parkingorders_count_all(); ?></span>
					</a>
				</li>
				
			<?php } ?>
			<a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalDocChaseExcel?controller='.$this->uri->segment(1) ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a>

		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>

		
		

		<div class="tab-content tab-space customtabpane">
			<div class="tab-pane active" id="orderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="tblDocChase_parent">
						<table class="table table-striped display nowrap" id="tblDocChase"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client</th>
									<th>Loan No</th>									
									<th>Loan Type</th>
									<th>Milestone</th>									
									<th>Current Status</th>
									<th class="no-sort">Workflows</th>
									<th>LastModified DateTime</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				<div class="tab-pane" id="workinprogresslist">
					<div class="col-md-12 col-xs-12 pd-0">
						<div class="material-datatables" id="tblDocChase_parent">
							<table class="table table-striped display nowrap" id="workingprogresstable"  cellspacing="0" width="100%"  style="width:100%">
								<thead>
									<tr>
										<th>Order No</th>
										<th>Client</th>
										<th>Loan No</th>									
										<th>Loan Type</th>
										<th>Milestone</th>									
										<th>Current Status</th>
										<th class="no-sort">Workflows</th>
										<th>LastModified DateTime</th>
										<th class="no-sort">Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="tab-pane " id="myorderlist">
					<div class="col-md-12 col-xs-12 pd-0">
						<div class="material-datatables" id="tblDocChase_parent">
							<table class="table table-striped display nowrap" id="myorderlisttable"  cellspacing="0" width="100%"  style="width:100%">
								<thead>
									<tr>
										<th>Order No</th>
										<th>Client</th>
										<th>Loan No</th>									
										<th>Loan Type</th>
										<th>Milestone</th>									
										<th>Current Status</th>
										<th class="no-sort">Workflows</th>
										<th>LastModified DateTime</th>
										<th class="no-sort">Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="tab-pane " id="parkingorderlist">
					<div class="col-md-12 col-xs-12 pd-0">
						<div class="material-datatables" id="tblDocChase_parent">
							<table class="table table-striped display nowrap" id="parkingorderlisttable"  cellspacing="0" width="100%"  style="width:100%">
								<thead>
									<tr>
										<th>Order No</th>
										<th>Client</th>
										<th>Loan No</th>
										<th>Milestone</th>									
										<th>Current Status</th>
										<th>Remainder On</th>
										<th>Raised By</th>
										<th>Remarks</th>
										<th>LastModified DateTime</th>
										<th class="no-sort">Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			<?php }	?>


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

<div class="modal fade" id="DocChaseChangeAssignment" tabindex="-1" role="dialog" aria-labelledby="ChangeDocChaseAssignmentLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 450px!important;">
		<div class="modal-content">
			<form class="form-horizontal" id="frmChangeDocChaseAssignment" action="#" method="post" style="margin-bottom: 10px;">
				<div class="modal-header" style="padding-top: 5px!important;">
					<h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;">Confirmation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="padding: 0 24px;">
					<div class="row">
						<div class="col-md-12">

					<h5 class="modal-title" id="ChangeDocChaseAssignmentLabel">This order was picked by <p class='DocChaseAssignmentName' style='display: inline-block;color: red;font-weight: 400;'></p></h5>Do you want to assign this order?
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding: 0 12px;">
					<button class="btn btn-success btnChangeDocChaseAssignment" name="submit" type="submit" id="btnChangeDocChaseAssignment" data-OrderUID='' value="btnChangeDocChaseAssignment">Yes</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
				</div>
			</form> 
		</div>
	</div>
</div>


<script type="text/javascript">
	var tblDocChase = false;
	$(function() {
		$("select.select2picker").select2({
			theme: "bootstrap",
		});
		$('#tblDocChase').DataTable().destroy();
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

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
			workinprogressinitialize('false');
			myorders('false');
			parkingorders('false');
		<?php }	?>


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
			if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (PackageUID == '') && (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
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
				else if(filterlist == '#myorderlist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						
						myorders(formData);
					<?php }	?>

				}else if(filterlist == '#parkingorderlist')
				{
					<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
						
						parkingorders(formData);
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
		else if(filterlist == '#myorderlist')
		{
			var filter= 'myorderlist';
		}
		else if(filterlist == '#parkingorderlist')
		{
			var filter= 'parkingorderlist';
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
			var formData = ({ 'ProductUID':ProductUID, 'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'Status':filter}); 
		}

		
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>DocChase_Orders/WriteExcel',
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
				var filename = $.trim($("ul.customtab .nav-link.active").clone().children().remove().end().text())+'.csv';
				
				if (typeof window.chrome !== 'undefined') {
					//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = "DocChase " + filename ;
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
		else if(filterlist == '#myorderlist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				
				myorders('false');
			<?php }	?>

		}else if(filterlist == '#parkingorderlist')
		{
			<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
				
				parkingorders('false');
			<?php }	?>

		}


	});

	function initialize(formData)
	{

		tblDocChase = $('#tblDocChase').DataTable( {
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
				"url": "<?php echo base_url('DocChase_Orders/DocChaseorders_ajax_list')?>",
				"type": "POST",
				"data" : {'formData':formData} 
			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]

		});

	}
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
					"url": "<?php echo base_url('DocChase_Orders/workinginprogress_ajax_list')?>",
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
	function myorders(formData){

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			myorderlisttable = $('#myorderlisttable').DataTable( {
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
					"url": "<?php echo base_url('DocChase_Orders/myorders_ajax_list')?>",
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

	function parkingorders(formData){

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			parkingorderlisttable = $('#parkingorderlisttable').DataTable( {
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
					"url": "<?php echo base_url('DocChase_Orders/parkingorders_ajax_list')?>",
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


	$(document).off('click', '.stacking').on('click', '.stacking', function (e) {
		e.preventDefault();

		var href = $(this).attr('href');

		var OrderUID = $(this).attr('data-orderuid');
		checkStacking(OrderUID);
		
	});

	/* --- CHECK STACKING AND AUDIT HAS STACKING DOCUMENT ENDS --- */

	function checkStacking(OrderUID){
		var requestdata = {
			'OrderUID': OrderUID
		};
		SendAsyncAjaxRequest('POST', 'CommonController/IsStackingDocumentAvailable', requestdata, 'json', true, true, function () {}).then(function (response) {
			if (response.validation_error == 1) {
				$.notify({
					icon: "icon-bell-check",
					message: response['message']
				}, {
					type: "danger",
					delay: 1000
				});
			} else if (response.validation_error == 2) {
				swal({
					title: 'No Stacking Document',
					text: response.message,
					type: 'error',
					timer: 10000,
					confirmButtonClass: "btn btn-success",
					buttonsStyling: false
				}).catch(swal.noop);
				console.log("error 2");
				removecardspinner('#Orderentrycard');
			} else if (response.validation_error == 3) {
				swal({
					title: 'Change to Stacking',
					text: response.message,
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, Do it!',
					cancelButtonText: 'No, keep it',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {

					SendAsyncAjaxRequest('POST', 'CommonController/ChangeToStacking', requestdata, 'json', true, true, function () {}).then(function (innerresponse) {

						swal({
							title: innerresponse.title,
							text: innerresponse.message,
							type: innerresponse.color,
							timer: 1000,
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).catch(swal.noop);
						window.location.href = href;
					}).catch(function (error) {});
				}, function (dismiss) {
					swal({
						title: 'Cancelled',
						timer: 1000,
						type: 'error',
						confirmButtonClass: "btn btn-info",
						buttonsStyling: false
					}).catch(swal.noop);
				});
			} else if (response.validation_error == 0) {
				//console.log("<?php echo base_url();?>Audit/index/"+OrderUID)
				window.location.href = "<?php echo base_url();?>Indexing/index/"+OrderUID;
			}
		}).catch(function (reject) {});
	}

	$(document).off('click','.PickDocOrder').on('click','.PickDocOrder',function(){

		var OrderUID = $(this).attr('data-orderuid');
		var Redirect = $(this).attr('data-redirect');
		$.ajax({

			type: "POST",
			url: '<?php echo base_url(); ?>DocChase_Orders/CheckAndPickDocChaseOrder',
			data:{'OrderUID':OrderUID},
			dataType:'json',
			beforeSend: function(){

			},
		})
		.done(function(response) {
			console.log("success", response);
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

					triggerpage(response.redirect+'/index/'+OrderUID);

				}, 3000);

			}else if(response.validation_error == 1){
				$('.DocChaseAssignmentName').html(response.message);
				$('.btnChangeDocChaseAssignment').attr('data-OrderUID',OrderUID);
				$('#DocChaseChangeAssignment').modal('show');

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

	$(document).off('click', '.btnChangeDocChaseAssignment').on('click', '.btnChangeDocChaseAssignment', function (e) 
	{

		var button = $('.btnChangeDocChaseAssignment');
		var button_text = $('.btnChangeDocChaseAssignment').html();

		$(button).prop('disabled', true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var OrderUID = $(this).attr('data-OrderUID');
		$.ajax({
			type: "POST",
			url: base_url + 'DocChase_Orders/PickDocChaseOrder',
			data: {'OrderUID':OrderUID},
			dataType: 'json',
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (data) {
				if (data.validation_error == 2) {
					/*Sweet Alert MSG*/

					$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:2000,onClose:redirecturl(data.redirect+'/index/'+OrderUID) });
					//disposepopover();

					$('#DocChaseChangeAssignment').modal('hide');
				} else {
					$.notify({
						icon: "icon-bell-check",
						message: data['message']
					}, {
						type: "danger",
						delay: 1000
					});
					button.html(button_text);
					button.attr("disabled", false);
				}
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to change</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: '300px',
					buttonsStyling: false
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			}
		});
	});

</script>
