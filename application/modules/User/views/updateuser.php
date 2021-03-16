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
</style>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">USER
		</div>
	</div>
	<div class="card-body">

		<form action="#"  name="user_form" id="user_form">

			<div class="row">
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">User Name <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="username" name="username" value="<?php echo $UpdateUsersDetails->UserName;?>" />
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="loginid" class="bmd-label-floating">Login ID <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="loginid" name="loginid" value="<?php echo $UpdateUsersDetails->LoginID;?>"/>
						<!-- <span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">This login ID already taken</span> -->
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="emailid" class="bmd-label-floating">E-mail ID <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="emailid" name="emailid" value="<?php echo $UpdateUsersDetails->EmailID;?>"/>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="contactno" class="bmd-label-floating">Contact No</label>
						<input type="text" class="form-control" id="contactno" name="contactno" value="<?php echo $UpdateUsersDetails->ContactNo;?>" data-mask="(000)000-0000" maxlength="13"/>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="faxno" class="bmd-label-floating">Fax No </label>
						<input type="text" class="form-control" id="faxno" name="faxno" value="<?php echo $UpdateUsersDetails->FaxNo;?>"data-mask="(000)000-0000" maxlength="13"/>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="roleuid" class="bmd-label-floating">Role Name<span class="mandatory"></span></label>
						<select class="select2picker form-control"  id="RoleUID" name="RoleUID">
							<option value=""></option>
							<?php foreach ($getroles as $key => $value) { 
								if($value->RoleUID == $UpdateUsersDetails->RoleUID)
									{ ?>

										<option value="<?php echo $value->RoleUID; ?>" selected><?php echo $value->RoleName; ?></option>

									<?php }else{ ?>
										<option value="<?php echo $value->RoleUID; ?>"><?php echo $value->RoleName; ?></option>
									<?php }
									?>
								<?php } ?>            
							</select>
						</div>

					</div>

					<?php if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="CustomerUID" class="bmd-label-floating">Client</label>
								<select class="select2picker form-control"  id="CustomerUID" name="CustomerUID">
									<option selected>No Client</option>
									<?php foreach ($Customer as $key => $value) { ?>
										<?php if ($UpdateUsersDetails->CustomerUID == $value->CustomerUID) { ?>
											<option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>

										<?php } else { ?>

											<option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName; ?></option>
										<?php } ?>
									<?php } ?>               
								</select>
							</div>
						</div>
					<?php } ?>


					<input type="hidden" class="form-control" id="UserUID" name="UserUID" value="<?php echo $UpdateUsersDetails->UserUID;?>" />

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="SetPassword" class="bmd-label-floating">Password </label>
							<input type="text" class="form-control" id="SetPassword" name="SetPassword" value="" />
						</div>
					</div>
				

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="roleuid" class="bmd-label-floating">Location<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="UserLocation" name="UserLocation">
								<option value="Offshore" <?php if($UpdateUsersDetails->UserLocation == 'Offshore'){ echo "selected"; } ?>>Offshore</option>
								<option value="Onshore" <?php if($UpdateUsersDetails->UserLocation == 'Onshore'){ echo "selected"; } ?>>Onshore</option>              
							</select>
						</div>
					</div>

				</div>
				<div class="row">

					<div class="col-md-3">   
						<div class="form-check" style="margin-top: 19pt;">
							<label class="form-check-label">
								<input class="form-check-input"  <?php if($UpdateUsersDetails->PasscodeVerify == 1){ echo "checked"; } ?> type="checkbox"  name="PasscodeVerify"  id="PasscodeVerify"  value="Angular"> Passcode Verify
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div> 
					</div>


					<!-- <div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="OrderQCSkipPercentage" class="bmd-label-floating">Percentage of QC Skip Orders </label>
							<input type="number" class="form-control" id="OrderQCSkipPercentage" name="OrderQCSkipPercentage" value="<?php echo $UpdateUsersDetails->OrderQCSkipPercentage;?>" />
						</div>
					</div> -->


					<div class="col-md-3">
						<div class="form-group togglebutton mt-20">
							<label class="label-color">Status
								<input type="checkbox" id="Active" name="Active" class="Active" <?php if($UpdateUsersDetails->Active == 1){ echo "checked"; } ?>>
								<span class="toggle"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="col-sm-12 form-group pull-right">
				<p class="text-right">
					<a href="<?php echo base_url('User'); ?>" class="btn btn-danger back" role="button" aria-pressed="true"><i class="icon-arrow-left16"></i>&nbsp; Back</a>
					<button type="button" class="btn btn-space btn-social btn-color btn-twitter updateuser" value="1">UPDATE USER</button>
				</p>
			</div> -->

			<div class="ml-auto text-right mb-10 col-md-12">
				<a href="<?php echo base_url(); ?>User" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
				<button type="submit" class="btn btn-fill btn-update btn-wd updateuser" name="updateuser"><i class="icon-floppy-disk pr-10"></i>Update User</button>
			</div>

		</form>
	</div>
</div>

</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script type="text/javascript">

	$(document).ready(function(){
		$(document).off('click','.updateuser').on('click','.updateuser', function(e) {

			$(".loginidunique").hide();
			var formdata = $('#user_form').serialize();
			button=$(this);
			button_val=$(this).val();
			button_text=$(this).html();
			$.ajax({
				type: "POST",
				url: "User/SaveUpdate",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> update..');

				},
				success: function (response) {
					if(response.Status == 2)
					{
						$.notify(
						{
							icon:"icon-bell-check",
							message:response.message
						},
						{
							type:"success",
							delay:1000 
						}); 
						setTimeout(function(){ 

							triggerpage('<?php echo base_url(); ?>User');

						}, 3000);
					}
					else
					{
						$.notify(
						{
							icon:"icon-bell-check",
							message:response.message
						},
						{
							type:"danger",
							delay:1000 
						});
						$.each(response, function(k, v) {
							console.log(k);
							$('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
							$('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; font-size: 11px;">'+v+'</span>');
							// $(".loginidunique").show();
						});

					}
					button.html(button_text);
					button.val(button_val);
					button.prop('disabled',false);

				}
			});

		});


		function log()
		{

			var loginid = $('#loginid').val();

			$.ajax({
				type: "POST",
				url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
				data: {'loginid' : loginid},
				dataType:'json',
				success: function (response) {

					if(response.Status == 1)
					{

						$('#loginexists').show();
					}else{
						$('#loginexists').hide();
					}


				},
				error:function(xhr){

					console.log(xhr);
				}
			});

		}


	});
</script>







