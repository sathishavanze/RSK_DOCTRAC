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
<div class="card customcardbody mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Investor
		</div>
   <div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6 text-right">
      <a href="Investor/AddInvestor"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Investor</a>
    </div>
  </div>
</div>
<div class="card-body">
  <div class="col-md-12">
    <div class="material-datatables">
      <table id="tblInvestors" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left" style="width:30px">S.No</th>
         <th  class="text-left">Investor No</th>
         <th  class="text-left">Investor Name</th>
         <th  class="text-left">AddressLine</th>
         <th  class="text-left">City</th>
         <th  class="text-left">State</th>
         <th  class="text-left">ZipCode</th>
         <th  class="text-left">Active</th>
         <th  class="text-left" style="width:60px">Action</th>
         </tr>
       </thead>
       <tbody>
        <?php $i=1;foreach($InvestorList as $row): ?>
        <tr>
         <td style="text-align: left;"><?php echo $i; ?></td>
         <td style="text-align: left;"><?php echo $row->InvestorNo; ?></td>
         <td style="text-align: left;"><?php echo $row->InvestorName; ?></td>
         <td style="text-align: left;"><?php echo $row->AddressLine1.' '.$row->AddressLine2; ?></td>
         <td style="text-align: left;"><?php echo $row->CityName; ?></td>
         <td style="text-align: left;"><?php echo $row->StateName; ?></td>
         <td style="text-align: left;"><?php echo $row->ZipCode; ?></td>
          <td style="text-align: center;"><div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?> disabled>
                    <span class="toggle"></span>
                  </label>
                </div></td>
         <td style="text-align: left"> 
          <span style="text-align: left;width:100%;">
            <a href="<?php echo base_url('Investor/EditInvestor/'.$row->InvestorUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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
    $("#tblInvestors").dataTable({
      processing: true,
      scrollX:  true,

      paging:true,
    });

   });
 </script>







