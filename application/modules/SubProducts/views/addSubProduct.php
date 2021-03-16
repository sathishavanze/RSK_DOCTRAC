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
<div class="card" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">SubProduct
    </div>
  </div>
  <div class="card-body">


    <div class="tab-content tab-space">

      <div class="tab-pane active" id="tab-Subproduct">
        <form action="#"  name="SubProduct_form" id="SubProduct_form" method="POST">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="SubProductName" class="bmd-label-floating">SubProduct Name <span class="mandatory"></span></label>
                <input type="text" class="form-control" id="SubProductName" name="SubProductName" />
              </div>
            </div>


            <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="SubProductCode" class="bmd-label-floating">SubProduct Code <span class="mandatory"></span></label>
                <input type="text" class="form-control" id="SubProductCode" name="SubProductCode" />
              </div>
            </div>


          <div class="col-md-4">
            <div class="form-group bmd-form-group">
              <label for="ProductUID" class="bmd-label-floating">Product Name<span class="mandatory"></label>
                <select class="select2picker form-control"  id="ProductUID" name="ProductUID">
                  <option value=""></option>
                  <?php
                  foreach ($Products as $key => $value) { ?>
                      <option value="<?php echo $value->ProductUID; ?>"><?php echo $value->ProductName; ?></option>
                      <?php
                  }
                ?>                 
              </select>
            </div>                                
          </div>
          </div>



        <div class="row">
          <div class="ml-auto text-right">
           <a href="<?php echo base_url('SubProducts'); ?>" class="btn btn-fill btn-dribbble btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
           <button type="submit" class="btn btn-fill btn-dribbble btn-wd addSubProduct" name="addSubProduct"><i class="icon-floppy-disk pr-10"></i>Save SubProduct</button>
         </div>
       </div>

     </form>
   </div>


 </div>

</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

  $(document).ready(function(){

    $('.select2picker').select2({

    });

    $(document).off('click','.addSubProduct').on('click','.addSubProduct', function(e) {      
      var formdata = $('#SubProduct_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('SubProducts/SaveSubProduct'); ?>",
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

            triggerpage('<?php echo base_url();?>SubProducts');

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







