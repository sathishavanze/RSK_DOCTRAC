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
	$WorkflowModuleUID = $this->config->item("Workflows")["BorrowerDoc"];
?>
<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<h4 class="card-title">Borrower Doc Orders</h4>
			<!-- <i class="icon-users"></i> -->
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		
		<div class="row">
<!-- 			<div class="col-md-10">
				<h4 class="card-title">Borrower Doc Orders</h4>
			</div> -->
			<!-- REPORT LINK -->
			<?php $Permissions = $this->Common_Model->get_rolepermissions(); 
			if(in_array('ChecklistReport', $Permissions)) { ?>
				<div class="col-md-2 text-right"> 
					<a href="ChecklistReport" target="_new" title="Standard Report" ><i class="fa fa-cogs" aria-hidden="true" style="font-size:20px;color:#0B781C;cursor: pointer;"></i></a>
				</div>
			<?php } ?>
		</div>

	</div>
	<div class="card-body" id="filter-bar">


		<ul class="nav nav-pills nav-pills-rose customtab dynamicqueuetab" role="tablist">

			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

				$Queues = $this->Common_Model->get_borrowerdynamicworkflow_queues();

				foreach ($Queues as $key => $queue) { ?>

					<li class="nav-item">
						<a class="nav-link exceptionqueue-navlink" data-toggle="tab" href="#Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-QueueUID="<?php echo $queue->QueueUID; ?>" data-tableid="#tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-workflowmodulename = "<?php echo $queue->WorkflowModuleName; ?>" role="tablist">
							<?php echo $queue->QueueName; ?>
							<span class="badge badge-pill badge-primary" id="Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>-count" style="background-color: #fff;color: #000;"><?php echo $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders(["QueueUID"=>$queue->QueueUID], "count_all"); ?></span>
						</a>
					</li>

				<?php }
			} ?>
			<a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalExcelSheet_BorrowerDocs?controller='.$this->uri->segment(1); ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a>

		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>


		

		<div class="tab-content tab-space customtabpane">

			<?php 
			
			foreach ($Queues as $key => $queue) { ?>


				<div class="tab-pane " id="Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>">
					<div class="col-md-12 col-xs-12 pd-0">

						<div class="text-left" style="display: inline-flex;"> 
							<?php  if(!empty($queue->IsFollowup)) {  ?>
								<button class="btn btn-facebook btn-xs followupfilter"> Total Follow Up : <span class="followupcount">0</span></button>
								<button class="btn btn-facebook btn-xs followupduetodayfilter"> Follow Up Due Today : <span class="followupduetodaycount">0</span></button>
								<button class="btn btn-facebook btn-xs followupduepastfilter"> Follow Up Past Due : <span class="followupduepastcount">0</span></button>
							<?php } ?>
						</div>

						<div class="material-datatables" id="tblcontainerQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>">
							<table class="table table-striped display nowrap" id="tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>"  cellspacing="0" width="100%"  style="width:100%" data-IsFollowup="<?php echo $queue->IsFollowup; ?>">
								<thead>
									<tr>
										<?php
										$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($queue->WorkflowModuleUID);

										if( !empty($QueueColumns) ) { ?>

											<?php foreach ($QueueColumns as $key => $QueueColumn) { ?>
												<th class="<?php echo ($QueueColumn->NoSort == 1 && empty($QueueColumn->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $QueueColumn->HeaderName; ?></th>
											<?php } ?>
											<th>Raised By</th>
											<th>Raised DateTime</th>

											<th class="no-sort">Actions</th>

										<?php } else { ?>

											<th>Order No</th>
											<th>Client</th>
											<th>Loan No</th>									
											<th>Loan Type</th>
											<th>Milestone</th>									
											<th>Current Status</th>
											<th>Aging</th>
											<th>Due DateTime</th>
											<th>LastModified DateTime</th>
											<th>Raised By</th>
											<th>Remarks</th>
											<th>Raised DateTime</th>
											<th class="no-sort">Actions</th>

										<?php } ?>

									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			<?php } ?>


		</div>
	</div>
