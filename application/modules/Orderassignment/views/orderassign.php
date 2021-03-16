<style>
	#orderassigntable tbody td{
	font-size: 10px !important;
}
#orderassigntable thead th{
	font-size: 10px !important;
}
</style>
<svg style="height: 50px;width: 50px;z-index: 99;display: none;" class="d2tspinner-circular" viewBox="25 25 50 50">
	<circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /></svg>
<div class="card">
	<div class="card-body">
		<div class="row col-md-12">

			<div class="col-md-6">

				<div class="form-group  bmd-form-group">
					<label for="Project" class="bmd-label-floating">Project</label>
					<select class="select2picker form-control" id="assignmentproject" name="assignmentproject">
						<option value="all" selected>All</option>
						<?php foreach ($Projects as $key => $project) {?>
						<option value="<?php echo $project->ProjectUID; ?>">
							<?php echo $project->ProjectName;?>
						</option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-6 text-right" style="margin-top: 10pt;">
				<h4>Total Orders : <span class="number" id="total_orders">0</span></h4>
			</div>
		</div>
		<div class="material-datatables table-responsive tablescroll" id="myordertable_parent">

			<table class="table table-striped display nowrap" id="orderassigntable" cellspacing="0" width="100%" style="width:100%">
				<thead>
					<tr>
						<th>Order No</th>
						<th>Client</th>
						<th>Project</th>
						<th>Status</th>
						<th>Property Address1</th>
						<th>Property City</th>
						<th>Property County</th>
						<th>Property StateCode</th>
						<th>Property ZipCode</th>
						<th>Entry Date</th>
						<th>Stacking</th>
						<th>Review</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="assignusers" class="row col-md-12">
			<?php 
	$Workflows = array('1'=>'Stacking','2'=>'Review');
	foreach ($Workflows as $key => $value) { ?>
			<div class="col-md-6">
				<div class="form-group bmd-form-group">
					<label class="control-label"><b>
							<?php echo $value; ?></b></label>
					<select class="select2picker workflowusers" id="<?php echo $value; ?>" data-id="<?php echo $key; ?>">
						<option value=""></option>

					</select>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="ml-auto text-right">
			<button type="button" class="btn btn-fill  btn-info btn-wd assignorder"><i class="icon-rotate-cw2 pr-10"></i>Assign
				Order</button>
			<button type="button" class="btn btn-fill  btn-danger btn-wd unassign_order"><i class="icon-rotate-ccw2 pr-10"></i>Unassign
				Order</button>
		</div>
	</div>
</div>



<!--END CONTENT-->
<script type="text/javascript">
	$(document).ready(function () {

		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
		var assignment_table = '';
		var SelectedProject = $('#assignmentproject').val();

		fn_orderassigntable_init(SelectedProject);

		$('#assignmentproject').off('change').on('change', function (e) {
			var SelectedProject = $(this).val();

			fn_orderassigntable_destroy();
			fn_orderassigntable_init(SelectedProject);
		});


		$(document).off('click', 'input[name="input_assigncheckbox"]').on('click', 'input[name="input_assigncheckbox"]',
			function (e) {
				var checkbox_count = $("input[name='input_assigncheckbox']:checked").length;
				if (checkbox_count == 1 /* && $(this).prop('checked') == true*/ ) {

					var SelectedProject = $('#assignmentproject').val();
					var OrderUID = $(this).attr('data-OrderUID');
					var obj = {
						"OrderUID": OrderUID
					};

					$.ajax({
						type: "POST",
						url: "Orderassignment/GetProjectUsers",
						data: {
							'OrderUID': OrderUID
						},
						dataType: "json",
						success: function (response) {

							$('#Stacking').append(response.Productionhtml);
							$('#Review').append(response.Reviewhtml);
							callselect2();
							fn_orderassigntable_destroy();
							fn_orderassigntable_init.call(obj, SelectedProject);
						}
					});
				} else if (checkbox_count > 1) {

					$('$Stacking option:selected').prop('selected', false);
					$('$Review option:selected').prop('selected', false);
					callselect2();
				} else if (checkbox_count == 0 && $(this).prop('checked') == false) {

					$('.workflowusers').html('<option value=""></option>');
					callselect2();
					var SelectedProject = $('#assignmentproject').val();
					var OrderUID = '';
					var obj = {
						"OrderUID": OrderUID
					};
					fn_orderassigntable_destroy();
					fn_orderassigntable_init.call(obj, SelectedProject);
				}
			});


		$(document).off('click', '.assignorder').on('click', '.assignorder', function (e) {
			var checkedorders = $("input[name='input_assigncheckbox']:checked");
			var OrderUIDs = [];
			$(checkedorders).each(function (key, value) {
				OrderUIDs.push($(value).attr('data-OrderUID'));
			});

			var Stacking = $('#Stacking').val();
			var Review = $('#Review').val();

			console.log(OrderUIDs);

			if (OrderUIDs.length == 0) {
				$.notify({
					icon: "icon-bell-check",
					message: "No Orders Choosen"
				}, {
					type: 'danger',
					delay: 1000
				});
				return;
			}

			if (!Stacking && !Review) {
				$.notify({
					icon: "icon-bell-check",
					message: "No Workflow Choosen"
				}, {
					type: 'danger',
					delay: 1000
				});
				return;

			}

			if (OrderUIDs.length != 0 && (Stacking || Review)) {



				button = $(this);
				button_val = $(this).val();
				button_text = $(this).html();

				$.ajax({
					type: "POST",
					url: "Orderassignment/assignorder",
					data: {
						'OrderUID': OrderUIDs,
						'Stacking': Stacking,
						'Review': Review
					},
					dataType: "json",
					
					beforeSend: function () {
						$('.spinnerclass').addClass("be-loading-active");
						button.attr("disabled", true);
						button.html('Loading ...');
					},

					success: function (response) {
						if (response.validation_error == 0) {

							$.notify({
								icon: "icon-bell-check",
								message: response.message
							}, {
								type: 'success',
								delay: 1000
							});

							triggerpage(window.location.href);
						} else if (response.validation_error == 1) {

							$.notify({
								icon: "icon-bell-check",
								message: response.message
							}, {
								type: 'success',
								delay: 1000
							});


						}
						button.html('Preview');
						button.removeAttr("disabled");

					}
				});
			} else {
				$.notify({
					icon: "icon-bell-check",
					message: "Order/Workflow Not Choosen"
				}, {
					type: 'danger',
					delay: 1000
				});
				return;

			}
		});

	/* --- ORDER UNASSIGN SCRIPT -- */

		
	$(document).off('click','.unassign_order').on('click','.unassign_order', function() {
		if($("input[name='input_assigncheckbox']:checked").length > 0)
		{


			swal({
				title: "<i class='icon-unlocked2 iconverify'></i><br><h5 class='pswverify'>Verify Password !</h5>",     
				html: '<div class="form-group">' +
				'<input name="VerifyPassword" id="VerifyPassword" type="password" class="form-control" placeholder="Enter Password" required/>' +
				'</div>',
				showCancelButton: true,
				confirmButtonClass: 'btn btn-success',
				cancelButtonClass: 'btn btn-danger',
				buttonsStyling: false,
				showLoaderOnConfirm: true,
				preConfirm: function() {
					return new Promise(function(resolve, reject) {
						var Password = $('#VerifyPassword').val();

						$.ajax({
							url: '<?php echo base_url(); ?>Ordersummary/verify_password',
							type: "POST",
							data: {Password:Password}, 
							dataType:'json',
							cache: false,
							success: function(data)
							{
								console.log(data);
								if(data.validation_error == 1)
								{
									fn_UnassignOrder();
								}
								else
								{
									reject('Invalid Password !!!');
								}
							},
							error:function(jqXHR, textStatus, errorThrown)
							{
								console.log(jqXHR.responseText);
							}
						});
					});

				}
			}).then(function () {
				console.log('modal is closed');
			});

		}
		else
		{

			$.notify(
			{
				icon:"icon-bell-check",
				message:'Please Select Orders'
			},
			{
				type:'danger',
				delay:1000 
			});
		}
	});
	/* --- ORDER UNASSIGN SCRIPT COMPLETED */



	}); //Document Load Complete

	var fn_orderassigntable_destroy = function () {
		$('#orderassigntable').DataTable().destroy();
	}
	var fn_orderassigntable_init = function (SelectedProject) {
		if (typeof this.OrderUID === 'undefined') {
			var OrderUID = '';
		} else {
			var OrderUID = this.OrderUID;
		}

		assignment_table = $('#orderassigntable').DataTable({
			processing: true, //Feature control the processing indicator.
			serverSide: true, //Feature control DataTables' server-side processing mode.
			scrollCollapse: true,
			paging: true,
			autoWidth: false,
			ordering: true,
			columnDefs: [{
				orderable: false,
				targets: "no-sort"
			}],
			responsive: false,
			lengthMenu: [
				[5, 10, 25, -1],
				[5, 10, 25, "All"]
			],

			iDisplayLength: 25,

			language: {
				sLengthMenu: "Show _MENU_ Orders",
				emptyTable: "No Orders Found",
				info: "Showing _START_ to _END_ of _TOTAL_ Orders",
				infoEmpty: "Showing 0 to 0 of 0 Orders",
				infoFiltered: "(filtered from _MAX_ total Orders)",
				zeroRecords: "No matching Orders found",
				processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
			},

			ajax: {
				url: "<?php echo base_url('Orderassignment/assignment_ajax_list')?>",
				type: "POST",
				data: {
					"ProjectUID": SelectedProject,
					"OrderUID": OrderUID,
				},
				dataSrc: function (json) {
					$(".orderassigntable-error").html("");
					$('#total_orders').html(json.recordsTotal);
					return json.data;
					callselect2();
				},
				error: function () {
					// error handling
					$(".orderassigntable-error").html("");
					$("#orderassigntable").append(
						'<tbody class="orderassigntable-error"><tr><td colspan="10" class="text-center">No Orders found</td></tr></tbody>'
					);
					$("#orderassigntable_processing").css("display", "none");

				}
			},
		});
	}

	
	var fn_UnassignOrder = function()
	{
			var checkedorders = $("input[name='input_assigncheckbox']:checked");
			var OrderUIDs = [];
			$(checkedorders).each(function (key, value) {
				OrderUIDs.push($(value).attr('data-OrderUID'));
			});

			var Stacking = $('#Stacking').val();
			var Review = $('#Review').val();

			console.log(OrderUIDs);

			if ( OrderUIDs.length == 0 ) {
				$.notify({
					icon: "icon-bell-check",
					message: "No Orders Choosen"
				}, {
					type: 'danger',
					delay: 1000
				});
				return;
			}


			if ( OrderUIDs.length != 0 ) {

				button = $('.unassign_order');
				button_val = $('.unassign_order').val();
				button_text = $('.unassign_order').html();

				$.ajax({
					type: "POST",
					url: "Orderassignment/assignorder",
					data: {
						'OrderUID': OrderUIDs,
						'Stacking': Stacking,
						'Review': Review
					},
					dataType: "json",
					
					beforeSend: function () {
						$('.spinnerclass').addClass("be-loading-active");
						button.attr("disabled", true);
						button.html('Loading ...');
					},

					success: function (response) {
						if (response.validation_error == 0) {

							$.notify({
								icon: "icon-bell-check",
								message: response.message
							}, {
								type: 'success',
								delay: 1000
							});

							triggerpage(window.location.href);
						} else if (response.validation_error == 1) {

							$.notify({
								icon: "icon-bell-check",
								message: response.message
							}, {
								type: 'success',
								delay: 1000
							});


						}
						button.html('Preview');
						button.removeAttr("disabled");

					}
				});
			} else {
				$.notify({
					icon: "icon-bell-check",
					message: "Order/Workflow Not Choosen"
				}, {
					type: 'danger',
					delay: 1000
				});
				return;

			}
	}

</script>
