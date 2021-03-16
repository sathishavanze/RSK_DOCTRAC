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
		<div class="card-icon">Hoi Company
		</div>
	</div>
	<div class="card-body">

   <form action="#"  name="Company_form" id="Company_form">
     <input type="hidden" class="form-control" id="CompanyUID" name="CompanyUID" value="<?php echo $CompanyDetails->CompanyUID;?>" />
     <div class="row">

      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="CompanyName" class="bmd-label-floating">Company Name <span class="mandatory"></span></label>
          <input type="text" class="form-control" id="CompanyName"  value="<?php echo $CompanyDetails->CompanyName;?>" name="CompanyName" />
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="FolderName" class="bmd-label-floating">Folder Name </label>
          <input type="text" class="form-control" id="FolderName"  value="<?php echo $CompanyDetails->FolderName;?>" name="FolderName" />
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="Website" class="bmd-label-floating">Website </label>
          <input type="text" class="form-control" id="Website"  value="<?php echo $CompanyDetails->Website;?>" name="Website" />
          </div>
        </div> 
      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="Email" class="bmd-label-floating">Email </label>
          <input type="text" class="form-control" id="Email"  value="<?php echo $CompanyDetails->Email;?>" name="Email" />
          </div>
        </div> 
    
      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="FaxNo" class="bmd-label-floating">FaxNo </label>
          <input type="text" class="form-control" id="FaxNo"  value="<?php echo $CompanyDetails->FaxNo;?>" name="FaxNo" />
          </div>
        </div> 
      
      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="ContactNo" class="bmd-label-floating">ContactNo </label>
          <input type="text" class="form-control" id="ContactNo"  value="<?php echo $CompanyDetails->ContactNo;?>" name="ContactNo" />
          </div>
        </div> 
    
      <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="MortgageOption" class="bmd-label-floating">Mortgage Option </label>
          <input type="text" class="form-control" id="MortgageOption"  value="<?php echo $CompanyDetails->MortgageOption;?>" name="MortgageOption" />
          </div>
        </div> 


        <div class="col-md-6">
        <div class="form-group bmd-form-group">
          <label for="MortgageCode" class="bmd-label-floating">Mortgage Code </label>
          <input type="text" class="form-control" id="MortgageCode"  value="<?php echo $CompanyDetails->MortgageCode;?>" name="MortgageCode" />
          </div>
        </div> 


        <div class="col-md-6">
          <div class="form-group bmd-form-group">
            <label for="Username" class="bmd-label-floating">Username </label>
            <input type="text" class="form-control" id="Username"  value="<?php echo $CompanyDetails->Username;?>" name="Username" />
          </div>
        </div> 
         <div class="col-md-6">
          <div class="form-group bmd-form-group">
            <label for="Password" class="bmd-label-floating">Password </label>
            <input type="text" class="form-control" id="Password"  value="<?php echo $CompanyDetails->Password;?>" name="Password" />
          </div>
        </div> 
         <div class="col-md-6">
          <div class="form-group bmd-form-group">
            <label for="LineOfBusiness" class="bmd-label-floating">Line Of Business </label>
            <input type="text" class="form-control" id="LineOfBusiness"  value="<?php echo $CompanyDetails->LineOfBusiness;?>" name="LineOfBusiness" />
          </div>
        </div> 
         <div class="col-md-6">
          <div class="form-group bmd-form-group">
            <label for="WhoPays" class="bmd-label-floating">Who Pays </label>
            <input type="text" class="form-control" id="WhoPays"  value="<?php echo $CompanyDetails->WhoPays;?>" name="WhoPays" />
          </div>
        </div> 

      </div>

      <div class="row">          
        <div class="col-md-4" >
          <div class="togglebutton">
            <label class="label-color"> Active:  
              <input type="checkbox" id="Active" name="Active" class="Active" <?php if($CompanyDetails->Active == 1){ echo "checked"; } ?>>
              <span class="toggle"></span>
            </label>
          </div>
        </div>
      </div>

      <div class="col-md-12">
       <div class="ml-auto text-right">
        <a href="<?php echo base_url('HoiCompanies'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
        <button type="submit" class="btn btn-fill btn-update btn-wd btn-editProduct" name="btn-editProduct"><i class="icon-floppy-disk pr-10"></i>Update Company </button>
      </div>
    </div>

  </form>
  </div>
</div>




<script type="text/javascript">


	$(document).ready(function(){
    
    $(document).off('click','.btn-editProduct').on('click','.btn-editProduct', function(e) {


      var formdata = $('#Company_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('HoiCompanies/UpdateCompany'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {
         button.prop("disabled", true);
         button.html('<i class="fa fa-spin fa-spinner"></i> Loading ...');
         button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');

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

            triggerpage('<?php echo base_url();?>HoiCompanies');

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







