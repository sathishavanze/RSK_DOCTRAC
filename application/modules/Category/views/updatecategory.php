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
.is-invalid {
background-size: 100% 100%, 100% 100%;
transition-duration: .3s;
box-shadow: none;
background-image: linear-gradient(to top, #F44336 2px, rgba(244, 67, 54, 0) 2px), linear-gradient(to top, #D2D2D2 1px, rgba(210, 210, 210, 0) 1px);
}
</style>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">CATEGORY
		</div>
	</div>
	<div class="card-body">


				<form action="#"  name="user_form" id="user_form">
					<input type="hidden" class="form-control" id="Categoryuid" name="CategoryUID" value="<?php echo $UserDetails->CategoryUID;?>" />
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="username" class="bmd-label-floating">CategoryName <span class="mandatory"></span></label>
								<input type="text" class="form-control" id="CategoryName" name="CategoryName" value="<?php echo $UserDetails->CategoryName;?>" />
							</div>
						</div>
              <div class="col-md-3 mt-20" >
                <div class="togglebutton">
                  <label class="label-color"> Active
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($UserDetails->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div>
              </div>
					</div>
  
			
         <div class="col-sm-12 form-group pull-right">
           <div class="ml-auto text-right">
          <a href="<?php echo base_url('Category'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-update btn-wd addcategory" name="addcategory"><i class="icon-floppy-disk pr-10"></i>Update Category</button>
          </div>

     </form>

</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){

   $(document).off('click','.addcategory').on('click','.addcategory', function(e) {


    var formdata = $('#user_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Category/UpdateCategory'); ?>",
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

          triggerpage('<?php echo base_url();?>Category');

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







