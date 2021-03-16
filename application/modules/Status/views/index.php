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
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Status 
		</div>
      <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
           <a href="<?php echo base_url('Status/AddStatus'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Status</a>

    
     </div>
   </div>
	</div>
	<div class="card-body">
    <div class="col-md-12">


    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left" style="width:30px">S.No</th>
         <th  class="text-left">Status Name</th>
         <th  class="text-left">StatusColor</th>
         <th  class="text-left">Module Name</th>
         <th  class="text-left"  style="width:60px">Active</th>
         <th  class="text-left"  style="width:60px">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php
       $i=1;foreach($StatusDetails as $row): ?>
      <tr>

       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->StatusName; ?></td>
       <td style="text-align: left;"><?php echo $row->StatusColor; ?></td>
       <td style="text-align: left;"><?php echo $row->ModuleName; ?></td>
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
        <a href="<?php echo base_url('Status/EditStatus/'.$row->StatusUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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

   });
 </script>







