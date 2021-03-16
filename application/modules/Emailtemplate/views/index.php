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
 .DTFC_RightBodyLiner {
    overflow-y: hidden !important;
}
</style>
<div class="card mt-20 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">EMAIL TEMPLATE
		</div>
 <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
              <a href="<?php echo base_url('Emailtemplate/AddEmailtemplate'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add Emailtemplate</a>

   
     </div>
   </div>

	</div>
	<div class="card-body">
<div class="col-md-12">

    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
          <th  class="text-left" style="width: 60px;" >S.No</th>
          <th  class="text-left">Email Template Name</th>
          <!-- <th  class="text-left">To MailID</th> -->
          <!-- <th  class="text-center">Address Line2</th> -->
          <!-- <th  class="text-left">BCC MailID</th> -->
          <th  class="text-left">Subject</th>
          <th  class="text-left">Body</th>
         <!--  <th  class="text-center">Office No</th>
          <th  class="text-center">Fax No</th>
          <th  class="text-center">Active</th> -->
          <th  class="text-left">Action</th>
        </tr> 

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($emailtemplate as $row): ?>
      <tr>
        <td style="text-align: left;"><?php echo $i; ?></td>
        <td style="text-align: left;"><?php echo $row->EmailTemplateName; ?></td>
        <!-- <td style="text-align: left;"><?php echo $row->ToMailID; ?></td> -->
        <!-- <td style="text-align: center;"><?php echo $row->AddressLine2; ?></td> -->
        <!-- <td style="text-align: left;"><?php echo $row->BCCMailID; ?></td> -->
        <td style="text-align: left;"><?php echo $row->Subject; ?></td>
        <td style="text-align: left;"><?php echo $row->Body; ?></td>
       <!--  <td style="text-align: center;"><?php echo $row->OfficeNo; ?></td>
        <td style="text-align: center;"><?php echo $row->FaxNo; ?></td>
        <td style="text-align: center;"><div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div></td> -->

        <td style="text-align: left"> 
          <span style="text-align: left;width:100%;">
            <a href="<?php echo base_url('Emailtemplate/EditEmailtemplate/'.$row->EmailTemplateUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs "><i class="icon-pencil"></i></a>

            &nbsp;<a title="Delete" href="" data-value="<?php echo $row->EmailTemplateUID;?>" class="btn btn-link btn-danger btn-just-icon btn-xs btnDelete remove_btn"><i class="fa fa-trash"></i>
                      </a>

            <!-- <a data-CustomerUID="<?php echo $row->CustomerUID; ?>"  class="btn btn-link btn-info btn-just-icon btn-xs confirmClick"><i class="fa fa-trash"></i></a>   -->
            <!--  <a href="<?php echo base_url("Customer/DeleteCustomer".$row->CustomerUID); ?>" class="">Delete</a> -->

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

<div id="alert-model" tabindex="-1" role="dialog" class="modal fade">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style="padding: 5px 10px 0 0;">
            <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>
          </div>
          <div class="modal-body">
            <div class="text-center">
              <h7 style="line-height: 30px;">Are You Sure Want to Delete ?</h7>
               <div class="xs-mt-10" style="width: 335px;display:block;margin: 0 auto;">
                
              </div> 
            </div>
            <div class="text-right">
            <button type="submit" class="btn input-sm btn-success Yes"  style="height: 37px;">Yes</button>
                <button type="submit" class="btn input-sm btn-primary No"  style="height: 37px;">No</button>
            </div>
          </div> 
        </div>
      </div>
</div>


<script type="text/javascript">
  $(document).ready(function()
  {
    $('#MaritalTableList').dataTable(
    {
       processing: true,
      scrollX:  true,
     
      paging:true,
      fixedColumns:   {
              leftColumns: 1,
              rightColumns: 1
            }
    });

    $(document).on("click",".btnDelete", function(){
      $("#alert-model").modal({
       backdrop: 'static',
       keyboard: false
     });
      var ID = $(this).attr('data-value');
      $('.Yes').attr('data-ID',ID);
      return false;
    });

    $('.No').click(function(){
      setTimeout(function() {$('#alert-model').modal('hide');});
    });

    $('.Yes').click(function(){

      var Id = $(this).attr('data-ID');
      $.ajax({
        url: '<?php echo base_url();?>Emailtemplate/DeleteEmailtemplate',
       type: "POST",
       data: {'dlt-id':Id}, 
       dataType:'json',
       cache: false,
       success: function(data)
       {
        // console.log(data);
        if(data.validation_error == 1)
        {
          $.notify(
            {
              icon:"icon-bell-check",
              message:data['message']
            },
            {
              type:"success",
              delay:1000 
            }); 

          setTimeout(function() {$('#alert-model').modal('hide');});
          setTimeout(function(){window.location.reload("<?php echo base_url();?>Emailtemplate");}, 1000);  
        }

        else{
          $.notify(
            {
              icon:"icon-bell-check",
              message:data['message']
            },
            {
              type:"danger",
              delay:1000 
            }); 
        }

      },
      error:function(jqXHR, textStatus, errorThrown)
      {
        console.log(jqXHR.responseText);
      }
    });
    })


  });
</script>
