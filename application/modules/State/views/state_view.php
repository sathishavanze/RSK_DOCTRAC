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
 .btnbtn-primary{
  color: red;
}
.form-group {
  margin: 0px;
}
span.errormessage {
  color: red;
  font-size: 10px;
}
label {
  margin-bottom: .0px;
}
.col-sm-12 {
  flex: 0 0 100%;
  max-width: 100%;
}
</style>
<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">State
		</div>
	</div>
	<div class="card-body">
    <div class="text-right">
      <a href="javascript:void(0);" class="btn btn-fill  btn-danger btn-wd" data-toggle="modal" data-target="#Modal_Add"><span class="fa fa-plus"></span> Add New</a></div>
    </div>

    <div class="material-datatables">
     <div class="col-sm-12">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
       <thead>
        <tr>
          <th  class="text-center">S.No</th>
          <th  class="text-center">StateCode</th>
          <th  class="text-center">StateName </th>
          <th  class="text-center">FIPSCode</th>
          <th  class="text-center">StateEmail</th>
          <th  class="text-center">StateWebsite</th>
          <th  class="text-center">StatePhoneNumber</th>
          <th  class="text-center">Active</th>
          <th  class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
       <?php $i=1;foreach($DocumentDetails as $row): ?>
       <tr>

         <td style="text-align: center;"><?php echo $i; ?></td>
         <td style="text-align: center;"><?php echo $row->StateCode; ?></td>
         <td style="text-align: center;"><?php echo $row->StateName; ?></td>
         <td style="text-align: center;"><?php echo $row->FIPSCode; ?></td>
         <td style="text-align: center;"><?php echo $row->StateEmail; ?></td>
         <td style="text-align: center;"><?php echo $row->StateWebsite; ?></td>
         <td style="text-align: center;"><?php echo $row->StatePhoneNumber; ?></td>
         <td style="text-align: center;"><div class="togglebutton">
          <label class="label-color"> 
            <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?>>
            <span class="toggle"></span>
          </label>
        </div></td>
        <td style="text-align: center"> 
          <span style="text-align: center;width:100%;">
            <a href="javascript:void(0);" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload btn_edit" data-id="<?php echo $row->StateUID; ?>"><i class="icon-pencil"></i></a> 
          </span>
        </td>
      </tr>
      <?php 
      $i++;
      endforeach; ?>

    </tbody>
  </table>
</div>
</div>
</div>
<div class="card-body">
 <div class="tab-content tab-space">
  <div class="tab-pane active" id="singleentry">


    <div class="modal fade custommodal" id="Modal_Add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add New State</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="user_form">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="statecode" class="bmd-label-floating">State Code</label>
                    <input type="text" name="StateCode" id="StateCode" class="form-control">
                    <span class="errormessage" style="display: none;">Alreay  State code is exit</span>
                  </div>

                </div>
                <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="statname" class="bmd-label-floating">State Name</label>

                    <input type="text" name="StateName" id="StateName" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="FIPSCode" class="bmd-label-floating">FIPS Code</label>

                    <input type="text" name="FIPSCode" id="FIPSCode" class="form-control">
                  </div>
                </div>
                  <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="StateCode" class="bmd-label-floating">State Email</label>

                    <input type="text" name="State" id="StateEmail" class="form-control">
                  </div>
                </div>     
              
                <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="StateCode" class="bmd-label-floating">State Website</label>

                    <input type="text" name="StateWebsite" id="StateWebsite" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group bmd-form-group">
                    <label for="StatePhoneNumber" class="bmd-label-floating">StatePhoneNumber</label>

                    <input type="text" name="StatePhoneNumber" id="StatePhoneNumber" class="form-control">
                  </div>
                </div></div>

                
                <div class="col-sm-65 form-group pull-right">
                 <!--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --><p class="text-right">
                 <button type="button" type="submit"  class="btn btn-space btn-social btn-color btn-twitter btn_save">Save</button>
               </p>

             </div>
           </form>
         </div>
       </div>
     </div>
   </div>

 </div>
</div>
</div>


