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
    <div class="card-icon">Create Email Template
    </div>
  </div>
  <div class="card-body">


        <form action="#"  name="template_form" id="template_form" class="mb-0">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group">
                <label for="EmailTemplateName" class="bmd-label-floating">Email Template Name</label>
                <input type="text" class="form-control" id="EmailTemplateName" name="EmailTemplateName" />
              </div>
            </div>
          </div>
         <!--  <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group">
                <label for="ToMailID" class="bmd-label-floating">To Mail ID<span class="mandatory"></span></label>
                <input type="text" class="form-control" id="ToMailID" name="ToMailID" />
              </div>
            </div>              
          </div> -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group">
                <label for="Subject" class="bmd-label-floating">Subject</label>
                <input type="text" class="form-control" id="Subject" name="Subject" required>
                
              </div> 
            </div>
          </div>

        <div class="row">
         <div class="col-md-6">
          <div class="form-group bmd-form-group">
           <label for="Body" class="bmd-label-floating">Body</label>
           <!-- <input type="text" class="form-control" id="Body" name="Body" required> -->
           <textarea id="Body" name="Body"></textarea>
        </div> 
      </div>
  </div>

  <div class="col-sm-12 form-group pull-right">
    <p class="text-right mb-0">
       <a href="<?php echo base_url('Emailtemplate') ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
      <button type="button" class="btn btn-save btn-social btn-color btn-twitter addtemplate" id="BtnSend" value="1"><i class="icon-floppy-disk pr-10"></i>Save template</button>
    </p>
  </div>

</form>

</div>
</div>

<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
  

  $(document).ready(function(){
    
    $('.select2').select2({
      theme: "bootstrap",
    });
    
    tinymce.init({
      selector: "textarea#Body",
      theme: "modern",
      height: 450,       
    });

    $(document).off('click','.addtemplate').on('click','.addtemplate',function(e)
    {
      e.preventDefault();
      button = $('#BtnSend');
      button_text = button.html();
      $('#Body').html( tinymce.get('Body').getContent() );

      var form_data = new FormData($("#template_form")[0]);      
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Emailtemplate/SaveEmailtemplate',
        data: form_data,
        dataType : 'JSON',
        processData: false,
        contentType: false,
        cache:false,
        beforeSend: function() {
        	button.attr("disabled", true);
        	button.html('<i class="fa fa-spinner fa-spin"></i> Saving ...'); 
				},
        success: function(data)
        {  
          if(data['validation_error'] == 0){
           $.notify(
            {
              icon:"icon-bell-check",
              message:data['message']
            },
            {
              type:"success",
              delay:1000 
            }); 
            setTimeout(function(){   location.href='Emailtemplate/EditEmailtemplate/'+data['templateuid']+'';  }, 1000);
          }else{
            $.notify(
            {
              icon:"icon-bell-check",
              message:data['message']
            },
            {
              type:"danger",
              delay:1000 
            });
           	button.html(button_text);
            button.removeAttr("disabled");
          }
        },
      });
    });


  });
</script>
