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
		<div class="card-icon">Edit Investor
		</div>
	</div>
  <div class="card-body pd-10">
    <form action="#"  name="frmUpdateInvestor" id="frmUpdateInvestor">
      <input type="hidden" name="InvestorUID" value="<?php echo $InvestorDetails->InvestorUID; ?>">
     <div class="row">
      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="InvestorNo" class="bmd-label-floating">Investor No <span class="mandatory"></span></label>
        <input type="text" class="form-control" id="InvestorNo" name="InvestorNo" value="<?php echo $InvestorDetails->InvestorNo; ?>" />
      </div>
    </div>

    <div class="col-md-3">
     <div class="form-group bmd-form-group">
      <label for="InvestorName" class="bmd-label-floating">Investor Name <span class="mandatory"></span></label>
      <input type="text" class="form-control" id="InvestorName" name="InvestorName" value="<?php echo $InvestorDetails->InvestorName; ?>" />
    </div>
  </div>


    <div class="col-md-3">
   <div class="form-group bmd-form-group">
    <label for="AddressLine1" class="bmd-label-floating">AddressLine1<span class="mandatory"></span></label>
    <input type="text" class="form-control" id="AddressLine1" name="AddressLine1"  value="<?php echo $InvestorDetails->AddressLine1; ?>" />
  </div>
</div>



</div>
<div class="row mt-10">

  <div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">AddressLine2</label>
    <input type="text" class="form-control" id="AddressLine2" name="AddressLine2" value="<?php echo $InvestorDetails->AddressLine2; ?>" />
  </div>
</div>


  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">Zipcode<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="ZipCode" name="ZipCode" value="<?php echo $InvestorDetails->ZipCode; ?>" />
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">CityName<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="CityName" name="CityName" value="<?php echo $InvestorDetails->CityName; ?>" />
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">StateName<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="StateName" name="StateName" value="<?php echo $InvestorDetails->StateName; ?>" />
    </div>
  </div>            
</div>
<div class="row">
              <div class="col-md-3">
                <div class="togglebutton">
                  <label class="label-color"> Active
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($InvestorDetails->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div>
              </div>  
    </div>


<div class="ml-auto text-right">
 <a href="<?php echo base_url('Investor'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
 <button type="submit" class="btn btn-fill btn-save btn-wd updateinvestor" name="updateinvestor"><i class="icon-floppy-disk pr-10"></i>Update Investor</button>
</div>
</form>
</div>

</div>
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">

	$(document).ready(function(){

   $(document).off('click','.updateinvestor').on('click','.updateinvestor', function(e) {
     var formdata = $('#frmUpdateInvestor').serialize();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();
     $.ajax({
      type: "POST",
      url: "<?php echo base_url('Investor/UpdateInvestor'); ?>",
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

            triggerpage('Investor');

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
 


   $('#ZipCode').change(function(event) {

    zipcode = $(this).val();

    $.ajax({
      type: "POST",
      url: 'CommonController/GetZipCodeDetails',
      data: {'Zipcode':zipcode}, 
      dataType:'json',
      cache: false,
      success: function(data)
      {
        $('#CityName').empty();
        $('#StateName').empty();

        if(data.success == 1){

          $('#CityName').val(data['City'][0]['CityName']).trigger('change');
          $('#StateName').val(data['State'][0]['StateCode']).trigger('change');

          $('#StateName').parent().addClass('is-dirty');
          $('#CityName').parent().addClass('is-dirty');

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

});
</script>







