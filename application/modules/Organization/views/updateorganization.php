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
  .picture {
    width:90px;
    height: 90px;
    background-color: #999999;
    border: 4px solid #CCCCCC;
    color: #FFFFFF;
    border-radius: 50%;
    margin: 5px auto;
    overflow: hidden;
    transition: all 0.2s;
    -webkit-transition: all 0.2s;
  }
  .picture-container {
    position: relative;
    cursor: pointer;
    text-align: center;
  }

  .picture-src {
    width: 100%;
  }

  .picture input[type="file"] {
    cursor: pointer;
    display: block;
    height: 100%;
    left: 0;
    opacity: 0 !important;
    position: absolute;
    top: 0;
    width: 100%;
  }
  .pictureTitle {
    color: #999999;
    font-size: 11px;
  }
  .pictureTitle, .card-pictureTitle, .footer-big p {
    color: #999999;
  }
  h6, .h6 {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 500;
  }
  .pounds{
    padding: 3px;
    margin-left: 5px;
    line-height: 10px;
    margin-bottom: 0px;
    font-size: 11px;
    text-transform: lowercase;
  }
</style>
<link href="<?php echo base_url(''); ?>/assets/css/material-dashboard.css?v=2.0.2" rel="stylesheet" />
<link href="<?php echo base_url(''); ?>/assets/demo/demo.css" rel="stylesheet" />
<?php
if(!empty($organization->OrganizationLogo))
{
  $OrgLinkLogo = $organization->OrganizationLogo;
  $ConformLink = 'Yes';
}
else
{
  $OrgLinkLogo = '/assets/img/default-avatar.png';
  $ConformLink = 'No';
}
?>
<form action="#"  name="organization_form" id="organization_form">
  <div class="card mt-40 customcardbody " id="Orderentrycard">
    <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">ORGANIZATION
      </div> <input type="hidden" name="OrgLogo" id="OrgLogo" value="<?php echo $ConformLink; ?>">

    </div>
    <div class="card-body">
      <input type="hidden" class="form-control" id="OrganizationUID" name="OrganizationUID" value="<?php echo $organization->OrganizationUID ?>" />
      <div class="col-md-12">


        <div class="row">
          <div class="col-md-10">
            <div class="row mt-10">
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="OrganizationName" class="bmd-label-floating">Organization Name<span class="mandatory"></span></label>
                  <input type="text" class="form-control" id="OrganizationName" name="OrganizationName" value="<?php echo $organization->OrganizationName ?>" />
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="OrganizationAddress1" class="bmd-label-floating">Organization Address1</label>
                  <input type="text"  class="form-control" id="OrganizationAddress1" name="OrganizationAddress1" value="<?php echo $organization->OrganizationAddress1 ?>"/>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="OrganizationAddress2" class="bmd-label-floating">Organization Address2</label>
                  <input type="text" class="form-control" id="OrganizationAddress2" name="OrganizationAddress2" value="<?php echo $organization->OrganizationAddress2 ?>" />
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                 <label for="OrganizationPhoneNo" class="bmd-label-floating">Organization PhoneNo</label>
                 <input type="number" class="form-control" id="OrganizationPhoneNo" name="OrganizationPhoneNo" value="<?php echo $organization->OrganizationPhoneNo ?>" required>
               </div> 
             </div>

           <!--  <div class="col-md-3">
              <div class="form-group bmd-form-group">
                <label for="OrganizationZib" class="bmd-label-floating">Organization Zib<span class="mandatory"></span></label>
                <input type="text" class="form-control" id="OrganizationZib" name="OrganizationZib" value="<?php echo $organization->OrganizationZib ?>"/>
              </div>
            </div> -->

          </div>

          <div class="row mt-10">
            <div class="col-md-3">
              <div class="form-group bmd-form-group">
               <label for="PropertyZipcode" class="bmd-label-floating">Zipcode<span class="mandatory"></span></label>
               <input type="text" class="form-control" id="PropertyZipcode" name="PropertyZipcode" required value="<?php echo $organization->OrganizationZib; ?>">
               <span data-modal="zipcode-form" class="label label-success label-zip md-trigger" id="zipcodeadd" style="display: none;">Add Zipcode</span>
             </div>
           </div>
           <div class="col-md-3">
            <div class="form-group bmd-form-group">
             <label for="PropertyCityName" class="bmd-label-floating">City<span class="mandatory"></span></label>
             <input type="text" class="form-control" id="PropertyCityName" name="PropertyCityName" value="<?php echo $organization->OrganizationCity; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
             <ul class="dropdown-menu dropdown-style PropertyCityName"></ul>
           </div>
         </div>
         <div class="col-md-3">
          <div class="form-group bmd-form-group">
           <label for="PropertyCountyName" class="bmd-label-floating">County<span class="mandatory"></span></label>
           <input type="text" class="form-control" id="PropertyCountyName" name="PropertyCountyName"  value="<?php echo $organization->OrganizationCounty; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
           <ul class="dropdown-menu dropdown-style PropertyCountyName"></ul>
         </div>
       </div>
       <div class="col-md-3">
        <div class="form-group bmd-form-group">
         <label for="PropertyStateCode" class="bmd-label-floating">State<span class="mandatory"></span></label>
         <input type="text" class="form-control" id="PropertyStateCode" name="PropertyStateCode"  value="<?php echo $organization->OrganizationState; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
         <ul class="dropdown-menu dropdown-style PropertyStateCode"></ul>
       </div>
     </div>

            <!-- <div class="col-md-3">
              <div class="form-group bmd-form-group">
                <label for="OrganizationCity" class="bmd-label-floating">Organization City</label>
                
                <input type="text" class="form-control" id="cityuid" name="cityuid" value="<?php echo $organization->OrganizationCity ?>" required>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group bmd-form-group">
                <label for="OrganizationCounty" class="bmd-label-floating">Organization County</label>

                <input type="text" class="form-control" id="CountyUID" name="CountyUID" value="<?php echo $organization->OrganizationCounty ?>" required>

              </div> 
            </div>

            <div class="col-md-3">
              <div class="form-group bmd-form-group">
                <label for="OrganizationState" class="bmd-label-floating">Organization State</label>
                <input type="text" class="form-control" id="stateuid" name="stateuid" value="<?php echo $organization->OrganizationState ?>" required>
              </div> 
            </div> -->            
          </div>



          <div class="row mt-10">
           <div class="col-md-3">
            <div class="form-group bmd-form-group">
             <label for="SMTPHost" class="bmd-label-floating">SMTP Host<span class="mandatory"></span></label>
             <input type="text" class="form-control" id="SMTPHost" name="SMTPHost" value="<?php echo $organization->SMTPHost ?>" required>
           </div> 
         </div>

         <div class="col-md-3">
          <div class="form-group bmd-form-group">
           <label for="SMTPUserName" class="bmd-label-floating">SMTP UserName<span class="mandatory"></span></label>
           <input type="text" class="form-control" id="SMTPUserName" name="SMTPUserName" value="<?php echo $organization->SMTPUserName ?>" required>
         </div> 
       </div>

       <div class="col-md-3">
        <div class="form-group bmd-form-group">
         <label for="SMTPPassword" class="bmd-label-floating">SMTP Password<span class="mandatory"></span></label>
         <input type="password" class="form-control password-field" id="SMTPPassword" name="SMTPPassword" value="<?php echo $organization->SMTPPassword ?>" required><span toggle=".password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password" style="font-size:12px"></span>
       </div> 
     </div>

     <div class="col-md-3">
      <div class="form-group bmd-form-group">
        <label for="SMTPPort" class="bmd-label-floating">SMTP Port<span class="mandatory"></span></label>
        <input type="number" class="form-control" id="SMTPPort" name="SMTPPort" value="<?php echo $organization->SMTPPort ?>" required>
      </div> 
    </div>
    <div class="col-md-3 mt-10">
      <div class="form-group bmd-form-group">
        <label for="BinCount" class="bmd-label-floating">Bin Count<span class="mandatory"></span></label>
        <input type="text" class="form-control" id="BinCount" name="BinCount" value="<?php echo $organization->BinCount ?>" required>
      </div> 
    </div>
    <div class="col-md-3 mt-10">
      <div class="form-group bmd-form-group">
        <label for="pageweight" class="bmd-label-floating">Page Weight <span class="badge badge-default pounds">lbs</span> <span class="mandatory"></span></label>
        <input type="number" class="form-control" id="pageweight" name="pageweight" value="<?php echo $organization->PageWeight ?>" required>
      </div> 
    </div>
  </div>
