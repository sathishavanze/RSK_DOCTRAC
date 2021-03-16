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
<div class="card mt-40">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Status
		</div>
	</div>
	<div class="card-body">



				<form action="#"  name="StatusUpdate_form" id="StatusUpdate_form">
					
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="StatusName" class="bmd-label-floating">Status Name<span class="mandatory"></span></label>
								<input type="text" class="form-control" id="StatusName" name="StatusName" value="<?php echo $UpdateStatusDetails->StatusName;?>" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="StatusColor" class="bmd-label-floating">Status Color<span class="mandatory"></span></label>
								<input type="color" class="form-control" id="StatusColor" name="StatusColor" value="<?php echo $UpdateStatusDetails->StatusColor;?>"/>

							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="ModuleName" class="bmd-label-floating">Module Name<span class="mandatory"></span></label>
								<input type="text" class="form-control" id="ModuleName" name="ModuleName" value="<?php echo $UpdateStatusDetails->ModuleName;?>"/>

							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group togglebutton">
								<label class="label-color">Status
									<input type="checkbox" id="Active" name="Active" class="Active" <?php if($UpdateStatusDetails->Active == 1){ echo "checked"; } ?>>
									<span class="toggle"></span>
								</label>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<input type="hidden" class="form-control" id="StatusUID" name="StatusUID" value="<?php echo $UpdateStatusDetails->StatusUID;?>" />
							</div>
						</div>
					</div>
			
					

				<div class="ml-auto text-right">
					<a href="<?php echo base_url('Status'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
					<button type="submit" class="btn btn-fill btn-update btn-wd UpdateStatusSaveBtn" name="UpdateStatusSaveBtn"><i class="icon-floppy-disk pr-10"></i>Update Status</button>
				</div>
				</form>
		

	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script type="text/javascript">

	$(document).ready(function(){
		$(document).off('click','.UpdateStatusSaveBtn').on('click','.UpdateStatusSaveBtn', function(e) {

			var formdata = $('#StatusUpdate_form').serialize();
			button=$(this);
			button_val=$(this).val();
			button_text=$(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('Status/UpdateStatus'); ?>",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> update..');

				},
            success: function (response) {
        if(response.Status == 1)
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

            triggerpage('<?php echo base_url();?>Status');

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

	});
</script>







