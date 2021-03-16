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
 .toggle-password {
        float: right;
        margin-right: 6px;
        margin-top: -25px;
        position: relative;
        z-index: 2;
        
    }
</style>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">SFTP
		</div>
	</div>
	<div class="card-body">

      <form action="#"  name="user_form" id="user_form">
     <div class="row mt-10">
        <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="username" class="bmd-label-floating">SFTP Name<span class="mandatory"></span></label>
          <input type="text" class="form-control" id="SFTPName" name="SFTPName" />
        </div>
      </div>
       <div class="col-md-3">
      <div class="form-group bmd-form-group">
       <label for="PriorityUID" class="bmd-label-floating">SFTP Protocol<span class="mandatory"></span></label>
       <select class="select2picker form-control PriorityUID"  id="SFTPProtocol" name="SFTPProtocol" required>
        <option value=""></option>
        <option value="SFTP">SFTP</option>
        <option value="FTP">FTP</option>
      </select>
    </div>
  </div>
  <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPHost" class="bmd-label-floating">SFTP Host<span class="mandatory"></span></label>
          <input type="text" class="form-control" id="SFTPHost" name="SFTPHost" />
        </div>
      </div>
 <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPPort" class="bmd-label-floating">SFTP Port</label>
          <input type="number" class="form-control" id="SFTPPort" name="SFTPPort" />
        </div>
      </div>
    </div>
    <div class="row mt-10">
       <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPUser" class="bmd-label-floating">SFTP User<span class="mandatory"></span></label>
          <input type="text" class="form-control" id="SFTPUser" name="SFTPUser" />
        </div>
      </div>
       <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPPassword" class="bmd-label-floating">SFTP Password<span class="mandatory"></span></label>
          <input type="password" class="form-control password-field" id="SFTPPassword" name="SFTPPassword"  />
          <span toggle=".password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password" style="font-size:12px"></span>
        </div>
      </div>
       <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPKeyFile" class="bmd-label-floating">SFTPKey File</label>
          <input type="text" class="form-control" id="SFTPKeyFile" name="SFTPKeyFile" />
        </div>
      </div>

      <div class="col-md-3">
      <div class="form-group bmd-form-group">
       <label for="EmailTemplateUID" class="bmd-label-floating">EmailTemplateUID<span class="mandatory"></span></label>
       <select class="form-control select2picker" id="EmailTemplateUID" name="EmailTemplateUID"    aria-haspopup="true" aria-expanded="false" required>
            <option></option>
           <?php 
           $EmailTemplate=$this->db->get('mEmailTemplate');

            foreach($EmailTemplate->result() as $row){?>
             
           <option value="<?php echo $row->EmailTemplateUID; ?>" ><?php echo $row->EmailTemplateName ?></option>

         <?php } ?>

           
          </select>
    </div>
  </div>
</div>
    <div class="row mt-10">
      <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="SFTPPath" class="bmd-label-floating">SFTP Path</label>
          <input type="text" class="form-control" id="SFTPPath" name="SFTPPath" />
        </div>
      </div>
      </div>

               


<!-- <div class="col-sm-12 form-group pull-right">
  <p class="text-right">
  <button type="button" class="btn btn-space btn-social btn-color btn-twitter adduser" value="1">SAVE DOCUMENT</button>
 </p>
</div> -->

           <div class="ml-auto text-right">
          <a href="<?php echo base_url('SFTP'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
           <button type="button" class="btn btn-fill btn-info btn-wd testsftp" name="testsftp"><i class="fa fa-share-square pr-10"></i>Test Connection</button>

          <button type="submit" class="btn btn-fill btn-save btn-wd addsftp" name="addsftp"><i class="icon-floppy-disk pr-10"></i>Save SFTP</button>
          </div>
</form>
</div>

</div>
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){

   $(".toggle-password").mousedown(function() {

      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      input.attr("type", "text");
 
    
  
});
    $(".toggle-password").mouseup(function() {

      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      input.attr("type", "password");
  
});


    $(document).off('click','.addsftp').on('click','.addsftp', function(e) {
     var formdata = $('#user_form').serialize();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();
     $.ajax({
      type: "POST",
      url: "<?php echo base_url('SFTP/SaveSFTP'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading ...');
        button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
      },
      success: function (response) {
        if(response.Status == 2)
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

            triggerpage('<?php echo base_url();?>SFTP');

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

    $('.testsftp').click(function()
    {

       var formdata = $('#user_form').serialize();
       button = $(this);
       button_val = $(this).val();
       button_text = $(this).html();
       $.ajax(
       {
          type: "POST",
          url: "<?php echo base_url('SFTP/TestSFTP'); ?>",
          data: formdata,
         
          beforeSend: function () 
          {
            button.prop("disabled", true);
            button.html('Loading ...');
            button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
          },
         success: function (response) 
         {
          
           if(response == 1)
           {
              swal(
                {
                  title : 'Success',
                  type : 'success',
                  text :'Test Connection Succeded',
                  timer :2000
                });
              }
              else
              {
                swal(
                {
                  title : 'Connection Failed',
                  type :  'warning',
                  text : 'Enter Valid  Details',
                  timer : 2000
                });
              }
                
              button.html(button_text);
              button.val(button_val);
              button.prop('disabled',false);

       }
       
     });
 
    });

    function log()
    {

      var loginid = $('#loginid').val();

      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
        data: {'loginid' : loginid},
        dataType:'json',
        success: function (response) {

          if(response.Status == 1)
          {

            $('#loginexists').show();
          }else{
           $('#loginexists').hide();
         }
       },
       error:function(xhr){

         console.log(xhr);
       }
     });

    }
  });
</script>







