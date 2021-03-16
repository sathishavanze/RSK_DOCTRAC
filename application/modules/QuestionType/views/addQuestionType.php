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
		<div class="card-icon">Audit Check List Type
		</div>
	</div>
	<div class="card-body">

				<form action="#"  name="user_form" id="user_form">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="categoryname" class="bmd-label-floating">Audit Check List Type<span class="mandatory"></span></label>
								<input type="text" class="form-control" id="QuestionTypeName" name="QuestionTypeName" />
							</div>
						</div>
					</div>
    
          <!-- <div class="col-sm-12 form-group pull-right">
            <p class="text-right">
             <button type="button" class="btn btn-space btn-social btn-color btn-twitter addquestiontype" value="1">Save QuestionType</button>
           </p>
         </div> -->
         <div class="ml-auto text-right">
         <!--  <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
           <a href="<?php echo base_url('QuestionType'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>

          <button type="submit" class="btn btn-fill btn-save btn-wd addquestiontype" name="addquestiontype"><i class="icon-floppy-disk pr-10"></i>Save Audit Check List Type </button>
          </div>
       </form>
   
 </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
    $(document).off('click','.addquestiontype').on('click','.addquestiontype', function(e) {
      var formdata = $('#user_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('QuestionType/SaveQuestionType'); ?>",
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

              triggerpage('<?php echo base_url();?>QuestionType');

            }, 3000);
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