</div>
<div class="col-sm-2">
  <div class="picture-container mt-40">
    <div class="picture">
      <img src="<?php echo base_url().$OrgLinkLogo; ?>" class="picture-src" id="wizardPicturePreview" title="" />
      <input type="file"  name="organizationeditlogo[]" id="organizationeditlogo">
    </div>
    <h6 class="pictureTitle">Choose Picture</h6>
  </div>
</div>
</div>
</div>
<div class="col-sm-12 form-group text-right">

 <a href="<?php echo base_url('Organization') ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
 <button type="button" class="btn btn-dribbble btn-social btn-color btn-update updateorganization" value="1"><i class="icon-floppy-disk pr-10"></i>Update Organization</button>

</div>

</div>


</div>
</form>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function()
  {
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

   $('#OrganizationZib').change(function(event) {

    zipcode = $(this).val();

    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>Organization/getzip',
      data: {'zipcode':zipcode}, 
      dataType:'json',
      cache: false,
      success: function(data)
      {
        $('#cityuid').empty();
        $('#stateuid').empty();

        if(data != ''){

            //$('#cityuid').val(data[0].CityName);
            $('#cityuid').val( data[0]['CityName'] );
            $('#stateuid').val(data[0]['StateName']);
            $('#CountyUID').val(data[0]['CountyName']);

            $('#stateuid').parent().addClass('is-dirty');
            $('#cityuid').parent().addClass('is-dirty');
            $("#CountyUID").parent().addClass('is-dirty');
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

   var viewtaskarr  = [];     
   var filenotificationarr  = []; 
   var viewnotificationarr  = [];
   
   function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#organizationeditlogo").change(function() {
    readURL(this);
    var ele = document.getElementById($(this).attr('id'));
    var result = ele.files;
    $("#OrgLogo").val('No');
    for(var x = 0;x< result.length;x++){
      var fle = result[x];
      fsize  = Math.ceil(fle.size /  1024);
      var fname = fle.name;
      var ext = fname.split('.').pop();      
      var appendtxt  = "<tr><td>" + fle.name + "</td><td>" + fsize + " KB</td><td><i class='fa fa-trash text-danger deleteKnwicon' id= task-"+x+" style='padding-left:10px;cursor:pointer;font-size: 16px;' name='task'></i></td></tr>"

      $("#outputfilelogo table").append(appendtxt);
      var obj = {};
      obj["filename"] = fle.name;
      obj["filesize"] = fsize;
      obj["id"] = x;
      obj["type"] = "Task";
      viewtaskarr.push(obj);
      viewnotificationarr.push({file: fle, filename: fle.name });
    }            
  });
  $(document).off('click','.updateorganization').on('click','.updateorganization', function(e) {

    //var formdata = $('#organization_form').serialize();
    var formdata = new FormData($('#organization_form')[0]); 
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('Organization/updateorganization'); ?>",
      data: formdata,
      dataType:'JSON',
      processData: false,
      contentType: false,
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

          triggerpage('<?php echo base_url();?>Organization');

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