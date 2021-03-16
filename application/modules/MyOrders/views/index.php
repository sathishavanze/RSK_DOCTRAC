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
</style>

<div class="card mt-40">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-point-right"></i>
		</div>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Indexing & Stacking</h4>
			</div>
		</div>

	</div>
	<div class="card-body" id="filter-bar">

		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->MyOrders_Model->count_all(); ?></span>

				</a>
			</li>
		<?php
		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
		?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#workinprogresslist" role="tablist">
					Work In Progress
					<span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->MyOrders_Model->inprogress_count_all(); ?></span>

				</a>
			</li>
		<?php }?>
		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>

                
		

		<div class="tab-content tab-space customtabpane">
			<div class="tab-pane active" id="orderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="myordertable_parent">
						<table class="table table-striped display nowrap" id="myordertable"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Pack No</th>
									<th>Client</th>
									<th>Loan No</th>									
									<th>Current Status</th>
									<th>Property Address</th>
									<th>Property City</th>
									<th>Property State</th>
									<th>Zip Code</th>
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
					<div class="material-datatables" id="myordertable_parent">
					<table class="table table-striped display nowrap" id="workingprogresstable"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Pack No</th>
								<th>Client </th>
								<th>Product</th>
								<th>Doc Type</th>
								<th>Project</th>
								<th>Loan No</th>
								<th>Assigned Username</th>
								<th>Current Status</th>
								<th>Property Address</th>
								<th>Property City</th>
								<th>Property State</th>
								<th>Zip Code</th>
								<th>OrderEntryDateTime</th>
								<th>OrderDueDateTime</th>
								<th>Actions</th>
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



			<script type="text/javascript">
				var myordertable = false;
				$(function() {
					$("select.select2picker").select2({
						//tags: false,
						theme: "bootstrap",
					});
					$('#myordertable').DataTable().destroy();
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
					<?php }	?>


					$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
					});

					$(window).resize(function() {
						$($.fn.dataTable.tables( true ) ).css('width', '100%');
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
					});


			$(document).on('click','.getnextorder',function(){

				var ProjectUID = $('#ProjectUID option:selected').val();
				var Workflow = $('#Workflow option:selected').val();

			    $.ajax({

					type: "POST",
					url: '<?php echo base_url(); ?>MyOrders/GetNextOrder',
					data:{'ProjectUID':ProjectUID,'Workflow':Workflow},
					dataType:'json',
					beforeSend: function(){

					},
					success: function(data)
					{
				 		  if(data.validation_error == 1)
				            {
								  $.notify(
					              {
					                icon:"icon-bell-check",
					                message:data.message
					              },
					              {
					                type:data.color,
					                delay:1000 
					              });
					        $.each(data, function(k, v) {
								$('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
								$('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

							});
				           }else{

				           		  $.notify(
					              {
					                icon:"icon-bell-check",
					                message:data.message
					              },
					              {
					                type:data.color,
					                delay:1000 
					              });
				           }
						
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						
						console.log(errorThrown);
						
					},
				});
			});



			$(document).off('click','.PickNewOrder_old').on('click','.PickNewOrder_old',function(){

				var OrderUID = $(this).attr('data-orderuid');
				
				var ProjectUID = $(this).attr('data-projectuid');
				

			    $.ajax({

					type: "POST",
					url: '<?php echo base_url(); ?>MyOrders/PickExistingOrderCheck',
					data:{'OrderUID':OrderUID,'ProjectUID':ProjectUID},
					dataType:'json',
					beforeSend: function(){

					},
					success: function(data)
					{
				 		  if(data.validation_error == 1)
				            {
								  $.notify(
					              {
					                icon:"icon-bell-check",
					                message:data.message
					              },
					              {
					                type:data.color,
					                delay:1000 
					              });

					           setTimeout(function(){ 
					           	
					              triggerpage('<?php echo base_url();?>Indexing/index/'+OrderUID);

					           }, 3000);
					           
				           }else{

				           		  $.notify(
					              {
					                icon:"icon-bell-check",
					                message:data.message
					              },
					              {
					                type:data.color,
					                delay:1000 
					              });

					           setTimeout(function(){ 
					           	checkStacking(OrderUID);
					              //triggerpage('<?php echo base_url();?>Ordersummary/index/'+OrderUID);

					           }, 3000);
				           }
						
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						
						console.log(errorThrown);
						
					},
				});
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
					      var FromDate = $('#adv_FromDate').val();
					      var ToDate = $('#adv_ToDate').val();
					      if((ProjectUID == '') && (PackageUID == '') && (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
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

					       var formData = ({ 'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 

					      	if(filterlist == '#orderslist')
					      	{
					       		initialize(formData);
					      	}
					      	else if(filterlist == '#workinprogresslist')
					      	{

					      		workinprogressinitialize(formData);
					      	}


					      
					      }
					     return false;
					});

				});

				$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

					var filterlist = $("#filter-bar .active").attr("href");
					if(filterlist == '#orderslist')
					{
						var filter = 'indexing';
					}
					else if(filterlist == '#workinprogresslist')
					{
						var filter= 'workinprogress';
					}

					var ProductUID = $('#adv_ProductUID option:selected').val();
					var ProjectUID = $('#adv_ProjectUID option:selected').val();
					var PackageUID = $('#adv_PackageUID option:selected').val();
					var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
					var CustomerUID = $('#adv_CustomerUID option:selected').val();
					var FromDate = $('#adv_FromDate').val();
					var ToDate = $('#adv_ToDate').val();
					if((ProjectUID == '') && (PackageUID == '') && (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
					{
						var formData = 'All';
					} 
					else 
					{
						var formData = ({ 'ProductUID':ProductUID, 'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'Status':filter}); 
					}

                   
					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>MyOrders/WriteExcel',
						xhrFields: {
							responseType: 'blob',
						},
						data: {'formData':formData},
						beforeSend: function(){
                        

						},
						success: function(data)
						{
							var filename = "IndexingStackingOrders.csv";
							if (typeof window.chrome !== 'undefined') {
				            //Chrome version
				            var link = document.createElement('a');
				            link.href = window.URL.createObjectURL(data);
				            link.download = "IndexingStackingOrders.csv";
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
					
					$("#adv_CustomerUID").val('All');
					$("#adv_ProductUID").val('All');
					$("#adv_ProjectUID").val('All');
					$("#adv_InputDocTypeUID").val('All');
					$("#adv_PackageUID").val('All');
					$("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
					$("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
					callselect2();


					var filterlist = $("#filter-bar .active").attr("href");

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

				});

				function initialize(formData)
				{

					myordertable = $('#myordertable').DataTable( {
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
							"url": "<?php echo base_url('MyOrders/myorders_ajax_list')?>",
							"type": "POST",
							"data" : {'formData':formData} 
						},
						"columnDefs": [ {
							"targets": 'no-sort',
							"orderable": false,
						} ]

					});

				}


				<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>

					function workinprogressinitialize(formData){

						<?php
						if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
							?>

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
						"url": "<?php echo base_url('MyOrders/workinginprogress_ajax_list')?>",
						"type": "POST",
						"data" : {'formData':formData} 
					}

				});

						<?php } ?>

					}
				<?php }	?>

			</script>


<script type="text/javascript">
	
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
</script>
