<style type="text/css">

</style>
<div class="card" id="Exceptionorders">

	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Completed</h4>
      </div>
    </div>
  </div>


  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#review" role="tablist">
          Completed Orders
        </a>
      </li>
    </ul>

		<?php $this->load->view('common/advancesearch'); ?>

  
               
	<div class="card-body">
        <table class="table table-hover table-striped" id="review">
            <thead>
               <tr>
                    <th>Order No</th>
                    <th>Client </th>
                    <th>Lender</th>
                    <th>Current Status</th>
                    <th>Property Address</th>   
                    <th>Property City</th>  
                    <th>Property State</th> 
                    <th>Zip Code</th>     
                    <th>Project</th>
<!--                     <th>OrderEntryDateTime</th>
                    <th>OrderDueDateTime</th>
 -->                    <th class="no-sort">Actions</th>
               </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
	</div>



  </div>
</div>

<script type="text/javascript">
          var review = false;
        $(function() {
          $("select.select2picker").select2({
            //tags: false,
            theme: "bootstrap",
          });
          $('#review').DataTable().destroy();
        });
	$(document).ready(function(){
    $('.datepicker').datetimepicker({
            icons: {
              time: "fa fa-clock-o",
              date: "fa fa-calendar",
              up: "fa fa-chevron-up",
              down: "fa fa-chevron-down",
              previous: 'fa fa-chevron-left',
              next: 'fa fa-chevron-right',
              today: 'fa fa-screenshot',
              clear: 'fa fa-trash',
              close: 'fa fa-remove'
            },
            format: 'MM/DD/YYYY'
          });
completedinitialize('false')
    function completedinitialize(formdata)
    {
          review = $('#review').DataTable( {
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            scrollY: '100vh',
            paging:  true,
             "bDestroy": true,
            "autoWidth": true,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          "pageLength": 50, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
                    fixedColumns: {
          leftColumns: 1,
          rightColumns: 2
        },

          language: {
            sLengthMenu: "Show _MENU_ Orders",
            emptyTable:     "No Orders Found",
            info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
            infoEmpty:      "Showing 0 to 0 of 0 Orders",
            infoFiltered:   "(filtered from _MAX_ total Orders)",
            zeroRecords:    "No matching Orders found",
            processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

          },

          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url('Completed/completedorders_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]
        });
    }

      
          $(document).off('click','.search').on('click','.search',function()
          {
              // alert();
                var ProjectUID = $('#adv_ProjectUID option:selected').val();
                var LenderUID = $('#adv_LenderUID option:selected').val();
                var CustomerUID = $('#adv_CustomerUID option:selected').val();
                var FromDate = $('#FromDate').val();
                var ToDate = $('#ToDate').val();
                if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == ''))
                {
                  
                  
                      $.notify(
                      {
                        icon:"icon-bell-check",
                        message:'Please Choose Search Keywords'
                      },
                      {
                        type:'danger',
                        delay:1000 
                      });
                } else {


                 var formData = ({ 'ProjectUID': ProjectUID ,'LenderUID': LenderUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 

                 completedinitialize(formData);
                
                }
               return false;
          });

            $(document).off('click','.reset').on('click','.reset',function()
          {
            
            $("#adv_ProjectUID").val('').trigger('change');
            $("#adv_LenderUID").val('').trigger('change');
            $("#adv_CustomerUID").val('').trigger('change');
            $("#FromDate").val('');
            $("#ToDate").val('');
            completedinitialize('false');

          });
	});


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  var LenderUID = $('#adv_LenderUID option:selected').val();
  var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var FromDate = $('#FromDate').val();
  var ToDate = $('#ToDate').val();
  if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == ''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({ 'ProjectUID': ProjectUID ,'LenderUID': LenderUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 
  }
  

  $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Completed/WriteExcel',
          xhrFields: {
      responseType: 'blob',
    },
     data: {'formData':formData},
    beforeSend: function(){

        
    },
    success: function(data)
    {
        var filename = "CompletedOrders.csv";
        if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "CompletedOrders.csv";
            link.click();
        } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
            //IE version
            var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            window.navigator.msSaveBlob(blob, filename);
        } else {
            //Firefox version
            var file = new File([data], filename, { type: 'application/octet-stream' });
            window.open(URL.createObjectURL(file));
        }
    },
    error: function (jqXHR, textStatus, errorThrown) {

      console.log(jqXHR);


    },
    failure: function (jqXHR, textStatus, errorThrown) {

      console.log(errorThrown);

    },
  });

 });



$(document).off('click', '.ExportDocument').on('click', '.ExportDocument', function (e) {  
  e.preventDefault();
  var currentrow = $(this).closest('tr');
  var OrderUID = $(this).attr('data-OrderUID');
  var OrderNumber = $(this).attr('data-OrderNumber');
  var LoanNumber = $(this).attr('data-LoanNumber');

  SwalConfirmExport(OrderUID, OrderNumber, currentrow, review, LoanNumber);
})


        
	</script>







