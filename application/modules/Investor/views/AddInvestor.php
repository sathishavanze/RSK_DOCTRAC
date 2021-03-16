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
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Investor
		</div>
	</div>
	<div class="card-body pd-10">
    <form action="#"  name="investorform" id="investorform">
     <div class="row">
      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="InvestorNo" class="bmd-label-floating">Investor No <span class="mandatory"></span></label>
        <input type="text" class="form-control" id="InvestorNo" name="InvestorNo" />
      </div>
    </div>

    <div class="col-md-3">
     <div class="form-group bmd-form-group">
      <label for="InvestorName" class="bmd-label-floating">Investor Name <span class="mandatory"></span></label>
      <input type="text" class="form-control" id="InvestorName" name="InvestorName" />
    </div>
  </div>


  <div class="col-md-3">
   <div class="form-group bmd-form-group">
    <label for="AddressLine1" class="bmd-label-floating">AddressLine1<span class="mandatory"></span></label>
    <input type="text" class="form-control" id="AddressLine1" name="AddressLine1" />
  </div>
</div>


</div>
<div class="row mt-10">

  <div class="col-md-3">
  <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">AddressLine2</label>
    <input type="text" class="form-control" id="AddressLine2" name="AddressLine2" />
  </div>
</div>



  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">Zipcode<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="ZipCode" name="ZipCode" />
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">CityName<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="CityName" name="CityName" />
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">StateName<span class="mandatory"></span></label>
      <input type="text" class="form-control" id="StateName" name="StateName" />
    </div>
  </div>            
</div>

 <div class="row mt-20">
              <div class="col-md-3">
                <div class="togglebutton">
                  <label class="label-color"> Active
                    <input type="checkbox" id="Active" name="Active" class="Active" >
                    <span class="toggle"></span>
                  </label>
                </div>
              </div>  
    </div>


<div class="ml-auto text-right">
 <a href="<?php echo base_url('Investor'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
 <button type="submit" class="btn btn-fill btn-save btn-wd saveinvestor" name="saveinvestor"><i class="icon-floppy-disk pr-10"></i>Save Investor</button>
</div>
</form>
</div>
</div>
<script type="text/javascript">

	$(document).ready(function(){

   $(document).off('click','.saveinvestor').on('click','.saveinvestor', function(e) {
     var formdata = $('#investorform').serialize();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();
     $.ajax({
      type: "POST",
      url: "Investor/Saveinvestor",
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







