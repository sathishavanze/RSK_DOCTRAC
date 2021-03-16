<style>
  th, td { text-align: center; }

  .card .card-header.card-header-icon .card-title,
  .card .card-header.card-header-text .card-title {
   margin-top: 1px !important;
   color: #ffffff;
 }
th, td { text-align: center; }
  .bold{ font-weight: bold;  }
  .scrool {
    overflow-x: auto !important;
}
.mdb-select {
  padding: 0;
}
 .fs-wrap.multiple .fs-option.selected .fs-checkbox i {
    background-color:#e91e63 !important;
  }
  .fs-option:hover {
    background-color: #e91e63 !important;
    color: #fff !important;
}
.padding
{
  padding: 8px;
}
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <!-- <i class="icon-file-check"></i> -->
      <h4 class="card-title">Completed Report</h4>
    </div>
    <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right"> 
      	<button class="btn btn-default btn-xs btn-link refresh-btn" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color:#900C3F;cursor: pointer;"><i class="fa fa-filter"></i></button>
      	<a class="btn btn-default btn-xs btn-link exceldownload" href="javascript:;"><i class="fa fa-file-excel-o" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
      	</a> 
      </div>
    </div>
  </div>


  <div id="advancedFilterForReport" class="mt-15 mb-20" >
    <fieldset class="advancedsearchdiv">

      <form id="advancedsearchdata">
        <legend>Advanced Search</legend>
        <div class="col-md-12 pd-0">
          <div class="row " >

            <div style="width: 20%" class="padding ">
              <div class="form-group bmd-form-group">
                <label for="adv_period" class="bmd-label-floating">Period</label>
                <select class="select2picker form-control" id="adv_period"  name="period">   
                  <option></option>
                  <option  value="today">Today</option>
                  <option  value="week">This Week</option>                
                  <option selected value="month">This Month</option>             
                  <option value="year">This Year</option>             
                </select>
              </div>
            </div>
            <!-- user name filter with multi selecet -->
            <div style="width: 20%" class="padding workflow">
              <div class="form-group bmd-form-group">
                <label for="adv_workflow" class="bmd-label-floating"> Workflow  <span style="color: red">*</span></label>
                <select class="form-control select2picker" id="adv_workflow"  name="workflow" placeholder="Select Workflow(s)">   
                  <?php foreach ($Customer_Workflow as $key => $value) { ?>
                    <option value="<?php echo $value['WorkflowModuleUID']; ?>" ><?php echo $value['WorkflowModuleName']; ?></option>
                  <?php } ?>                      
                </select>
              </div>
            </div>
            <?php if(isset($Customer_Workflow[0])){
                $ProcessUsers = $this->Common_Model->CompletedUsersByWorkflowModule($Customer_Workflow[0]['WorkflowModuleUID']);
              } ?>
            <!-- user name filter with multi selecet -->
            <div style="width: 20%" class="padding agent">
              <div class="form-group bmd-form-group">
                <label for="adv_Process" class="bmd-label-floating">Process Users  <span style="color: red">*</span></label>
                <select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select User(s)">   
                  <?php foreach ($ProcessUsers as $key => $value) { ?>
                    <option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
                  <?php } ?>                      
                </select>
              </div>
            </div>            

            <!-- From Date filter with from date  -->
            <div style="width: 20%" class="padding">
              <div class="form-group bmd-form-group">
                <label for="adv_fromDate" class="bmd-label-floating">From Date  <span style="color: red">*</span></label>
                <input type="text" id="adv_fromDate" name="fromDate" class="form-control datepicker" value="<?php echo $date['firstday'] ?>">
              </div> 
            </div>

            <!-- To Date filter with from date  -->
            <div style="width: 20%" class="padding">
              <div class="form-group bmd-form-group">
                <label for="adv_toDate" class="bmd-label-floating">To Date <span style="color: red">*</span></label>
                <input type="text" id="adv_toDate" name="toDate" class="form-control datepicker" value="<?php echo $date['lastday'] ?>">
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

  <div class="col-md-12 material-datatables" id="tableDiv" style="display: none;">
          <table class="table table-hover table-striped" id="completedRecord">
            <thead id="tableDivHead">
              <tr>
               <tr id="tableDivHead">
                  <th>Order No</th>
                  <th>Client </th>
                  <th>Loan No</th>
                  <th>Loan Type</th>
                  <th>Milestone</th>
                  <th>Current Status</th>
                  <th>State</th>
                  <th>LastModified DateTime</th>
                  <th>Completed By</th>
                  <th>Completed Date and Time</th>
                  <th class="no-sort">Actions</th>
                </tr>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
  </div>

</div>

