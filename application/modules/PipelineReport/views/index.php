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

  .badge {
    padding: 4px 8px;
    text-transform: capitalize;
  }

  .mt-0{
    margin-bottom: 0px !important;
  }

  .width-100{
    width:100%;
  }

  .excel-btn{
    position: relative;
    float: right;
  }

  .dataTables_scrollBody table {
    margin-left:0px;
  }

  div.scrollmenu {
  
  overflow: auto;
  white-space: nowrap;
}

div.scrollmenu a {
  display: inline-block;
  color: white;
  text-align: center;
  padding: 14px;
  text-decoration: none;
}

div.scrollmenu a:hover {
  background-color: #777;
}

.card .card-header.card-header-icon .card-title,
.card .card-header.card-header-text .card-title {
 margin-top: 1px !important;
 color: #ffffff;
}
</style>
<div class="card mt-10" id="Exceptionorders">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <!-- <i class="icon-file-check"></i> -->
      <h4 class="card-title">Pipeline Report</h4>
    </div>
<!--     <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Pipeline Report</h4>
      </div>
    </div> -->
  </div>
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
                  <label for="adv_CustomerUID" class="bmd-label-floating">Client Name </label>
                  <select class="select2picker form-control" id="adv_CustomerUID"  name="CustomerUID">  
                    <option value="All">All</option>
                    <?php 
                    foreach ($Clients as $key => $value) { ?>

                      <option value="<?php echo $value->CustomerUID; ?>" ><?php echo $value->CustomerName; ?></option>
                    <?php } ?>

                  </select>
                </div>
              </div> -->

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_ProjectUID" class="bmd-label-floating">Project </label>
                  <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
                    <option value="All">All</option>
                    <?php foreach ($Projects as $key => $value) { ?>

                      <option value="<?php echo $value->ProjectUID; ?>" ><?php echo $value->ProjectName; ?></option>
                    <?php } ?>                     
                  </select>
                </div>
              </div>

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
                  <select class="select2picker form-control" id="adv_WorkflowModuleUID"  name="WorkflowModuleUID">   
                    <option value="All">All</option>
                    <?php foreach ($Modules as $key => $value) { ?>
                      <option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->SystemName; ?></option>
                    <?php } ?>                      
                  </select>
                </div>
              </div>

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_Status" class="bmd-label-floating">Status</label>
                  <select class="select2picker form-control" id="adv_Status"  name="Status">   
                    <option value="All">All</option>
                    <option value="NA">N/A</option>                
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                    <option value="NotReady">Not Ready</option>
                    <option value="ProblemIdentified">Issue</option>                
                    <!--  <option value="Pending">Pending</option>
                      <option value="NotReady">Not Ready</option -->>                
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row " >
                  <div class="col-md-3 datadiv">
                    <div class="bmd-form-group row">
                      <div class="col-md-6 pd-0 inputprepand" >
                        <p class="mt-5"> Order Entry From Date</p>
                      </div>
                      <div class=" col-md-6 " style="padding-left: 0px">
                        <div class="datediv">
                          <input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="">
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
                          <input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value=""/>
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

            </form>
          </fieldset>
        </div>
  

<div class="material-datatables">
  <table class="table table-hover table-striped" id="PendingReport">
    <thead>
     <tr>
      <th>Order Number</th>
      <th>Loan Number</th>
      <th>Borrower</th>
      <th>Milestone</th>
      <th>State</th>
      <th>Loan Type</th>
      <?php foreach ($Modules as $key => $value) { ?>
        <th><?php echo $value->SystemName; ?></th>
      <?php } ?> 

       <?php foreach ($Modules as $key => $value) { ?>
        <th><?php echo $value->SystemName. " Completed Date & Time"; ?></th>
        <th><?php echo $value->SystemName. " Completed By"; ?></th>
      <?php } ?>
      <th>Pending Workflows</th>
      <th>Total Pending Count</th>
      <th>Comments</th>
      <th class="no-sort">Actions</th>
    </tr>
  </thead>

  <tbody>

  </tbody>
</table>
</div>

</div>


<script type="text/javascript">
  var PendingReport = false;
  $(function() {
    $("select.select2picker").select2({
      //tags: false,
      theme: "bootstrap",
    });
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
            "url": "<?php echo base_url('PipelineReport/FetchPipelineReportReport')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ],
        });
       $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }


    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
              var ProjectUID = $('#adv_ProjectUID option:selected').val();
              // var CustomerUID = $('#adv_CustomerUID option:selected').val();
              var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();
              var Status = $('#adv_Status').val();
              var FromDate = $('#adv_FromDate').val();
              var ToDate = $('#adv_ToDate').val();
              if((ProjectUID == '')  && (WorkflowModuleUID == '') && (Status == '')&& (FromDate == '')&& (ToDate == ''))
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
              }
               else 
              {
               var formData = ({'WorkflowModuleUID':WorkflowModuleUID,'ProjectUID': ProjectUID ,'Status':Status,'FromDate':FromDate,'ToDate':ToDate}); 
               dailypendinginitialize(formData);
             }

             return false;
           });

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_WorkflowModuleUID").val('All');
      $("#adv_ProjectUID").val('All');
      // $("#adv_CustomerUID").val('All');
      $("#adv_Status").val('All');
      $("#adv_FromDate").val('');
      $("#adv_ToDate").val('');
      dailypendinginitialize('false');
      callselect2();

    });
  });


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  // var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var WorkflowModuleUID = $('#adv_WorkflowModuleUID option:selected').val();
  var Status = $('#adv_Status option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '')  && (WorkflowModuleUID == '') && (Status == '')&& (FromDate == '')&& (ToDate == ''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({'WorkflowModuleUID':WorkflowModuleUID,'ProjectUID': ProjectUID ,'Status':Status,'FromDate':FromDate,'ToDate':ToDate}); 
  }
  

  $.ajax({
   type: "POST",
   url: '<?php echo base_url();?>PipelineReport/WriteExcel',
   xhrFields: {
    responseType: 'blob',
  },
  data: {'formData':formData},
  beforeSend: function(){


  },
  success: function(data)
  {
    var filename = "PipelineReportReportOrders.xlsx";
    if (typeof window.chrome !== 'undefined') {
      //Chrome version
      var link = document.createElement('a');
      link.href = window.URL.createObjectURL(data);
      link.download = filename;
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
    // var filename = "PipelineReportReportOrders.csv";
    // if (typeof window.chrome !== 'undefined') {
    //   //Chrome version
    //   var link = document.createElement('a');
    //   link.href = window.URL.createObjectURL(data);
    //   link.download = "PipelineReportReportOrders.csv";
    //   link.click();
    // } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
    //   //IE version
    //   var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    //   window.navigator.msSaveBlob(blob, filename);
    // } else {
    //   //Firefox version
    //   var file = new File([data], filename, { type: 'application/octet-stream' });
    //   window.open(URL.createObjectURL(file));
    // }
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

  SwalConfirmExport(OrderUID, OrderNumber, currentrow, PendingReport, LoanNumber);
})

/*$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
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

  $(document).off('click','.morelinktoggle').on('click','.morelinktoggle',function(event) {
    if($(this).hasClass("less")) {
      $(this).removeClass("less");
      $(this).html("...");
    } else {
      $(this).addClass("less");
      $(this).html("less");
    }
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    $(window).trigger('resize');      
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().rows().recalcHeight().draw;
    event.preventDefault();
    return false;
  });

  $(window).resize(function() {
    $($.fn.dataTable.tables( true ) ).css('width', '100%');
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
  });

</script>







