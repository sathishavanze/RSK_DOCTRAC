<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />

<style type="text/css">
.pd-btm-0{
	padding-bottom: 0px;
}

.margin-minus8{
	margin: -8px;
}

.mt--15{
	margin-top: -15px;
}

.bulk-notes
{
	list-style-type: none
}
.bulk-notes li:before
{
	content: "*  ";
	color: red;
	font-size: 15px;
}

.nowrap{
	white-space: nowrap
}

.table-format > thead > tr > th{
	font-size: 12px;
}
#loadcontent {
    margin-top: 20px;
}
td
{
	padding: 5px;
}
</style>
<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">ORDER SEARCH
		</div>
	</div>
	<div class="card-body">
		<!-- <ul class="nav nav-pills nav-pills-danger" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#singleentry" role="tablist">
					Single Entry
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#bulkentry" role="tablist">
					Bulk Entry 
				</a>
			</li>
		</ul> -->

		<div class="tab-content tab-space">
			<div class="tab-pane active" id="singleentry">
				<form action="#"  name="order_search_frm" id="order_search_frm">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="OrderNumber" class="bmd-label-floating">Order Number</label>
								<input type="text" class="form-control" id="OrderNumber" name="OrderNumber" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="LoanNumber" class="bmd-label-floating">Loan Number</label>
								<input type="text" class="form-control" id="LoanNumber" name="LoanNumber" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyAddress" class="bmd-label-floating">Property Address</label>
								<input type="text" class="form-control" id="PropertyAddress" name="PropertyAddress">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="CustomerReferenceNumber" class="bmd-label-floating">Customer Reference Number</label>
								<input type="text" class="form-control" id="CustomerReferenceNumber" name="CustomerReferenceNumber">
							</div>
						</div>
					</div>
					<div class="col-sm-12 form-group">
						<p class="text-right">
							<button type="button" class="btn btn-space btn-social btn-color btn-twitter SearchOrder" value="1" id="SearchOrder"><span class="icon-search4"></span> Search Order</button>
							<button type="button" class="btn btn-space btn-social btn-color" value="1" id="Reset"><span class="icon-undo"></span> Reset</button>
						</p>
					</div>
				</form>
				<table class="table table-hover table-striped" id="SearchOrderTable" width="100%;">
					<thead>
						<tr>
							<th>Order No</th>
							<th>Client </th>
							<th>Customer Ref No. </th>
							<th>Project</th>
							<th>Loan No</th>
							<th>Lender</th>
							<th>Property Address</th>   
							<th class="no-sort">Actions</th>
						</tr>
					</thead>
					<tbody class="OrderDetails">

					</tbody>
				</table>
			</div>
		</div>
		<div >

			<div class="row col-sm-12">
			<div class="material-datatables" id="myordertable_parent">


			</div></div>
		</div>
	</div>
</div>




<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>

<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$("select.select2picker").select2({
			theme: "bootstrap",
		});
		$('#SearchOrderTable').DataTable({
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            scrollY: '100vh',
            paging:  true,
             "bDestroy": true,
            "autoWidth": true,
          "processing": true, //Feature control the processing indicator.
          "order": [], //Initial no order.
          "pageLength": 50, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],

		});
		$('#Reset').off('click').on('click', function (e) {
				$('#OrderNumber').val('');
				$('#LoanNumber').val('');
				$('#PropertyAddress').val('');
				$('#CustomerReferenceNumber').val('');
				$('.OrderDetails').empty();
				if ( $.fn.DataTable.isDataTable('#SearchOrderTable') ) {
					$('#SearchOrderTable').DataTable().destroy();
				}
				$('#SearchOrderTable').DataTable({
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            scrollY: '100vh',
            paging:  true,
             "bDestroy": true,
            "autoWidth": true,
          "processing": true, //Feature control the processing indicator.
          "order": [], //Initial no order.
          "pageLength": 50, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],

		});
				//$('.OrderDetails').html('<div class="col-md-12"><p style="text-align:center;">find data</p></div>');

		});
		$('#SearchOrder').off('click').on('click', function (e) {
			var form_data = $("#order_search_frm").serialize();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			var OrderNumber = $('#OrderNumber').val();
			var LoanNumber = $('#LoanNumber').val();
			var PropertyAddress = $('#PropertyAddress').val();
			var CustomerReferenceNumber = $('#CustomerReferenceNumber').val();
			if(OrderNumber != '' || LoanNumber != '' || PropertyAddress != '' || CustomerReferenceNumber != '')
			{
				var url = 'CustomerOrderSearch/GetCustomerOrderSearchDetails';
				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>' + url,
					data: form_data,
					dataType: 'json',
					beforeSend: function(){
						$('.spinnerclass').addClass("be-loading-active");
						button.attr("disabled", true);
						button.html('Loading ...'); 
					},
					success: function(data)
					{
						$('.OrderDetails').empty();
						if ( $.fn.DataTable.isDataTable('#SearchOrderTable') ) {
							$('#SearchOrderTable').DataTable().destroy();
						}
						$('.OrderDetails').html(data);
						$('#SearchOrderTable').DataTable({
							scrollX:        true,
							scrollCollapse: true,
							fixedHeader: false,
							scrollY: '100vh',
							paging:  true,
							"bDestroy": true,
							"autoWidth": true,
					        "processing": true, //Feature control the processing indicator.
					        "order": [], //Initial no order.
					        "pageLength": 50, // Set Page Length
					        "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
					      });
						button.attr("disabled", false);
						button.html(button_text); 

					}
				});
			}
			else
			{
				 $.notify(
                      {
                        icon:"icon-bell-check",
                        message:'Please Enter any one Search Keywords'
                      },
                      {
                        type:'danger',
                        delay:1000 
                      });
			}
			
		});
	});


</script>