<div class="modal fade" id="Modal_Edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit State</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="user_form_update" class="update">
         <div>

          <div class="row">
            <input type="hidden" name="StateUID" id="StateUIDUpdate" value="">
            <div class="col-md-3">
             <label for="statecode" class="bmd-label-floating">State Code</label>
             <div class="form-group bmd-form-group">
               <input type="text" name="StateCode" id="StateCodeUpdate" class="form-control" value="" />
      
             </div>
           </div>
           <div class="col-md-3">
            <label  for="statname" class="bmd-label-floating">State Name</label>
            <div class="form-group bmd-form-group">

              <input type="text" name="StateName" id="StateNameUpdate" class="form-control" value="" />
            </div>
          </div>
          <div class="col-md-3">
            <label for="FIPSCode" class="bmd-label-floating">FIP Code</label>
            <div class="form-group bmd-form-group">
             <input type="text" name="FIPSCode" id="FIPSCodeUpdate" class="form-control" value="" />
           </div>
         </div>

         <div class="col-md-3">
           <label for="FIPSCode" class="bmd-label-floating">State Email</label>
           <div class="form-group bmd-form-group">
             <input type="email" name="StateEmail" id="StateEmailUpdate" class="form-control" value="" />
           </div>         </div>
         <div class="col-md-3">
          <label for="StateCode" class="bmd-label-floating">State Website</label>
          <div class="form-group bmd-form-group">
           <input type="text" name="StateWebsite" id="StateWebsiteUpdate" class="form-control" value="" />
         </div>
       </div>
       <div class="col-md-3">
         <label for="StatePhoneNumber" class="bmd-label-floating">StatePhoneNumber</label>
         <div class="form-group bmd-form-group">
           <input type="text" name="StatePhoneNumber" id="StatePhoneNumberUpdate" class="form-control" value="" />
         </div>
       </div>
       <div class="col-md-3">
        <div class="togglebutton" style="margin-top: 35pt">
          <label class="label-color"> Active
            <input type="checkbox" id="ActiveUpdate" name="Active" class="">
            <span class="toggle"></span>
          </label>
        </div>
      </div>  
    </div>


    <div class="col-sm-65 form-group pull-right">
     <!--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --><p class="text-right">
     <button type="button" id="btn_update" class="btn btn-space btn-social btn-color btn-twitter btn_update">Update</button>
   </p>

 </div>
</div>
</form>
</div>
</div>
</div>
</div>









<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

  $(document).ready(function(){



    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  true,
      scrollY:  "400px",
      paging:true,



    });
    //function show all product
    $(document).off('click','.btn_save').on('click','.btn_save', function(e) {


      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('state/Save'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {

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
          else if(response.Status == 3)
          {
            $('.errormessage').css('display','block');
            $.notify(
            {
              icon:"icon-bell-check",
              message:response.message
            },
            {
              type:"danger",
              delay:1000 
            });
            $('#StateCode').addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
          }
          else if(response.Status == 0)
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
            $('#Modal_Add').modal('hide');
            setTimeout(function(){ 
              triggerpage('<?php echo base_url();?>state');
            }, 500);
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



            
          }

        }
      });

    });



    $(document).off('click','.close').on('click','.close', function(e) {
      $('.bodyoverlaydiv').css('display','none');
      setTimeout(function(){ 

        triggerpage('<?php echo base_url();?>state');

      }, 500);
    }); 
    $(document).off('click','.btn_edit').on('click','.btn_edit', function(e) {
      var StateUID = $(this).attr('data-id');
    
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('state/getdatabyStateUID'); ?>",
      data: {'StateUID':StateUID},
      dataType:'json',
      success: function (response) {
        console.log(response);
        $('#StateUIDUpdate').val(response.StateUID);
        $('#StateCodeUpdate').val(response.StateCode);
        $('#StateNameUpdate').val(response.StateName);
        $('#FIPSCodeUpdate').val(response.FIPSCode);
        $('#StateEmailUpdate').val(response.StateEmail);
        $('#StateWebsiteUpdate').val(response.StateWebsite);
        $('#StatePhoneNumberUpdate').val(response.StatePhoneNumber);
        if(response.Active == 1)
        {
          $("#ActiveUpdate").prop("checked", true);
        }
        else
        {
          $("#ActiveUpdate").prop("checked", false);
        }
        $('#Modal_Edit').modal('show');
        $('.bodyspinner_svg').css('display','none');
      }
    });
  });

    $(document).off('click','.btn_update').on('click','.btn_update', function(e) {
      console.log(formdata);
      button=$(this);
      button_val=$(this).val();
      button_text=$(this).html();
     var formdata = $('#user_form_update').serialize();
  $.ajax({
        type: "POST",
        url: "<?php echo base_url('state/Update'); ?>",
        data: formdata ,
        dataType:'json',
        beforeSend: function () {
          button.prop("disabled", true);
          button.html('Loading ...');
          button.val('<i class="fa fa-spin fa-spinner"></i> update..');
          $('#Modal_Edit').modal('hide');
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

            triggerpage('<?php echo base_url();?>state');

          }, 3000);
         }
         else if(response.Status == 4)
         {
          $('.errormessage').css('display','block');
          $.notify(
          {
            icon:"icon-bell-check",
            message:response.message
          },
          {
            type:"danger",
            delay:1000 
          });
          $('#StateCodeUpdate').addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
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
  </body>
  </html>








