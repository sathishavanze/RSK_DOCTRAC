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
	.pd-0-10{
		padding: 0px 10px !important;
	}
	.table .form-check .form-check-sign {
    top: -21px;
    left: 0;
    padding-right: 0;
}
</style>

<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon"> <i class="icon-file-text"></i>
		</div>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Order Upload</h4>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="col-md-12 mt-10">
		<ul class="nav nav-pills nav-pills-danger customtab entrytab" role="tablist">
		<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#singleentry" data-href="#singleentry" role="tablist">
					Single Entry
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#bulkentry" data-href="#bulkentry" role="tablist">
					Bulk Entry 
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#fileuploadtab" data-href="#fileuploadtab" role="tablist">
					Doc Upload
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload " href="<?php echo base_url(); ?>Orderentry/exceptionimport" role="tablist">
					Data Exception
				</a>
			</li>
			<?php
			if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
				?>
				<li class="nav-item">
					<a class="nav-link ajaxload active" href="<?php echo base_url(); ?>Orderentry/DocumentTracking/missingOrders" role="tablist">
						Missing Orders
					</a>
				</li>
			<?php }?>
      <li class="nav-item">
        <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/BulkWorkflow" role="tablist">
          Bulk Workflow Complete
        </a>
      </li>
		</ul>
	</div>

		<div class="tab-content tab-space customtabpane">

			<div class="tab-pane active" id="missingOrders">
				<div class="col-md-12">

					<div class="text-right"> 
						<i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
						<i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
					</div>
				</div>

				<div class="col-md-12 col-sm-12 pd-0-10">
					<div id="advancedsearch"  style="display: none;">
						<fieldset class="advancedsearchdiv">
							<legend>Advanced Search</legend>
							<form id="advancedsearchdata">
								<div class="col-md-12 pd-0">
									<div class="row " >
										<div class="col-md-4">
											<div class="form-group bmd-form-group">
												<label for="adv_UserUID" class="bmd-label-floating">Uploaded User <span class="mandatory"></span></label>
												<select class="select2picker form-control" id="adv_UserUID"  name="UserUID">  
													<?php if (count($Users) > 1) { ?>
														<option value="All">All</option>
													<?php } ?>
													<?php 
													foreach ($Users as $key => $value) { ?>

														<option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
													<?php } ?>

												</select>
											</div>
										</div>
										<div class="col-md-4 datadiv">
											<div class="form-group bmd-form-group">
												<label for="adv_CustomerUID" class="bmd-label-floating">Client <span class="mandatory"></span></label>
												<select class="select2picker form-control" id="adv_CustomerUID"  name="CustomerUID">  
													<?php if (count($Clients) > 1) { ?>
														<option value="All">All</option>
													<?php } ?>
													<?php 
													foreach ($Clients as $key => $value) { ?>

														<option value="<?php echo $value->CustomerUID; ?>" ><?php echo $value->CustomerName; ?></option>
													<?php } ?>

												</select>
											</div>
										</div>
										<div class="col-md-4 datadiv">
											<div class="form-group bmd-form-group">
												<label for="adv_LenderUID" class="bmd-label-floating">Lender <span class="mandatory"></span></label>
												<select class="select2picker form-control" id="adv_LenderUID"  name="LenderUID">  
													<?php if (count($Lenders) > 1) { ?>
														<option value="All">All</option>
													<?php } ?>
													<?php 
													foreach ($Lenders as $key => $value) { ?>

														<option value="<?php echo $value->LenderUID; ?>" ><?php echo $value->LenderName; ?></option>
													<?php } ?>

												</select>
											</div>
										</div>
									</div>

									<div class="col-md-12">
										<div class="row " >
											<div class="col-md-4 datadiv">
												<div class="bmd-form-group row">
													<div class="col-md-3 pd-0 inputprepand" >
														<p class="mt-5"> From </p>
													</div>
													<div class=" col-md-9 " style="padding-left: 0px">
														<div class="datediv">
															<input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-90 days")); ?>">
														</div>
													</div>
												</div>
											</div>

											<div class="col-md-4 datadiv">
												<div class="bmd-form-group row">
													<div class="col-md-3 pd-0 inputprepand" >
														<p class="mt-5"> To </p>
													</div>
													<div class=" col-md-9 " style="padding-left: 0px">
														<div class="datediv">
															<input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
														</div>
													</div>
												</div>
											</div>


										</div>
									</div>
									<div class="col-md-12  text-right pd-0 mt-10">
										<button type="button" class="btn btn-fill btn-facebook  search" >Submit</button>
										<button type="button" class="btn btn-tumblr btn-danger  reset">Reset</button> 


									</div>



								</div>
							</form>
						</fieldset>
					</div>
				</div>



				<div class="tab-content tab-space customtabpane">
					<div class="tab-pane active" id="orderslist">
						<div class="col-md-12 col-xs-12 ">
							<div class="material-datatables" id="tbl_DocumentTracking_parent">
								<table class="table table-striped display nowrap" id="tbl_DocumentTracking"  cellspacing="0" width="100%"  style="width:100%">
									<thead>
										<tr>
											<th>Document Tracking</th>
											<th>Client</th>
											<th>Lender</th>
											<th>Uploaded UserName</th>
											<th>Uploaded Date</th>
											<th>Document Status</th>
											<th class="no-sort">Actions</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<div class="col-md-12 col-xs-12">
								<button id="btnattachloan" type="button" class="btn btn-social btn-success pull-right"> Attach to Loan</button>
								<button id="btnsendemail-modal" type="button" class="btn btn-social btn-twitter pull-right"> Send Email</button>
								<button id="btndeletedocs" type="button" class="btn btn-social btn-danger pull-right"> Delete Docs</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>






