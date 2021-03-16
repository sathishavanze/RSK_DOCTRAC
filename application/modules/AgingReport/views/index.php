<style>
	th, td { text-align: center; 
	}

.card .card-header.card-header-icon .card-title,
.card .card-header.card-header-text .card-title {
 margin-top: 1px !important;
 color: #ffffff;
}
.bold{ font-weight: bold;  }
</style>
<div class="card mt-10" id="agingcount-view">

	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<h4 class="card-title">Aging Report</h4>
			<!-- <i class="icon-database-time2"></i> -->
		</div>
		<div class="row">
<!-- 			<div class="col-md-6">
				<h4 class="card-title">Aging Report</h4>
			</div> -->
			<div class="col-md-12 text-right"> 
				<!-- <div class="form-check" style="display: inline;">
				<label class="form-check-label" style="margin-top: 10px;">
				<input class="form-check-input" id="agingpercentage" type="checkbox" value=""> Aging %
				<span class="form-check-sign">
				<span class="check"></span>
				</span>
				</label>
			</div> -->
			<button class="btn btn-default btn-xs btn-link refresh-btn" style="font-size: 13px;color:#900C3F;cursor: pointer;"><i class="fa fa-filter"></i></button>
		</div>
	</div>
</div>


<div id="advancedFilterForReport"  style="display: none;">
	<fieldset class="advancedsearchdiv">
		<legend>Advanced Search</legend>
		<form id="advancedsearchdata">
			<div class="col-md-12 pd-0">
				<div class="row" >

					<div class="col-md-3 ">
						<div class="form-group bmd-form-group">
							<label for="adv_ProductUID" class="bmd-label-floating">Product <span class="mandatory"></span></label>
							<select class="select2picker form-control" id="adv_ProductUID"  name="ProductUID">   
								<option value="All">All</option>                  
							</select>
						</div>
					</div>

					<div class="col-md-3 ">
						<div class="form-group bmd-form-group">
							<label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
							<select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
								<option value="All">All</option>                  
							</select>
						</div>
					</div>

					<!-- <div class="col-md-3 ">
						<div class="form-group bmd-form-group">
							<label for="adv_MilestoneUID" class="bmd-label-floating">Milestone </label>
							<select class="select2picker form-control" id="adv_MilestoneUID"  name="MilestoneUID">   
								<option value="All">All</option>
								<?php foreach ($Modules as $key => $value) { ?>
									<option value="<?php echo $value->MilestoneUID; ?>" ><?php echo $value->MilestoneName; ?></option>
								<?php } ?>                      
							</select>
						</div>
					</div> -->

						<div class="col-md-3 datadiv">
							<div class="bmd-form-group row">
								<div class="col-md-6 pd-0 inputprepand" >
									<p class="mt-5"> Order Entry From Date</p>
								</div>
								<div class=" col-md-6 " style="padding-left: 0px">
									<div class="datediv">
										<input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php //echo date('Y-m-d',strtotime('-90 days'));?>">
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-3 datadiv">
							<div class="bmd-form-group row">
								<div class="col-md-6 pd-0 inputprepand" >
									<p class="mt-5"> Order Entry To Date</p>
								</div>
								<div class=" col-md-6 " style="padding-left: 0px">
									<div class="datediv">
										<input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php //echo date('Y-m-d');?>"/>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="col-md-12  text-right pd-0 mt-10">
					<button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
					<button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
				</div>

		</form>
	</fieldset>
</div>

<div class="col-md-12 material-datatable">

	<table class="table table-hover" id="aging-data" style="">
		<thead>
			<tr>

				<th class="text-center no-sort" rowspan="2"><b>TAT</b></th>
				<th class="text-center no-sort" rowspan="2"><b>Milestone</b></th>
				<th class="text-center no-sort" rowspan="1" colspan="<?php echo count($AgingHeader); ?>"><b>Aging</b></th>
			</tr>
			<tr>
				<?php foreach ($AgingHeader as $Header) { ?>
					<th class="no-sort"><?php echo $Header; ?></th>
				<?php } ?> 

			</tr>
		</thead>
		<tbody id="OrderAge_Count">
			<?php foreach ($Modules as $key => $value) {  ?>
				<tr class="text-center" data-id="<?php echo $value->SystemName; ?>">
					<td><?php echo hourstodays($value->SLA); ?></td>
					<td><?php echo $value->MilestoneName.' ('.$value->WorkflowModuleName.')'; ?></td>

					<?php foreach ($AgingHeader as $Agingkey => $Header) {
						
						 $bold_cls = ($Agingkey == 'total') ? 'bold' : '';
						 ?>
						<td class="text-center <?php echo $bold_cls;?>"><a href="javascript:void(0);" class="text-primary listorders" data-count="<?php echo $Agingkey.$value->SystemName; ?>"  title="<?php echo $value->MilestoneName.' ('.$value->WorkflowModuleName.')'.' - '.$Header; ?>"  data-orderid="" data-workflowmoduleuid="<?php echo $value->WorkflowModuleUID; ?>">0</a></td>
					<?php } ?> 
				</tr>
			<?php } ?> 

		</tbody>  
	</table>
</div>

</div>

<!-- Orders Table -->
<?php //$this->load->view('orderstable'); ?>
<!-- Orders Table -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.counterup.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>

