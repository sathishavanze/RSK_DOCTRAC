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
		<div class="card-icon">Documents Received
		</div>
 <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
              <a href="<?php echo base_url('DocsReceived/AddDocsReceived'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add Documents </a>

   
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
          <th  class="text-left">Order ID</th>
          <th  class="text-left">Document Name</th>
          <th  class="text-left">Document Type</th>
          <th  class="text-left">Uploaded Date Time</th>
          <th  class="text-left">Uploaded User</th>
          <th  class="text-left">Action</th>
        </tr> 

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($tDocuments as $Document): ?>
        <tr>
            <td style="text-align: left;"><?php echo $i; ?></td>
                <td style="text-align: left;">
                  <?php echo $Document->OrderUID; ?>
                </td>
                <td style="text-align: left;">
                  <?php echo $Document->DocumentName; ?>
                </td>
                <td style="text-align: left;">
                  <?php echo $Document->TypeofDocument; ?>
                </td>
                <td style="text-align: left;">
                  <?php echo $Document->UploadedDateTime; ?>
                </td>
               
                <td style="text-align: left;">
                  <?php echo $Document->UploadedUser; ?>
                </td>
                <td style="text-align: left;">
                <a target="_blank" title="" href="<?php echo base_url().$Document->DocumentURL; ?>" class="btn btn-sm btn-xs viewFile" style="background-color: #f2f2f2;color: #000;"><span class="mdi mdi-eye"></span>  View</a>
                <button data-value="<?php echo $Document->DocumentUID;?>" class="btn btn-link btn-danger btn-just-icon btn-xs btnDelete remove_btn">X</button>
                    
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
        url: '<?php echo base_url();?>DocsReceived/DeleteDocsReceived',
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
          setTimeout(function(){window.location.reload("<?php echo base_url();?>DocsReceived");}, 1000);  
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
