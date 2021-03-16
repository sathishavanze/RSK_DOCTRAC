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
   content: "*";
   color: red;
   font-size: 15px;
 }

 .nowrap{
   white-space: nowrap
 }

 .table-format > thead > tr > th{
   font-size: 12px;
 }
 .DTFC_RightBodyLiner {
  overflow-y: hidden !important;
}
</style>
<div class="card customcardbody mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
      Users
    </div>
    <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
        <i class="fa fa-file-excel-o ExcelSDownload" title="Export Excel" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer;"></i>
        <a href="User/Adduser"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add User</a>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="col-md-12">
    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left">S.No</th>
         <th  class="text-left">User Name</th>
         <th  class="text-left">Login ID</th>
         <!-- <th  class="text-left">Email ID</th> -->
         <th  class="text-left">Contact No</th>
         <th  class="text-left">Role</th>
         <th  class="text-left">Active</th>
         <th  class="text-left">Action</th>
         <th  class="text-left">Verification</th>
       </tr>
     </thead>
     <tbody>
      <?php
      $i=1;foreach($UsersDetails as $row): ?>
      <tr>

       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->UserName; ?></td>
       <td style="text-align: left;"><?php echo $row->LoginID; ?></td>
       <!-- <td style="text-align: left;"><?php echo $row->EmailID; ?></td> -->
       <td style="text-align: left;"><?php echo $row->ContactNo; ?></td>
       <td style="text-align: left;"><?php echo $row->RoleName; ?></td>
       <td style="text-align: left;"> 
        <div class="form-check">
         <label class="form-check-label">
          <input class="form-check-input" type="checkbox" name="Active" <?php if($row->Active == 1){ echo "checked"; } ?>  disabled > 
          <span class="form-check-sign">
            <span class="check"></span>
          </span>
        </label>
      </div>
    </td>
    <td style="text-align: left"> 
      <span style="text-align: left;width:100%;">
        <a href="<?php echo base_url('User/EditUser/'.$row->UserUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
      </span>
    </td>

    <td style="text-align: left"> 
      <span style="text-align: left;width:100%;">
        <?php $status=0; if ($row->VerificationStatus == 0) { $status=0;} else { $status=1; }?>
        <a href="javascript:void(0)" onclick="sendVerificationLink(<?php echo $status.",".$row->UserUID.",'".$row->EmailID."'";?>)" style=" background: <?php if ($row->VerificationStatus == 0) { echo "#999";} else { echo "green"; }?> !important;padding: 5px 10px;border-radius:0px;" class="btn" id="verificationLink">
          <?php if ($row->VerificationStatus == 0) {
            echo "Pending";
          } else { echo "Verified"; }?>
        </a> 
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
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 25%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Do you want to send verification link?</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <!-- <div class="modal-body">
        ...
      </div> -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="send">Send</button>
      </div>
    </div>
  </div>
</div>



<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){

   
    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  true,
      
      paging:true,
      // fixedColumns:   {
      //   rightColumns: 1
      // }



    });


    $(document).off('click','.adduser').on('click','.adduser', function(e) {


      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('ProjectCustomer/SaveDocument'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {

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
          }

        }
      });

    });


			// $(document).off('keyup','#loginid').on('keyup','#loginid', function(e) {

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
			// });

   });

  function sendVerificationLink(status,UserUID,email){
    if(status == 0){
      $("#exampleModal").modal('show');

      $("#send").on('click',function(){

      $.ajax({
        type:'post',
        url: '<?php echo base_url();?>User/sendVerificationEmailLink',
        data:{'email':email,'UserUID':UserUID},
        dataType:'json',
        success:function(data){
          
          //console.log(data)
          if (data.validation_error==1) {
            $("#exampleModal").modal('hide');
            $.notify(
                {
                  icon:"icon-bell-check",
                  message:data.message
                },
                {
                  type:"success",
                  delay:1000 
                });
          }else{
            $.notify(
                {
                  icon:"icon-bell-check",
                  message:data.message
                },
                {
                  type:"danger",
                  delay:1000 
                });
          }
        }    
      });

      });
    }else{

    }
  }
 </script>







