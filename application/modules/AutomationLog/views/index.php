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

  .input-group-prepend{
    background: #00bcd4;
    height: 35px;
    font-size: 12px;
    color: #ddd;
    text-align: center;
  }
  .input-group-text{
    font-size: 12px;
    color: #fff;
    text-align: center;
  }
  .ma-0{
    margin: 0px !important;
  }

</style>

<div class="card mt-40" id="Exceptionorders">

	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-thumbs-up2"></i>
    </div>
  </div>

  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active automation-advance-trigger" data-toggle="tab" href="#fax_details" role="tablist"> Automation Log
          <span class="badge badge-pill badge-primary" id="newdoc-checkin-count" style="background-color: #fff;color: #000;"></span>
        </a>
      </li>
    </ul>

    <div class="tab-content tab-space customtabpane">
      <div class="tab-pane active" id="fax_details">
        <?php $this->load->view('automation_advance_filter'); ?>
        <div  class="material-datatables" >
          <table class="table table-hover table-striped" id="automationLog">
            <thead>
              <tr>
                <th>Order&nbsp;Number</th>
                <th>Loan&nbsp;Number</th>
                <th>Automation&nbsp;Type</th>
                <th>Status</th>
                <th>Date&nbsp;Time</th>  
                <th class="no-sort">Actions</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="md-metadata" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;">Fax Meta Data</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmMetaData">
              <div class="col-md-12">
                <div class="form-group">
                  <span class="fax-metadata"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <button class="btn btn-space btn-social btn-color btn-danger" data-dismiss="modal" style="" id="Close">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="md-emailresponse" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;"> Email Content </h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmEmailResponse">
              <div class="col-md-12">
                <div class="form-group">
                  <div id="html_content">
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <button class="btn btn-space btn-social btn-color btn-danger AddAttachment" >Add to Attachment</button>
            <button class="btn btn-space btn-social btn-color btn-danger Close" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="md-OCRResponse" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;">OCR Response</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmOCRResponse">
              <div class="col-md-12">
                <div class="form-group">
                  <span class="ocr_response"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <button class="btn btn-space btn-social btn-color btn-danger AddAttachment" >Add to Attachment</button>
            <button class="btn btn-space btn-social btn-color btn-danger Close" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  
  $('.automation-advance').click(function(){
    $("#automationadvancedsearch").slideToggle();
  });

  var automationLog = false;
  $(function() {
    $("select.select2picker").select2({
      theme: "bootstrap",
    });
    $('#automationLog').DataTable().destroy();
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

    log_initialization('false');
    function log_initialization(formdata)
    {
      automationLog = $('#automationLog').DataTable( {
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        scrollY: 360,
        paging:  true,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 10,
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
        "ajax": {
          "url": "<?php echo base_url('AutomationLog/log_ajax_list')?>",
          "type": "POST",
          "data" : {'formData':formdata}  
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }]
      });
      var searchGet = '<?php echo (isset($_GET) && $_GET['search'] ) ? $_GET['search'] : ''; ?>';
      if(searchGet != ''){
        automationLog.search( searchGet ).draw();
      }
      
    }

    $(document).off('click','.btn_metadata').on('click','.btn_metadata',function() { 
      var EFaxDataUID = $(this).attr('data-logid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/GetMetaDataByFaxID',
        data: {'EFaxDataUID':EFaxDataUID},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          $('#md-metadata').modal('show');
          $('.fax-metadata').html('');
          $('.fax-metadata').html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
        },
        failure: function (jqXHR, textStatus, errorThrown) {
          console.log(errorThrown);
        },
      });
    });

    $(document).off('click','.btn_emaildata').on('click','.btn_emaildata',function() { 
      var EmailUID = $(this).attr('data-logid');
      var OrderUID = $(this).attr('data-orderid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>AutomationLog/GetEmailResponse',
        data: {'EmailUID':EmailUID,'OrderUID':OrderUID},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          if(data.status == 1) {            
            $('#md-emailresponse').modal('show');
            $('#md-emailresponse .modal-dialog').addClass('md-lg');
            $('#html_content').html(data.html);
          } else {
            $.notify(
            {
              icon:"icon-bell-check",
              message:'No Data Found'
            },
            {
              type:'danger',
              delay:1000 
            });
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

    $(document).off('click','.btn_loandata').on('click','.btn_loandata',function() { 
      var DocumentUID = $(this).attr('data-logid');
      var OrderUID = $(this).attr('data-orderid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>AutomationLog/GetOCRResponse',
        data: {'DocumentUID':DocumentUID,'OrderUID':OrderUID},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          if(data.status == 1){
            $('#md-OCRResponse').modal('show');
            $('.ocr_response').html('');
            $('.ocr_response').html(data.response);
          } else {
            $.notify(
            {
              icon:"icon-bell-check",
              message:'No Response Found'
            },
            {
              type:'danger',
              delay:1000 
            });
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

    $(document).off('click','.search').on('click','.search',function()
    {
      var OrderNumber = $('#OrderNumber').val();
      var LoanNumber = $('#LoanNumber').val();
      var AutomationType = $('#AutomationType').val();
      var AutomationStatus = $('#AutomationStatus').val();
      var FromDate = $('#FromDate').val();
      var ToDate = $('#ToDate').val();

      if((OrderNumber == '') && (LoanNumber == '') && (AutomationType == '') && (AutomationStatus == '') && (FromDate == '') && (ToDate == ''))
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
        var formData = ({'OrderNumber':OrderNumber,'LoanNumber':LoanNumber,'AutomationType':AutomationType,'AutomationStatus':AutomationStatus,'FromDate':FromDate,'ToDate':ToDate}); 
        log_initialization(formData);
      }
      return false;
    });

    $(document).off('click','.reset').on('click','.reset',function()
    {
      $('#OrderNumber').val('');
      $('#LoanNumber').val('');
      $('#AutomationType').val('');
      $('#AutomationStatus').val('');
      $("#FromDate").val('<?php echo date('m/d/Y',strtotime("-30 days")); ?>');
      $("#ToDate").val('<?php echo date('Y-m-d'); ?>');
      log_initialization('false');
      callselect2();
    });
  });

$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
  var OrderNumber = $('#OrderNumber').val();
  var LoanNumber = $('#LoanNumber').val();
  var AutomationType = $('#AutomationType').val();
  var AutomationStatus = $('#AutomationStatus').val();
  var FromDate = $('#FromDate').val();
  var ToDate = $('#ToDate').val();

  if((OrderNumber == '') && (LoanNumber == '') && (AutomationType == '') && (AutomationStatus == '') && (FromDate == '') && (ToDate == ''))
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
    var formData = ({'OrderNumber':OrderNumber,'LoanNumber':LoanNumber,'AutomationType':AutomationType,'AutomationStatus':AutomationStatus,'FromDate':FromDate,'ToDate':ToDate}); 
  }

  $.ajax({
    type: "POST",
    url: '<?php echo base_url();?>AutomationLog/WriteExcel',
    xhrFields: {
      responseType: 'blob',
    },
    data: {'formData':formData},
    beforeSend: function(){
    },
    success: function(data)
    {
      var filename = "AutomationLog.csv";
      if (typeof window.chrome !== 'undefined') {
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(data);
        link.download = "AutomationLog.csv";
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

</script>
