<style type="text/css">
  th, td { text-align: center; }
</style>
<div class="card mt-20" id="Exceptionorders">

	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Daily Completion</h4>
      </div>
    </div>
  </div>


  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#DailyCompletionReport" role="tablist">
          Daily Completion Orders
          <span class="badge badge-pill badge-primary" id="completed-count" style="background-color: #fff;color: #000;"><?php echo $this->DailyCompletionmodel->count_all(); ?></span>

        </a>
      </li>
    </ul>

    <?php //$this->load->view('common/advancesearch'); ?>
		<?php $this->load->view('common/report_filters'); ?>

  
               
<div class="material-datatables" >
        <table class="table table-hover table-striped" id="DailyCompletionReport">
            <thead>
               <tr>
                <th>Agent ID</th>
                <th>Agent Name</th>
                <th>Order Number</th>
                <th>Loan Number</th>
                <th>Borrower Name</th>
                <th>Client Name</th>
                <th>Document Status</th>
                <th>Completed Date</th>
                <th class="no-sort">Actions</th>
               </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
	</div>



  </div>
</div>

<script type="text/javascript">
          var DailyCompletionReport = false;
        $(function() {
          $("select.select2picker").select2({
            //tags: false,
            theme: "bootstrap",
          });
          $('#DailyCompletionReport').DataTable().destroy();
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
dailycompletedinitialize('false')
    function dailycompletedinitialize(formdata)
    {
          DailyCompletionReport = $('#DailyCompletionReport').DataTable( {
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
          "pageLength": 10, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
                    /*fixedColumns: {
          leftColumns: 1,
          rightColumns: 2
        },*/

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
            "url": "<?php echo base_url('DailyCompletion/completedorders_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]
        });
    }

      
          $(document).off('click','.filterreport').on('click','.filterreport',function()
          {
              // alert();
                var ProductUID = $('#adv_ProductUID option:selected').val();
                var ProjectUID = $('#adv_ProjectUID option:selected').val();
                var CustomerUID = $('#adv_CustomerUID option:selected').val();
                var FromDate = $('#adv_FromDate').val();
                var ToDate = $('#adv_ToDate').val();
                if((ProjectUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
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


                 var formData = ({ 'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 

                 dailycompletedinitialize(formData);
                
                }
               return false;
          });

           $(document).off('click','.reset').on('click','.reset',function(){
            
            $('#adv_ProjectUID').html('<option value = "All">All</option>');
            $('#adv_ProductUID').html('<option value = "All">All</option>');

            $("#adv_ProductUID").val('All');
            $("#adv_DateSelecter").val('Default');
            $("#adv_ProjectUID").val('All');
            $("#adv_CustomerUID").val('All');
            $("#adv_FromDate").val('');
            $("#adv_ToDate").val('');
            dailycompletedinitialize('false');
            callselect2();

          });
	});


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var ProductUID = $('#adv_ProductUID option:selected').val();
  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({ 'ProductUID':ProductUID,'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 
  }
  

  $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>DailyCompletion/WriteExcel',
          xhrFields: {
      responseType: 'blob',
    },
     data: {'formData':formData},
    beforeSend: function(){

        
    },
    success: function(data)
    {
        var filename = "DailyCompletionOrders.csv";
        if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "DailyCompletionOrders.csv";
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

  SwalConfirmExport(OrderUID, OrderNumber, currentrow, DailyCompletionReport, LoanNumber);
})


        
	</script>







