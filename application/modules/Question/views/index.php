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
<div class="card  mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Audit Check List
		</div>
       <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
            <a href="<?php echo base_url('Question/AddQuestion'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add Audit Check List</a>
  
     </div>
   </div>
	</div>
	<div class="card-body">
    <div class="col-md-12">


    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-stripedtable-hover order-column">
       <thead>
        <tr>
         
          <th  class="text-left">AuditName</th>
          <th  class="text-left">AuditTypeName</th>
          <th  class="text-left">ProjectName</th>
          <th  class="text-left">LenderName</th>
          <th  class="text-left">DocumentTypeName</th>
          <th  class="text-left">FreeAudit</th>
          <th  class="text-left">Active</th>
          <th  class="text-left">Action</th>
        </tr>

      </tr>
    </thead>
   
</table>
</div>
</div>
</div>
</div>




<!-- <script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
-->


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
            sLengthMenu: "Show _MENU_ Question",
            emptyTable:     "No Question Found",
            info:           "Showing _START_ to _END_ of _TOTAL_ Question",
            infoEmpty:      "Showing 0 to 0 of 0 Question",
            infoFiltered:   "(filtered from _MAX_ total Question)",
            zeroRecords:    "No matching Question found",
            processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

          },

          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url('Question/question_ajax_list')?>",
            "type": "POST"
              
          },
          
        });
    }


    $(document).off('click','.adduser').on('click','.adduser', function(e) {


      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Users/SaveUser'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {

        },

        success: function (response) {


          if(response.validation_error == 1)
          {
            $.notify(
            {
              icon:"icon-bell-check",
              message:response.message
            },
            {
              type:"danger",
              delay:1000 
            });



            $.each(response, function(k, v) {

              $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
              $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

            });
          }
          else
          {
            $.notify(
            {
              icon:"icon-bell-check",
              message:response.message
            },
            {
              type:"success",
              delay:1000 
            }); 
          }

        }
      });

    });

    $('.confirmClick').click(function(e){

      e.preventDefault();
      console.log('Customer', this);
      var CustomerUID=$(this).attr('data-CustomerUID');
      alert(CustomerUID);

      swal({
        title: 'Are you sure?',
        text: 'You will not be able to recover this imaginary file!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      }).then(function() {

        if(CustomerUID!=''){
          $.ajax({
            url:"<?php echo base_url();?>Customer/DeleteCustomer",
            method:"post",
            data:{'CustomerUID':CustomerUID},
            success:function(data){
              triggerpage('<?php echo base_url();?>Customer');
            }

          });
        }
        

// ajax request


}, function(dismiss) {
        // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
        if (dismiss === 'cancel') {
          swal({
            title: 'Cancelled',
            text: 'Your imaginary file is safe :)',
            type: 'error',
            confirmButtonClass: "btn btn-info",
            buttonsStyling: false
          }).catch(swal.noop)
        }
      })
  // var sure = confirm('Are you sure ?');
  // if(sure){
  //   return true;
  // }
  // return false;
})

			// $(document).off('keyup','#loginid').on('keyup','#loginid', function(e) {

     });
   </script>







