<style type="text/css">


	.nowrap {
		white-space: nowrap
	}

	.table-format>thead>tr>th {
		font-size: 12px;
	}

	.modal-header {
		padding:9px 15px;
		color:white;
		font-size: 20px;
		font-weight: bold;
		border-bottom:1px solid #eee;
		background-color: #0480be;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}

	.mdb-select {
		padding: 0;
	}


	.fs-dropdown
	{
		z-index: 50000 !important;
		position: fixed !important;
		width: 30% !important;

	}

	.mr-10{
		margin-right: 10px;
	}


</style>

<div class="card mt-20 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon"> <?php echo $dynamic_header;?>
	</div>
</div>

<div class="card-body">
	<div class="row">
		<div class="col-md-6"></div>
		<div class="col-md-6 text-right">

			<a href="javascript:;" class="btn btn-fill btn-success btn-wd ajaxload addcol"><i class="icon-plus22 pr-10"></i> Add Column</a>
			<a href="<?php echo base_url('DynamicColumn'); ?>" class="btn btn-fill btn-success btn-wd mr-10 ajaxload"><i class="icon-arrow-left8 pr-10"></i>Back</a>

		</div>
	</div>
	<div class="col-md-12">
		<div class="material-datatables">
			<table id="dynamic_column_table" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column dynamic_column_table">
				<thead>
					<tr>
						<th class="text-left">Column</th>
						<th class="text-left">Display Queues</th>
						<th class="text-left">Checklist</th>
						<th class="text-left">Sorting</th>
						<th class="text-left">Sorting Column</th>
						<th class="text-left">Created By</th>
						<th class="text-left">Created On</th>
						<th class="text-left">Action</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal Add Dynamic Columns-->

<!-- End Modal Add Dynamic Column-->




</div>


<!-- Modal Dynamic Columns Edit-->
<div class="modal fade" id="editDynamicColumnModal" tabindex="-1" role="dialog" aria-labelledby="editDynamicColumnModal" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 900px;">
		<div class="modal-content">
		</div>
	</div>
</div>

<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/jquery.nestable.js"></script>

<script type="text/javascript">

	var WorkflowModuleUID = '<?php echo $this->uri->segment(3); ?>';

	//create_fselect();

	function create_fselect()
	{
		$(".mdb-select").each(function(){
			var placeholder = $(this).attr('placeholder');
			$(this).fSelect({
				placeholder: placeholder,
				numDisplayed: 2,
				overflowText: '{n} selected',
				//selected = [].concat(this.$select.val(1,2,3)), // force an array
				//settings.multiple = this.$select.is('[multiple]'),         
				showSearch: true
			}); 
		});   
	}

	function destroy_fselect()
	{
		$(".mdb-select").fSelect('destroy');
	}

	$(document).ready(function() {

		$(".select2picker").select2({
			theme: "bootstrap",
		});

		$('#editDynamicColumnModal').on('shown.bs.modal', function() {

			$("#editDynamicColumnModal .select2picker").select2({
				theme: "bootstrap",
			});		
		});




		$(document).on('click', '.addcol', function() {
			var button = $(this);
			var button_text = button.html();

			$.ajax({
				type: "POST",
				url: "<?php echo base_url('DynamicColumn/fetch_addworkflowqueuecolumn'); ?>",
				data:{'WorkflowModuleUID':WorkflowModuleUID},
				dataType: 'json',
				beforeSend: function() {
					button.prop("disabled", true);
					button.html('<i class="fa fa-spin fa-spinner"></i> Loading...');
				},
				success: function(response) {
					// Call Modal Edit
					$('#editDynamicColumnModal').modal('show');
					$('#editDynamicColumnModal .modal-content').html(response.data);


					button.html(button_text);
					button.prop('disabled', false);
				},
				error: function (jqXHR, textstatus, errorThrown) {
					$('#editDynamicColumnModal .modal-content').html('');
					$('#editDynamicColumnModal').modal('hide');

					$.notify({
						icon: "icon-bell-check",
						message: 'Failed'
					}, {
						type: "danger",
						delay: 1000
					});
					button.html(button_text);
					button.prop('disabled', false);
				}
			});
		});

		$(document).on('click', '.editcol', function() {
			var button = $(this);
			var button_text = button.html();

			var QueueColumnUID = $(this).data('id');

			$.ajax({
				type: "POST",
				url: "<?php echo base_url('DynamicColumn/fetch_editworkflowqueuecolumn'); ?>",
				data:{'QueueColumnUID':QueueColumnUID,'WorkflowModuleUID':WorkflowModuleUID},
				dataType: 'json',
				beforeSend: function() {
					button.prop("disabled", true);
					button.html('<i class="fa fa-spin fa-spinner"></i>');
				},
				success: function(response) {
					// Call Modal Edit
					$('#editDynamicColumnModal').modal('show');
					$('#editDynamicColumnModal .modal-content').html(response.data);


					button.html(button_text);
					button.prop('disabled', false);
				},
				error: function (jqXHR, textstatus, errorThrown) {
					$('#editDynamicColumnModal .modal-content').html('');
					$('#editDynamicColumnModal').modal('hide');

					$.notify({
						icon: "icon-bell-check",
						message: 'Failed'
					}, {
						type: "danger",
						delay: 1000
					});
					button.html(button_text);
					button.prop('disabled', false);
				}
			});
		});


		$('#addDynamicColumnModal').on('hidden.bs.modal', function () { 


			$('#dynamiccolumn-form')[0].reset();

		});




		$('#editDynamicColumnModal').on('hidden.bs.modal', function () { 

			$('#dynamiccolumn-form')[0].reset();

		});




		//$("#addDynamicColumnModal").css("width", "90%");
		//addDynamicColumnModal
		$('body').on('hidden.editDynamicColumnModal', '.modal', function () {
			$(this).removeData('editDynamicColumnModal');
		}); 


		$(document).on('click', '.updatecolumn', function(){

			var formdata = $('#dynamiccolumn-form').serializeArray();

			button = $(this);
			button_text = $(this).html();
			var IsCheck = $('#IsChecklist').prop('checked') ? 1 : 0;
			var IsNoSort  = $('#NoSort').prop('checked') ? 1 : 0;

			formdata.push({ 'name':'IsCheck', 'value':IsCheck });
			formdata.push({ 'name':'IsNoSort', 'value':IsNoSort });
			formdata.push({ 'name':'WorkflowUID', 'value':WorkflowModuleUID });


			$.ajax({
				type: "POST",
				url: "<?php echo base_url('DynamicColumn/UpdateDynamicColumn'); ?>",
				data:formdata,
				dataType: 'json',
				beforeSend: function() {
					button.prop("disabled", true);
					button.html('<i class="fa fa-spin fa-spinner"></i>');
				},
				success: function(response) {
					if (response.Status == 2) {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "success",
							delay: 1000,
							onClose: redirecturl(window.location.href),

						});


					} else {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "danger",
							delay: 1000
						});

						button.html(button_text);
						button.prop('disabled', false);

						$.each(response, function(k, v) {
							console.log(k);
							$('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');
						});
					}
				},
				error: function (jqXHR, textstatus, errorThrown) {

					button.html(button_text);
					button.prop('disabled', false);
				}
			});



		});

		$(document).on('click', '.addcolumn', function(){

			var formdata = $('#dynamiccolumn-form').serializeArray();

			button = $(this);
			button_text = $(this).html();
			var IsCheck = $('#IsChecklist').prop('checked') ? 1 : 0;
			var IsNoSort  = $('#NoSort').prop('checked') ? 1 : 0;

			formdata.push({ 'name':'IsCheck', 'value':IsCheck });
			formdata.push({ 'name':'IsNoSort', 'value':IsNoSort });
			formdata.push({ 'name':'WorkflowUID', 'value':WorkflowModuleUID });


			$.ajax({
				type: "POST",
				url: "<?php echo base_url('DynamicColumn/SaveDynamicColumn'); ?>",
				data:formdata,
				dataType: 'json',
				beforeSend: function() {
					button.prop("disabled", true);
					button.html('<i class="fa fa-spin fa-spinner"></i> Loading...');
				},
				success: function(response) {
					if (response.Status == 2) {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "success",
							delay: 1000,
							onClose: redirecturl(window.location.href),

						});


					} else {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "danger",
							delay: 1000
						});

						button.html(button_text);
						button.prop('disabled', false);

						$.each(response, function(k, v) {
							$('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');
						});
					}
				},
				error: function (jqXHR, textstatus, errorThrown) {

					button.html(button_text);
					button.prop('disabled', false);
				}
			});



		});


		/* 
		* Drag And Drop Position on Table row
		*author sathis kannan(sathish.kannan@avanzegroup.com)
		*since Date:28-Jul-2020
		*/

		sortRequest = null;
		$(".dynamic_column_table tbody").sortable({
			axis: "y",
			cursor: "grabbing",
			handle: ".move-handle-icon",
			opacity: 1,
			stop: function(event, ui) {
				var current = ui.item.attr('id');

				var wrkprz = ui.item.find('td:nth-child(1)').text();
				var sortData = new Array();

				$('.dynamic_column_table tbody tr').each(function() {
					sortData.push({

						ID: $(this).find('.icon_action').attr("data-position")
					});
				});

				if (sortRequest != null) {
					sortRequest.abort();
					sortRequest = null;
				}

				sortRequest = $.ajax({
					type: 'POST',
					dataType: 'JSON',
					global: false,
					url: '<?php echo base_url(); ?>DynamicColumn/DynamicColumnPosition',
					data: {
						sortData,
						current: current,
						wPzt: current,
					},
					success: function(data) {
						console.log(data);
						$.notify({
							icon: "icon-bell-check",
							message: data.msg
						}, {
							type: data.type,
							delay: 1000
						});
						setTimeout(function() {
							location.reload();
						}, 2000);
					}
				});

			}
		});


		/* 
		* Delete the Selected Queue Column on Table 
		*author sathis kannan(sathish.kannan@avanzegroup.com)
		*since Date:30-Jul-2020
		*/

		$(document).off('click', '.delete').on('click', '.delete', function() {

			var DeleteID = $(this).attr("data-delete");



			/*SWEET ALERT CONFIRMATION*/
			swal({
				title: "<i class='icon-warning iconwarning'></i>",     
				html: '<p>Are you sure want to delete this Column?</p>',
				icon: 'success',
				showCancelButton: true,
				confirmButtonClass: 'btn btn-success',
				cancelButtonClass: 'btn btn-danger',
				buttonsStyling: false,
				closeOnClickOutside: false,
				allowOutsideClick: false,
				confirmButtonText: 'Yes, delete it!'
			}).then(function(confirm) {


				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>DynamicColumn/DeleteDynamicColumn',
					data: {
						'DeleteID': DeleteID
					},
					dataType: 'json',
					success: function (data) {

						$.notify(
						{
							icon:"icon-bell-check",
							message:data.msg
						},
						{
							type:data.color,
							delay:1000 
						});
						location.reload();
					},
					error: function (jqXHR, textstatus, errorThrown) {

					}
				});

			},
			function(dismiss) { 
				console.log(dismiss)   
				if(dismiss && dismiss != 'cancel') {
					location.reload();
					
				}          
			});


		});



		function fn_WorkflowColumndatatable(formdata)
		{
			columndatatable = $('#dynamic_column_table').DataTable( {
				scrollX:        true,
				scrollCollapse: true,
				fixedHeader: false,
				scrollY: '50vh',
				paging:  false,
				searchDelay:1500,
				//"bInfo" : false,
				"bDestroy": true,
				"autoWidth": true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": false, //Feature control DataTables' server-side processing mode.
				"order": [], //Initial no order.
				"pageLength": 10, // Set Page Length
				"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
				/*fixedColumns: {
					leftColumns: 1,
					rightColumns: 1
				},*/
				dom: 'Bfrtip',
				"buttons": [
				{
					extend: 'excelHtml5',
					title: 'Columns',
					footer: true,
				},
				],

				language: {
					processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

				},
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?php echo base_url('DynamicColumn/GetDocument') ?>",
					"type": "POST",
					"data": {
						'formData': formdata,
						'workflow': WorkflowModuleUID,
					}
				},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ],
			});

			return columndatatable;
		}
		/* Call data table */
		var formData = ({
			'WorkflowModuleUID': WorkflowModuleUID

		});
		fn_WorkflowColumndatatable(formData);

		$(document).on("click", ".IsChecklist", function(e) {

			if ($('#IsChecklist').is(":checked")) {
				$('.DocumentTypeName').show();
			}else{
				$('.DocumentTypeName').hide();
			}
		});

		$(document).on("click", ".NoSort", function(e) {
			if ($('#NoSort').is(":checked")) {
				$('.SortColumnName-div').show();
			}else {
				$('.SortColumnName-div').hide();
			}
		});  


		$(document).on("change", ".SortColumnName", function(e) {
			if ($('#SortColumnName').val() == 'Custom') {
				$('.SortCustomColumnName-div').show();
			}else {
				$('.SortCustomColumnName-div').hide();
			}
		}); 

		$('body').click(function(evt){    
		    $('#ColumnName').select2('close');
	    	$('#PermissionQueueUIDS').select2('close');
	    	$('#SortColumnName').select2('close');
	    	$('#DocumentTypeUID').select2('close');
		}); 

	});

</script>
