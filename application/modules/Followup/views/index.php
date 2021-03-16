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

<div class="card mt-20 customcardbody">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-alarm"></i>
		</div>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Follow Ups</h4>
			</div>
		</div>

	</div>
	<div class="card-body" id="filter-bar">
		<div class="col-md-12">
		<ul class="nav nav-pills nav-pills-danger customtab entrytab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#Pending" data-status='Pending' role="tablist">
					Pending <span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Followup_Model->Pending_count_all(); ?></span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#Completed" data-status='Completed' role="tablist">
					Completed <span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"><?php echo $this->Followup_Model->Completed_count_all(); ?></span>
				</a>
			</li>
		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>

                
		

		<div class="tab-content tab-space customtabpane">
			<div class="tab-pane active" id="Pending">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="PendingOrdertable_parent">
						<table class="table table-striped display nowrap" id="PendingOrdertable"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Pack No</th>
									<th>FollowUpStartDateTime</th>
									<th>RaisedUser</th>
									<th>Remarks</th>
									<th>FollowUpStatus</th>
									<th>FollowUpType</th>
									<th class="no-sort">Actions</th>
									
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="Completed">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="PendingOrdertable_parent">
						<table class="table table-striped display nowrap" id="CompletedOrdertable"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Pack No</th>
									<th>FollowUpStartDateTime</th>
									<th>FollowUpEndDateTime</th>
									<th>RaisedUser</th>
									<th>CompletedUser</th>
									<th>CompletedType</th>
									<th>Remarks</th>
									<th>CompletedCommand</th>
									<th>FollowUpStatus</th>
									<th>FollowUpType</th>									
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
    	</div>
	</div>
