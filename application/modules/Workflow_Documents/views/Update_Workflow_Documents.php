<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>
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
      <div class="card-icon"><?php echo $WorkFlowDetails->WorkflowModuleName;?> Document
      </div>
   </div>
   <div class="card-body spinnerclass">
      <form action="#"  name="Workflow_form" id="Workflow_form">
         <input type="hidden" class="form-control" id="WorkflowModuleUID" name="WorkflowModuleUID" value="<?php echo $WorkFlowDetails->WorkflowModuleUID;?>" />
         <div class="row">
          <div class="col-md-12 fileentry">
            <form  name="formfile"  class="formfile">
              <input type="file" name="file" id="file_entry" class="dropify" value="">
            </form>           
          </div>
         </div>
         <div class="row">
            <div class="ml-auto text-right">
               <a href="<?php echo base_url('Workflow_Documents'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
               <button type="submit" class="btn btn-fill btn-update btn-wd btn-upload" name="btn-upload"><i class="icon-floppy-disk pr-10"></i>Upload</button>
            </div>
         </div>
      </form>

      <?php if(!empty($WorkflowDocuments)){ ?>
        <h4 class="card-title" style="padding-left:5px;padding-bottom:5px;">Loan Files</h4>
        <!-- <label class="bmd-label-floating"> Order Files </label> -->
        <table class="table">
          <thead>
            <th>Document Name</th>
            <th>Uploaded Date Time</th>
            <th>Uploaded User</th>
            <th style="width:80px;text-align:center;">Action</th>
          </thead>  
          <tbody>
            <?php                
            foreach($WorkflowDocuments as $Document){ ?>
              <tr>
                <td>
                  <?php echo $Document->DocumentName; ?>
                </td>
                <td>
                  <?php echo $Document->UploadedDateTime; ?>
                </td>
                <td>
                  <?php echo $Document->UploadedUser; ?>
                </td>
                <td style="text-align:center;">
                  <a target="_blank" href="<?php echo base_url().$Document->DocumentURL; ?>" type="button" class=" viewFile" title="View" style="float: left; margin-left: 10px;"><img src="assets/img/icon.png" style="max-width: 21px;margin-top: 2px;"></a>

                  <!-- <a target="_blank" title="" href="<?php echo base_url().$Document->DocumentURL; ?>" class="btn btn-sm btn-xs viewFile" style="background-color: #f2f2f2;color: #000;"><span class="mdi mdi-eye"></span>  View</a> -->
                  <a class="removeFile" data-id="" title="Delete Document" style="cursor: pointer; float: left;margin-left: 5px;" data-documentuid="<?php echo $Document->DocumentUID; ?>">
                    <i class="fa fa-trash" aria-hidden="true" style="font-size: 24px; color: red;"></i>
                  </a>                 
                </td>
              </tr>
            <?php } ?>
          </tbody>    
        </table>
      <?php } ?>
   </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
   $(document).ready(function(){

      // Initialize file upload
      $('#file_entry').dropify(); 

      // Upload file
      $(document).off('click',  '.btn-upload').on('click',  '.btn-upload', function (event) {
        event.preventDefault();
        button = $(this);
        button_text = $(this).html();
        var WorkflowModuleUID = $('#WorkflowModuleUID').val();

        if($('#file_entry')[0].files.length === 0) {
          $.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
          return false;
        }

        var file_data = $('#file_entry').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('WorkflowModuleUID',WorkflowModuleUID);

        $.ajax({
          type: "POST",
          url: 'Workflow_Documents/Upload_Workflow_Document',
          data: form_data,
          processData: false,
          contentType: false,
          cache:false,
          dataType:'json',
          beforeSend: function(){         
          // $('.btn-upload').addClass("be-loading-active");
          button.attr("disabled", true);
          button.html('Loading ...'); 
        },
        success: function(data)
        {         

          if(data.status == 0) {

            $.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:3000 });
            button.html('Refreshing ...');   
            setTimeout(function(){location.href=location.href} , 5000);        

          } else if(data.status==1) {

            $.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });

            button.html('Upload');
            button.removeAttr("disabled");
          }                     
        },
        error: function (jqXHR, textStatus, errorThrown) {          
          button.html('Upload');
          button.removeAttr("disabled");
        },
      });
    });

    $('.removeFile').on('click',function(e){

      var DocumentUID = $(this).attr('data-documentuid');
      var WorkflowModuleUID = $('#WorkflowModuleUID').val();
      console.log(DocumentUID);
      var button = $(this);

      $.ajax({
        url: '<?php echo base_url('Workflow_Documents/DeleteWorkflowDocument');?>',
        data: {'DocumentUID':DocumentUID,'WorkflowModuleUID':WorkflowModuleUID},
        type:"POST",
        cache:false,
        dataType:'json',
        beforeSend: function(){
          $('.spinnerclass').addClass('be-loading-active');
        },
        success: function(data)
        {
          if(data.status == 0) {

            $.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:3000 });
            
            setTimeout(function(){location.href=location.href} , 3000);

          } else if(data.status==1) {

            $.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
          }   
          $('.spinnerclass').removeClass('be-loading-active');

        },
        error: function(jqXHR, textStatus, errorThrown)
        {
          $('.spinnerclass').removeClass('be-loading-active');

        },
      });

    });
   
  });
</script>