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
		<div class="card-icon">Audit Check List

		</div>
	</div>
	<div class="card-body">


				<form action="#"  name="user_form" id="user_form">
					<input type="hidden" class="form-control" id="QuestionUID" name="QuestionUID" value="<?php echo $UserDetails->QuestionUID;?>" />
         <div class="row mt-10">

          <div class="col-md-4">
            <div class="form-group bmd-form-group">
             <label for="QuestionTypeUID" class="bmd-label-floating">Audit Type<span class="mandatory"></span></label>
             <select  class="form-control select2picker " id="QuestionTypeUID"value="<?php echo $UserDetails->QuestionTypeUID;?>" name="QuestionTypeUID" aria-haspopup="true" aria-expanded="false" required>
              <option value=""></option>
              <?php foreach ($UserDetails1 as $key => $value) { 
                if($value->QuestionTypeUID == $UserDetails->QuestionTypeUID)
                  { ?>

                    <option value="<?php echo $value->QuestionTypeUID; ?>" selected><?php echo $value->QuestionTypeName; ?></option>

                  <?php }else{ ?>
                    <option value="<?php echo $value->QuestionTypeUID; ?>"><?php echo $value->QuestionTypeName; ?></option>
                  <?php }
                  ?>
                <?php } ?>
                <ul class="dropdown-menu dropdown-style QuestionTypeUID"></ul>
              </select>
            </div> 
          </div>
          <div class="col-md-4">
            <div class="form-group bmd-form-group">
             <label for="InputDocTypeUID" class="bmd-label-floating">Input Doc Type </label>
         <select class="form-control select2picker" id="InputDocTypeUID" name="InputDocTypeUID" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
           <option value=""></option>
           <?php
           foreach ($InputDocType as $value) {
            if($value->InputDocTypeUID == $UserDetails->InputDocTypeUID)
              { ?>

                <option value="<?php echo $value->InputDocTypeUID; ?>" selected><?php echo $value->DocTypeName; ?></option>

              <?php }else{ ?>
                <option value="<?php echo $value->InputDocTypeUID; ?>"><?php echo $value->DocTypeName; ?></option>
              <?php }

          } ?>
        </select>
            </div> 
          </div>
                    <div class="col-md-4">
            <div class="form-group bmd-form-group">
             <label for="ProjectUID" class="bmd-label-floating">Project<span class="mandatory"></span></label>
             <select class="form-control select2picker" id="ProjectUID" name="ProjectUID" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
               <option value=""></option>
               <?php
               foreach ($UserDetails3 as $row) {
                 if($row->ProjectUID == $UserDetails->ProjectUID)
              { ?>
                <option value="<?php echo $row->ProjectUID; ?>" selected><?php echo $row->ProjectName ?></option>
                <?php }else{ ?>

                <option value="<?php echo $row->ProjectUID; ?> "><?php echo $row->ProjectName ?></option>
                 <?php } 
              } ?>
              <ul class="dropdown-menu dropdown-style ProjectUID"></ul>
            </select>
          </div> 
        </div>
        </div>
    
      <div class="row mt-10">
       <div class="col-md-4">
        <div class="form-group bmd-form-group">
             <label for="LenderUID" class="bmd-label-floating">Lender </label>
             <select class="form-control select2picker check_question" id="LenderUID" value="<?php echo $UserDetails->LenderUID;?>" name="LenderUID"  aria-haspopup="true" aria-expanded="false" required>
              <option value=""></option>
              <?php foreach ($UserDetails4 as $key => $value) { 
                if($value->LenderUID == $UserDetails->LenderUID)
                  { ?>

                    <option value="<?php echo $value->LenderUID; ?>" selected><?php echo $value->LenderName; ?></option>

                  <?php }else{ ?>
                    <option value="<?php echo $value->LenderUID; ?>"><?php echo $value->LenderName; ?></option>
                  <?php }
                  ?>
                <?php } ?>
                <ul class="dropdown-menu dropdown-style LenderUID"></ul>
              </select>
            </div>  
    </div>
       <div class="col-md-4">
        <div class="form-group bmd-form-group">
         <label for="DocumentTypeUID" class="bmd-label-floating">Document Type </label>
         <select class="form-control select2picker check_question" id="DocumentTypeUID" value="<?php echo $UserDetails->DocumentTypeUID;?>" name="DocumentTypeUID" aria-haspopup="true" aria-expanded="false" required>
          <option value=""></option>
          <?php foreach ($UserDetails2 as $key => $value) { 
            if($value->DocumentTypeUID == $UserDetails->DocumentTypeUID)
              { ?>

                <option value="<?php echo $value->DocumentTypeUID; ?>" selected><?php echo $value->DocumentTypeName; ?></option>

              <?php }else{ ?>
                <option value="<?php echo $value->DocumentTypeUID; ?>"><?php echo $value->DocumentTypeName; ?></option>
              <?php }
              ?>
            <?php } ?>
          </select>
        </div> 
      </div>

      <div class="col-md-3"  style="margin-top: 15pt;">
        <div class="form-check">
          <label class="form-check-label">
            <input class="form-check-input check_question"  id="question_checkbox" type="checkbox" <?php if($UserDetails->FreeQuestion == 1) { echo "checked"; } ?> name="FreeQuestion" value="FreeQuestion">FreeAudit
            <span class="form-check-sign">
              <span class="check"></span>
            </span>
          </label>
        </div>
      </div>
    </div>  
    <div class="row">    	
      <div class="col-md-12">
        <div class="form-group bmd-form-group">
          <label class="bmd-label-floating">Audit Name<span class="mandatory"></span></label>
          <textarea rows="3" cols="3" class="form-control mt-20 " id="QuestionName" name="QuestionName"><?php echo $UserDetails->QuestionName;?></textarea>

        </div>
      </div>			
    </div>      
      <div class="row">
       <div class="col-md-4">
  <div class="togglebutton">
    <label class="form-check-label"> Active
      <input type="checkbox" id="Active"  <?php if($UserDetails->Active == 1) { echo "checked"; } ?> name="Active" class="Active" >
      <span class="toggle"></span>
    </label>
  </div>
