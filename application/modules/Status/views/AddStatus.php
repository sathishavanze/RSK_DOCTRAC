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
		<div class="card-icon">Status
		</div>
	</div>
	<div class="card-body">


				<form action="#"  name="Status_form" id="Status_form">
         <div class="row">
          <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="StatusName" class="bmd-label-floating">Status Name <span class="mandatory"></span></label>
            <input type="text" class="form-control" id="StatusName" name="StatusName" />
          </div>
        </div>


        <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="StatusColor" class="bmd-label-floating">StatusColor Code<span class="mandatory"></span></label>
          <input type="color" class="form-control" id="StatusColor" name="StatusColor" />

        </div>
      </div>

      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="ModuleName" class="bmd-label-floating">ModuleName<span class="mandatory"></span></label>
        <input type="text" class="form-control" id="ModuleName" name="ModuleName" />
      </div>
    </div>


  </div>
  <div class="row mt-10">
  </div>

  <div class="ml-auto text-right">
    <a href="<?php echo base_url('Status'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
    <button type="submit" class="btn btn-fill btn-save btn-wd SaveStatus" name="SaveStatus"><i class="icon-floppy-disk pr-10"></i>Save Status</button>
  </div>

</form>
</div>


</div>

</div>
</div>


<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){
   $(document).off('click','.SaveStatus').on('click','.SaveStatus', function(e) {
    var formdata = $('#Status_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Status/SaveStatus'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading ...');
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
            $('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">'+v+'</span>');
            // $(".loginidunique").show();
            // $('#loginid'+ k);

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






