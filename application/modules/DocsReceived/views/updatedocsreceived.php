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
		<div class="card-icon">Received document details
		</div>
	</div>
	<div class="card-body">


				<form action="#"  name="template_form" id="template_form">
					<input type="hidden" class="form-control" id="DocumentUID" name="DocumentUID" value="<?php echo $Docdetails->DocumentUID;?>" />
          <input type="hidden" class="form-control" id="EmailUID" name="EmailUID" value="<?php echo $Docdetails->EmailUID;?>" />
         <div class="row">
          <div class="col-md-6">
            <div class="form-group bmd-form-group">
              <label for="RecipientEmail" class="bmd-label-floating"> Recipient Email </label>
              <input type="text" class="form-control" id="RecipientEmail"  value="<?php echo $Docdetails->RecipientEmail;?>" name="RecipientEmail" />
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group bmd-form-group">
              <label for="Subject" class="bmd-label-floating">Subject</label>
              <input type="text" class="form-control" id="Subject"  value="<?php echo $Docdetails->EmailSubject;?>" name="EmailSubject" required>
             
            </div> 
          </div>
        </div>
      <div class="row">
         <div class="col-md-6">
          <div class="form-group bmd-form-group">
           <label for="Body" class="bmd-label-floating">Body</label>
           <!-- <input type="text" class="form-control" id="Body" name="Body" required> -->
           <textarea id="Body" name="EmailBody"><?php echo $Docdetails->EmailBody;?></textarea>
        </div> 
      </div>
  </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group bmd-form-group">
            <label for="Uploadfile"> Upload File </label>
            <input type="file" name="file" class="form-control" id="Uploadfile" >
        </div>
      </div>
    </div>
      
    <div class="col-sm-12 form-group pull-right">
      <p class="text-right mb-0">
        <a href="<?php echo base_url('DocsReceived') ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
       <button type="button" class="btn btn-update btn-social btn-color btn-twitter editdocsreceived " value="1"><i class="icon-floppy-disk pr-10"></i>Update Email</button>
     </p>
   </div>

 </form>
</div>




</div>

</div>
</div>
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>


<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>

<script type="text/javascript">

  $(document).ready(function()
  {
    tinymce.init({
      selector: "textarea#Body",
      theme: "modern",
      height: 450,       
    });

    $(document).off('click','.editdocsreceived').on('click','.editdocsreceived',function(e)
    {
      $('#Body').html( tinymce.get('Body').getContent() );
      var form_data = new FormData($("#template_form")[0]);
      button = $(this);
      button_val = $(this).val();
      button_text = $(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('DocsReceived/UpdateDocsReceived'); ?>",
        data: form_data,
        dataType:'JSON',
        processData: false,
        contentType: false,
        cache:false,
        beforeSend: function () {
         button.attr("disabled", true);
         button.html('<i class="fa fa-spinner fa-spin"></i> Saving ...'); 
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
            triggerpage('<?php echo base_url();?>DocsReceived');
          }, 3000); 
        }
        button.html(button_text);
        button.val(button_val);
        button.removeAttr("disabled");

      }
    });

    });


  });

</script>
