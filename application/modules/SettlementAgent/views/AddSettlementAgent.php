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
 .table-format > thead > tr > th{
   font-size: 12px;
 }
</style>
<div class="card mt-40 customcardbody" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">Settlement Agent
    </div>
  </div>
  <div class="card-body pd-10">
    <form action="#"  name="user_form" id="user_form">
      <div class="col-md-12 col-sm-12">
       <div class="row mt-10">
        <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SettlementAgentNo" class="bmd-label-floating">Settlement Agent No</label>
          <input type="text" class="form-control" id="SettlementAgentNo" name="SettlementAgentNo" />
        </div>
      </div>
      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="SettlementAgentName" class="bmd-label-floating">Settlement Agent Name <span class="mandatory"></span></label>
        <input type="text" class="form-control" id="SettlementAgentName" name="SettlementAgentName" />
      </div>
    </div>
    <div class="col-md-3">
     <div class="form-group bmd-form-group">
      <label for="SettlementAgentPhone" class="bmd-label-floating">Settlement Agent Phone</label>
      <input type="text" class="form-control" id="SettlementAgentPhone" name="SettlementAgentPhone" />
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="SettlementAgentFax" class="bmd-label-floating">Settlement Agent Fax</label>
      <input type="text" class="form-control" id="SettlementAgentFax" name="SettlementAgentFax" />
    </div>
  </div>  
</div>
<div class="row mt-10">  
 <div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="SettlementAgentEmail" class="bmd-label-floating">Settlement Agent Email</label>
    <input type="text" class="form-control" id="SettlementAgentEmail" name="SettlementAgentEmail" />
  </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="AddressLine1" class="bmd-label-floating">Address Line1<span class="mandatory"></span></label>
    <input type="text" class="form-control" id="AddressLine1" name="AddressLine1" />             
  </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="AddressLine2" class="bmd-label-floating">Address Line2</label>
    <input type="text" class="form-control" id="AddressLine2" name="AddressLine2" />
  </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="ZipCode" class="bmd-label-floating">ZipCode <span class="mandatory"></span></label>
    <input type="text" class="form-control" id="ZipCode" name="ZipCode" />
  </div>
</div>
</div>

<div class="row mt-10">
  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="CityName" class="bmd-label-floating">City Name <span class="mandatory"></span></label>
      <select  class="form-control select2picker" id="CityName" name="CityName" ></select>
    </div>
  </div>
  <div class="col-md-3 ">
    <div class="form-group bmd-form-group">
      <label for="StateName" class="bmd-label-floating">State Name <span class="mandatory"></span></label>
      <select  class="form-control select2picker" id="StateName" name="StateName" ></select>
    </div>
  </div>
</div>

<div class="ml-auto text-right">  
 <a href="<?php echo base_url('SettlementAgent'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
 <button type="submit" class="btn btn-fill btn-save btn-wd adduser" name="adduser"><i class="icon-floppy-disk pr-10"></i>Save Settlement Agent</button>
</div>
</div>
</form>
</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">

  $(document).ready(function(){

   $(document).off('click','.adduser').on('click','.adduser', function(e) {
     var formdata = $('#user_form').serialize();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();
     $.ajax({
      type: "POST",
      url: "<?php echo base_url('SettlementAgent/SaveSettlementAgent'); ?>",
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
          delay:1000 
        }); 
         setTimeout(function(){ 
          triggerpage('<?php echo base_url();?>SettlementAgent');

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

        });

      }
      button.html(button_text);
      button.val(button_val);
      button.prop('disabled',false);
    }
  });

   });


 /*$(document).on('click','.Back',function()
   {
      setTimeout(function(){ 

          triggerpage('<?php echo base_url();?>Lender');

        },50);
   });
   */


   $('#ZipCode').change(function(event) {

    zipcode = $(this).val();

    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>Lender/getzip',
      data: {'zipcode':zipcode}, 
      dataType:'json',
      cache: false,
      success: function(data)
      {
        $('#CityName').empty();
        $('#StateName').empty();

        if(data != ''){

//$('#cityuid').val(data[0].CityName);
$('#CityName').append('<option value="' + data[0]['CityUID'] + '" selected>' + data[0]['CityName'] + '</option>').trigger('change');
$('#StateName').append('<option value="' + data[0]['StateUID'] + '" selected>' + data[0]['StateName'] + '</option>').trigger('change');

$('#CityName').parent().addClass('is-dirty');
$('#StateName').parent().addClass('is-dirty');

}
callselect2();

},
error: function (jqXHR, textStatus, errorThrown) {

  console.log(errorThrown);

},
failure: function (jqXHR, textStatus, errorThrown) {

  console.log(errorThrown);

},
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