<script type="text/javascript">
	var orderslist = false;
	var agingdatatable = false;
	$(function() {
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
		
	});


	function fetch_agingcounts(data) {

		return new Promise(function (resolve, reject) {  
			SendAsyncAjaxRequest('POST', 'AgingReport/fetch_agingcounts', data, 'json', true, true, function () {
				addcardspinner($('#agingcount-view'));
				$("#agingcount-view").fadeIn('fast');
				$("#orderstablediv").fadeOut('fast');
			}).then(function (response) {

				if (response.success == 1) {
					//$('#aging-data tbody tr').hide();
					$.each(response.data, function(key, value) {
						/* iterate through array or object */
						//$("[data-count='"+key+"']:hidden").closest('tr').show();
						$("[data-count='"+key+"']").attr('data-orderid', value);

						var charCount = (value == "" || value === null || value === "undefined") ? 0 : value.split(/[\.,\?]+/).length;

						$("[data-count='"+key+"']").text(charCount);
					});



				}

				fnaging_datatable();
				resolve('success');
				removecardspinner($('#agingcount-view'));

			}).catch(function (error) {

				console.log(error);
				removecardspinner($('#agingcount-view'));
			});
		})
	}

	function fnaging_datatable()
	{
		agingdatatable = $('#aging-data').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY:300,
			paging:  false,
			//"bInfo" : false,
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": false, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			fixedColumns: {
				leftColumns: 2,
				rightColumns: 1
			},
			dom: 'Bfrtip',
			"buttons": [
			{
				extend: 'csv',
				title: 'Aging Report',
			},
			{
				extend: 'excelHtml5',
				title: 'Aging Report',
				footer: true
			},
			],

			language: {
				processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
		});

		return agingdatatable;
	}


	function fetchorders(OrderUID,WorkflowModuleUID,Orderlistname)
	{
		$("#agingcount-view").fadeOut('fast');
		$("#orderstablediv").fadeIn('fast');
		$('#orderlisttitle').text(Orderlistname);
		$('#orderlist_orderuids').val(OrderUID);
		$('#orderlist_workflowmoduleuid').val(WorkflowModuleUID);
		orderslist = $('#orderslist').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY: '100vh',
			paging:  true,
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			fixedColumns: {
				leftColumns: 1,
				rightColumns: 1
			},

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
				"url": "<?php echo base_url('AgingReport/fetchorders')?>",
				"type": "POST",
				"data" : {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID}  
			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
		});
	}

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


		$(document).off('click','.listorders').on('click','.listorders',function() {
			var td = $(this).closest('td');
			var OrderUID = $(this).attr('data-orderid');
			var WorkflowModuleUID = $(this).attr('data-workflowmoduleuid');
			var title = $(this).attr('title');
			var Orderlistname = 'Aging - '+title;

			if(!OrderUID || OrderUID.length === 0) {
				$.notify(
				{
					icon:"icon-bell-check",
					message:'No Orders Available'
				},
				{
					type:'danger',
					delay:1000 
				});
				return false;
			}

			fetchorders(OrderUID,WorkflowModuleUID,Orderlistname)
		});

		$('.fa-filter').click(function(){
			$("#advancedFilterForReport").slideToggle();
		});

		$(document).off('click','.filterreport').on('click','.filterreport',function()
		{
			var ProjectUID = $('#adv_ProjectUID option:selected').val();
			var CustomerUID = $('#adv_CustomerUID option:selected').val();
			var MilestoneUID = $('#adv_MilestoneUID').val();
			var FromDate = $('#adv_FromDate').val();
			var ToDate = $('#adv_ToDate').val();
			if((ProjectUID == '')  && (MilestoneUID == '') && (CustomerUID == '') && (FromDate == '')&& (ToDate == ''))
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
			}
			else 
			{
				var formData = ({'MilestoneUID':MilestoneUID,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 
				fetch_agingcounts(formData).then(function (resolve) {  
					removecardspinner($('#agingcount-view'));
					$('.counter').counterUp({
						delay: 10,
						time: 2000,
					});
				});
			}

			return false;
		});

		$(document).off('click','.reset').on('click','.reset',function(){
			$("#adv_ProjectUID").val('All');
			$("#adv_CustomerUID").val('All');
			$("#adv_FromDate").val('');
			$("#adv_ToDate").val('');
			$('.filterreport').trigger('click');
			callselect2();

		});

		$(document).on("click" , ".orderclose" , function(){
			$("#orderstablediv").fadeOut('fast');
			$("#agingcount-view").fadeIn('fast');
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
		})

		$('.filterreport').trigger('click');
	});


	$(document).off('click','.excelorderlist').on('click','.excelorderlist',function(){

		var OrderUID = $('#orderlist_orderuids').val();
		var WorkflowModuleUID = $('#orderlist_workflowmoduleuid').val();
		var filename = 'Aging Report - '+$('#orderlisttitle').text()+'.xlsx';
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>AgingReport/WriteOrdersExcel',
			xhrFields: {
				responseType: 'blob',
			},
			data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'filename':filename},
			beforeSend: function(){


			},
			success: function(data)
			{
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


	$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
		e.preventDefault();
		var $dataobject = {'CustomerUID': $(this).val()};
		if($(this).val() == 'All')
		{
			$('#adv_ProjectUID').html('<option value="All">All</option>');
			callselect2();
		}
		else
		{
			SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {
				//addcardspinner($('#AuditCard'));
			}).then(function (data) {
				if (data.validation_error == 0) {
					var Product_Select = data.Products.reduce((accumulator, value) => {
						
						return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
					}, '<option value="All">All</option>');         
					$('#adv_ProductUID').html(Product_Select);
					$('#adv_ProductUID').trigger('change');
				}
				callselect2();

			}).catch(function (error) {

				console.log(error);
			});
		}
	});

	$(document).on('change', '#agingpercentage', function (e) {  
		$('.filterreport').trigger('click');
	});


	$(window).resize(function() {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
	});

</script>