<div class="modal fade custommodal"  id="Orders-Modal">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h5 class="modal-title">Order List</h5>
			</div>
			<div class="modal-body">
				<div class="table-responsive perfectscrollbar" style="max-height: 300px; overflow: auto;">
					<table class="table table-striped" id="tblOrderGrid">
						<thead id="tblHead">
							<tr>
								<th>Order No</th>
								<th>Loan Number</th>
								<th>Client</th>
								<th>Product</th>
								<th>Project</th>
								<th>Lender</th>
								<th>Current Status</th>
								<th>Property Address</th>
								<th>Property City</th>
								<th>Property State</th>
								<th>ZipCode</th>
								<th>OrderEntry DateTime</th>
								<th>OrderDue DateTime</th>
								<th class="no-sort">Action</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				<button type="button" id="Doc-Order-Attach-Merge" value="Merge" class="btn btn-social btn-facebook">Attach & Merge</button>
				<button type="button" id="Doc-Order-Attach" value="Separate" class="btn btn-social btn-openid">Attach</button>
			</div>

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade custommodal" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmsendmail">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="emailModalLabel">Send Email</h4>
				</div>
				<div class="modal-body pd-10">
					<fieldset>

						<div class="card-body pd-10">
							<div class="form-group bmd-form-group">
								<label for="Email" class="bmd-label-floating"> Email Address *</label>
								<input type="text" class="form-control" id="Email" name="Email" required="true" aria-required="true">
							</div>

							<div class="col-sm-10 checkbox-radios">
								<div class="form-check form-check-inline">
									<label class="form-check-label">
										<input class="form-check-input" id="cbx-customeremail" type="checkbox" value="" data-emails=""> Client
										<span class="form-check-sign">
											<span class="check"></span>
										</span>
									</label>
								</div>
								<div class="form-check form-check-inline">
									<label class="form-check-label">
										<input class="form-check-input" id="cbx-lenderemail" type="checkbox" value="" data-emails=""> Lender
										<span class="form-check-sign">
											<span class="check"></span>
										</span>
									</label>
								</div>
							</div>
							<div class="form-group bmd-form-group">
								<label for="Subject" class="bmd-label-floating"> Subject</label>
								<input type="text" class="form-control" id="Subject" name="Subject" aria-required="true">
							</div>
							<div class="form-group bmd-form-group">
								<label for="Body" class="bmd-label-floating"> Email Body</label>
								<textarea name="Body" class="form-control" id="Body" required="true" rows="5"></textarea>     
							</div>
						</div>
					</fieldset>

				</div>
				<div class="modal-footer" style="padding: 0px 20px;">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" id="btnsendemail">Send Email</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	var tbl_DocumentTracking = false;
	$(function() {
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
		$('#tbl_DocumentTracking').DataTable().destroy();
		$('#tblOrderGrid').DataTable().destroy();
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


		$('.fa-filter').click(function(){
			$("#advancedsearch").slideToggle();
		});


		initialize('false');
		Ordersinitialize('false');
					// workinprogressinitialize('false');

					$(document).off('click', '#btnattachloan').on('click', '#btnattachloan', function (e) {
						// $(tblOrderGrid).api().draw();
						if ($('.DocumentTrackingUID:checked').length < 1) {
							$.notify({
								icon: "icon-bell-check",
								message: "No Document Choosen to Attach"
							}, {
								type: 'danger',
								delay: 1000
							});
							return false;
						}
						$('#Orders-Modal').modal('show');
					})

					$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
					});

					$(window).resize(function() {
						$($.fn.dataTable.tables( true ) ).css('width', '100%');
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
					});

					$('#Orders-Modal').on('shown.bs.modal', function (e) {
						addspinnertodiv('#Orders-Modal .modal-content');
						$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
						removespinnertodiv('#Orders-Modal .modal-content');
					});


				});


	$(document).off('click','#Doc-Order-Attach, #Doc-Order-Attach-Merge').on('click','#Doc-Order-Attach, #Doc-Order-Attach-Merge',function(e){
		e.preventDefault();

		var button = this;
		var button_text = this.innerHTML;
		var DocumentTrackingUID = [];
		$('.DocumentTrackingUID:checked').each(function (key, value) {
			DocumentTrackingUID.push($(value).attr('data-documenttrackinguid'));
		});

		console.log($('.DocumentTrackingUID:checked'));
		var OrderUID = $('.OrderUID:checked').attr('data-OrderUID');
		console.log(OrderUID);

		if (!OrderUID) {
			$.notify({
				icon: "icon-bell-check",
				message: "No Order Choosen"
			}, {
				type: 'danger',
				delay: 1000
			});
			return false;

		}

		if (DocumentTrackingUID.length < 1) {
			$.notify({
				icon: "icon-bell-check",
				message: "No Document Choosen to Attach"
			}, {
				type: 'danger',
				delay: 1000
			});
			return false;

		}

		var UploadType = this.value;

		$.ajax({
			url: 'Orderentry/DocumentTracking/AttachDocumentToOrder',
			type: 'POST',
			dataType: 'json',
			data: {'DocumentTrackingUID': DocumentTrackingUID, 'OrderUID': OrderUID, 'UploadType': UploadType},
			beforeSend: function () {
				$(button).prop('disabled', true);
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Loading...');
			}
		})
		.done(function(response) {
			console.log(response);

			if (response.validation_error == 1) {
				$.notify({
					icon: "icon-bell-check",
					message: response.message
				}, {
					type: 'danger',
					delay: 1000
				});
			}
			else{
				initialize();
				$.notify({
					icon: "icon-bell-check",
					message: response.message
				}, {
					type: 'success',
					delay: 1000
				});
				$('#Orders-Modal').modal('hide');
			}



		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
			$(button).prop('disabled', false);
			$(button).html(button_text);

		});

	});

	$(document).off('click','#btndeletedocs').on('click','#btndeletedocs',function(e){
		e.preventDefault();
		var button = this;
		var button_text = this.innerHTML;
		var DocumentTrackingUID = [];
		$('.DocumentTrackingUID:checked').each(function (key, value) {
			DocumentTrackingUID.push($(value).attr('data-documenttrackinguid'));
		});


		if (DocumentTrackingUID.length < 1) {
			$.notify({
				icon: "icon-bell-check",
				message: "No Document Choosen to Delete"
			}, {
				type: 'danger',
				delay: 1000
			});
			return false;

		}

		swal({
			title: 'Are you sure?',
			text: 'Do you want to discard the document!',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, do it!',
			cancelButtonText: 'No, keep it',
			confirmButtonClass: "btn btn-success",
			cancelButtonClass: "btn btn-danger",
			buttonsStyling: false
		}).then(function (confirm) {

			$.ajax({
				url: 'Orderentry/DocumentTracking/DeleteDocuments',
				type: 'POST',
				dataType: 'json',
				data: {'DocumentTrackingUID': DocumentTrackingUID},
				beforeSend: function () {
					$(button).prop('disabled', true);
					$(button).html('<i class="fa fa-spin fa-spinner"></i> Loading...');
				}
			})
			.done(function(response) {
				console.log(response);
				initialize();

			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
				$(button).prop('disabled', false);
				$(button).html(button_text);

			});
		}, function (dismiss) {});


	});

	$(document).off('click', '#btnsendemail-modal').on('click', '#btnsendemail-modal', function (e) {

		e.preventDefault();

		var DocumentTrackingUID = [];
		$('.DocumentTrackingUID:checked').each(function (key, value) {
			DocumentTrackingUID.push($(value).attr('data-documenttrackinguid'));
		});

		if (DocumentTrackingUID.length == 1) {

			var requestdata = {'DocumentTrackingUID':DocumentTrackingUID};

			SendAsyncAjaxRequest('POST', 'Orderentry/DocumentTracking/GetCustomerLenderEmail', requestdata, 'json', true, true, function () {}).
			then(function (response) {

				if (response.validation_error == 0) {

					var lenderemails = response.lenderemails.join(";");
					var customeremails = response.customeremails.join(";");
					$('#cbx-customeremail').attr('data-emails', customeremails + ';');
					$('#cbx-lenderemail').attr('data-emails', lenderemails + ';');

				}

				$('#emailModal').find("input, textarea").val("");
				$('#emailModal').modal('show');
			});
		}
		else{
			$.notify({
				icon: "icon-bell-check",
				message: "Choose only one document to send mail"
			}, {
				type: 'danger',
				delay: 1000
			});
		}

	});


	$(document).off('change', '#cbx-lenderemail, #cbx-customeremail').on('change', '#cbx-lenderemail, #cbx-customeremail', function (e) {

		e.preventDefault();

		if ($(this).prop('checked')) {

			var previousvalue = $('#Email').val();

			$('#Email').val(previousvalue + $(this).attr('data-emails')).trigger('change');
		}
	})


	$(document).off('submit', '#frmsendmail').on('submit', '#frmsendmail', function (e) {
		e.preventDefault();

		var button = $('btnsendemail');
		var button_text = button.innerHTML;

		var email_elem = $('#emailModal').find('#Email').val();

		console.log(email_elem);
		var emails = email_elem.split(";");

		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var is_invalid = false;

		$.each(emails, function (key, email) {
							// body...
							if (!filter.test(email.trim())) {

								$.notify({
									icon: "icon-bell-check",
									message: "Invalid Email(s)"
								}, {
									type: 'danger',
									delay: 1000
								});

								$('#Email').addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
								is_invalid = true;
								return false;
							}
						});

		if (is_invalid) {
			return false;
		}

		$('#Email').removeClass("is-invalid").closest('.form-group').addClass('has-success').removeClass('has-danger');

		var formdata = new FormData($(this)[0]);


		var DocumentTrackingUID = "undefined";
		$('.DocumentTrackingUID:checked').each(function (key, value) {
			formdata.append('DocumentTrackingUID', $(value).attr('data-documenttrackinguid'));
		});
		$.ajax({
			url: 'Orderentry/DocumentTracking/SendEmail',
			type: 'POST',
			dataType: 'json',
			data: formdata,
			contentType: false,
			processData: false,
			beforeSend: function () {
				$(button).prop('disabled', true);
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Send Email...');
				addspinnertodiv('#emailModal .modal-content');

			}
		})
		.done(function(response) {
			console.log("success");
			if (response.validation_error == 1) {
				$.notify({
					icon: "icon-bell-check",
					message: "Unable to send email"
				}, {
					type: 'danger',
					delay: 1000
				});

			}
			else{
				$.notify({
					icon: "icon-bell-check",
					message: response.message
				}, {
					type: 'success',
					delay: 1000
				});

			}
			$('#emailModal').modal('hide');
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
			$(button).prop('disabled', false);
			$(button).html(button_text);
			removespinnertodiv('#emailModal .modal-content');

		});

	})

	$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

		var UploadedByUserUID = $('#adv_UserUID option:selected').val();
		var LenderUID = $('#adv_LenderUID option:selected').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		if((LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == ''))
		{

			var formData = 'All';
		} else {


			var formData = ({ 'LenderUID': LenderUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate, 'UploadedByUserUID':UploadedByUserUID}); 


		}


		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>Orderentry/DocumentTracking/DocumentExcel',
			xhrFields: {
				responseType: 'blob',
			},
			data: {'formData':formData},
			beforeSend: function(){


			},
			success: function(data)
			{
				var filename = "DocumentTracking.csv";
				if (typeof window.chrome !== 'undefined') {
				            //Chrome version
				            var link = document.createElement('a');
				            link.href = window.URL.createObjectURL(data);
				            link.download = "DocumentTracking.csv";
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
					$('#adv_LenderUID').html('<option value = "All">All</option>');
					
					$("#adv_CustomerUID").val('All');
					$("#adv_ProjectUID").val('All');
					$("#adv_UserUID").val('All');
					$("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
					$("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
					callselect2();

					initialize(false);


				});

	$(document).off('click','.search').on('click','.search',function(){

		var UploadedByUserUID = $('#adv_UserUID option:selected').val();
		var LenderUID = $('#adv_LenderUID option:selected').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		if((LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == ''))
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


			var formData = ({ 'LenderUID': LenderUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate, 'UploadedByUserUID':UploadedByUserUID}); 

			initialize(formData);



		}
		return false;

	});

	function initialize(formData)
	{

		tbl_DocumentTracking = $('#tbl_DocumentTracking').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY: '100vh',
			paging:  true,
			fixedColumns:   {
				leftColumns: 1,
							// rightColumns: 1
						}, 
						"bDestroy": true,
						"autoWidth": true,
						"processing": true, //Feature control the processing indicator.
						"serverSide": true, //Feature control DataTables' server-side processing mode.
						"order": [], //Initial no order.
						"pageLength": 50, // Set Page Length
						"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
						language: {
							sLengthMenu: "Show _MENU_ Documents",
							emptyTable:     "No Documents Found",
							info:           "Showing _START_ to _END_ of _TOTAL_ Documents",
							infoEmpty:      "Showing 0 to 0 of 0 Orders",
							infoFiltered:   "(filtered from _MAX_ total Documents)",
							zeroRecords:    "No matching Documents found",
							processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
						},
						// Load data for the table's content from an Ajax source
						"ajax": {
							"url": "<?php echo base_url('Orderentry/DocumentTracking/TrackingDocuments_AjaxList')?>",
							"type": "POST",
							"data" : {'formData':formData} 
						},
						"columnDefs": [ {
							"targets": 'no-sort',
							"orderable": false,
						} ],
						"drawCallback": function( settings ) {
							$('.main-panel').scrollTop(0).perfectScrollbar('update');
							$('.perfectscrollbar').perfectScrollbar();
						}

					});

	}

	function Ordersinitialize(formData){


		var tblOrderGrid = $('#tblOrderGrid').DataTable( {
			scrollX:        true,
							// scrollY:        "300px",
							scrollCollapse: true,
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
					"pageLength": 50, // Set Page Length
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
						"url": "<?php echo base_url('Orderentry/DocumentTracking/Orders_ajax_list')?>",
						"type": "POST",
						"data" : {'formData':formData} 
					},
					"drawCallback": function( settings ) {
						$('.main-panel').scrollTop(0).perfectScrollbar('update');
						$('.perfectscrollbar').perfectScrollbar();
					}


				});


	}

	function addspinnertodiv(ele){
		$(ele).append(spinner); 
		$(ele).append(overlaydiv);
		$(ele).closest(".card .btn").addClass("reduceindex");
	}	


	function removespinnertodiv(ele){
		$('.spinner_svg').remove();
		$('.d2tspinner-overlay').remove();
		$(ele).closest(".card .btn").removeClass("reduceindex");
	}

</script>
