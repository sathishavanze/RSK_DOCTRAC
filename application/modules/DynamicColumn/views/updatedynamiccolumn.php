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

<?php $workflow=$_GET['WorkflowUID']; ?>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Edit Dynamic Column
		</div>
	</div>
	<div class="card-body">

		<form action="#" name="user_form" id="user_form">
			<?php  foreach ($DynamicColumnDetails as $key => $Editinfo) {
						
					 ?>
			<div class="row">
				
				<div class="col-md-4" style="display: none;">
					<div class="form-group bmd-form-group" >
						<label for="QueueColumnUID" class="bmd-label-floating">QueueColumnUID</label>
						<input type="text" class="form-control" id="QueueColumnUID" name="QueueColumnUID" value="<?php echo $Editinfo->QueueColumnUID; ?>"  />
					</div>
				</div>

				<div class="col-md-4">
					
					<div class="form-group bmd-form-group">
						<label for="HeaderName" class="bmd-label-floating">Header Name</label>
						<input type="text" class="form-control" id="HeaderName" name="HeaderName" value="<?php echo $Editinfo->HeaderName; ?>"  />
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="ColumnName" class="bmd-label-floating">Column Name</label>
					<!-- 	<input type="text" class="form-control" id="ColumnName" name="ColumnName" value="<?php echo $Editinfo->ColumnName; ?>" /> -->
						  <select class="select2picker form-control" id="ColumnName" name="ColumnName">
							<option value=""></option>
							<?php  foreach ($QueueColumn as $key => $value) {if(trim($value) == trim($Editinfo->ColumnName)){
								?>
								<option value="<?php echo $value; ?>" selected><?php echo $key; ?></option>
							<?php } else{?>
								<option value="<?php echo $value; ?>" ><?php echo $key; ?></option>
							<?php } } ?>
						</select> 
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="WorkflowUID" class="bmd-label-floating">WorkflowModuleName</label>
						<!-- <input type="text" class="form-control" id="WorkflowUID" name="WorkflowUID" value="<?php echo $Editinfo->WorkflowModuleName; ?>" /> -->
						<select class="select2picker form-control" id="WorkflowUID" name="WorkflowUID">
							<option value=""></option>
							<?php foreach ($ModuleDetails as $key => $value) { if($value->WorkflowModuleName == $Editinfo->WorkflowModuleName){
								?>
								<option value="<?php echo $value->WorkflowModuleUID; ?>" selected><?php echo $value->WorkflowModuleName; ?></option>
							<?php } else{?>
								<option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->WorkflowModuleName; ?></option>
							<?php } } ?>
						</select>
					</div>
				</div>

			<!-- 	<div class="col-md-3" style="display: none;">
					<div class="form-group bmd-form-group">
						<label for="WorkflowUID" class="bmd-label-floating">WorkflowUID</label>
						<input type="hidden" class="form-control" id="WorkflowUID" name="WorkflowUID" value="<?php echo $Editinfo->WorkflowUID; ?>" />
					</div>
				</div>  -->
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="Section" class="bmd-label-floating">Section</label>
						<!-- <input type="text" class="form-control" id="Section" name="Section" value="<?php echo $Editinfo->Section; ?>" /> -->
						 <select class="select2picker form-control" id="Section" name="Section">
							<option value=""></option>
							<?php foreach ($SectionDetails as $key => $value) { if($value->FieldName == $Editinfo->Section){
								?>
								<option value="<?php echo $value->FieldName; ?>" selected><?php echo $value->FieldName; ?></option>
							<?php } else{?>
								<option value="<?php echo $value->FieldName; ?>" ><?php echo $value->FieldName; ?></option>
							<?php } } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4" style="display: none;">
					<div class="form-group bmd-form-group">
						<label for="CustomerUID" class="bmd-label-floating">CustomerUID</label>
						<input type="text" class="form-control" id="CustomerUID" name="CustomerUID" value="<?php echo $Editinfo->CustomerUID; ?>" />
					</div>
				</div>
				<!-- <div class="col-md-4">
					<div class="form-group dynamicCheckedList">
						<label for="IsChecklist" class="bmd-label-floating" >IsChecklist</label>
						<input class="form-check-input" type="checkbox" name="IsChecklist" id="IsChecklist" data-incre="IsChecklist"  style="position:absolute;left: 5px;" value="<?php echo $Editinfo->IsChecklist; ?>"<?php echo $DocumentDetails->IsChecklist == 1 ? 'checked' : ''; ?>> 
						
					</div>
				</div> -->
				<div class="col-md-4"> 
					<div class="form-check dynamicCheckedList" style="position: absolute;top:20px;">
						<label class="form-check-label Dashboardlable" for="IsChecklist" style="color: teal">
							<input class="form-check-input IsChecklist" id="IsChecklist" type="checkbox"  name="IsChecklist" value="<?php echo $Editinfo->IsChecklist; ?>"<?php echo $Editinfo->IsChecklist == 1 ? 'checked' : ''; ?>> IsChecklist<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>
				</div>
					<div class="col-md-4 DocumentTypeName">
					<div class="form-group bmd-form-group">
						<label for="DocumentTypeUID" class="bmd-label-floating">DocumentTypeName</label>
						<!-- <input type="text" class="form-control " id="DocumentTypeName" name="DocumentTypeName" value="<?php echo $Editinfo->DocumentTypeName; ?>" /> -->
						<select class="select2picker form-control" id="DocumentTypeUID" name="DocumentTypeUID">
							<option value=""></option>
							<?php foreach ($DocumentDetails as $key => $value) { if($value->DocumentTypeName ==$Editinfo->DocumentTypeName){
								?>
								<option value="<?php echo $value->DocumentTypeUID; ?>" selected><?php echo $value->DocumentTypeName; ?></option>
							<?php } else{?>
						<option value="<?php echo $value->DocumentTypeUID; ?>" ><?php echo $value->DocumentTypeName; ?></option>
					<?php } } ?>

						</select>
					</div>
				</div>
			</div>

			<div class="row">
				<!-- <div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="NoSort" class="bmd-label-floating" >NoSort</label>
						<input class="form-check-input" type="checkbox" name="NoSort" id="NoSort" data-incre="NoSort"  style="position:absolute;left: 5px;top:5px;" value="<?php echo $Editinfo->NoSort; ?>"<?php echo$Editinfo->NoSort == 1 ? 'checked' : ''; ?>>&nbsp;&nbsp; 
					</div>
				</div> -->
                	<div class="col-md-4"> 
					<div class="form-check dynamicCheckedList" style="position: absolute;top:20px;">
						<label class="form-check-label Dashboardlable" for="NoSort" style="color: teal">
							<input class="form-check-input NoSort" id="NoSort" type="checkbox" name="NoSort" value="<?php echo $Editinfo->NoSort; ?>"<?php echo$Editinfo->NoSort == 1 ? 'checked' : ''; ?> > NoSort<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>
				</div>
                
					<div class="col-md-4 SortColumnName">
					<div class="form-group bmd-form-group">
						<label for="SortColumnName" class="bmd-label-floating">Sort Column</label>
						<!-- <input type="text" class="form-control" id="SortColumnName" name="SortColumnName" value="<?php echo  $Editinfo->SortColumnName; ?>" /> -->
                       <select class="select2picker form-control" id="SortColumnName" name="SortColumnName">
							<option value=""></option>
							<?php foreach ($QueueColumn as $key => $value) { if($value == $Editinfo->SortColumnName){
								?>
								<option value="<?php echo $value; ?>" selected><?php echo $value; ?></option>
							<?php } else{?>
								<option value="<?php echo $value; ?>" ><?php echo $value; ?></option>
							<?php } } ?>
						</select>

					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="DocumentTypeUID" class="bmd-label-floating">Position</label>
						<input type="text" class="form-control" id="Position" name="Position" value="<?php echo $Editinfo->Position; ?>" />
					</div>
				</div>

	
		</div>

	
<?php } ?>
		


					<div class="ml-auto text-right">
					
						<a href="<?php echo base_url('DynamicColumn'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Cancel</a>

						<button type="submit" class="btn btn-fill btn-update btn-wd updateuser" name="updateuser"><i class="icon-floppy-disk pr-10"></i>Update Dynamic Column </button>
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
						button = $('.updateuser');
						button_val = $('.updateuser').val();
						button_text = $('.updateuser').html();
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

						});
					}

				});
				$('#ParentDocumentTypeUID').on('change', function(e) {
					var parentId = $(this).val();
					$("#ChildLable").val(null).trigger("change");
					button = $('.updateuser');
					button_val = $('.updateuser').val();
					button_text = $('.updateuser').html();
					$.ajax({
						url: 'Documenttype/CheckLabel',
						type: 'POST',
						data: {
							parentId: parentId
						},
						dataType: 'json',
						beforeSend: function() {
							button.prop("disabled", true);
							button.html('Loading ...');
							button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
						},
						success: function(data) {
							if (data.Ptname && data.Ptkey) {
								$("#field_type").val(null).trigger("change");
								$("#field_type").attr('disabled', true);

								$('#ChildLabelDiv').removeClass('hide');
								$('#ChildLabel').html(data.labels);
							} else {
								$('#ChildLabelDiv').addClass('hide');
								$("#field_type").removeAttr('disabled');
							}
							button.html(button_text);
							button.val(button_val);
							button.prop('disabled', false);
						}
					});
				});


				$(document).off('click', '.updateuser').on('click', '.updateuser', function(e) {
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
						url: "<?php echo base_url('DynamicColumn/UpdateDynamicColumn'); ?>",
						data:formdata,
						dataType: 'json',
						beforeSend: function() {
							button.prop("disabled", true);
							button.html('Loading ...');
							button.val('<i class="fa fa-spin fa-spinner"></i> update..');

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

									triggerpage('<?php echo base_url(); ?>DynamicColumn');

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

				/* append child checklist */
				var countChild = 0;
				$(document).on('click', '.addchecklist', function() {
					countChild++;
					$.post('DynamicColumn/appendChildChecklist', {
						'count': countChild
					}, function(result) {
						$('.appendRow').append(result);
						$('.appendRow').find('select.select2picker').select2();
					});
				});

		//remove checklist
		$(document).off("click", ".removechecklist").on("click", ".removechecklist", function(e) {
			$(this).closest(".icon_minus_td").remove();
			var checklist = $(this).closest('.child_checklist').data('checklist');
			var split_checklist = checklist.split('~');
			if (checklist) {
				$.ajax({
					type: "post",
					url: base_url + "Ordersummary/removeChecklist",
					data: {
						DocumentTypeUID: split_checklist[0],
						DocumentTypeName: split_checklist[1]
					},
					success: function(data) {
						if (data) {
							$.notify({
								message: "Deleted Successfully",
							}, {
								type: "success",
								delay: 1000,
							});
							console.log(data);
						} else {
							$.notify({
								message: "Failed Delete Checklist ",
							}, {
								type: "danger",
								delay: 1000,
							});
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
				});
			}
		});


		/*$(document).on('click','.Back',function()
   {
      setTimeout(function(){ 

          triggerpage('<?php echo base_url(); ?>Documenttype');

        },50);
   });
   */
   function log() {

   	var loginid = $('#loginid').val();

   	$.ajax({
   		type: "POST",
   		url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
   		data: {
   			'loginid': loginid
   		},
   		dataType: 'json',
   		success: function(response) {

   			if (response.Status == 1) {

   				$('#loginexists').show();
   			} else {
   				$('#loginexists').hide();
   			}


   		},
   		error: function(xhr) {

   			console.log(xhr);
   		}
   	});

   }

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

   var IsChecklist = "<?php echo $Editinfo->IsChecklist; ?>";

   if(IsChecklist == 1){
   	$('.DocumentTypeName').show();
   }
   else{
   	$('.DocumentTypeName').hide();
   }

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

 var IsChecklist = "<?php echo $Editinfo->NoSort; ?>";
   if(IsChecklist == 1){

   	$('.SortColumnName').show();

   }else{

   	$('.SortColumnName').hide();

   }

});
</script>