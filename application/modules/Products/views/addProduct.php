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
    <div class="card-icon">Product
    </div>
  </div>
  <div class="card-body">



        <form action="#"  name="Product_form" id="Product_form" method="POST">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="ProductCode" class="bmd-label-floating">Product Code <span class="mandatory"></span></label>
                <input type="text" class="form-control" id="ProductCode" name="ProductCode" />
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="form-group bmd-form-group">
                <label for="ProductName" class="bmd-label-floating">Product Name <span class="mandatory"></span></label>
                <input type="text" class="form-control" id="ProductName" name="ProductName" />
              </div>
            </div>
            
            <div class="col-nd-2" style="margin-top: 20px; margin-left: 15px;"> 
              <div class="form-check">
                <label class="form-check-label Dashboardlable" for="OcrEnable">
                  <input class="form-check-input" id="OcrEnable" type="checkbox" value="" name="OcrEnable" data-attr="" checked> 
                  Is OCR
                  <span class="form-check-sign">
                    <span class="check"></span>
                  </span>
                </label>
              </div>
            </div>            

            <?php if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
              <div class="col-md-2">  
                <div class="form-group  bmd-form-group">
                 <label for="CustomerUID" class="bmd-label-floating">Client</label>
                  <select class="select2picker"  id="CustomerUID"  name="CustomerUID" style="width: 100%;">
                    <option value=""></option>
                    <?php foreach ($CustomerDetails as $row)  { ?>  
                      <option value="<?php echo $row->CustomerUID; ?>"><?php echo $row->CustomerName; ?></option>
                    <?php } ?>
                  </select>
                </div>          
              </div>
            <?php } ?>

          </div>

          <div class="row">

            <div class="col-md-12 mt-10">
              <div class="form-group bmd-form-group">
                <label for="productdoctype" class="bmd-label-floating">Product Doc Types<span class="mandatory"></label>
                  <select class="select2picker form-control"  id="productdoctype" name="productdoctype[]" multiple>
                    <option value=""></option>
                    <?php foreach ($InputDocTypes as $key => $value) { ?>
                      <option value="<?php echo $value->InputDocTypeUID; ?>"><?php echo $value->DocTypeName; ?></option>
                    <?php } ?>                
                  </select>
                </div>
              </div> 
          </div>



      <div class="row">
        <div class="col-md-12">
          <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Import Rules </h4>
        </div>
        <div class="col-md-6">
          <div class="form-group bmd-form-group">
            <label for="ddlRules" class="bmd-label-floating">Rules </label>
            <select class="select2picker form-control"  id="ddlRules" name="ddlRules">
              <option></option>
              <?php foreach ($Rules as $key => $rule) { ?>
                <option value="<?php echo $rule->RuleUID; ?>" data-rulename = "<?php echo $rule->RuleName; ?>"><?php echo $rule->RuleName; ?></option>
              <?php } ?>                
            </select>
          </div>
        </div> 
        <div class="col-md-6" style="margin-top: 11px;">
          <button type="button" class="btn btn-success btn-round addrule"><i class="icon-plus22"></i> Add</button>
        </div>
        <div class="col-md-12 mt-10">
          <table class="table table-bordered" id="tblProductRules" style="display: none;">
            <thead>
              <tr>
                <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
                <th style="padding: 3px 3px !important;" class="text-left">Rule</th>
                <th style="padding: 3px 3px !important;" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>


        <div class="row">
          <div class="ml-auto text-right">
           <a href="<?php echo base_url('Products'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
           <button type="submit" class="btn btn-fill btn-save btn-wd addProduct" name="addProduct"><i class="icon-floppy-disk pr-10"></i>Save Product</button>
         </div>
       </div>

     </form>


</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

  $(document).ready(function(){

    $('.select2picker').select2({

    });

    $(document).off('click','.addProduct').on('click','.addProduct', function(e) {      
      var formdata = $('#Product_form').serialize();
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Products/SaveProduct'); ?>",
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

            triggerpage('<?php echo base_url();?>Products');

          }, 3000);
        }
        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    });

    });


    $(document).off('click', '.addrule').on('click', '.addrule', function (e) {
      
      e.preventDefault();


      var currentrule = $('#ddlRules').find('option:selected');

      console.log($('#ddlRules').find('option:selected'));
      if ($(currentrule).val()) {
        var ruleuid = $(currentrule).val();
        var rulename = $(currentrule).attr('data-rulename');

        var sno = $('#tblProductRules tbody tr').length;

        var appendrow = `<tr>
        <input type="hidden" name="Rules[]" value="` + ruleuid + `">
        <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>
        <td class="text-left">` + rulename + `</td>
        <td class="text-center"><button data-ruleuid = "`+ ruleuid +`" data-rulename = "`+ rulename +`" class="btn btn-pinterest removerules"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
        </tr>`;

        $(currentrule).remove();

        $('#tblProductRules').slideDown('slow');
        $('#tblProductRules tbody').append(appendrow);

        $('#tblProductRules tbody tr').each(function (key, row) {
          $(row).find('td:first').html(key + 1);
        });

        callselect2();


      }
    });

    $(document).off('click', '.removerules').on('click', '.removerules', function (e) {
      
      e.preventDefault();

      var rules = {};

      rules.RuleUID = $(this).attr('data-ruleuid');
      rules.RuleName = $(this).attr('data-rulename');

      if (rules.RuleUID) {

        var appendoption = `<option value="`+rules.RuleUID+`" data-rulename = "`+rules.RuleName+`">`+rules.RuleName+`</option>`;
        $('#ddlRules').append(appendoption);
        callselect2();

        $(this).closest('tr').remove();

      }
    });


  });
</script>







