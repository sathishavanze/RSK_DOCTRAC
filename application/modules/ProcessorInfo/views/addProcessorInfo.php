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
		<div class="card-icon">Processor
		</div>
	</div>
	<div class="card-body">
		<form action="#"  name="ProcessorForm" id="ProcessorForm">
			<input type="hidden" class="form-control" id="ProcessorUID" name="ProcessorUID" value="<?php if(isset($ProcessorDetails->LastName)) { echo $ProcessorDetails->ProcessorUID;} ?>" />
			<div class="row">
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="FirstName" class="bmd-label-floating">First Name <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="FirstName" name="FirstName" value="<?php if(isset($ProcessorDetails->FirstName)) { echo $ProcessorDetails->FirstName;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="LastName" class="bmd-label-floating">Last Name </label>
						<input type="text" class="form-control" id="LastName" name="LastName" value="<?php if(isset($ProcessorDetails->LastName)) { echo $ProcessorDetails->LastName;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="TeamLeader" class="bmd-label-floating">Team Leader </label>
						<input type="text" class="form-control" id="TeamLeader" name="TeamLeader" value="<?php if(isset($ProcessorDetails->TeamLeader)) { echo $ProcessorDetails->TeamLeader;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="Manager" class="bmd-label-floating">Manager </label>
						<input type="text" class="form-control" id="Manager" name="Manager" value="<?php if(isset($ProcessorDetails->Manager)) { echo $ProcessorDetails->Manager;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="VP" class="bmd-label-floating">VP </label>
						<input type="text" class="form-control" id="VP" name="VP" value="<?php if(isset($ProcessorDetails->VP)) { echo $ProcessorDetails->VP;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="PhoneNumber" class="bmd-label-floating">Phone Number </label>
						<input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="<?php if(isset($ProcessorDetails->PhoneNumber)) { echo $ProcessorDetails->PhoneNumber;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="HoursofOperation" class="bmd-label-floating">Hours of Operation </label>
						<input type="text" class="form-control" id="HoursofOperation" name="HoursofOperation" value="<?php if(isset($ProcessorDetails->HoursofOperation)) { echo $ProcessorDetails->HoursofOperation;} ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="TimeZone" class="bmd-label-floating">Time Zone </label>
						<input type="text" class="form-control" id="TimeZone" name="TimeZone" value="<?php if(isset($ProcessorDetails->TimeZone)) { echo $ProcessorDetails->TimeZone;} ?>"/>
					</div>
				</div>
				<?php if(isset($ProcessorDetails->Active)) { ?>
					<div class="col-md-3">
						<div class="form-group togglebutton mt-20">
							<label class="label-color">Status
								<input type="checkbox" id="Active" name="Active" class="Active" <?php if($ProcessorDetails->Active == 1){ echo "checked"; } ?>>
								<span class="toggle"></span>
							</label>
						</div>
					</div>
				<?php } ?>				
			</div>
			<div class="row mt-10">
			</div>
			<div class="ml-auto text-right">
				<a href="ProcessorInfo" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
				<button type="submit" class="btn btn-fill btn-save btn-wd addprocessor" name="addprocessor"><i class="icon-floppy-disk pr-10"></i>Save Processor</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(document).off('click','.addprocessor').on('click','.addprocessor', function(e) {
			var formdata = $('#ProcessorForm').serialize();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "ProcessorInfo/SaveProcessor/",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
				},

				success: function (response) {
					if(response.Status == 0)
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

							triggerpage('<?php echo base_url(); ?>ProcessorInfo');

						}, 3000);
					} else {
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
							$('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">'+v+'</span>');

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






