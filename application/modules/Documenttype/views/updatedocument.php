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
			<input type="hidden" class="form-control" id="username" name="DocumentTypeUID" value="<?php echo $DocumentDetails->DocumentTypeUID; ?>" />
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
						<label for="username" class="bmd-label-floating">Naming Conventions</label>
						<input type="text" class="form-control" id="NamingConventions" name="NamingConventions" value="<?php echo $DocumentDetails->NamingConventions; ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">Screen code</label>
						<input type="text" class="form-control" id="ScreenCode" name="ScreenCode" value="<?php echo $DocumentDetails->ScreenCode; ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="roleuid" class="bmd-label-floating">Category</label>
						<select class="select2picker form-control" id="CategoryUID" name="CategoryUID">
							<option value=""></option>
							<?php foreach ($Category as $key => $value) {
								if ($value->CategoryUID == $DocumentDetails->CategoryUID) { ?>

									<option value="<?php echo $value->CategoryUID; ?>" selected><?php echo $value->CategoryName; ?></option>

								<?php } else { ?>
									<option value="<?php echo $value->CategoryUID; ?>"><?php echo $value->CategoryName; ?></option>
								<?php }
								?>
							<?php } ?>
						</select>
					</div>
				</div>

			</div>
			<div class="row">

				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="roleuid" class="bmd-label-floating">Loan Type</label>
						<select class="select2picker form-control" id="Groups" name="Groups">
							<option value=""></option>
							<?php
							foreach ($GetLoanTypeDetails as $key => $LoanType) { ?>
								<option value="<?php echo $LoanType->LoanTypeName; ?>" <?php if ($DocumentDetails->Groups == $LoanType->LoanTypeName) {
									echo "selected";
								} ?>><?php echo $LoanType->LoanTypeName; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="CustomerUID" class="bmd-label-floating">Client</label>
						<select class="select2picker form-control" id="CustomerUID" name="CustomerUID">
							<option value=""></option>
							<?php foreach ($Customer as $key => $value) { ?>
								<?php if ($DocumentDetails->CustomerUID == $value->CustomerUID) { ?>
									<option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>

								<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="SequenceNo" class="bmd-label-floating">Sequence Number</label>
						<input type="text" class="form-control" id="SequenceNo" name="SequenceNo" value="<?php echo $DocumentDetails->SequenceNo; ?>" />
					</div>
				</div>
			</div>

			<div class="row">


				<div class="col-md-4">
					<div class="form-group bmd-form-group">
						<label for="CalculateDays" class="bmd-label-floating">Calculate Days</label>
						<input type="number" class="form-control" id="CalculateDays" name="CalculateDays" value="<?php echo $DocumentDetails->CalculateDays; ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group bmd-form-group">

						<select name="StateCode" class="select2picker form-control" id="State">
							<option value="">Select State... </option>
							<?php
							$State = $this->Common_Model->GetStateDetails();
							foreach ($State as $key => $value) { ?>
								<?php if ($DocumentDetails->StateCode == $value->StateCode) { ?>
									<option value="<?php echo $value->StateCode; ?>" selected><?php echo $value->StateCode; ?></option>

								<?php } else { ?>

									<option value="<?php echo $value->StateCode; ?>"><?php echo $value->StateCode; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="col-md-4 StateCalculateDays" <?php if ($DocumentDetails->StateCode == '' || $DocumentDetails->StateCode == 0) {
					echo 'style="display: none"';
				} ?>>
				<div class="form-group bmd-form-group ">
					<label for="StateCalculateDays" class="bmd-label-floating">State Calculate Days</label>
					<input type="number" class="form-control" id="StateCalculateDays" name="StateCalculateDays" value="<?php echo $DocumentDetails->StateCalculateDays; ?>" />
				</div>
			</div>
		</div>


		<!-- <div class="row">
			<div class="col-md-4">
				<div class="">
					<label class="bmd-label-floating" for="tomails"> To Mail Ids </label>
					<input type="text" id="tomails" name="ToMails" class="tomails form-control" value="<?php if ($DocumentDetails->ToMails) {
						echo $DocumentDetails->ToMails;
					} ?>">
				</div>
			</div>

			<div class="col-md-4">
				<div class="">
					<label class="bmd-label-floating" for="email_template"> Email Templete</label>
					<select name="EmailTemplate" class="select2picker form-control" id="email_template">
						<option value=""> Select Email Template... </option>
						<?php
						foreach ($EmailTemplate as $key => $templt) { ?>
							<option value="<?php echo $templt->EmailTemplateUID; ?>" <?php if ($DocumentDetails->EmailTemplate == $templt->EmailTemplateUID) {
								echo "selected";
							} ?>> <?php echo $templt->EmailTemplateName; ?> </option>
							<?php }
							?>
						</select>
						<span class="view_template <?php if ($DocumentDetails->EmailTemplate) {
							} else {
								echo 'hide';
								} ?>" title="View Template" style="float: right !important; width: 16px;"><a href="<?php if ($DocumentDetails->EmailTemplate) {
									echo base_url() . 'Emailtemplate/EditEmailtemplate/' . $DocumentDetails->EmailTemplate;
								} ?>" target="_blank"><i class="fa fa-eye"></i></a></span>
							</div>
						</div>

						<div class="col-md-4">
							<div class="">
								<label class="bmd-label-floating" for="field_type"> Field Type</label>
								<select name="FieldType" class="select2picker form-control" id="field_type">
									<option value=""> Select field type... </option>
									<option value="text" <?php if (isset($DocumentDetails->FieldType) && $DocumentDetails->FieldType == 'text') {
										echo 'selected';
									} ?>>Text field</option>
									<option value="checkbox" <?php if (isset($DocumentDetails->FieldType) && $DocumentDetails->FieldType == 'Checkbox') {
										echo 'selected';
									} ?>>Checkbox</option>
									<option value="select" <?php if (isset($DocumentDetails->FieldType) && $DocumentDetails->FieldType == 'select') {
										echo 'selected';
									} ?>>Combo box</option>
									<option value="radio" <?php if (isset($DocumentDetails->FieldType) && $DocumentDetails->FieldType == 'radio') {
										echo 'selected';
									} ?>>Radio Button</option>
								</select>

							</div>
						</div>
					</div>
					<div class="row <?php if ($DocumentDetails->FieldType != 'select') {
						echo 'hide';
					} ?>" id="dropdown_addon">

					<div class="col-md-4">
						<div class="">
							<label class="bmd-label-floating"> Select table name </label>
							<?php echo $DbTables; ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="">
							<label class="bmd-label-floating"> Select Table Value </label>
							<select name="TableKey" class="select2picker form-control" id="table_key">
								<?php echo $DbTableKeys; ?>

							</select>
						</div>
					</div>

				</div> -->
				<?php foreach ($getChildChecklist as $keyChild => $valueChild) { ?>
					<div class="row <?php echo $valueChild->DocumentTypeUID; ?>">
						<div class="col-md-4">
							<label class="bmd-label-floating" for="ParentDocumentTypeUID"> Child Checklist</label>
							<input type="text" class="form-control" value="<?php echo $valueChild->DocumentTypeName; ?>">
						</div>
						<div class="col-md-1" style="padding-top: 30px;">
							<a style="width:8%;float:right;"><i class="fa fa-minus-circle removechildchecklist pull-right" aria-hidden="true" data-removechild="<?php echo $valueChild->DocumentTypeUID; ?>" style="font-size: 20px;margin-top: 10px;"></i></a>
						</div>
					</div>
				<?php } ?>
				<div class="row">
					<div class="col-md-4">
						<label class="bmd-label-floating" for="ParentDocumentTypeUID"> Child Checklist</label>
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
						<div class="appendRow"></div>
					</div>

					<div class="col-md-4 <?php if (!isset($childLabel)) {
						echo 'hide';
					} ?>" id="ChildLabelDiv">
					<label class="bmd-label-floating" for="ChildLable"> Select parent value </label>
					<select name="ChildLabel" class="select2picker form-control" id="ChildLabel">
						<?php
						if (isset($childLabel)) {
							echo $childLabel;
						}
						?>
					</select>
				</div>

			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="togglebutton">
						<label class="label-color"> Active
							<input type="checkbox" id="Active" name="Active" class="Active" <?php if ($DocumentDetails->Active == 1) {
								echo "checked";
							} ?>>
							<span class="toggle"></span>
						</label>
					</div>
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
						<input type="text" class="form-control checklistdatepicker endDate" data-date="" id="endDate" name="Alert[0][endDate]" />
					</div>
				</div>
				<div class="col-md-1">
					<a style="width:8%;float:right;"><i class="fa fa-plus-circle addComments pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a>
				</div>
			</div>
			<div class="appendComments"></div>
			<!-- <div class="col-sm-12 form-group pull-right">
				<p class="text-right">
					<button type="button" class="btn btn-space btn-social btn-color btn-twitter updateuser" value="1">Update Document</button>
				</p>
			</div>
		-->
		<div class="ml-auto text-right">
			<!-- <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
			<a href="<?php echo base_url('Documenttype'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Cancel</a>

			<button type="submit" class="btn btn-fill btn-update btn-wd updateuser" name="updateuser"><i class="icon-floppy-disk pr-10"></i>Update Document </button>
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


		/* remove child checklist */
		$(document).on('click', '.removechildchecklist', function() {
			var removelist = $(this).attr("data-removechild");
			if (removelist) {
				$.ajax({
					type: "post",
					url: base_url + "Documenttype/removeChildChecklist",
					data: {
						DocumentTypeUID: removelist
					},
					success: function(data) {
						$('.' + removelist).closest('.childchildlist').remove();
						if (data) {
							$.notify({
								message: "Deleted Successfully",
							}, {
								type: "success",
								delay: 1000,
							});
							console.log(data);
							setTimeout(function() {
								location.reload();
							}, 2000);
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
			var remove = $(this).attr('data-remove');
			if (remove) {
				$.ajax({
					type: "post",
					url: base_url + "Documenttype/removeAlertMessage",
					data: {
						AlertUID: remove
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
							setTimeout(function() {
								location.reload();
							}, 2000);
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
			} else {
				$(this).closest('.removecomment').remove();
			}

		});

		/* 
		update alert meassage
		*/
		$(document).on('blur','.updateAlert',function(){
			var updateAlert=$(this).attr('data-updateAlert');
			var splitupdateAlert=updateAlert.split('~');
			var updatevalue=$(this).val();
			if(updatevalue){
				$.ajax({
					type: "POST",
					url: base_url + "Documenttype/updateAlertMessage",
					data: {AlertUID:splitupdateAlert[0],field:splitupdateAlert[1],value:updatevalue},
					dataType: 'json',
					success: function(response) {
						console.log(response);
					},
					error: function(xhr) {

						console.log(xhr);
					}
				});
			}else{
				$.notify({
					message: "Please Fill All Mandatory Fields",
				}, {
					type: "danger",
					delay: 1000,
				});
			}

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