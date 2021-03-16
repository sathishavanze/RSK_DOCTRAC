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
		<div class="card-icon">Reasons
		</div>
	</div>
	<div class="card-body">
		<form action="#"  name="ReasonsUpdate_form" id="ReasonsUpdate_form">
			
			<div class="row">
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="ReasonName" class="bmd-label-floating">Reasons Name<span class="mandatory"></span></label>
						<input type="text" class="form-control" id="ReasonName" name="ReasonName" value="<?php echo $UpdateReasonsDetails->ReasonName;?>" />
					</div>
				</div>

         		<div class="col-md-4">
             		<div class="form-group bmd-form-group">
            				<label for="QueueUID" class="bmd-label-floating">Queue Name<span class="mandatory"></span></label>
            			<select class="select2picker form-control"  id="QueueUID" name="QueueUID">
              				<option value=""></option>
              				<?php
                 				foreach ($QueueDetail as $row) {
                 				if($UpdateReasonsDetails->QueueUID=='') {
                 					} else {
                  				if($UpdateReasonsDetails->QueueUID == $row->QueueUID) {
                    		?>
                    			<option value="<?php echo $row->QueueUID; ?>" selected><?php echo $row->QueueName; ?> (<?php echo $row->WorkflowModuleName; ?>)</option>
                    		<?php
                  				} else {
                    		?>
                    			<option value="<?php echo $row->QueueUID; ?>"><?php echo $row->QueueName; ?> (<?php echo $row->WorkflowModuleName; ?>)</option>
                    		<?php
                  						}
                					}
              					}
            				?>                 
            			</select>
          			</div>                                
    			</div>

				<div class="col-md-3">
					<div class="form-group togglebutton">
						<label class="label-color">Active
							<input type="checkbox" id="Active" name="Active" class="Active" <?php if($UpdateReasonsDetails->Active == 1){ echo "checked"; } ?>>
							<span class="toggle"></span>
						</label>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<input type="hidden" class="form-control" id="ReasonUID" name="ReasonUID" value="<?php echo $UpdateReasonsDetails->ReasonUID;?>" />
					</div>
				</div>
			</div>
			<div class="ml-auto text-right">
				<a href="<?php echo base_url('Reasons'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
				<button type="submit" class="btn btn-fill btn-update btn-wd UpdateReasonsSaveBtn" name="UpdateReasonsSaveBtn"><i class="icon-floppy-disk pr-10"></i>Update Reasons</button>
			</div>
		</form>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script type="text/javascript">

	$(document).ready(function(){
		$(document).off('click','.UpdateReasonsSaveBtn').on('click','.UpdateReasonsSaveBtn', function(e) {

			var formdata = $('#ReasonsUpdate_form').serialize();
			button=$(this);
			button_val=$(this).val();
			button_text=$(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('Reasons/UpdateReasons'); ?>",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> update..');

				},
            success: function (response) {
        if(response.Reasons == 1)
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

            triggerpage('<?php echo base_url();?>Reasons');

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







