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
		<div class="card-icon">Resources
		</div>
	</div>
	<div class="card-body">

				<form action="#"  name="Resources_form" id="Resources_form">
    <div class="row">
          <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="controller" class="bmd-label-floating">Controller <span class="mandatory"></span></label>
            <input type="text" class="form-control" id="controller" name="controller" />
          </div>
        </div>
        <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="FieldName" class="bmd-label-floating">Field Name<span class="mandatory"></span></label>
            <input type="text" class="form-control" id="FieldName" name="FieldName" />
            
          </div>
        </div>
        <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="FieldSection" class="bmd-label-floating">Field Section<span class="mandatory"></span></label>
            <input type="text" class="form-control" id="FieldSection" name="FieldSection" />
          </div>
        </div>
         <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="IconClass" class="bmd-label-floating">Icon Class</label>
            <input type="text" class="form-control" id="IconClass" name="IconClass" />
          </div>
        </div>
         <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="Position" class="bmd-label-floating">Position</label>
            <input type="number" class="form-control" id="Position" name="Position" />
          </div>
        </div>

                <div class="col-md-3">
                 <div class="form-group bmd-form-group">
                  <label for="MenuBarType" class="bmd-label-floating">Menu Bar<span class="mandatory"></span></label>
                  <select class="select2picker form-control" id="MenuBarType" name="MenuBarType"> 
                    <option value=""></option>
                    <option value="sidebar">Sidebar</option>
                    <option value="jumbobar">Jumbobar</option>
                    <option value="common">Common</option>
                    <option value="submenu1">Submenu1</option>
                    <option value="submenu2">Submenu2</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3 workflow_div" style="display: none;">
               <div class="form-group bmd-form-group">
                <label for="WorkflowModuleUID" class="bmd-label-floating">Workflow<span class="mandatory"></span></label>
                <select class="select2picker form-control" id="WorkflowModuleUID" name="WorkflowModuleUID"> 
                	<option value="NA">N/A</option>
                  <?php foreach ($workflows as $key => $value) 
                  { ?>
                   <option value="<?php echo $value->WorkflowModuleUID ;?>"><?php echo $value->WorkflowModuleName ?></option>
                 <?php }?>
               </select>
             </div>
           </div>

            <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="NotificationEle" class="bmd-label-floating">Notification Element</label>
            <input type="text" class="form-control" id="NotificationEle" name="NotificationEle" />
          </div>
        </div>
        <div class="col-md-3">
               <div class="form-group bmd-form-group">
                <label for="ParentType" class="bmd-label-floating">MasterMenu</label>
                <input type="text" class="form-control" id="ParentType" name="ParentType" value="<?php echo $UpdateResources->ParentType ?>"/>
              </div>
            </div>

        <div class="col-md-3">
          <label for="CustomerUID" class="bmd-label-floating">Clients</label>
          <select class="select2picker CustomerUID" id="CustomerUID" autocomplete="off" name="CustomerUID[]" multiple="multiple" style="width: 100% !important;">
            <?php foreach ($CustomerDetails as $key => $value) { ?>
              <option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName;?></option>
            <?php } ?>
          </select>
        </div>

    </div>

 <div class="ml-auto text-right">
          <a href="<?php echo base_url('Resources'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
          <button type="submit" class="btn btn-fill btn-save btn-wd SaveResource" name="SaveResource"><i class="icon-floppy-disk pr-10"></i>Save Resources </button>
          </div>



<!-- <div class="col-sm-12 form-group pull-right"> 
  <p class="text-right">
    <a href="<?php echo base_url('Resources'); ?>" class="btn btn-danger back" role="button" aria-pressed="true"><i class="icon-arrow-left16"></i>&nbsp; Back</a>
    <button type="button" class="btn btn-space btn-social btn-color btn-twitter SaveResource" value="1">SAVE Resources</button>
  </p>
</div> -->

</form>
</div>


</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){
   $(document).off('click','.SaveResource').on('click','.SaveResource', function(e) {
    var formdata = $('#Resources_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Resources/SaveResources'); ?>",
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

            triggerpage('<?php echo base_url();?>Resources');

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
   
 $(document).off('keyup','#FieldSection').on('keyup','#FieldSection', function(e) 
 {
  if($(this).val().toUpperCase() == 'ORDERWORKFLOW')
  {
    $('.workflow_div').show();
  }
  else
  {
    $('.workflow_div').hide();
  }
 });

});
</script>






