<style type="text/css">
	.pd-btm-0 {
		padding-bottom: 0px;
	}

	.margin-minus8 {
		margin: -8px;
	}

	.mt--15 {
		margin-top: -15px;
	}

	.bulk-notes {
		list-style-type: none
	}

	.bulk-notes li:before {
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap {
		white-space: nowrap
	}

	.table-format>thead>tr>th {
		font-size: 12px;
	}

	.custom_add_icon {
		font-weight: 600;
		text-decoration: underline;
		font-size: 11px;
		text-align: right;
		margin: 0;
		display: block;
		cursor: pointer;
		color: #1A73E8;
	}

	.add_icon .bmd-form-group {
		float: left;
		width: 92%;
	}
</style>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Add Dynamic Column
		</div>
	</div>
	<div class="card-body">

		<form action="#" name="user_form" id="user_form">
			<div class="row">
				<div class="col-md-4">
					
					<div class="form-group bmd-form-group">
						<label for="HeaderName" class="bmd-label-floating">Header Name</label>
						<input type="text" class="form-control" id="HeaderName" name="HeaderName" value="" />
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="ColumnName" class="bmd-label-floating">Column Name</label>
						<!-- <input type="text" class="form-control" id="ColumnName" name="ColumnName" value="" /> -->
						<select class="select2picker form-control" id="ColumnName" name="ColumnName">
							<option value=""></option>
							<?php foreach ($QueueColumn as $key => $value) { ?>
								<option value="<?php echo $value; ?>"><?php echo $key; ?></option>
							<?php } ?>
						</select>		

					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="WorkflowUID" class="bmd-label-floating">Workflow Name</label>
						<select class="select2picker form-control" id="WorkflowUID" name="WorkflowUID">
							<option value=""></option>
							<?php foreach ($ModuleDetails as $key => $value) { ?>
								<option value="<?php echo $value->WorkflowModuleUID; ?>"><?php echo $value->WorkflowModuleName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<!-- 	<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="DocumentTypeUID" class="bmd-label-floating">DocumentTypeUID</label>
						<input type="hidden" class="form-control" id="DocumentTypeUID" name="DocumentTypeName" value="" />
					</div>
				</div> -->
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="Section" class="bmd-label-floating">Section</label>
						<!-- <input type="text" class="form-control" id="Section" name="Section" value="" /> -->
						<select class="select2picker form-control" id="Section" name="Section">
							<option value=""></option>
							<?php foreach ($SectionDetails as $key => $value) { ?>
								<option value="<?php echo $value->FieldName; ?>"><?php echo $value->FieldName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4" style="display: none">
					<div class="form-group bmd-form-group">
						<label for="CustomerUID" class="bmd-label-floating">CustomerUID</label>
						<input type="text" class="form-control" id="CustomerUID" name="CustomerUID" value="<?php echo $CustomerUID; ?>" />
					</div>
				</div>
<!-- 
				<div class="col-md-4">
					<div class="form-group dynamicCheckedList">
						<label for="IsChecklist" class="bmd-label-floating" >IsChecklist</label>
						<input class="form-check-input" type="checkbox" name="IsChecklist" id="IsChecklist" data-incre="IsChecklist"  style="position:absolute;left: 5px;" value=""> 
					</div>
				</div> -->
				<div class="col-md-4"> 
					<div class="form-check dynamicCheckedList" style="position: absolute;top:20px;">
						<label class="form-check-label Dashboardlable" for="IsChecklist" style="color: teal">
							<input class="form-check-input IsChecklist" id="IsChecklist" type="checkbox" value="" name="IsChecklist" > IsChecklist<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>
				</div>
				<div class="col-md-4 DocumentTypeName">
					<div class="form-group bmd-form-group">
						<label for="DocumentTypeUID" class="bmd-label-floating">DocumentTypeName</label>
						<select class="select2picker form-control" id="DocumentTypeUID" name="DocumentTypeUID">
							<option value=""></option>
							<?php foreach ($DocumentDetails as $key => $value) { ?>
								<option value="<?php echo $value->DocumentTypeUID; ?>"><?php echo $value->DocumentTypeName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

			</div>

			<div class="row">
				
				<!-- <div class="col-md-4">
					<div class="form-check dynamicCheckedList">
						<label for="NoSort" class="form-check-label" >NoSort</label>
						<input class="form-check-input" type="checkbox" name="NoSort" id="NoSort" data-incre="NoSort"  style="position:absolute;left: 5px;top:5px;" value=""><span class="form-check-sign">
                            <span class="check"></span>
                          </span>&nbsp;&nbsp; 
					</div>
				</div> -->

				<div class="col-md-4"> 
					<div class="form-check dynamicCheckedList" style="position: absolute;top:20px;">
						<label class="form-check-label Dashboardlable" for="NoSort" style="color: teal">
							<input class="form-check-input NoSort" id="NoSort" type="checkbox" value="" name="NoSort" > NoSort<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>
				</div>
				<div class="col-md-4 SortColumnName">
					<div class="form-group bmd-form-group">
						<label for="SortColumnName" class="bmd-label-floating">Sort Column</label>
						<!-- <input type="text" class="form-control" id="SortColumnName" name="SortColumnName" value="" /> -->
						<select class="select2picker form-control" id="SortColumnName" name="SortColumnName">
							<option value=""></option>
							<?php foreach ($QueueColumn as $key => $value) { ?>
								<option value="<?php echo $value; ?>"><?php echo $key; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="DocumentTypeUID" class="bmd-label-floating">Position</label>
						<input type="text" class="form-control" id="Position" name="Position" value="" />
					</div>
				</div>
			</div>

			<div class="ml-auto text-right">
				<a href="<?php echo base_url('DynamicColumn'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
				<button type="submit" class="btn btn-fill btn-save btn-wd adduser" name="adduser"><i class="icon-floppy-disk pr-10"></i>Save Dynamic Column</button>
			</div>
		</form>
		
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {



		$('#table_name').on('change', function() {

			$("#table_key").val(null).trigger("change");
			var table = $(this).val();
			if ($("#field_type").val() == 'select') {
				button = $('.adduser');
				button_val = $('.adduser').val();
				button_text = $('.adduser').html();
				$.ajax({
					url: 'Documenttype/getTableKey',
					type: 'POST',
					data: {
						table: table
					},
					beforeSend: function() {
						button.prop("disabled", true);
						button.html('Loading ...');
						button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
					},
					success: function(data) {
						if (data) {
							$('#table_key').html(data);
						}
						button.html(button_text);
						button.val(button_val);
						button.prop('disabled', false);

					}

				})
			}

		});


		$(document).off('click', '.adduser').on('click', '.adduser', function(e) {
			var formdata = $('#user_form').serializeArray();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();

			var IsCheck = $('#IsChecklist').prop('checked') ? 1 : 0;
			var NoSort  = $('#NoSort').prop('checked') ? 1 : 0;

			formdata.push({ 'name':'IsCheck', 'value':IsCheck });
			formdata.push({ 'name':'NoSort', 'value':NoSort });
			
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('DynamicColumn/SaveDynamicColumn'); ?>",
				data: formdata,
				dataType: 'json',
				beforeSend: function() {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
				},
				success: function(response) {
					if (response.Status == 2) {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "success",
							delay: 1000
						});
						setTimeout(function() {

							triggerpage('<?php echo base_url(); ?>DynamicColumn/Adddynamiccolumn');

						}, 3000);
					} else {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "danger",
							delay: 1000
						});
						$.each(response, function(k, v) {
							console.log(k);
							$('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');

						});

					}
					button.html(button_text);
					button.val(button_val);
					button.prop('disabled', false);

				}
			});

		});

/* 
     * Click Function On IsChecklist to Enable and Disble Div 
     *author sathis kannan(sathish.kannan@avanzegroup.com)
     *since Date:30-Jul-2020
     */

     $(document).off("click", ".IsChecklist").on("click", ".IsChecklist", function(e) {

     	var CheckedValue = $('#IsChecklist').prop('checked') ? 1 : 0;


     	if(CheckedValue == 1){

     		$('.DocumentTypeName').show();

     	}else if(CheckedValue == 0)
     	{
     		$('.DocumentTypeName').hide();

     	}
     }); 

  /* 
     * Click Function On NoSort to Enable and Disble Div 
     *author sathis kannan(sathish.kannan@avanzegroup.com)
     *since Date:30-Jul-2020
     */

     $(document).off("click", ".NoSort").on("click", ".NoSort", function(e) {

     	var SortValue = $('#NoSort').prop('checked') ? 1 : 0;;

     	if(SortValue == 1){

     		$('.SortColumnName').show();

     	}else if(SortValue == 0)
     	{
     		$('.SortColumnName').hide();

     	}
     }); 

 });
</script>