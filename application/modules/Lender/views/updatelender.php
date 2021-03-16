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
		<div class="card-icon">TPO
		</div>
	</div>
	<div class="card-body pd-20">
    <form action="#"  name="user_form" id="user_form">
     <div class="row">
      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="username" class="bmd-label-floating">TPO Name <span class="mandatory"></span></label>
        <input type="text" class="form-control" id="LenderName" name="LenderName" value="<?php echo $DocumentDetails->LenderName;?>" />
      </div>
    </div>

    <div class="col-md-3">
     <div class="form-group bmd-form-group">
      <label for="TPOCode" class="bmd-label-floating">TPO Code</label>
      <input type="text" class="form-control" id="TPOCode" name="TPOCode" value="<?php echo $DocumentDetails->TPOCode;?>"/>
    </div>
  </div>


  <div class="col-md-3">
   <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">AddressLine1<span class="mandatory"></span></label>
    <input type="text" class="form-control" id="AddressLine1" name="AddressLine1" value="<?php echo $DocumentDetails->AddressLine1;?>" />
  </div>
</div>

<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">AddressLine2</label>
    <input type="text" class="form-control" id="AddressLine2" name="AddressLine2" value="<?php echo $DocumentDetails->AddressLine2;?>" />
  </div>
</div>


</div>
<div class="row mt-10">
 <div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">Zipcode<span class="mandatory"></span></label>
    <input type="text" class="form-control" id="ZipCode" name="ZipCode" value="<?php echo $DocumentDetails->ZipCode;?>" />
  </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">CityName<span class="mandatory"></span></label>
    <select class="select2picker form-control"  id="cityuid" name="CityUID" value="<?php echo $DocumentDetails->CityUID;?>" >  
     <?php $cities=$this->db->get_where('mCities',   array('CityUID' => $DocumentDetails->CityUID))->row(); ?>

     <?php 

     if (!empty($cities)) {
      ?>

      <option value="<?php echo $cities->CityUID; ?>"><?php echo $cities->CityName; ?></option>   
      <?php 
    }
    ?>
  </select>         
</div>
</div>

<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">StateName<span class="mandatory"></span></label>
    <select class="select2picker form-control"  id="stateuid" name="StateUID" value="<?php echo $DocumentDetails->StateUID;?>" >
     <?php 

     $States=$this->db->get_where('mStates',   array('StateUID' => $DocumentDetails->StateUID))->row(); 
     if (!empty($States)) {
      ?>

      <option value="<?php echo $States->StateUID; ?>"><?php echo $States->StateName; ?></option>

      <?php 
    }
    ?>

  </select>
</div>

</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">officeNo</label>
    <input type="text" class="form-control" id="officeNo" name="OfficeNo" value="<?php echo $DocumentDetails->OfficeNo;?>" />
  </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">FaxNo</label>
    <input type="text" class="form-control" id="FaxNo" name="FaxNo" value="<?php echo $DocumentDetails->FaxNo;?>" />
  </div>
</div>
<div class="col-md-3 mt-20">
  <div class="togglebutton">
    <label class="label-color"> Active
      <input type="checkbox" id="Active" name="Active" class="Active" <?php if($DocumentDetails->Active == 1){ echo "checked"; } ?>>
      <span class="toggle"></span>
    </label>
  </div>
</div>  

</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <input type="hidden" class="form-control" id="LenderUID" name="LenderUID" value="<?php echo $DocumentDetails->LenderUID;?>" />
    </div>
  </div>


</div>


    <!--   <div class="col-sm-12 form-group pull-right">
      <p class="text-right">
       <button type="button" class="btn btn-space btn-social btn-color btn-twitter adduser" value="1">UPDATE LENDER</button>
     </p>
   </div> -->
   <div class="ml-auto text-right">
    <!-- <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
    <a href="<?php echo base_url('Lender'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
    <button type="submit" class="btn btn-fill btn-update btn-wd adduser" name="adduser"><i class="icon-floppy-disk pr-10"></i>Update TPO</button>
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
      url: "<?php echo base_url('Lender/Updatelender'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading ...');
        button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
        

      },

      success: function (response) {
        if(response.Status == 3)
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

          triggerpage('<?php echo base_url();?>Lender');

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
        $('#cityuid').empty();
        $('#stateuid').empty();

        if(data != ''){

//$('#cityuid').val(data[0].CityName);
$('#cityuid').append('<option value="' + data[0]['CityUID'] + '" selected>' + data[0]['CityName'] + '</option>').trigger('change');
$('#stateuid').append('<option value="' + data[0]['StateUID'] + '" selected>' + data[0]['StateName'] + '</option>').trigger('change');

$('#stateuid').parent().addClass('is-dirty');
$('#cityuid').parent().addClass('is-dirty');
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







