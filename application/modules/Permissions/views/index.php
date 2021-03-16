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
<div class="card customcardbody mt-40"  id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Permissions 
		</div>
        <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
             <a href="<?php echo base_url('Permissions/AddPermissions'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Permissions</a>


      </div>
    </div>

	</div>
	<div class="card-body">
    <div class="col-md-12">
 
    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         
         <th  class="text-left">Resource</th>
         <th  class="text-left">PermissionName</th>
         <th  class="text-left">Section Name</th>
         <th  class="text-left">Field Name</th>
         <!-- <th  class="text-center">Active</th> -->
         <th  class="text-left">Action</th>
       </tr>
     </thead>
    
</table>
</div>
</div>

</div>
</div>




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){



    completedinitialize();

     function completedinitialize()
    {
          review = $('#MaritalTableList').DataTable( {
           
            scrollX: true,

             "scrollCollapse": true,
       
           
            "bDestroy": true,
           // "autoWidth": true,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          //"order": [], //Initial no order.
          "pageLength": 10, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
          
                   fixedColumns: {
        leftColumns: 0,
        rightColumns: 1
    },

          language: {
            sLengthMenu: "Show _MENU_ Records",
            emptyTable:     "No Records Found",
            info:           "Showing _START_ to _END_ of _TOTAL_ Records",
            infoEmpty:      "Showing 0 to 0 of 0 Records",
            infoFiltered:   "(filtered from _MAX_ total Records)",
            zeroRecords:    "No matching Orders found",
            processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

          },

          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url('Permissions/permission_ajax_list')?>",
            "type": "POST"
              
          },
          
        });
    }


   });
 </script>







