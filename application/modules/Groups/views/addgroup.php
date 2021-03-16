<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Add Group
		</div>


	</div>
	<div class="card-body">
		<div class="col-md-12">
			<form action="#"  name="Group_form" id="Group_form" class="Group_form mb-0">
				<div class="form-group bmd-form-group">
					<label for="recipient-name" class="bmd-label-floating">Group Name <span class="mandatory"></span></label>
					<input type="text" class="form-control" id="GroupName" name="GroupName">
				</div>
				<div class="form-group bmd-form-group">
					<label for="message-text" class="form-label">Customer <span class="mandatory"></span></label>
					<select class="select2picker form-control"  id="GroupCustomerUID" name="GroupCustomerUID">
						<option value=""></option>
						<?php foreach ( $getcustomers as $key => $value) { ?>
							<?php if($this->parameters['DefaultClientUID'] == $value->CustomerUID) { ?>
				              <option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
				            <?php }
				        } ?>               
					</select>
				</div>
				<div class="form-group bmd-form-group">
					<label for="message-text" class="form-label">Team Leader <span class="mandatory"></span></label>
					<select class="select2picker form-control"  id="GroupTeamUserUID" name="GroupTeamUserUID[]" multiple="multiple">
						<option value=""></option>
						<?php foreach ( $getteamleaderusers as $key => $value) { ?>
							<option value="<?php echo $value->UserUID; ?>"><?php echo $value->UserName; ?></option>
						<?php } ?>                
					</select>
				</div>
				<div class="form-group bmd-form-group">
					<label for="message-text" class="form-label">User <span class="mandatory"></span></label>
					<select class="select2picker form-control"  id="GroupUserUID" name="GroupUserUID[]" multiple="multiple">
						<option value=""></option>
						<?php foreach ( $getusers as $key => $value) { ?>
							<option value="<?php echo $value->UserUID; ?>"><?php echo $value->UserName; ?></option>
						<?php } ?>                
					</select>
				</div>
				<div class="form-group bmd-form-group mt-40">
					<label for="message-text" class="form-label">State</label>
					<select class="select2picker form-control"  id="GroupStateUID" name="GroupStateUID[]" multiple="multiple">
						<option value=""></option>
						<?php foreach ( $getstates as $key => $value) { ?>
							<option value="<?php echo $value->StateUID; ?>"><?php echo $value->StateName; ?></option>
						<?php } ?>               
					</select>
				</div>
				<div class="form-group bmd-form-group is-filled">
					<div class="togglebutton">
						<label class="label-color" style="margin-top: 18pt"> Active
							<input type="checkbox" id="Active" name="Active" class="Active" checked>
							<span class="toggle"></span>
						</label>
					</div>
				</div>
				<div class="ml-auto mt-10 mb-10 text-right">
					<a href="<?php echo base_url() ?>Groups" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back<div class="ripple-container"></div></a>
					<button type="submit" class="btn btn-fill btn-save btn-wd save_Group" id="save_Group"><i class="icon-floppy-disk pr-10"></i>Save Group</button>
				</div>
			</form>


		</div>

	</div>
</div>


<script type="text/javascript">
	$('.card-icon').show();
	$(document).ready(function(){

		$(document).off('click','.save_Group').on('click','.save_Group', function(e) {
			e.preventDefault();
			var formdata = $('#Group_form').serialize();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('Groups/SaveGroup'); ?>",
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
							delay:2000 
						}); 

						window.setTimeout(function(){
							triggerpage('<?php echo base_url();?>Groups');
						},3000);

					}
					else if(response.Status == 10){
						$.notify(
						{
							icon:"icon-bell-check",
							message:response.message
						},
						{
							type:"info",
							delay:2000 
						}); 
						button.html(button_text);
						button.val(button_val);
						button.prop('disabled',false);
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
							delay:2000 
						});
						$.each(response, function(k, v) {
							console.log($('#'+ k +'.select2picker'));
							$('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
						});

						button.html(button_text);
						button.val(button_val);
						button.prop('disabled',false);
					}

				},
				error : function() {
					button.html(button_text);
					button.val(button_val);
					button.prop('disabled',false);
				}
			});

		});

	});

</script>







