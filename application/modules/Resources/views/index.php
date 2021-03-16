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
</style>
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Resources 
		</div>

        <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
              <a href="<?php echo base_url('Resources/AddResources'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Resources</a>
  
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
         <th  class="text-left">Controller</th>
         <th  class="text-left">Field Name</th>
         <th  class="text-left">Field Section</th>
         <!-- <th  class="text-center">Notification Element</th> -->
         <!-- <th  class="text-center">Menu Bar</th>
         <th  class="text-center">Icon Class</th>
         <th  class="text-center">Position</th>
         <th  class="text-center">Active</th> -->
         <th  class="text-left" style="width:60px">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php
       $i=1;foreach($ResourcesDetails as $row): ?>
      <tr>

       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->controller; ?></td>
       <td style="text-align: left;"><?php echo $row->FieldName; ?></td>
       <td style="text-align: left;"><?php echo $row->FieldSection; ?></td>
       <!-- <td style="text-align: center;"><?php echo $row->NotificationElement; ?></td> -->
       <!--  <td style="text-align: center;"><?php if ($row->MenuBarType=="sidebar"){ echo  'Sidebar';}
        else if($row->MenuBarType=='jumbobar'){ echo 'Jumbobar';}
  else { echo  'Common'; }  ?></td>
        <td style="text-align: center;"><?php echo $row->IconClass; ?></td>
         <td style="text-align: center;"><?php echo $row->Position; ?></td>
       <td style="text-align: center;"><div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" disabled="disabled" <?php if($row->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div></td> -->
       <td style="text-align: left"> 
        <span style="text-align: left;width:100%;">
          <a href="<?php echo base_url('Resources/UpdateResources/'.$row->ResourceUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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



    });


   });
 </script>







