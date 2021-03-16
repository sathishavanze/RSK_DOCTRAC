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
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">ORGANIZATION
		</div>
      <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
          <a href="<?php echo base_url('Organization/AddOrganization'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add Organization</a>   
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
          <th  class="text-left">Organization Name</th>
          <th  class="text-left">Organization Address</th>
          <!-- <th  class="text-center">Address Line2</th> -->
          <th  class="text-left">Organization City</th>
          <th  class="text-left">Organization County</th>
          <th  class="text-left">Organization State</th>
          <th  class="text-left">Organization PhoneNo</th>
         <!--  <th  class="text-center">Office No</th>
          <th  class="text-center">Fax No</th>
          <th  class="text-center">Active</th> -->
          <th  class="text-left" style="width: 60px;">Action</th>
        </tr> 

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($organization as $row): ?>
      <tr>
        <td style="text-align: left;"><?php echo $i; ?></td>
        <td style="text-align: left;"><?php echo $row->OrganizationName; ?></td>
        <td style="text-align: left;"><?php echo $row->Address; ?></td>
        <!-- <td style="text-align: center;"><?php echo $row->AddressLine2; ?></td> -->
        <td style="text-align: left;"><?php echo $row->OrganizationCity; ?></td>

        <td style="text-align: left;"><?php echo $row->OrganizationCounty; ?></td>
         <td style="text-align: left;"><?php echo $row->OrganizationState; ?></td>
        <td style="text-align: left;"><?php echo $row->OrganizationPhoneNo; ?></td>
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
                    <a href="<?php echo base_url('Organization/EditOrganization/'.$row->OrganizationUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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
  $(document).ready(function()
  {
    $('#MaritalTableList').dataTable(
    {
       processing: true,
      paging:true,
      scrollX : true,
      fixedColumns:   {
              leftColumns: 1,
              rightColumns: 1
            }
    });
  });
</script>