</div>
</div>



			<script type="text/javascript">
				var PendingOrdertable = false;
				var CompletedOrdertable = false;
				
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
					CompletedOrdertableInitialize('false')
					


					$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
					});

					$(window).resize(function() {
						$($.fn.dataTable.tables( true ) ).css('width', '100%');
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
					});

					$('.exceldownload').after('&nbsp;&nbsp;<i class="icon-file-spreadsheet csvdownload" title="Export CSV" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;	"></i>');

			$(document).off('click','.PickNewOrder_old').on('click','.PickNewOrder_old',function(){

				var OrderUID = $(this).attr('data-orderuid');
				
				var ProjectUID = $(this).attr('data-projectuid');
				

			    $.ajax({

					type: "POST",
					url: '<?php echo base_url(); ?>DocumentCheckInOrders/PickExistingOrderCheck',
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

					              triggerpage('<?php echo base_url();?>Ordersummary/index/'+OrderUID);

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

					              triggerpage('<?php echo base_url();?>Ordersummary/index/'+OrderUID);

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
						
						  var filterlist = $("#filter-bar .active").attr("href");
						  var ProductUID = $('#adv_ProductUID option:selected').val();
					      var ProjectUID = $('#adv_ProjectUID option:selected').val();
					      var PackageUID = $('#adv_PackageUID option:selected').val();
					      var LenderUID = $('#adv_LenderUID option:selected').val();
					      var CustomerUID = $('#adv_CustomerUID option:selected').val();
					      var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
					      var FromDate = $('#adv_FromDate').val();
					      var ToDate = $('#adv_ToDate').val();
					      if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='') && (PackageUID =='') && (InputDocTypeUID == ''))
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
					       var formData = ({ 'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID' : PackageUID , 'LenderUID': LenderUID,'CustomerUID':CustomerUID, 'InputDocTypeUID' : InputDocTypeUID ,'FromDate':FromDate,'ToDate':ToDate}); 

					       	if(filterlist == '#Pending')
					      	{
					       		initialize(formData);
					      	}
					      	else if(filterlist == '#Completed')
					      	{

					      		CompletedOrdertableInitialize(formData);
					      	}
					       	
					      }
					     return false;
					});

				});

				$(document).off('click','.csvdownload').on('click','.csvdownload',function(){
					
					var ProductUID = $('#adv_ProductUID option:selected').val();
					var ProjectUID = $('#adv_ProjectUID option:selected').val();
					 var PackageUID = $('#adv_PackageUID option:selected').val();
					var LenderUID = $('#adv_LenderUID option:selected').val();
					var CustomerUID = $('#adv_CustomerUID option:selected').val();
					var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
					var FromDate = $('#adv_FromDate').val();
					var ToDate = $('#adv_ToDate').val();
					var filterlist = $("#filter-bar .active").attr("data-status");
					if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='') && (PackageUID == '') && (InputDocTypeUID == ''))
					{
						var formData = ({'advancesearch':'All','download_format':'csv','filterlist':filterlist});
					} 
					else 
					{
						var formData = ({'ProductUID':ProductUID,'ProjectUID': ProjectUID , 'PackageUID' : PackageUID,'LenderUID': LenderUID,'CustomerUID':CustomerUID, 'InputDocTypeUID' : InputDocTypeUID,'FromDate':FromDate,'ToDate':ToDate,'download_format':'csv','filterlist':filterlist}); 
					}


					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>Followup/WriteCSV',
						xhrFields: {
							responseType: 'blob',
						},
						data: {'formData':formData},
						beforeSend: function(){


						},
						success: function(data)
						{
							var filename = "Followup.csv";
							if (typeof window.chrome !== 'undefined') {
							//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = "Followup.csv";
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

				$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
					
					var ProductUID = $('#adv_ProductUID option:selected').val();
					var ProjectUID = $('#adv_ProjectUID option:selected').val();
					 var PackageUID = $('#adv_PackageUID option:selected').val();
					var LenderUID = $('#adv_LenderUID option:selected').val();
					var CustomerUID = $('#adv_CustomerUID option:selected').val();
					var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
					var FromDate = $('#adv_FromDate').val();
					var ToDate = $('#adv_ToDate').val();
					var filterlist = $("#filter-bar .active").attr("data-status");
					if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='') && (PackageUID == '') && (InputDocTypeUID == ''))
					{
						var formData = ({'advancesearch':'All','download_format':'excel','filterlist':filterlist});
					} 
					else 
					{
						var formData = ({'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID' : PackageUID,'LenderUID': LenderUID,'CustomerUID':CustomerUID,'InputDocTypeUID' : InputDocTypeUID,'FromDate':FromDate,'ToDate':ToDate,'download_format':'excel','filterlist':filterlist}); 
					}


					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>Followup/WriteCSV',
						xhrFields: {
							responseType: 'blob',
						},
						data: {'formData':formData},
						beforeSend: function(){


						},
						success: function(data)
						{
							var filename = "Followup.xlsx";
							if (typeof window.chrome !== 'undefined') {
							//Chrome version
							var link = document.createElement('a');
							link.href = window.URL.createObjectURL(data);
							link.download = "Followup.xlsx";
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
					$('#adv_ProjectUID').html('<option value = "All">All</option>');
					$('#adv_ProductUID').html('<option value = "All">All</option>');
					$('#adv_LenderUID').html('<option value = "All">All</option>');
					$('#adv_PackageUID').html('<option value = "All">All</option>');
					$('#adv_InputDocTypeUID').val('All').attr("selected", "selected");
					
					$("#adv_CustomerUID").val('All');
					$("#adv_ProjectUID").val('All');
					$("#adv_ProductUID").val('All');
					$("#adv_LenderUID").val('All');
					$("#adv_PackageUID").val('All');
					$("#adv_InputDocTypeUID").val('All');
					$("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
					$("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
					callselect2();
					var filterlist = $("#filter-bar .active").attr("href");

					if(filterlist == '#Pending')
					{
						initialize('false');
					}
					else if(filterlist == '#Completed')
					{

						CompletedOrdertableInitialize('false');
					}
					
				});

				function initialize(formData)
				{

					PendingOrdertable = $('#PendingOrdertable').DataTable( {
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
							"url": "<?php echo base_url('Followup/followup_ajax_list')?>",
							"type": "POST",
							"data" : {'formData':formData,'filter':'Pending'} 
						},
						"columnDefs": [ {
							"targets": 'no-sort',
							"orderable": false,
						} ]

					});

				}
				function CompletedOrdertableInitialize(formData)
				{

					PendingOrdertable = $('#CompletedOrdertable').DataTable( {
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
							"url": "<?php echo base_url('Followup/Completed_ajax_list')?>",
							"type": "POST",
							"data" : {'formData':formData,'filter':'Completed'} 
						},
						"columnDefs": [ {
							"targets": 'no-sort',
							"orderable": false,
						} ]

					});

				}
				

			</script>
