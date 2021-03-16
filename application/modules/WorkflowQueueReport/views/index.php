<style type="text/css">

  /*overall excel export*/
  .nav-pills-rose.customtab{
    position:relative;
  }
  .excel-expo-btn{
    position:absolute;
    right:13px;
  }
  .excel-expo-btn i{
    font-size:15px;
    color:#0B781C;
    cursor: pointer;
    margin-top: 13px;
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
      <!-- <i class="icon-thumbs-up2"></i> -->
      <h4 class="card-title">Workflow Queue Report</h4>
    </div>
<!--     <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">WorkflowQueueReport</h4>
      </div>
    </div> -->
  </div>


  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#WorkflowQueueReport" role="tablist">
          Workflow Queue Report Orders
          <span class="badge badge-pill badge-primary" id="WorkflowQueueReport-count" style="background-color: #fff;color: #000;"><?php echo $this->WorkflowQueueReportordersmodel->count_all(); ?></span>

        </a>
      </li>
        <!-- <a class="excel-expo-btn" href="<?php //echo base_url().'CommonController/WriteGlobalExcel?controller='.$this->uri->segment(1) ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a> -->
    </ul>

    <?php $this->load->view('common/advancesearch'); ?>

    <?php
    $QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
    ?>

    <div  class="material-datatables" >
      <?php if ( !empty($QueueColumns) ) { ?>
      <table class="table table-hover table-striped" id="WorkflowQueueReport">
        <thead>
         <tr>
          <?php foreach ($QueueColumns as $key => $queue) { ?>
            <th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
          <?php } ?>

          <th class="no-sort">Actions</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
    <?php } else { ?>
    <table class="table table-hover table-striped" id="WorkflowQueueReport">
      <thead>
       <tr>
        <th>Order No</th>
        <th>Client </th>
        <th>Loan No</th>
        <th>Loan Type</th>
        <th>Milestone</th>
        <th>Current Status</th>
        <th>State</th>
        <th>LastModified DateTime</th>   
        <!-- <th>Property City</th>  
        <th>Property State</th> 
        <th>Zip Code</th> -->     

        <th class="no-sort">Actions</th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
  <?php } ?>
  </div>



</div>
</div>

<script src="assets/js/Queue.js"></script>

<script type="text/javascript">
  var WorkflowQueueReport = false;
  var ModuleController = '';

  $(document).ready(function(){

    $("select.select2picker").select2({
      //tags: false,
      theme: "bootstrap",
    });

    $('#WorkflowQueueReport').DataTable().destroy();

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

    WorkflowQueueReportinitialize('false');

    function WorkflowQueueReportinitialize(formdata)
    {
      WorkflowQueueReport = $('#WorkflowQueueReport').DataTable( {
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
            rightColumns: 2
          },
          searchDelay:1000,
          initComplete: function(settings, json) {
            $($.fn.dataTable.tables( true ) ).css('width', '100%');
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
            "url": "<?php echo base_url('WorkflowQueueReport/WorkflowQueueReportorders_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]
        });
    }

    $(document).off('click','.exceldownload').on('click','.exceldownload',function(){

      var ProductUID = $('#adv_ProductUID option:selected').val();
      var ProjectUID = $('#adv_ProjectUID option:selected').val();
      var PackageUID = $('#adv_PackageUID option:selected').val();
      var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
      var CustomerUID = $('#adv_CustomerUID option:selected').val();
      //Milestone, state, loanNo
      var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
      var StateUID = $('#adv_StateUID option:selected').val();
      var LoanNo = $('#adv_LoanNo').val();
      var FromDate = $('#adv_FromDate').val();
      var ToDate = $('#adv_ToDate').val();
      if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (PackageUID == '') &&  (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
      {
        var formData = 'All';
      } 
      else 
      {
        var formData = ({ 'ProductUID':ProductUID, 'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 
      }
      

      $.ajax({
       type: "POST",
       url: '<?php echo base_url();?>WorkflowQueueReport/WriteExcel',
       xhrFields: {
        responseType: 'blob',
      },
      data: {'formData':formData},
      beforeSend: function(){


      },
      success: function(data)
      {
        var filename = 'WorkflowQueueReport.xlsx';
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
      },
      error: function (jqXHR, textStatus, errorThrown) {

        console.log(jqXHR);


      },
      failure: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
    });

    });

    $(document).off('click','.reset').on('click','.reset',function()
    {
      var startdate = '';
      var currentdate = '';
      $("#adv_ProductUID").val('All');
      $("#adv_ProjectUID").val('All');
      $("#adv_MilestoneUID").val('All');
      $("#adv_StateUID").val('All');
      $("#adv_LoanNo").val('');
      $("#adv_FromDate").val(startdate);
      $("#adv_ToDate").val(currentdate);
      WorkflowQueueReportinitialize('false');
      callselect2();

    });

    $(document).off('click','.search').on('click','.search',function()
    {
      // alert();
      var ProductUID = $('#adv_ProductUID option:selected').val();
      var ProjectUID = $('#adv_ProjectUID option:selected').val();
      var PackageUID = $('#adv_PackageUID option:selected').val();
      //added Milestone,State, loan no.
      var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
      var StateUID = $('#adv_StateUID option:selected').val();
      var LoanNo = $('#adv_LoanNo').val();    
      var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
      var CustomerUID = $('#adv_CustomerUID option:selected').val();
      var FromDate = $('#adv_FromDate').val();
      var ToDate = $('#adv_ToDate').val();
      if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (PackageUID == '') &&  (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
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


       var formData = ({ 'ProductUID':ProductUID,'MilestoneUID':MilestoneUID,'StateUID':StateUID,'LoanNo':LoanNo,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 

       WorkflowQueueReportinitialize(formData);

      }
      return false;
    });

  });

</script>