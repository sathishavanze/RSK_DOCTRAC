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
<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Fields
		</div>
	</div>
	<div class="card-body">
    <div class="text-right">
      <a href="<?php echo base_url('SubProducts/AddSubProduct'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload" ><i class="fa fa-user"></i> Add SubProduct</a>
    </div>

    <div class="material-datatables">
      <table id="tbl-SubProducts" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
       <thead>
        <tr>
          <th  class="text-center" style="width: 60px;" >S.No</th>
          <th  class="text-center">SubProduct Code</th>
          <th  class="text-center">SubProduct Name</th>
          <th  class="text-center">Product Name</th>
          <th  class="text-center">Status</th>
          <th  class="text-center">Action</th>
        </tr>

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($SubProducts as $row): ?>
      <tr>
        <td style="text-align: center;"><?php echo $i; ?></td>
        <td style="text-align: center;"><?php echo $row->SubProductCode; ?></td>
        <td style="text-align: center;"><?php echo $row->SubProductName; ?></td>
        <td style="text-align: center;"><?php echo $row->ProductName; ?></td>
        <td style="text-align: center;">
          <div class="togglebutton">
            <label class="label-color"> 
              <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?> disabled>
              <span class="toggle"></span>
            </label>
          </div>
        </td>
        <td style="text-align: center"> 
          <span style="text-align: center;width:100%;">
            <a href="<?php echo base_url('SubProducts/EditSubProduct/'.$row->SubProductUID);?>" class="ajaxload btn btn-link btn-info btn-just-icon btn-xs "><i class="icon-pencil"></i></a>
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



<script type="text/javascript">

	$(document).ready(function(){



    $("#tbl-SubProducts").dataTable({
      processing: true,
      scrollX:  true,
    
      paging:true,

      

    });

     });
   </script>







