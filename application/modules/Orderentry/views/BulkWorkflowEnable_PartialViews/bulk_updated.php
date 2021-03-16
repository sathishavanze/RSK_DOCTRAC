<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small>
			Updated&nbsp;<i class="fa fa-check-circle"></i></small>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#error-data" role="tablist"><small>
			Not Updated&nbsp;<i class="fa fa-times-circle-o"></i></small>
		</a>
	</li>
</ul>
<div class="tab-content tab-space">
	<div id="success-table" class="tab-pane active cont">

		<div class="">
			<button type="button" class="btn btn-facebook btn-social" onClick="UpdateSuccessExcel();">Excel</button>
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="tblsuccessentries">
					<thead>
						<tr>
							<th>Order Number</th>
							<?php 	

							foreach ($headingsArray as $key => $value) {
								?><th><?php echo $value; ?></th><?php
							}

							?>
						</tr>
					</thead>
					<tbody>

					</tbody>

				</table>
			</div>
		</div>
	</div>

	<div id="error-data" class="tab-pane cont">

		<div class="">
			<button type="button" class="btn btn-facebook btn-social btn-sm" onClick="UpdateFailedExcel();">Excel</button>
			<span class="badge badge-pill pull-right" style="background-color: #757575;">Invalid</span>
			<span class="badge badge-pill pull-right" style="background-color: #ff04ec;">Duplicate LoanNumber</span>
			<span class="badge badge-pill pull-right" style="background-color: #ff0013;">Not Available</span>
			<span class="badge badge-pill pull-right" style="background-color: #795548;">Workflow is not available</span>
			<span class="badge badge-pill pull-right" style="background-color: #9575cd;">Borrower is not matched</span>
			<!-- <span class="badge badge-pill pull-right" style="background-color: #ffc107;">Milestone is not matched</span> -->
			<span class="badge badge-pill pull-right" style="background-color: #2196f3;">LoanType is not matched</span>
			<!-- <span class="badge badge-pill pull-right" style="background-color: #ff9800;">Workflow is already enabled</span> -->
			<!-- <span class="badge badge-pill pull-right" style="background-color: #4caf50;">Workflow is already completed</span> -->
			<span class="badge badge-pill pull-right" style="background-color: #607d8b;">Workup Enable Error</span>

		</div>
		<div class="">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="tblfailedentries">
					<thead>
						<tr>
							<?php 	

							foreach ($headingsArray as $key => $value) {
								?><th><?php echo $value; ?></th><?php
							}

							?>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value="<?php echo $InsertedOrderUID; ?>" name="InsertedOrderUID[]" id="InsertedOrderUID">


<script type="text/javascript">
	
	function UpdateSuccessExcel() {		

		$.ajax({
			type: "POST",
			url: 'Orderentry/outputupdateSuccessExcel',
			xhrFields: {
				responseType: 'blob',
			},
			beforeSend: function(){


			},
			success: function(data)
			{
				var filename = "BulkWorkflowEnable_Update_Success.xlsx";
				if (typeof window.chrome !== 'undefined') {
					//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = "BulkWorkflowEnable_Update_Success.xlsx";
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

	}
	
	function UpdateFailedExcel() {		

		$.ajax({
			type: "POST",
			url: 'Orderentry/outputUpdateFailedExcel',
			xhrFields: {
				responseType: 'blob',
			},
			beforeSend: function(){

			},
			success: function(data)
			{
				var filename = "BulkWorkflowEnable_Update_Failed.xlsx";
				if (typeof window.chrome !== 'undefined') {
					//Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(data);
					link.download = "BulkWorkflowEnable_Update_Failed.xlsx";
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

	}
</script>