</div> 
</div> 
   <!--  <div class="col-sm-12 form-group pull-right">
      <p class="text-right">
       <button type="button" class="btn btn-space btn-social btn-color btn-twitter addcustomer" value="1">Update Question</button>
     </p>
   </div> -->
   <div class="ml-auto text-right">
         <!--  <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
           <a href="<?php echo base_url('Question'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-update btn-wd addcustomer" name="addcustomer"><i class="icon-floppy-disk pr-10"></i>Update Audit Check List</button>
          </div>


 </form>
</div>
</div>
</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){
    //restrict quetion to select one at a time
    $(document).off('change','.check_question').on('change','.check_question',function(e)
    {
     
      var value = $(this).val();
      var id= $(this).attr('id');
      $('.check_question').val('');
      $('.check_question').removeAttr('checked');
      $('.check_question').attr('disabled','true');
      $(this).removeAttr('disabled');
      $(this).val(value);
      if(id == 'question_checkbox')
      {
        var check = $(this).is(':checked'); 
        if(check == true)
        {
          value = 'true';
        }
        else
        {
           value = '';
        }
      } 
      if(value == ''  )
      { 
         $('.check_question').removeAttr('disabled');
      }
    });

   $(document).off('click','.addcustomer').on('click','.addcustomer', function(e) {


    var formdata = $('#user_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Question/UpdateQuestion'); ?>",
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

          triggerpage('<?php echo base_url();?>Question');

        }, 3000); 

      }
      button.html(button_text);
      button.val(button_val);
      button.prop('disabled',false);

    }
  });

  });



/* $(document).on('click','.Back',function()
   {
      setTimeout(function(){ 

          triggerpage('<?php echo base_url();?>Question');

        },50);
   });
*/


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







