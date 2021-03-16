
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
		<div class="card-icon">WORKFLOW DURATION
		</div>
	</div>
	<div class="card-body">
				<form action="#"  name="duration_form" id="duration_form">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="WorkFlow" class="bmd-label-floating workflow">WORKFLOW<span class="mandatory"></span></label>
								<!--input type="text" class="form-control" id="Duration" name="Duration" /-->
								<select type="text" class="form-control workflowid" id="WorkFlow" name="WorkFlow">
                           <option></option>
                           <?php 
                              foreach($GetWorkflowList as $key_get =>$value_get){ ?>
                                 <option value="<?php echo $value_get['WorkflowModuleUID'];?>"><?php echo $value_get['WorkflowModuleName']; ?></option>
                            <?php  }  ?>
								</select>
							</div>
                  </div>
                  <div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="Duration" class="bmd-label-floating">DURATION(min)<span class="mandatory"></span></label>
								<input type="text" class="form-control" maxlength="5" id="Duration" name="Duration" />
							</div>
                  </div>
                  <div class="col-md-3 mt-20" >
                     <div class="togglebutton">
                        <label class="label-color"> Active
                        <input type="checkbox" id="Active" name="Active" class="Active" checked>
                        <span class="toggle"></span>
                        </label>
                     </div>
                  </div>
					</div>
          
          
          
       
           <div class="ml-auto text-right">
          <a href="<?php echo base_url('Duration'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-save btn-wd addduration" name="addduration"><i class="icon-floppy-disk pr-10"></i>Add Duration</button>
          </div>
         
       </form>


 </div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){

   $(document).off('click','.addduration').on('click','.addduration', function(e) {


    var formdata = $('#duration_form').serialize();console.log(formdata);
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Duration/SaveMilestone'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading .....');
        button.val('<i class="fa fa-spin fa-spinner"></i> Saving.....');
      },

      success: function (response) {


       if(response.validation_error == 1)
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

            triggerpage('<?php echo base_url();?>Duration');

          }, 3000);
        }

        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    }); 

  });

  $(document).on('change','.workflowid',function(){
    if($('.workflowid').val()){
      $('.workflow').hide();
    }else{
      $('.workflow').show();
    }
  });

  $('#Duration').keypress(function (event) {
            return isNumber(event, this)
        });

    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

/*   //validation of duration
  $(document).on('change','#Duration',function(){
    var Duration=$('#Duration').val();
    if(Duration>60){
      $.notify(
          {
            icon:"icon-bell-check",
            message:"response.message"
          },
          {
            type:"danger",
            delay:1000 
          });
    }
  }); */

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







