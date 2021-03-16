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
   </div>

	</div>
	<div class="card-body">
  <div class="col-md-12">
    <div class="material-datatables">
      <table id="tbl-Products" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
          <th  class="text-left" style="width: 30px;" >S.No</th>
          <th  class="text-left">Workflows</th>
          <th  class="text-left" style="width: 60px;" >Action</th>
        </tr>

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($Customer_Workflow as $row): ?>
      <tr>
        <td style="text-align: left;"><?php echo $i; ?></td>
        <td style="text-align: left;"><?php echo $row['WorkflowModuleName']; ?></td>
        <td style="text-align: left"> 
          <span style="text-align: left;width:100%;">
            <a href="<?php echo base_url('Workflow_Documents/Update_Workflow_Documents/'.$row['WorkflowModuleUID']);?>" class="ajaxload btn btn-link btn-info btn-just-icon btn-xs "><i class="icon-pencil"></i></a>
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