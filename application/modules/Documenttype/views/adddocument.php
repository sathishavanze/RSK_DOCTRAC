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
		<div class="card-icon">Checklist
		</div>
	</div>
	<div class="card-body">

		<form action="#" name="user_form" id="user_form">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">Checklist Name</label>
						<textarea class="form-control" id="DocumentTypeName" name="DocumentTypeName"><?php echo $DocumentDetails->DocumentTypeName; ?></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">Heading</label>
						<textarea class="form-control" id="HeadingName" name="HeadingName"><?php echo $DocumentDetails->HeadingName; ?></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="roleuid" class="bmd-label-floating">Category<span class="mandatory"></span></label>
						<select class="select2picker form-control" id="CategoryUID" name="CategoryUID">
							<option value=""></option>
							<?php foreach ($Roles as $key => $value) { ?>
								<option value="<?php echo $value->CategoryUID; ?>"><?php echo $value->CategoryName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">Naming Conversion</label>
						<input type="text" class="form-control" id="NamingConventions" name="NamingConventions" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">Screen code</label>
						<input type="text" class="form-control" id="ScreenCode" name="ScreenCode" />
					</div>
				</div>	


			</div>

			<div class="row">

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="roleuid" class="bmd-label-floating">Loan Type</label>
						<select class="select2picker form-control" id="Groups" name="Groups">
							<option value="empty">Select Loan Type</option>
							<?php foreach ($GetLoanTypeDetails as $key => $LoanType) { ?>
								<option value="<?php echo $LoanType->LoanTypeName; ?>"><?php echo $LoanType->LoanTypeName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="CustomerUID" class="bmd-label-floating">Client<span class="mandatory"></span></label>
						<select class="select2picker form-control" id="CustomerUID" name="CustomerUID">
							<option value=""></option>
							<?php foreach ($Customer as $key => $value) { ?>
								<?php if ($this->parameters['DefaultClientUID'] == $value->CustomerUID) { ?>
									<option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="SequenceNo" class="bmd-label-floating">Sequence Number</label>
						<input type="text" class="form-control" id="SequenceNo" name="SequenceNo" />
					</div>
				</div>
			</div>

			<div class="row">


				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="CalculateDays" class="bmd-label-floating">Calculate Days</label>
						<input type="number" class="form-control" id="CalculateDays" name="CalculateDays" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">

						<select name="StateCode" class="select2picker form-control" id="State">
							<option value="">Select State... </option>
							<?php
							$State = $this->Common_Model->GetStateDetails();
							foreach ($State as $key => $value) {
								echo '<option value="' . $value->StateCode . '">' . $value->StateCode . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<div class="col-md-4 StateCalculateDays" style="display: none;">
					<div class="form-group bmd-form-group ">
						<label for="StateCalculateDays" class="bmd-label-floating">State Calculate Days</label>
						<input type="number" class="form-control" id="StateCalculateDays" name="StateCalculateDays" />
					</div>
				</div>
			</div>


			<!-- <div class="row">
				<div class="col-md-4">
					<div class="">
						<label class="bmd-label-floating" for="tomails"> To Mail Ids </label>
						<input type="text" id="tomails" name="ToMails" class="tomails form-control" placeholder="example1@gmail.com,example2@gmail.com">


					</div>
				</div>

				<div class="col-md-4">
					<div class="">
						<label class="bmd-label-floating" for="email_template"> Email Templete</label>
						<select name="EmailTemplate" class="select2picker form-control" id="email_template">
							<option value=""> Select Email Template... </option>
							<?php
							foreach ($EmailTemplate as $key => $templt) {
								echo '<option value="' . $templt->EmailTemplateUID . '">' . $templt->EmailTemplateName . '</option>';
							}
							?>
						</select>

						<span class="view_template hide" title="View Template" style="float: right !important; width: 16px;"><a href="" target="_blank"><i class="fa fa-eye"></i></a></span> </div>
					</div>

					<div class="col-md-4">
						<div class="">
							<label class="bmd-label-floating" for="field_type"> Field Type</label>
							<select name="FieldType" class="select2picker form-control" id="field_type">
								<option value=""> Select field type... </option>
								<option value="text">Text field</option>
								<option value="checkbox">Checkbox</option>
								<option value="select">Combo box</option>
								<option value="radio">Radio Button</option>
							</select>

						</div>
					</div>
				</div> -->
				<div class="row hide" id="dropdown_addon">

					<div class="col-md-4">
						<div class="">
							<label class="bmd-label-floating"> Select table name</label>
							<?php echo $DbTables; ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="">
							<label class="bmd-label-floating"> Select table key</label>
							<select name="TableKey" class="select2picker form-control" id="table_key">
								<option value=""> Select Table Key... </option>
							</select>
						</div>
					</div>

				</div>

				<div class="row">
					<div class="col-md-4">
						<label class="bmd-label-floating" for="ParentDocumentTypeUID"> Child Checklist</label>
						<div class="icon_minus_td add_icon">
							<select name="childchecklist[0]" class="select2picker form-control" id="ParentDocumentTypeUID">
								<option value=""> Select Child Checklist </option>
								<?php
								foreach ($parentchecklists as $parentchecklist) { ?>
									<option value="<?php echo $parentchecklist->DocumentTypeUID; ?>" <?php if ($DocumentType->ParentDocumentTypeUID == $parentchecklist->DocumentTypeUID) {
										echo "selected";
									} ?>> <?php echo $parentchecklist->DocumentTypeName; ?> </option>
								<?php }
								?>
							</select>
							<p class="custom_add_icon pull-right addchecklist" data-child="0" aria-hidden="true">Add New Checklist</p>
						</div>
						<div class="appendRow"></div>

					</div>
					<div class="col-md-4 hide" id="ChildLableDiv">
						<label class="bmd-label-floating" for="ChildLable"> Child label </label>
						<select name="ChildLabel" class="select2picker form-control" id="ChildLable">

						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" style="margin-top: 50px;">
						<h5 class="welcome_head_h5" style="">Checklist Alert</h5>
					</div>
				</div>
				<?php foreach ($getAlertMessage as $keyAlert => $valueAlert) { ?>
					<div class="row removecomment">
						<div class="col-md-4">
							<div class="form-group bmd-form-group">
								<label for="comments" class="bmd-label-floating">Comments</label>
								<textarea type="text" class="form-control updateAlert" data-updateAlert="<?php echo $valueAlert->AlertID . '~ChecklistComment'; ?>" rows="1" value="<?php echo $valueAlert->ChecklistComment; ?>"><?php echo $valueAlert->ChecklistComment; ?></textarea>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group bmd-form-group">
								<label for="startDate" class="bmd-label-floating">Start Date</label>
								<input type="text" title="Document Date" class="form-control checklistdatepicker startDate updateAlert" data-updateAlert="<?php echo $valueAlert->AlertID . '~AlertStartDate'; ?>" value="<?php echo date("m/d/Y", strtotime($valueAlert->AlertStartDate)); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="endDate" class="bmd-label-floating">End Date</label>
								<input type="text" class="form-control checklistdatepicker updateAlert" id="endDate" data-updateAlert="<?php echo $valueAlert->AlertID . '~AlertEndDate'; ?>" value="<?php echo date("m/d/Y", strtotime($valueAlert->AlertEndDate)); ?>" />
							</div>
						</div>
						<div class="col-md-1">
							<a style="width:8%;float:right;"><i class="fa fa-minus-circle removeComments pull-right" aria-hidden="true" data-remove="<?php echo $valueAlert->AlertID; ?>" style="font-size: 20px;margin-top: 10px;"></i></a>
						</div>
					</div>
				<?php } ?>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group bmd-form-group">
							<label for="comments" class="bmd-label-floating">Comments</label>
							<textarea type="text" class="form-control" id="Commands" name="Alert[0][Comments]" rows="1"></textarea>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group bmd-form-group">
							<label for="startDate" class="bmd-label-floating">Start Date</label>
							<input type="text" title="Document Date" name="Alert[0][startDate]" class="form-control checklistdatepicker startDate">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="endDate" class="bmd-label-floating">End Date</label>
							<input type="text" class="form-control checklistdatepicker" id="endDate" name="Alert[0][endDate]" />
						</div>
					</div>
					<div class="col-md-1">
						<a style="width:8%;float:right;"><i class="fa fa-plus-circle addComments pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a>
					</div>
				</div>
				<div class="appendComments"></div>

				<div class="ml-auto text-right">
					<a href="<?php echo base_url('Documenttype'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
					<button type="submit" class="btn btn-fill btn-save btn-wd adduser" name="adduser"><i class="icon-floppy-disk pr-10"></i>Save Document</button>
				</div>
			</form>
		</div>
	</div>

	<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			$('#email_template').on('change', function() {
				var email = $(this).val();
				if (email != '') {
					var link = '<?php echo base_url(); ?>' + 'Emailtemplate/EditEmailtemplate/' + email;
					$('.view_template')
					.removeClass('hide')
					.find('a').attr('href', link);
				} else {
					$('.view_template').addClass('hide');
				}
			});

			$('#field_type').on('change', function() {
				if ($(this).val() == 'select') {
					$('#dropdown_addon').removeClass('hide');
				} else {
					$('#dropdown_addon').addClass('hide');
					$("#table_key").val(null).trigger("change");
					$("#table_name").val(null).trigger("change");
				}
			});
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

			$('#ParentDocumentTypeUID').on('change', function(e) {
				var parentId = $(this).val();
				$("#ChildLable").val(null).trigger("change");
				button = $('.adduser');
				button_val = $('.adduser').val();
				button_text = $('.adduser').html();
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

							$('#ChildLableDiv').removeClass('hide');
							$('#ChildLable').html(data.labels);
						} else {
							$('#ChildLableDiv').addClass('hide');
							$("#field_type").removeAttr('disabled');
						}
						button.html(button_text);
						button.val(button_val);
						button.prop('disabled', false);
					}
				});
			});

			$(document).off('click', '.adduser').on('click', '.adduser', function(e) {
				var formdata = $('#user_form').serialize();
				button = $(this);
				button_val = $(this).val();
				button_text = $(this).html();
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('Documenttype/SaveDocument'); ?>",
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
								message: "Added Successsfully"
							}, {
								type: "success",
								delay: 1000
							});
							setTimeout(function() {

								triggerpage('<?php echo base_url(); ?>Documenttype');

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
				$.post('Documenttype/appendChildChecklist', {
					'count': countChild
				}, function(result) {
					$('.appendRow').append(result);
					$('.appendRow').find('select.select2picker').select2();
				});
			});

			//remove child checklist
			$(document).off("click", ".removechecklist").on("click", ".removechecklist", function(e) {
				$(this).closest(".icon_minus_td").remove();
			});

			/* append Comments and date */
			var addComment = 0;
			$(document).on('click', '.addComments', function() {
				addComment++;
				$.post('Documenttype/appendCommentsDate', {
					'count': addComment
				}, function(result) {
					$('.appendComments').append(result);
					//$('.appendComments').find('input.checklistdatepicker').select2();
					checklistdatepicker_init();
				});
			});
			/* remove Comments and Date */
			$(document).on('click', '.removeComments', function() {
				$(this).closest('.removecomment').remove();
			});


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

			$('#State').change(function() {
				var data = $(this).val();
				if (data != '') {
					$('.StateCalculateDays').show();
				} else {
					$('.StateCalculateDays').hide();
					$('#StateCalculateDays').val('');
				}
			});
		});
	</script>