<style type="text/css">.pd-btm-0 {
  padding-bottom: 0px;
}

.margin-minus8 {
  margin: -8px;
}

.mt--15 {
  margin-top: -15px;
}

.bulk-notes {
  list-style-type: none
}

.bulk-notes li:before {
  content: "*  ";
  color: red;
  font-size: 15px;
}

.nowrap {
  white-space: nowrap
}

.table-format>thead>tr>th {
  font-size: 12px;
}

</style>
<div class="card mt-40 customcardbody" id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Sub Queues
      </div>
      <div class="row">
         <div class="col-md-6">
         </div>
         <div class="col-md-6 text-right">
            <a href="<?php echo base_url('SubQueues/SubQueue'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add SubQueue</a>
         </div>
      </div>
   </div>
   <div class="card-body">
      <div class="col-md-12">
         <div class="material-datatables">
            <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
               <thead>
                  <tr>
                     <th  class="text-left" style="width: 5%;">S.No</th>
                     <th  class="text-left" style="width: 15%;">Workflow</th>
                     <th  class="text-left" style="width: 70%;">Queue Name</th>
                     <th  class="text-left" style="width: 5%;">Active</th>
                     <th  class="text-left" style="width: 5%;">Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $i=1;foreach($SubQueuesDetails as $row): ?>
                  <tr>
                     <td style="text-align: left;"><?php echo $i; ?></td>
                     <td style="text-align: left;"><?php echo $row->WorkflowModuleName; ?></td>
                     <td style="text-align: left;"><?php echo $row->QueueName; ?></td>
                     <td style="text-align: left;">
                        <div class="togglebutton">
                           <label class="label-color">
                           <input type="checkbox" id="Active" name="Active" class="Active" 
                              <?php if($row->Active == 1) { 
                                 echo "checked"; 
                                 ?>
                              <?php } ?> data-queueuid="<?php echo $row->QueueUID; ?>">
                           <span class="toggle"></span>
                        </div>
                     </td>
                     <td style="text-align: left"> 
                        <span style="text-align: left;width:100%;">
                        <a href="<?php echo base_url('SubQueues/SubQueue/'.$row->QueueUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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
<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type = "text/javascript">
  $(document).ready(function () {

    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX: true,
      fixedColumns: {
        leftColumns: 0,
        rightColumns: 0
      },
      paging: true,
    });

    $(document).off('click', '.Active').on('click', '.Active', function (e) {
      if($(this).prop("checked") == true){
         var Active = 1;
      }else{
         var Active = 0;
      }
      var QueueUID = $(this).data('queueuid');
      $.ajax({
          type: 'POST',
          dataType: 'JSON',
          global: false,
          url: '<?php echo base_url();?>SubQueues/InActiveQueueDetails',
          data: {
            'QueueUID': QueueUID,
            'Active': Active
          },
          success: function(data) { 
            $.notify({
                icon: "icon-bell-check",
                message: data.message
            }, {
                type: data.type,
                delay: 2000
            });
          }
      });
    });

  }); 
</script>