<script type="text/javascript" src="assets/lib/fselect/fSelect.js"></script>
<script type="text/javascript">
  create_fselect();
    function create_fselect(){
    $(".mdb-select").each(function(){
      var placeholder = $(this).attr('placeholder');
      $(this).fSelect({
        placeholder: placeholder,
        numDisplayed: 2,
        overflowText: '{n} selected', 
        showSearch: true
      }); 
    });   
  }
  

  function destroy_fselect(){
    $(".mdb-select").fSelect('destroy');
  }

   var completedRecord = false;
  $(function() {
    $("select.select2picker").select2({
      theme: "bootstrap",
    });
    $('#completedRecord').DataTable().destroy();
  });

   $("#advancedFilterForReport").show();
  $('.fa-filter').click(function(){
    $("#advancedFilterForReport").slideToggle();
  });

  // $(document).off('change', '#adv_workflow').on('change', '#adv_workflow', function(){
   $('#adv_workflow').on("change", function(e) {
    var WorkflowModuleUID = $(this).val();
    $.ajax({
      url: 'CompletedReport/AppendUsers',
      data: {'WorkflowModuleUID': WorkflowModuleUID},
      type: 'POST',
      success: function(data){
        destroy_fselect();
        $('#adv_Process').replaceWith(data);
        create_fselect();
      }
    });
  });
  
  $(document).off('click','.filterreport').on('click','.filterreport',function()
  {
   var Process = $('#adv_Process').val();
   var workflow = $('#adv_workflow').val();
   var FromDate = $('#adv_fromDate').val();
   var ToDate = $('#adv_toDate').val();

   if((workflow == '')  || (FromDate == '') || (ToDate == ''))
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:"Please choose user, workflow and date range"
    },
    {
      type:'danger',
      delay:1000 
    });
   }
   else 
   {
     var formData = ({'workflow': workflow, 'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate});
     CompletedReportHeader(formData);
   }

   return false;
 });

   $(document).off('click','.reset').on('click','.reset',function(){
    var period = 'month';
    $("#adv_period").val(period);
    $("#adv_Status").val('Pending');
    getDate(period);
    $("#adv_period").select2();
    $("#adv_Status").select2();
    $('.processinflow').hide();
  });
   function CompletedReportHeader(formData){
      $.ajax({
        url: 'CompletedReport/getCompletedReportHead',
        data:  {'formData':formData},
        type: 'POST',
        dataType: 'JSON',
        success: function(response){
          if(response.status == 1){
            var json = response.html;
            // $('#tableDivHead').html(response.html);
            $("#tableDiv").empty();

            var content = '<table class="table table-hover table-striped" id="completedRecord">';
            content += '<thead>'; 
            content += '<tr>';

              $.each(json, function(i, val){
                if(val.NoSort == 1 && val.SortColumnName == ''){
                  var sort =  "no-sort";
                }else{
                  var sort =  ""; 
                }
                 
                content += "<th class='"+sort+"''>"+val.HeaderName+"</th>";
              });

            content += "<th class='no-sort'>Completed By</th>";
            content += "<th class='no-sort'>Complete Date Time</th>";
            content += "<th class='no-sort'>Action</th>";  
            content += '</th>';            
            content += '</tr>';      
            content += '</thead>';
            content += "</table>"

            $('#tableDiv').append(content);
          }
          CompletedReport(formData);
        }
      });
   }

  function CompletedReport(formData)
  {   
      
      $('#tableDiv').show();
      completedRecord = $('#completedRecord').DataTable( {
        scrollX:        true,
      scrollCollapse: true,
      fixedHeader: false,
      scrollY: '100vh',
      paging:  true,
      "bDestroy": true,
      "autoWidth": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.  
      "pageLength": 10, // Set Page Length
      "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
      fixedColumns: {
        leftColumns: 1,
        rightColumns: 1,
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
        "url": "<?php echo base_url('CompletedReport/getCompletedReport')?>",
        "type": "POST",
        "data" : {'formData':formData}  
      },
      "columnDefs": [ {
        "targets": 'no-sort',
        "orderable": true,
      } ],
      });
  }


  $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
    var Process = $('#adv_Process').val();
    var FromDate = $('#adv_fromDate').val();
    var ToDate = $('#adv_toDate').val();
    var workflow = $('#adv_workflow').val();
    var formData = ({'workflow': workflow, 'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate});

    var filename = 'CompletedReport.xlsx';
     $.ajax({
    type: "POST",
    url: '<?php echo base_url();?>CompletedReport/WriteExcel',
    xhrFields: {
      responseType: 'blob',
    },
    data: {'formData':formData},
    beforeSend: function(){
    },
    success: function(data)
    {
      var filename = "CompletedReport.csv";
      if (typeof window.chrome !== 'undefined') {
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(data);
        link.download = "CompletedReport.csv";
        link.click();
      } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
        var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        window.navigator.msSaveBlob(blob, filename);
      } else {
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


  $('#adv_period').change(function()
  {
    var period = $(this).val();
    getDate(period);
  });

  function getDate(period)
  {   
    if(period == 'today')
    {
      $('#adv_fromDate').val("<?php echo date('m/d/Y') ?>");
      $('#adv_toDate').val("<?php echo date('m/d/Y') ?>");
    }
    else
    {
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>CompletedReport/getFromToDate',
        data: {'period':period},
        dataType: 'JSON',
        success: function(data)
        {
          $('#adv_fromDate').val(data.fromDate);
          $('#adv_toDate').val(data.toDate);
        }
      });
    }
  }

</script>



