<style>
  th, td { text-align: center; }
</style>
<div class="card mt-20" id="Exceptionorders">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Doc Waiting</h4>
      </div>
    </div>
  </div>
  <!-- <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#PendingReport" role="tablist">
          Doc Waiting Report
          <span class="badge badge-pilsadge-primary" id="completed-count" style="background-color: #fff;color: #000;"><?php echo $this->DocWaitingmodel->count_all(); ?></span>

        </a>
      </li>
    </ul> -->

    <div class="text-right"> 
      <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
      <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
    </div>


    <div id="advancedFilterForReport"  style="display: none;">
      <fieldset class="advancedsearchdiv">
        <legend>Advanced Search</legend>
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row " >
              <!-- <div class="col-md-3 ">
               <div class="form-group bmd-form-group">
                <label for="adv_CustomerUID" class="bmd-label-floating">Client Name <span class="mandatory"></span></label>
                <select class="select2picker form-control" id="adv_CustomerUID"  name="CustomerUID">  
                  <?php if (count($Clients) > 1) { ?>
                    <option value="All">All</option>
                  <?php } ?>
                  <?php 
                  foreach ($Clients as $key => $value) { ?>

                    <option value="<?php echo $value->CustomerUID; ?>" ><?php echo $value->CustomerName; ?></option>
                  <?php } ?>

                </select>
              </div>
            </div> -->
            <div class="col-md-3 datadiv">
             <div class="form-group bmd-form-group">
              <label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
              <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
                <option value="All">All</option>
                <?php foreach ($Projects as $key => $value) { ?>

                  <option value="<?php echo $value->ProjectUID; ?>" ><?php echo $value->ProjectName; ?></option>
                <?php } ?>                     
              </select>
            </div>
          </div>

          <div class="col-md-3 datadiv">
           <div class="form-group bmd-form-group">
            <label for="adv_WorkflowModuleUID" class="bmd-label-floating">Modules <span class="mandatory"></span></label>
            <select class="select2picker form-control" id="adv_WorkflowModuleUID"  name="WorkflowModuleUID" multiple="true">   
              <?php foreach ($Modules as $key => $value) { ?>

                <option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->WorkflowModuleName; ?></option>
              <?php } ?>                  
            </select>
          </div>
        </div>


        <div class="col-sm-1.5"> 
         <div class="form-check" style="margin-top: 18pt;">
           <label class="form-check-label">
            <input class="form-check-input" type="checkbox"  name="PasscodeVerify"  id="adv_Hours"> 48 Hours
            <span class="form-check-sign">
              <span class="check"></span>
            </span>
          </label>
        </div>
      </div>

       <!--  <div class="col-md-3 datadiv">
         <div class="form-group bmd-form-group">
          <label for="adv_hours" class="bmd-label-floating">48 Hours <span class="mandatory"></span></label>
          <input type="checkbox" name="">
        </div>
      </div> -->

      <div class="col-md-3 datadiv">
       <div class="form-group bmd-form-group">
        <label for="adv_ProjectUID" class="bmd-label-floating">Status <span class="mandatory"></span></label>
        <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
          <option value="Pending">Pending</option>  
          <option value="Completed">Completed</option>                  
        </select>
      </div>
    </div>


  </div>
    <!--  <div class="row " >
        <p style="margin: 0px;color: #266596;font-weight: 600;">Date Filter</p>

      </div>
    -->
    <div class="col-md-12">
      <div class="row " >
        <div class="col-md-3 datadiv">
          <div class="bmd-form-group row">
            <div class="col-md-6 pd-0 inputprepand" >
              <p class="mt-5"> Order Entry From Date</p>
            </div>
            <div class=" col-md-6 " style="padding-left: 0px">
              <div class="datediv">
                <input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php //echo date('m/d/Y',strtotime("-90 days")); ?>">
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 datadiv">
          <div class="bmd-form-group row">
            <div class="col-md-6 pd-0 inputprepand" >
              <p class="mt-5"> Order Entry To Date</p>
            </div>
            <div class=" col-md-6 " style="padding-left: 0px">
              <div class="datediv">
                <input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php //echo (date("m/d/Y")); ?>"/>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
    <div class="col-md-12  text-right pd-0 mt-10">
      <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
      <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>

      
    </div>



  </div>
</form>
</fieldset>
</div>

