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
		<div class="card-icon">Field
		</div>
	</div>
	<div class="card-body">




				<form action="#"  name="field_form" id="field_form">
					<input type="hidden" class="form-control" id="CustomerUID" name="FieldUID" value="<?php echo $FieldDetails->FieldUID;?>" />
         <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group">
              <label for="FieldName" class="bmd-label-floating">Field Name <span class="mandatory"></span></label>
              <input type="text" class="form-control" id="FieldName"  value="<?php echo $FieldDetails->FieldName;?>" name="FieldName" />
            </div>
          </div>

          <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="FieldType" class="bmd-label-floating">Field Type<span class="mandatory"></span></label>
                <select class="select2picker form-control"  id="FieldType" name="FieldType">
                  <option value=""></option>
                  <?php
                    $FieldType = $this->config->item('FieldsType');
                    foreach ($FieldType as $key => $value) {
                     if($key==''){
                     }
                     else{
                      if($key == $FieldDetails->FieldType)
                      {
                        ?>
                        <option value="<?php echo $key; ?>" selected><?php echo $value; ?></option>
                        <?php
                      }
                      else
                      {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php
                      }
                    }
                  }
                ?>                 
                </select>
              </div>                                
            </div>

            <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="FieldLabel" class="bmd-label-floating">Field Label <span class="mandatory"></span></label>
                <input type="text" class="form-control" id="FieldLabel" name="FieldLabel" value="<?php echo $FieldDetails->FieldLabel;?>" />
              </div>
            </div>
          </div>
          <div class="row mt-10">
            <div class="col-md-2">
              <div class="form-check">
                <label class="form-check-label">
                  <input class="form-check-input" type="checkbox" name="IsStacking" id="IsStacking" value="Stacking" <?php echo $FieldDetails->IsStacking == 1 ? "Checked" : "" ?>> Stacking
                  <span class="form-check-sign">
                    <span class="check"></span>
                  </span>
                </label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-check" >
                <label class="form-check-label">
                  <input class="form-check-input" type="checkbox" name="IsIndexing" id="IsIndexing" value="Stacking" <?php echo $FieldDetails->IsIndexing == 1 ? "Checked" : "" ?>> Indexing
                  <span class="form-check-sign">
                    <span class="check"></span>
                  </span>
                </label>
              </div>
            </div>

       <!--  </div>

        <div class="row"> -->
          <div class="col-md-2" >
            <div class="togglebutton">
              <label class="label-color"> Active  
                <input type="checkbox" id="Active" name="Active" class="Active" <?php if($FieldDetails->Active == 1){ echo "checked"; } ?>>
                <span class="toggle"></span>
              </label>
            </div>
          </div>
          </div>
     


   <div class="ml-auto text-right">
          <a href="<?php echo base_url('Fields'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-update btn-wd btn-editfield" name="btn-editfield"><i class="icon-floppy-disk pr-10"></i>Update Field </button>
          </div>

 </form>
</div>




</div>

</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">


	$(document).ready(function(){
    $('#ZipCode').change(function(event) {

      zipcode = $(this).val();

      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Customer/getzip',
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


   //   $(document).on('click','.Back',function()
   // {
   //    setTimeout(function(){ 

   //        triggerpage('<?php echo base_url();?>Customer');

   //      },50);
   // });

    
    $(document).off('click','.btn-editfield').on('click','.btn-editfield', function(e) {


      var formdata = $('#field_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Fields/UpdateField'); ?>",
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

            triggerpage('<?php echo base_url();?>Fields');

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







