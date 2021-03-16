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
		<div class="card-icon">Settings
		</div>
	</div>

	<div class="card-body">
		<form action="#"  name="field_form" id="field_form">
			<input type="hidden" class="form-control" id="SettingUID" name="SettingUID" value="<?php echo $setting->SettingUID;?>" />
			<input type="hidden" class="form-control" id="SettingField" name="SettingField" value="<?php echo $setting->SettingField; ?>" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group bmd-form-group">
						<label for="DisplayName" class="bmd-label-floating">Name <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="DisplayName"  value="<?php echo $setting->DisplayName;?>" name="DisplayName" />
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group bmd-form-group">
						<label for="Description" class="bmd-label-floating">Description<span class=""></span></label>
						<input type="text" class="form-control" id="Description"  value="<?php echo $setting->Description;?>" name="Description" />
					</div>
				</div>

				<?php if ($setting->SettingField != 'Scrolling_Text') {
					if (in_array($setting->SettingField, ['PriorityReport_MilestonesList','PriorityReport_OnlyMilestones','Except_PriorityReport_PipelineMilestones'])) { ?>
					<div class="col-md-12">
						<div class="form-group bmd-form-group">
							<label for="SettingValue" class="bmd-label-floating">Milestones<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="SettingValue" name="SettingValue[]" multiple="true">
								<option value=""></option>
								<?php 
								$SettingValue = explode(',',$setting->SettingValue);
								foreach ($Milestones as $Milestoneskey => $Milestone) { ?>			

									<option value="<?php echo $Milestone->MilestoneUID; ?>" <?php echo (in_array($Milestone->MilestoneUID, $SettingValue)) ? 'selected' : '' ?>><?php echo $Milestone->MilestoneName; ?></option>

								<?php } ?>                 
							</select>
						</div>                                
					</div>
				<?php } else { ?>
					<div class="col-md-12">
					<div class="form-group bmd-form-group">
						<label for="SettingValue" class="bmd-label-floating">Value<span class=""></span></label>
						<input type="text" class="form-control" id="SettingValue"  value="<?php echo $setting->SettingValue;?>" name="SettingValue" />
					</div>
				</div>
			<?php	} }?>

			</div>
			<div class="row mt-10">


				<div class="col-md-2" >
					<div class="togglebutton">
						<label class="label-color"> Active  
							<input type="checkbox" id="Active" name="Active" class="Active" <?php if($setting->Active == 1){ echo "checked"; } ?>>
							<span class="toggle"></span>
						</label>
					</div>
				</div>
			</div>



			<div class="ml-auto text-right">
				<a href="<?php echo base_url('Settings'); ?>" class="btn btn-fill btn-back btn-wd"><i class="icon-arrow-left15 pr-10 Back"></i> Cancel</a>
				<button type="submit" class="btn btn-fill btn-update btn-wd btn-edit" name="btn-edit"><i class="icon-floppy-disk pr-10"></i>Update </button>
			</div>

		</form>
	</div>



</div>


<script type="text/javascript">


	$(document).ready(function(){


		$(document).off('click','.btn-edit').on('click','.btn-edit', function(e) {


			var formdata = $('#field_form').serialize();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('Settings/Update'); ?>",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('<i class="fa fa-spin fa-spinner"></i> Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');

				},

			})
			.done(function(response) {
				if(response.success == 0)
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

						$('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
						$('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

					});
				}
				else
				{

					$.notify({icon:"icon-bell-check",message:response.message},{type:'success',delay:2000,onClose:redirecturl(window.location.href) });

				}
				button.html(button_text);
				button.val(button_val);
				button.prop('disabled',false);
			})
			.fail(function(jqXHR) {
				console.error("error", jqXHR);
				$.notify(
				{
					icon:"icon-bell-check",
					message:"Failed"
				},
				{
					type:"danger",
					delay:1000 
				});
				button.html(button_text);
				button.val(button_val);
				button.prop('disabled',false);
			})
		});

	});

</script>







