<style type="text/css">
	.ft-weight600s{
		font-weight: 600;
	}

	.production_tat_table tr td:first-child{
		font-weight: 600;
		text-align: left;
	}

	.production_tat_table tbody tr td {
		min-width: 0;
		text-align: center;

	}

	.production_tat_table thead tr th {
		min-width: 0;
		text-align: center;
		line-height: 1.4;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card mt-20" id="OrderAge_Card">
			<div class="card-header card-header-icon card-header-info">
				<div class="card-icon">
					Order Ageing 
					<i class="icon-database-time2"></i>
				</div>
				<div class="row mt-10"> 
					<div class="col-md-12 text-right"> 				
						<button id="OrderAge_refresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="col-md-12 col-xs-12">
					<div id="OrderAge-div" class="table-responsive">
						<table class="table table-striped table-bordered grp-mdl production_tat_table" id="OrderAge-data">
							<thead>
								<tr>
									<th></th>
									<th>0</th>
									<th>1</th>
									<th>2</th>
									<th>3</th>
									<th>4</th>
									<th>5</th>
									<th>6</th>
									<th>7</th>
									<th>8</th>
									<th>9</th>
									<th>10</th>
									<th>10+ Days</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
                <?php foreach ($workflow as $key => $value) { ?>
								  <tr>
									  <td><?php echo $value->WorkflowModuleName ?></td>
                    <?php 
                    $workflow = str_replace(' ', '', $value->SystemName);
                    for ($i=0; $i<=10; $i++) {  							   echo '<td><a href="javascript:void(0);" data-title="'.$workflow.' '.$i.' Days - Orders" class="counter '.$workflow.$i.'days" onclick="OrderAgeOrders(this,\''.$value->WorkflowModuleUID.'\','.$i.')">0</a></td>';
                    }
                    ?> 
                    <td><a href="javascript:void(0);" data-title="<?php echo $workflow; ?> 10+ Days - Orders" class="counter <?php echo $workflow;?>10plus" onclick="OrderAgeOrders(this,'<?php echo $workflow; ?>10plus','10plus')">0</a></td>
                    <td><a href="javascript:void(0);" data-title="<?php echo $value->WorkflowModuleName ?> Total - Orders" class="counter <?php echo $workflow;?>total" onclick="OrderAgeOrders(this,'<?php echo $value->WorkflowModuleUID;?>','Total')">0</a></td>
								  </tr>                  
                <?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>	
	</div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.counterup.min.js"></script>
<script type="text/javascript">

	function OrderAgeOrders(event,status,days)
	{
		if($(event).text() == '0' || $(event).text() == ''){
			$.notify({icon:"icon-bell-check",message:'No Orders Found'},{type:"danger",delay:3000 });
			return false;
		}
		$('.dashboardview').hide();
		$('.orderstable').show();
		OrderAgeOrdersinitialize(status,days);
		$('#orderstablestatus').val(status);
		$('#orderstabledays').val(days);
		$('.table-title-heading').removeClass('hide');
		$('.table-title-heading').text($(event).attr('data-title'));
		ScrolltoTop();
	}

	function OrderAgeOrdersinitialize(status,days)
	{

		CustomerUID = $("#DashboardCustomer").val();
		ProductUID =$("#DashboardProduct").val();
		ProjectUID = $("#DashboardProject").val();
		InputDocTypeUID = $("#DashboardDocType").val();
		FromDate = $("#chartfdate").val();
		ToDate = $("#charttdate").val();
		if (CustomerUID == "") {
			CustomerUID = "all";
		}
		if (ProductUID == "") {
			ProductUID = "all";
		}
		if (ProjectUID == "") {
			ProjectUID = "all";
		}
		if (InputDocTypeUID == "") {
			InputDocTypeUID = "all";
		}
		var dategroup = $("#datefilter_type option:selected").attr("data-groupby");

		var data = {};
		data.Customer = CustomerUID;
		data.Product = ProductUID;
		data.Project = ProjectUID;
		data.DocType = InputDocTypeUID;
		data.from = FromDate;
		data.to = ToDate;
		data.dategroup = dategroup;
		data.status = status;
		data.days = days;
		$('#myordertable').DataTable().destroy();
		myordertable = $('#myordertable').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			paging:  true,
			fixedColumns:   {
				leftColumns: 1,
				rightColumns: 1
			}, 
			"bDestroy": true,
			"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		//"order": [], //Initial no order.
		//"pageLength": 50, // Set Page Length
		//"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
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
			"url": "<?php echo base_url('Dashboard/get_OrderAge_Orders')?>",
			"type": "POST",
			"data" : data 
			
		}

	});
		

		$('.exceldownloadbtn').attr('status-id',status);
		$('.CSVdownloadbtn').attr('status-id',status);
		$('.downloadbtndiv1').hide();
		$('.downloadbtndiv').hide();
		$('.downloadbtndiv2').show();
	}


	function OrderAge(data)
	{

		return new Promise(function (resolve, reject) {  

			$.ajax ({
				type:'POST',
				url:'<?php echo base_url();?>Dashboard/OrderAging',
				dataType: 'JSON',
				data: data,
				beforeSend: function() {

				},
				success:function(response)
				{   

					if (response.validation_error == "0") {
						/*Receivedcounts*/
						$.each(response.counts, function(mainkey, firstvalue) {
							$.each(firstvalue, function(elementkey, value) {

								$('.'+elementkey).text((value == null) ? 0 : value);
							})
						})
						resolve('success');

					}

				}

			});
		});
	}

	$('#OrderAge_refresh').off('click').on('click', function (e) {  
		e.preventDefault();
		dashboardaddcardspinner($('#OrderAge_Card'));
		CustomerUID = $("#DashboardCustomer").val();
		ProductUID =$("#DashboardProduct").val();
		ProjectUID = $("#DashboardProject").val();
		DocTypeUID = $("#DashboardDocType").val();
		FromDate = $("#chartfdate").val();
		ToDate = $("#charttdate").val();
		if (CustomerUID == "") {
			CustomerUID = "all";
		}
		if (ProductUID == "") {
			ProductUID = "all";
		}
		if (ProjectUID == "") {
			ProjectUID = "all";
		}
		if (DocTypeUID == "") {
			DocTypeUID = "all";
		}
		var dategroup = $("#datefilter_type option:selected").attr("data-groupby");

		var data = {};
		data.Customer = CustomerUID;
		data.Product = ProductUID;
		data.Project = ProjectUID;
		data.DocType = DocTypeUID;
		data.from = FromDate;
		data.to = ToDate;
		data.dategroup = dategroup;

		OrderAge(data).then(function (resolve) {  
			dashboardremovecardspinner($('#OrderAge_Card'));
			$('.counter').counterUp({
				delay: 10,
				time: 2000,
			});
		});

	})

	/*EXCEL GENERATION AND CSV GENERATION*/
	$(document).off('click', '.OrderAgeExcel').on('click', '.OrderAgeExcel', function(event) {
		var status = $('#orderstablestatus').val();
		var days = $('#orderstabledays').val();		
		var btntext = $(this).text();
		CustomerUID = $("#DashboardCustomer").val();
		ProjectUID = $("#DashboardProject").val();
		LenderUID = $("#DashboardLender").val();
		FromDate = $("#chartfdate").val();
		ToDate = $("#charttdate").val();
		if (CustomerUID == "") {
			CustomerUID = "all";
		}
		if (ProjectUID == "") {
			ProjectUID = "all";
		}
		if (LenderUID == "") {
			LenderUID = "all";
		}
		var dategroup = $("#datefilter_type option:selected").attr(
			"data-groupby"
			);

		var data = {};
		data.Customer = CustomerUID;
		data.Project = ProjectUID;
		data.Lender = LenderUID;
		data.from = FromDate;
		data.to = ToDate;
		data.dategroup = dategroup;
		data.status = status;
		data.days = days;

		var formdata = '';
		var filename = $('.table-title-heading').text()+'.xlsx';

		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>Dashboard/OrderAge_Excel',
			xhrFields: {
				responseType: 'blob',
			},
			data: data,
			beforeSend: function(){
				$('.OrderAgeExcel').html('Please wait...');
				$(".OrderAgeExcel").attr('disabled','disabled');
			},
			success:function(data)
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
				$('.OrderAgeExcel').html('Excel');
				$(".OrderAgeExcel").removeAttr('disabled');

			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
			},
			failure: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			},
		});
	});

	$(document).off('click', '.OrderAgeCSV').on('click', '.OrderAgeCSV', function(event) {
		var status = $('#orderstablestatus').val();
		var days = $('#orderstabledays').val();
		var btntext = $(this).text();
		CustomerUID = $("#DashboardCustomer").val();
		ProjectUID = $("#DashboardProject").val();
		LenderUID = $("#DashboardLender").val();
		FromDate = $("#chartfdate").val();
		ToDate = $("#charttdate").val();
		if (CustomerUID == "") {
			CustomerUID = "all";
		}
		if (ProjectUID == "") {
			ProjectUID = "all";
		}
		if (LenderUID == "") {
			LenderUID = "all";
		}
		var dategroup = $("#datefilter_type option:selected").attr(
			"data-groupby"
			);

		var data = {};
		data.Customer = CustomerUID;
		data.Project = ProjectUID;
		data.Lender = LenderUID;
		data.from = FromDate;
		data.to = ToDate;
		data.dategroup = dategroup;
		data.status = status;
		data.days = days;
		var filename = $('.table-title-heading').text()+'.csv';
		var formdata = '';
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>Dashboard/OrderAge_CSV',
			xhrFields: {
				responseType: 'blob',
			},
			data:data,
			beforeSend: function(){
				$('.OrderAgeCSV').html('Please wait...');
				$(".OrderAgeCSV").attr('disabled','disabled');
			},
			success:function(data)
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
				$('.OrderAgeCSV').html('CSV');
				$(".OrderAgeCSV").removeAttr('disabled');

			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
			},
			failure: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			},
		});
	});

</script>