</div>

<!-- common queue -->
<?php $this->load->view('orderinfoheader/commonqueue'); ?>

<script type="text/javascript">
	var queuetable;
	function initialize_queue(formData, QueueUID, tblId){

		IsFollowup = $(tblId).attr('data-IsFollowup');

		<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

			console.log("table init");
			queuetable = $(tblId).DataTable( {
				scrollX:        true,
				scrollCollapse: true,
				fixedHeader: false,
				scrollY:300,
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
					"url": "<?php echo base_url('ExceptionQueue_Orders/ExceptionQueue_Orders_ajax')?>",
					"type": "POST",
					"data" : {'formData':formData, 'QueueUID':QueueUID} 
				},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ]

			});
			//Followup counts;
			if(IsFollowup == 1) { fetch_followupcounts(QueueUID); }
		<?php } ?>

	}


	function exceptionresetadvancedfilter() {
		$("#adv_ProductUID").val('All');
		$("#adv_ProjectUID").val('All');
		$("#adv_MilestoneUID").val('All');
		$("#adv_StateUID").val('All');
		$("#adv_LoanNo").val('');
		$("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
		$("#adv_ToDate").val('<?php echo (date("m/d/Y")); ?>');
		$(".followupfilter").removeClass('active');
		$(".followupduetodayfilter").removeClass('active');
		$(".followupduepastfilter").removeClass('active');
		callselect2();

		var filterlist = $(".exceptionqueue-navlink.active").attr("href");
		var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
		var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");


		var formData = ({ 'ProductUID':'','MilestoneUID':'','StateUID':'','LoanNo':'','ProjectUID':'' ,'PackageUID':'','InputDocTypeUID':'','CustomerUID':'','FromDate':'','ToDate':'', 'QueueUID':QueueUID}); 

		console.log(filterlist);
		if(filterlist && filterlist != "")
		{
			initialize_queue(formData, QueueUID, tableid);
		}


	}

	function fetch_followupcounts(QueueUID)
	{
		$.ajax({
			url: 'CommonController/fetch_followupcounts',
			type: 'POST',
			dataType: 'json',
			data: {'QueueUID': QueueUID},
			beforeSend: function(){

			},
		})
		.done(function(response) {
			$('.followupcount').text(response.followupcount);	
			$('.followupduetodaycount').text(response.followupduetodaycount);	
			$('.followupduepastcount').text(response.followupduepastcount);	

		})
		.fail(function(jqXHR) {
			console.error("error", jqXHR);
		});
	}


	$(document).off('click','.followupfilter').on('click','.followupfilter',function(){
		$('.followupduetodayfilter,.followupduepastfilter').removeClass('active');
		$(this).addClass('active');
		$('.exceptionsearch').trigger('click');
	})

	$(document).off('click','.followupduetodayfilter').on('click','.followupduetodayfilter',function(){
		$(this).addClass('active');
		$('.followupduepastfilter,.followupfilter').removeClass('active');
		$('.exceptionsearch').trigger('click');
	})

	$(document).off('click','.followupduepastfilter').on('click','.followupduepastfilter',function(){
		$('.followupduetodayfilter,.followupfilter').removeClass('active');
		$(this).addClass('active');
		$('.exceptionsearch').trigger('click');
	})


	$(document).off('click','.exceptionsearch').on('click','.exceptionsearch',function()
	{
		var filterlist = $(".exceptionqueue-navlink.active").attr("href");
		var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
		var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");

		if (filterlist && filterlist != "") {

			var ProductUID = $('#adv_ProductUID option:selected').val();
			var ProjectUID = $('#adv_ProjectUID option:selected').val();
			var CustomerUID = $('#adv_CustomerUID option:selected').val();
			//added Milestone,State, loan no.
			var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
			var StateUID = $('#adv_StateUID option:selected').val();
			var LoanNo = $('#adv_LoanNo').val();
			var FromDate = $('#adv_FromDate').val();
			var ToDate = $('#adv_ToDate').val();
			var Followup = $('.followupfilter:visible').hasClass('active');
			var Followupduetoday = $('.followupduetodayfilter:visible').hasClass('active');
			var Followupduepast = $('.followupduepastfilter:visible').hasClass('active');
			if((ProjectUID == '')  && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
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


				var formData = ({ 'ProductUID':ProductUID,'MilestoneUID':MilestoneUID,'StateUID':StateUID,'LoanNo':LoanNo,'ProjectUID': ProjectUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate, 'QueueUID':QueueUID,'Followup':Followup,'Followupduetoday':Followupduetoday,'Followupduepast':Followupduepast}); 

				if(filterlist && filterlist != "")
				{
					initialize_queue(formData, QueueUID, tableid);
				}


			}
		}

		$('.followupfilter').removeClass('active');
		$('.followupduetodayfilter').removeClass('active');
		$('.followupduepastfilter').removeClass('active');

		return false;
	});


	$(document).off('click','.exceptionreset').on('click','.exceptionreset',function(){
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
		$(".followupfilter").removeClass('active');
		$(".followupduetodayfilter").removeClass('active');
		$(".followupduepastfilter").removeClass('active');;
		callselect2();

		exceptionresetadvancedfilter()

	});


	$(document).off('click','.exceptionqueue-navlink').on('click','.exceptionqueue-navlink',function(){

		$("#advancedsearch").slideUp();
		setTimeout(function () {
			console.log($(".exceptionqueue-navlink.active").attr("href"));

			exceptionresetadvancedfilter();
			
		}, 500);
	});


	$(document).off('click','.exceptionexceldownload').on('click','.exceptionexceldownload',function(){

		/*@Author Parthasarathy <parthasarathy.m@avanzegroup.com> @Updated APR 25 2020*/
		var filterlist = $(".exceptionqueue-navlink.active").attr("href");
		var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
		var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");
		var workflowmodulename = $(".exceptionqueue-navlink.active").attr("data-workflowmodulename");

		/*End*/

		var ProductUID = $('#adv_ProductUID option:selected').val();
		var ProjectUID = $('#adv_ProjectUID option:selected').val();
		var PackageUID = $('#adv_PackageUID option:selected').val();
		var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		/*Milestone, state, loanNo*/
		var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
		var StateUID = $('#adv_StateUID option:selected').val();
		var LoanNo = $('#adv_LoanNo').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		var Followup = $('.followupfilter').hasClass('active');
		var Followupduetoday = $('.followupduetodayfilter').hasClass('active');
		var Followupduepast = $('.followupduepastfilter').hasClass('active');
		if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
		{
			var formData = 'All';
		} 
		else 
		{
			var formData = ({ 'ProductUID':ProductUID, 'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'ProjectUID': ProjectUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'QueueUID':QueueUID,'Followup':Followup,'Followupduetoday':Followupduetoday,'Followupduepast':Followupduepast}); 
		}



		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>CommonController/WriteGlobalExcelSheet_BorrowerDocs',
			xhrFields: {
				responseType: 'blob',
			},
			data: {'formData':formData, 'QueueUID': QueueUID},
			beforeSend: function(){


			},
			success: function(data)
			{
				/**
				*Function Description: Separate EXCEL SHEET
				*@author Shruti <shruti.vs@avanzegroup.com>
				*@since Date
				*/
				var filename = workflowmodulename + " " + $.trim($("ul.customtab .nav-link.active").clone().children().remove().end().text())+'.xlsx';

				if (typeof window.chrome !== 'undefined') {
					//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = filename;
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


	$(document).ready(function () {
		$("ul.dynamicqueuetab li:first a").trigger('click');

			$('.dateraideparking').datetimepicker({
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
			}
		});
	})


</script>
