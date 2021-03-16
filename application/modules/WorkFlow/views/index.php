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
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">WorkFlow
		</div>
         <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
            <a href="<?php echo base_url('WorkFlow/AddWorkFlow'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add WorkFlow</a>

   
     </div>
   </div>



	</div>
	<div class="card-body">
  <div class="col-md-12">
    <div class="material-datatables">
      <table id="tbl-Products" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
          <th  class="text-left" style="width: 30px;" >S.No</th>
          <th  class="text-left">Workflow Module Name</th>
          <th  class="text-left">Workflow Icon</th>
          <th  class="text-left" style="width: 60px;" >Status</th>
          <th  class="text-left" style="width: 60px;" >Action</th>
        </tr>

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($WorkFlowList as $row): ?>
      <tr>
        <td style="text-align: left;"><?php echo $i; ?></td>
        <td style="text-align: left;"><?php echo $row->WorkflowModuleName; ?></td>
        <td style="text-align: left;"><?php echo $row->WorkflowIcon; ?></td>
        <td style="text-align: left;">
          <div class="togglebutton">
            <label class="label-color"> 
              <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?> disabled>
              <span class="toggle"></span>
            </label>
          </div>
        </td>
        <td style="text-align: left"> 
          <span style="text-align: left;width:100%;">
            <a href="<?php echo base_url('WorkFlow/EditWorkFlow/'.$row->WorkflowModuleUID);?>" class="ajaxload btn btn-link btn-info btn-just-icon btn-xs "><i class="icon-pencil"></i></a>
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



    $("#tbl-Products").dataTable({
      processing: true,
      scrollX:  true,
    
      paging:true,

      

    });

     });
   </script>







