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
		<div class="card-icon">WorkFlow
		</div>
	</div>
	<div class="card-body">


				<form action="#"  name="Workflow_form" id="Workflow_form">
					<input type="hidden" class="form-control" id="WorkflowModuleUID" name="WorkflowModuleUID" value="<?php echo $WorkFlowDetails->WorkflowModuleUID;?>" />
         <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group">
              <label for="WorkflowModuleName" class="bmd-label-floating">Workflow Module Name<span class="mandatory"></span></label>
              <input type="text" class="form-control" id="WorkflowModuleName"  value="<?php echo $WorkFlowDetails->WorkflowModuleName;?>" name="WorkflowModuleName" />
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group bmd-form-group">
              <label for="WorkflowIcon" class="bmd-label-floating">Workflow Icon<span class="mandatory"></span></label>
              <input type="text" class="form-control" id="WorkflowIcon"  value="<?php echo $WorkFlowDetails->WorkflowIcon;?>" name="WorkflowIcon" />
            </div>
          </div>

          <div class="col-md-4" style="margin-top: 22px;">
            <div class="togglebutton">
              <label class="label-color"> Active:  
                <input type="checkbox" id="Active" name="Active" class="Active" <?php if($WorkFlowDetails->Active == 1){ echo "checked"; } ?>>
                <span class="toggle"></span>
              </label>
            </div>
          </div>

          </div>
  
   

      <div class="row">
   <div class="ml-auto text-right">
          <a href="<?php echo base_url('WorkFlow'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-update btn-wd btn-editWorkFlow" name="btn-editWorkFlow"><i class="icon-floppy-disk pr-10"></i>Update WorkFlow </button>
          </div>
          </div>

 </form>

</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">


	$(document).ready(function(){
    
    $(document).off('click','.btn-editWorkFlow').on('click','.btn-editWorkFlow', function(e) {


      var formdata = $('#Workflow_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('WorkFlow/UpdateWorkFlow'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {
         button.prop("disabled", true);
         button.html('<i class="fa fa-spin fa-spinner"></i> Loading ...');
         button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');

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

            triggerpage('<?php echo base_url();?>WorkFlow');

          }, 3000); 

        }
        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    });

    });




  });
</script>