<div class="material-datatables">
  <table class="table table-hover table-striped" id="PendingReport">
    <thead>
     <tr>
      <th>Order Number</th>
      <th>Loan Number</th>
      <th>Loan Type</th>
      <th>Status</th>
      <th>Pre-screen</th>
      <th>48 Hours Waiting</th>
      <th>Welcome Call</th>
      <th>Title</th>
      <th>FHA-VA</th>
      <th>Thirdparty</th>
      <th>HOI</th>
      <th>PayOff</th>
      <th>Doc Chase</th>
      <th>Due DateTime</th>
     <!--  <th>Workup</th>
      <th>Underwriter</th>
      <th>Scheduling</th>
      <th>Closing</th> -->
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
  var PendingReport = false;
  $(function() {
    $("select.select2picker").select2({
      //tags: false,
      theme: "bootstrap",
    });
    $('#PendingReport').DataTable().destroy();
  });
  $('.fa-filter').click(function(){
    $("#advancedFilterForReport").slideToggle();
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
    dailypendinginitialize('false')
    function dailypendinginitialize(formdata)
    {
      PendingReport = $('#PendingReport').DataTable( {
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
          fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
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
            "url": "<?php echo base_url('DocWaiting/FetchDocWaitingReport')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ],
          'rowCallback': function(row, data, index){
            if(data[3] == 'Yes'){
              $(row).find('td:eq(3)').css('color', 'green');
            }
            else if(data[3] == 'No'){
              $(row).find('td:eq(3)').css('color', 'red');
            }
            if(data[4] == 'No'){
              $(row).find('td:eq(4)').css('color', 'red');
            }
            else if(data[4] == 'Yes'){
              $(row).find('td:eq(4)').css('color', 'green');
            }
            if(data[5] == 'No'){
              $(row).find('td:eq(5)').css('color', 'red');
            }
            else if(data[5] == 'Yes'){
              $(row).find('td:eq(5)').css('color', 'green');
            }
            if(data[6] == 'No'){
              $(row).find('td:eq(6)').css('color', 'red');
            }
            else if(data[6] == 'Yes'){
              $(row).find('td:eq(6)').css('color', 'green');
            }
            if(data[7] == 'No'){
              $(row).find('td:eq(7)').css('color', 'red');
            }
            else if(data[7] == 'Yes'){
              $(row).find('td:eq(7)').css('color', 'green');
            }
            if(data[8] == 'No'){
              $(row).find('td:eq(8)').css('color', 'red');
            }
            else if(data[8] == 'Yes'){
              $(row).find('td:eq(8)').css('color', 'green');
            }
            if(data[9] == 'No'){
              $(row).find('td:eq(9)').css('color', 'red');
            }
            else if(data[9] == 'Yes'){
              $(row).find('td:eq(9)').css('color', 'green');
            } 
            if(data[10] == 'No'){
              $(row).find('td:eq(10)').css('color', 'red');
            }
            else if(data[10] == 'Yes'){
              $(row).find('td:eq(10)').css('color', 'green');
            } 
            if(data[11] == 'No'){
              $(row).find('td:eq(11)').css('color', 'red');
            }
            else if(data[11] == 'Yes'){
              $(row).find('td:eq(11)').css('color', 'green');
            } 
            if(data[12] == 'No'){
              $(row).find('td:eq(12)').css('color', 'red');
            }
            else if(data[12] == 'Yes'){
              $(row).find('td:eq(12)').css('color', 'green');
            }
           
          }
        });
    }


    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
              // alert();
              var ProjectUID = $('#adv_ProjectUID option:selected').val();
              // var CustomerUID = $('#adv_CustomerUID option:selected').val();
              var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();
              // console.log(WorkflowModuleUID);
              var FromDate = $('#adv_FromDate').val();
              var ToDate = $('#adv_ToDate').val();
              //var Hours = $("#adv_Hours").val();
              var Hours = 0;
              var chkHours = document.getElementById("adv_Hours");
                if (chkHours.checked) {
                    Hours = 1;
                } else {
                    Hours = 0;
                }
              if((ProjectUID == '')  && (WorkflowModuleUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
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


               var formData = ({'WorkflowModuleUID':WorkflowModuleUID,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate,'Hours':Hours}); 

               dailypendinginitialize(formData);

             }
             return false;
           });

    $(document).off('click','.reset').on('click','.reset',function(){

      // $('#adv_ProjectUID').html('<option value = "All">All</option>');
      // $('#adv_ProductUID').html('<option value = "All">All</option>');
      $("#adv_WorkflowModuleUID").val('');
      $("#adv_ProductUID").val('All');
      $("#adv_DateSelecter").val('Default');
      $("#adv_ProjectUID").val('All');
      // $("#adv_CustomerUID").val('All');
      $("#adv_FromDate").val('');
      $("#adv_ToDate").val('');
      dailypendinginitialize('false');
      callselect2();

    });
  });


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var ProductUID = $('#adv_ProductUID option:selected').val();
  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  // var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({ 'ProductUID':ProductUID,'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate}); 
  }
  

  $.ajax({
   type: "POST",
   url: '<?php echo base_url();?>DocWaiting/WriteExcel',
   xhrFields: {
    responseType: 'blob',
  },
  data: {'formData':formData},
  beforeSend: function(){


  },
  success: function(data)
  {
    var filename = "DocWaitingReportOrders.csv";
    if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "DocWaitingReportOrders.csv";
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



/*$(document).off('click', '.ExportDocument').on('click', '.ExportDocument', function (e) {  
  e.preventDefault();
  var currentrow = $(this).closest('tr');
  var OrderUID = $(this).attr('data-OrderUID');
  var OrderNumber = $(this).attr('data-OrderNumber');
  var LoanNumber = $(this).attr('data-LoanNumber');

  SwalConfirmExport(OrderUID, OrderNumber, currentrow, PendingReport, LoanNumber);
})

$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
  e.preventDefault();
  var $dataobject = {'CustomerUID': $(this).val()};
  if($(this).val() == 'All')
  {
    $('#adv_ProjectUID').html('<option value="All">All</option>');
    callselect2();
  }
  else
  {
    SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {
        // addcardspinner($('#AuditCard'));
      }).then(function (data) {
        if (data.validation_error == 0) {
          var Product_Select = data.Products.reduce((accumulator, value) => {
            
            return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
          }, '<option value="All">All</option>');         
          $('#adv_ProductUID').html(Product_Select);
          $('#adv_ProductUID').trigger('change');
        }
        callselect2();

      }).catch(function (error) {

        console.log(error);
      });
    }
  });*/

</script>







