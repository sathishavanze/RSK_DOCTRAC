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
 .table-bordered>thead>tr>th{
      padding: 5px 10px!important;
 }
 .DTFC_RightWrapper{
  width : 170px !important;
 }
</style>
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">PROJECT 
		</div>
    <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
       <a href="<?php echo base_url('ProjectCustomer/AddProject'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Project</a>
     </div>
   </div>



 </div>
 <div class="card-body">
  <div class="col-md-12">


  <div class="material-datatables">
    <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
     <thead>
      <tr>
       <th  class="text-left" style="width:30px">S.No</th>
       <th  class="text-left">Project Name</th>
       <th  class="text-left">Project Code</th>
       <th  class="text-left">Client</th>
       <th  class="text-left">AutoExport</th>
         <!-- <th  class="text-center">Priority</th>
         <th  class="text-center">Priority Time</th>
         <th  class="text-center">BulkImportTemplate Name</th>
         <th  class="text-center">BulkImportFormat</th> -->
         <th  class="text-left">Active</th> 
         <th class="no-sort">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php
      $i=1;foreach($DocumentDetails as $row): ?>
      <tr>

       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->ProjectName; ?></td>
       <td style="text-align: left;"><?php echo $row->ProjectCode; ?></td>
       <td style="text-align: left;"><?php echo $row->CustomerName; ?></td>

       <!-- <td style="text-align: center;"> <div class="form-check">
       <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="project1" <?php if($row->IsStacking == 1){ echo "checked"; } ?>  disabled > 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label></div></td>
       <td style="text-align: center;"><div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input" name="project2" type="checkbox" <?php if($row->IsAuditing == 1){ echo "checked"; } ?> disabled > 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>    </td>
       <td style="text-align: center;"><div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="project4" <?php if($row->IsReview == 1){ echo "checked"; } ?> disabled> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div></td>
       <td style="text-align: center;"><div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox"  <?php if($row->IsShipping == 1){ echo "checked"; } ?>  disabled >
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div> </td>
       <td style="text-align: center;"><div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="project4" <?php if($row->IsExport == 1){ echo "checked"; } ?> disabled > 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div></td>  -->
                <td style="text-align: left;"><div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="IsAutoExport" <?php if($row->IsAutoExport == 1){ echo "checked"; } ?> disabled > 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div></td>

      <!--  <td style="text-align: center;"><?php echo $row->Priority; ?></td>
       <td style="text-align: center;"><?php echo $row->PriorityTime; ?></td>
       <td style="text-align: center;"><?php echo $row->BulkImportTemplateName; ?></td>
       <td style="text-align: center;"><?php echo $row->BulkImportFormat; ?></td> -->
       <td style="text-align: left;"><div class="togglebutton">
        <label class="label-color"> 
          <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?> disabled>
          <span class="toggle"></span>
        </label>
      </div></td> 
      <td style="text-align: left"> 
        <span style="text-align: left;width:100%;">
          <a href="<?php echo base_url('ProjectCustomer/EditProject/'.$row->ProjectUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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

<script type="text/javascript">

	$(document).ready(function(){



    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  true,
      paging:true,
      fixedColumns:   {
        rightColumns: 1
      }

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
 </script>







