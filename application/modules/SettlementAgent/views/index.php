<style type="text/css">
 .table-format > thead > tr > th{
   font-size: 12px;
 }
</style>
<div class="card customcardbody mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Settlement Agent
		</div>
   <div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6 text-right">
      <a href="SettlementAgent/AddSettlementAgent"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Settlement Agent</a>
    </div>
  </div>
</div>
<div class="card-body">
  <div class="col-md-12 mt-10">
    <div class="material-datatables">
      <table id="tblSettlementAgent" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left" style="width:30px">S.No</th>
         <th  class="text-left">Settlement Agent No</th>
         <th  class="text-left">Settlement Agent Name</th>
         <th  class="text-left">Settlement AgentPhone</th>
         <th  class="text-left">Settlement Agent Fax</th>
         <th  class="text-left">Settlement Agent Email</th>
         <th  class="text-left">Settlement Agent Address</th>        
         <th  class="text-left">CityName</th>
         <th  class="text-left">StateName</th>
         <th  class="text-left">ZipCode</th>
         <th  class="text-left" style="width:60px">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php 
      foreach ($DocumentDetails as $key => $value) {  $i = $key+1; ?>
        <tr>
     
       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $value->SettlementAgentNo; ?></td>
       <td style="text-align: left;"><?php echo $value->SettlementAgentName; ?></td>
       <td style="text-align: left;"><?php echo $value->SettlementAgentPhone; ?></td>
       <td style="text-align: left;"><?php echo $value->SettlementAgentFax; ?></td>
       <td style="text-align: left;"><?php echo $value->SettlementAgentEmail; ?></td>
       <td style="text-align: left;"><?php echo $value->AddressLine1.' '.$value->AddressLine2; ?></td>       
       <td style="text-align: left;"><?php echo $value->CityName; ?></td>
       <td style="text-align: left;"><?php echo $value->StateName; ?></td>
       <td style="text-align: left;"><?php echo $value->ZipCode; ?></td>    
        <td style="text-align: left"> 
          <span style="text-align: left;width:100%;">
            <a href="<?php echo base_url('SettlementAgent/EditSettlementAgent/'.$value->SettlementAgentUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
          </span>
        </td>
     </tr>
     <?php  }  ?>

   </tbody>
 </table>
</div>
</div>

</div>
</div>
<script type="text/javascript">

	$(document).ready(function(){
    $("#tblSettlementAgent").dataTable({
      processing: true,
      scrollX:  true,
      paging:true,
          fixedColumns:   {
              leftColumns: 0,
              rightColumns: 1
            }
    });

  });
</script>







