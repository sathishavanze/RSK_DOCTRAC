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
		<div class="card-icon">Permission
		</div>
	</div>
	<div class="card-body">

				<form action="#"  name="Permissions_form" id="Permissions_form">
    <div class="row">
          <div class="col-md-3">
          <div class="form-group bmd-form-group">
            <label for="ResourceUID" class="bmd-label-floating">Resource<span class="mandatory"></span></label>
            <select class="select2picker form-control"  id="ResourceUID" name="ResourceUID">
           <option value=""></option>
           <?php foreach ( $GetResources as $key => $value) { ?>
           <option value="<?php echo $value->ResourceUID; ?>"><?php echo $value->FieldName; ?></option>
           <?php } ?>               
         </select>
          </div>
        </div>
        <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="PermissionName" class="bmd-label-floating">Permission Name<span class="mandatory"></span></label>
            <input type="text" class="form-control" id="PermissionName" name="PermissionName" />
            
          </div>
        </div>
        <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="SectionName" class="bmd-label-floating">Section Name</label>
            <input type="text" class="form-control" id="SectionName" name="SectionName" />
          </div>
        </div>
         <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="PermissionFieldName" class="bmd-label-floating">Field Name<span class="mandatory"></span></label>
            <input type="text" class="form-control" id="PermissionFieldName" name="PermissionFieldName" />
          </div>
        </div>
    </div>
<div class="row mt-10">
</div>
 <div class="ml-auto text-right">
          <a href="<?php echo base_url('Permissions'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-save btn-wd SavePermissions" name="SavePermissions"><i class="icon-floppy-disk pr-10"></i>Save Permissions</button>
          </div>



<!-- <div class="col-sm-12 form-group pull-right"> 
  <p class="text-right">
    <a href="<?php echo base_url('Permissions'); ?>" class="btn btn-danger back" role="button" aria-pressed="true"><i class="icon-arrow-left16"></i>&nbsp; Back</a>
    <button type="button" class="btn btn-space btn-social btn-color btn-twitter SavePermissions" value="1"><i class="icon-floppy-disk pr-10"></i>SAVE Permissions</button>
  </p>
</div> -->

</form>


</div>
</div>


<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){
   $(document).off('click','.SavePermissions').on('click','.SavePermissions', function(e) {
    var formdata = $('#Permissions_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Permissions/SavePermissions'); ?>",
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

            triggerpage('<?php echo base_url();?>Permissions');

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


});
</script>






