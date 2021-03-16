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

  /** @desc css for overflow of search results
    * @author Yagavi G <yagavi.g2avanzegroup.com>
    * @since July 20th 2020
  **/
  .overflow-search {
    width: 100%;
    height: 415px;
    overflow-y: scroll;
  }

</style>

<div class="card mt-40" id="Exceptionorders">

	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-thumbs-up2"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Efax Orders</h4>
      </div>
    </div>
  </div>


  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active fax-advance-trigger" search-division="faxadvancedsearch" data-toggle="tab" href="#fax_details" role="tablist">
          Fax Orders
        </a>
      </li>
      <!-- Non Mapped Fax Orders Start -->
      <li class="nav-item">
        <a class="nav-link fax-non-mapped-orders" search-division="FaxReceiveSearch" data-toggle="tab" href="#FaxNonMappedOrder" role="tablist">
          Fax Orders (Not Mapped)
        </a>
      </li>
       <!-- Non Mapped Fax Orders End -->
      <li class="nav-item">
        <a class="nav-link fax-sent-advance-trigger" search-division="sent_faxadvancedsearch" data-toggle="tab" href="#sent_fax_details" role="tablist">
          List of Fax Sent
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link fax-receive-advance-trigger" search-division="receive_faxadvancedsearch" data-toggle="tab" href="#receive_fax_details" role="tablist">
          List of Fax Receive
        </a>
      </li>  
    </ul>

    <div class="tab-content tab-space customtabpane">
      <div class="tab-pane active" id="fax_details">
        <?php $this->load->view('fax_advance_filter'); ?>
        <div  class="material-datatables" >
          <table class="table table-hover table-striped" id="eFaxTable">
            <thead>
              <tr>
                <th>Order&nbsp;No</th>
                <th>Client</th>
                <th>Fax&nbsp;ID</th>
                <!-- <th>From&nbsp;Fax&nbsp;Number</th> -->
                <th>To&nbsp;Fax&nbsp;Number</th>
                <th>Transmission&nbsp;Status</th>
                <th>Fax&nbsp;Status</th>
                <th>Loan&nbsp;No</th>
                <th>LastModified&nbsp;DateTime</th>  
                <th class="no-sort">Actions</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Non Mapped Fax Orders Start -->
      <div class="tab-pane active" id="FaxNonMappedOrder">
        <?php $this->load->view('fax_receive_filter'); ?>
        <div  class="material-datatables" >
          <table class="table table-hover table-striped" id="eFaxTableReceive" style="display: none;">
            <thead>
              <tr>
                <th>Fax&nbsp;ID</th>
                <th>From&nbsp;Fax&nbsp;Number</th>
                <th>To&nbsp;Fax&nbsp;Number</th>
                <th>Transmission&nbsp;Status</th>
                <th>Fax&nbsp;Status</th>
                <th>Loan&nbsp;No</th>
                <th>LastModified&nbsp;DateTime</th>  
                <th class="no-sort">Actions</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
      <!-- Non Mapped Fax Orders End -->

      <div class="tab-pane" id="sent_fax_details">
        <?php $this->load->view('list_fax_advance_filter'); ?>
        <div  class="material-datatables" >
          <table class="table table-hover table-striped" id="eFaxLiveTable" style="display: none;">
            <thead>
              <tr>
                <th>Fax&nbsp;ID</th>
                <th>Size</th>
                <th>Duration</th>
                <th>Pages</th>
                <th>Image&nbsp;Download</th>
                <th>Fax&nbsp;Status</th>
                <th>Completed&nbsp;Timestamp</th>  
                <th>Originating&nbsp;Fax&nbsp;Number</th>
                <th>Destination&nbsp;Fax&nbsp;Number</th>
                <th>Routing&nbsp;To&nbsp;Name</th>
                <th>Routing&nbsp;To&nbsp;Company</th>
                <th>Routing&nbsp;To&nbsp;Subject</th>
                <th>Transmission&nbsp;Error&nbsp;Code</th>
                <th>Transmission&nbsp;Error&nbsp;Message</th>
                <th>Transmission&nbsp;Status</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane" id="receive_fax_details">
        <?php $this->load->view('receive_fax_advance_filter'); ?>
        <div  class="material-datatables" >
          <table class="table table-hover table-striped" id="eFaxReceiveTable">
            <thead>
              <tr>
                <th>Fax&nbsp;ID</th>
                <th>Size</th>
                <th>Duration</th>
                <th>Pages</th>
                <th>Image&nbsp;Download</th>
                <th>Fax&nbsp;Status</th>
                <th>Completed&nbsp;Timestamp</th>  
                <th>Originating&nbsp;Fax&nbsp;Number</th>
                <th>Destination&nbsp;Fax&nbsp;Number</th>
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
            <button class="btn btn-space btn-social btn-color btn-danger FaxClose" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="md-faximage" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;">Fax Image</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmFaxImage">
              <div class="col-md-12">
                <div class="form-group">
                  <span class="fax-faximage"></span>
                  <input type="hidden" name="fax-imageURL" class="fax-imageURL" id="fax-imageURL">
                  <input type="hidden" name="FaxOrderUID" class="FaxOrderUID" id="FaxOrderUID">
                  <input type="hidden" name="FaxImageName" class="FaxImageName" id="FaxImageName">
                  <iframe id="iFramePDF" width="100%" height=450 src="" ></iframe>
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

  <!-- Start: Modal for Fax Meta Data for Receive Fax -->
  <div id="md-receive-metadata" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;">Fax Meta Data</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="Frm_ReceiveMetaData">
              <div class="col-md-12">
                <div class="form-group">
                  <span class="fax-metadata"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <button class="btn btn-space btn-social btn-color btn-danger FaxClose" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End: Modal for Fax Meta Data for Receive Fax -->

  <!-- Start: Modal for Fax Image for Receive Fax -->
  <div id="md-receive-faximage" tabindex="-1" role="dialog"  class="modal fade custommodal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;">
          <h5 style="color: #fff;">Fax Image</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmFaxImage">
              <div class="col-md-12">
                <div class="form-group">
                  <span class="fax-faximage"></span>
                  <input type="hidden" name="fax-imageURL" class="fax-imageURL" id="fax-imageURL">
                  <input type="hidden" name="FaxOrderUID" class="FaxOrderUID" id="FaxOrderUID">
                  <input type="hidden" name="FaxImageName" class="FaxImageName" id="FaxImageName">
                  <iframe id="iFramePDF" width="100%" height=450 src="" ></iframe>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <!-- <button class="btn btn-space btn-social btn-color btn-danger AddAttachment" >Add to Attachment</button> -->
            <button class="btn btn-space btn-social btn-color btn-danger Close" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End: Modal for Fax Image for Receive Fax -->

  <!-- Start: Modal for Fax Image for Receive Fax -->
  <div id="md-faximage-receive" tabindex="-1" role="dialog"  class="modal fade custommodal" style="margin-top: -90px;">
    <div class="modal-dialog" style="max-width: 90%">
      <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #1d4870;padding: 16px 15px 0px !important">
          <h5 style="color: #fff;">Fax Image</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div id="append_history"></div>
            <form id="FrmFaxImage">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <span class="fax-faximage"></span>
                    <input type="hidden" name="fax-imageURL" class="fax-imageURL" id="fax-imageURL">
                    <input type="hidden" name="FaxID" class="FaxID" id="FaxID">
                    <input type="hidden" name="FaxImageName" class="FaxImageName" id="FaxImageName">
                    <iframe id="iFramePDF" width="100%" height="450px" src="" ></iframe>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row" style="float: right;">
                    <form id="form_search">
                      <span class="bmd-form-group">
                        <div class="input-group no-border">
                          <input type="text" name="SearchOrderDetails" class="form-control" placeholder="Search..." id="SearchOrderDetails">
                          <button class="btn btn-white btn-round btn-just-icon" id="BtnSearchOrderDetails">
                            <i class="icon-search4"></i>
                            <div class="ripple-container"></div>
                          </button>
                        </div>
                      </span>
                    </form>
                  </div>
                  <div class="row overflow-search" id="SearchResults"></div>
                </div>
              </div>
            </form>
          </div>
          <div class="text-right">
            <button class="btn btn-space btn-social btn-color btn-danger AddToAttachment" >Add to Attachment</button>
            <button class="btn btn-space btn-social btn-color btn-danger Close faxreceiveclose" data-dismiss="modal" style="" id="Close" value="" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End: Modal for Fax Image for Receive Fax -->

</div>

<script type="text/javascript">
  $('.FaxClose').click(function(){
     $(".search").trigger('click');
   });
  $('.nav-link').on('click', function(){
    var search_devision = $(this).attr('search-division');
    $('.SearchBlock').hide();
    $('#'+search_devision).show();
  });
  var eFaxTable = false;
  var eFaxLiveTable = false;
  var eFaxTableReceive = false;
  $(function() {
    $("select.select2picker").select2({
      theme: "bootstrap",
    });
    $('#eFaxTable').DataTable().destroy();
    $('#eFaxLiveTable').DataTable().destroy();
    $('#eFaxTableReceive').DataTable().destroy();
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

    efax_initialization('false');
    function efax_initialization(formdata)
    {
      eFaxTable = $('#eFaxTable').DataTable( {
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
          "url": "<?php echo base_url('EfaxOrders/efax_ajax_list')?>",
          "type": "POST",
          "data" : {'formData':formdata}  
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }]
      });
    }

    $(document).off('click','.fax-non-mapped-orders').on('click','.fax-non-mapped-orders',function() {
      NonMappedOrders('false');
    });

    $(document).off('click','.btn_metadata').on('click','.btn_metadata',function() { 
      var EFaxDataUID = $(this).attr('data-faxid');
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

    $(document).off('click','.btn_faximage').on('click','.btn_faximage',function() { 
      var EFaxDataUID = $(this).attr('data-faxid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/GetFaxImageByFaxID',
        data: {'EFaxDataUID':EFaxDataUID},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          $('#md-faximage').modal('show');
          if(data.status == 1){
            $('#md-faximage .modal-dialog').addClass('md-lg');
            $('.fax-imageURL').val('');
            $('.fax-imageURL').val(data.filepath);
            $('.FaxOrderUID').val('');
            $('.FaxOrderUID').val(data.OrderUID);
            $('.FaxImageName').val('');
            $('.FaxImageName').val(data.filename);
            $('.fax-faximage').html('');
            $('#iFramePDF').css('display','block');
            $('#iFramePDF').attr('src', '<?php echo base_url()?>/uploads/Efax_files/'+data.filename+'?<?php echo microtime(1); ?>');
          } else {
            $('#md-faximage .modal-dialog').removeClass('md-lg');
            $('.fax-faximage').html('');
            $('.fax-faximage').html(data.error);
            $('#iFramePDF').css('display','none');
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

    /* @Desc: Ajax to get the efax meta data for single fax id @author: Yagavi G <yagavi.g@avanzegroup.com> @since July 17th 2020*/
    $(document).off('click','.btn_receive_metadata').on('click','.btn_receive_metadata',function() { 
      var faxid = $(this).attr('data-faxid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/GetReceiveMetaDataByFaxID',
        data: {'faxid':faxid},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          $('#md-receive-metadata').modal('show');
          $('#md-receive-metadata .fax-metadata').html('');
          $('#md-receive-metadata .fax-metadata').html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
        },
        failure: function (jqXHR, textStatus, errorThrown) {
          console.log(errorThrown);
        },
      });
    });

    /* @Desc: Ajax to get the efax image for single fax id @author: Yagavi G <yagavi.g@avanzegroup.com> @since July 17th 2020 */
    $(document).off('click','.btn_receive_faximage').on('click','.btn_receive_faximage',function() { 
      var faxid = $(this).attr('data-faxid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/GetReceiveFaxImageByFaxID',
        data: {'faxid':faxid},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          $('#md-receive-faximage').modal('show');
          if(data.status == 1){
            $('#md-receive-faximage .modal-dialog').addClass('md-lg');
            $('#md-receive-faximage .fax-imageURL').val('');
            $('#md-receive-faximage .fax-imageURL').val(data.filepath);
            $('#md-receive-faximage .FaxOrderUID').val('');
            $('#md-receive-faximage .FaxOrderUID').val(data.OrderUID);
            $('#md-receive-faximage .FaxImageName').val('');
            $('#md-receive-faximage .FaxImageName').val(data.filename);
            $('#md-receive-faximage .fax-faximage').html('');
            $('#md-receive-faximage #iFramePDF').css('display','block');
            $('#md-receive-faximage #iFramePDF').attr('src', '<?php echo base_url()?>uploads/Efax_files/'+data.filename+'?<?php echo microtime(1); ?>');
          } else {
            $('#md-receive-faximage .modal-dialog').removeClass('md-lg');
            $('#md-receive-faximage .fax-faximage').html('');
            $('#md-receive-faximage .fax-faximage').html(data.error);
            $('#md-receive-faximage #iFramePDF').css('display','none');
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
      var FaxID = $('#FaxID').val();
      var FromFaxNumber = $('#FromFaxNumber').val();
      var ToFaxNumber = $('#ToFaxNumber').val();
      var TransmissionStatus = $('#TransmissionStatus').val();
      var FaxStatus = $('#FaxStatus').val();
      var FromDate = $('#FromDate').val();
      var ToDate = $('#ToDate').val();

      if((OrderNumber == '') && (FaxID == '') && (FromFaxNumber == '') && (ToFaxNumber == '') && (TransmissionStatus == '') &&  (FaxStatus == '') && (FromDate == '') && (ToDate == ''))
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
        var formData = ({'OrderNumber':OrderNumber,'FaxID':FaxID,'FromFaxNumber':FromFaxNumber,'ToFaxNumber':ToFaxNumber,'TransmissionStatus':TransmissionStatus,'FaxStatus':FaxStatus,'FromDate':FromDate,'ToDate':ToDate}); 
        efax_initialization(formData);
      }
      return false;
    });

    $(document).off('click','.reset').on('click','.reset',function()
    {
      $('#TransmissionStatus').val('');
      $('#FaxStatus').val('');
      $('#OrderNumber').val('');
      $('#FaxID').val('');
      $('#FromFaxNumber').val('');
      $('#ToFaxNumber').val('');
      $("#FromDate").val('<?php echo date('m/d/Y',strtotime("-30 days")); ?>');
      $("#ToDate").val('<?php echo date('Y-m-d'); ?>');
      efax_initialization('false');
      callselect2();
    });

    $(document).off('click','.fax_reset').on('click','.fax_reset',function()
    {
      $('#transmission_status').val('COMPLETE');
      $('#fax_status').val('');
      $('#transaction_id').val('');
      $('#image_downloaded').val('');
      $('#originating_fax_number').val('');
      $('#destination_fax_number').val('');
      $('#error_code').val('');
      $('#min_pages').val('');
      $('#max_pages').val('');
      $('#search_text').val('');
      $("#min_completed_timestamp").val('<?php echo date('m/d/Y',strtotime("-30 days")); ?>');
      $("#max_completed_timestamp").val('<?php echo date('Y-m-d'); ?>');
      sent_efax_initialization('false');
      callselect2();
    });

    $(document).off('click','.fax_receive_reset').on('click','.fax_receive_reset',function()
    {
      $('#receive_image_downloaded').val('true');
      $('#receive_originating_fax_number').val('');
      $('#receive_destination_fax_number').val('');
      $('#receive_error_code').val('');
      $('#receive_min_pages').val('');
      $('#receive_max_pages').val('');
      $('#receive_search_text').val('');
      $("#receive_min_completed_timestamp").val('<?php echo date('m/d/Y',strtotime("-30 days")); ?>');
      $("#receive_max_completed_timestamp").val('<?php echo date('Y-m-d'); ?>');
      receive_efax_initialization('false');
      callselect2();
    });


    $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
      var OrderNumber = $('#OrderNumber').val();
      var FaxID = $('#FaxID').val();
      var FromFaxNumber = $('#FromFaxNumber').val();
      var ToFaxNumber = $('#ToFaxNumber').val();
      var TransmissionStatus = $('#TransmissionStatus').val();
      var FaxStatus = $('#FaxStatus').val();
      var FromDate = $('#FromDate').val();
      var ToDate = $('#ToDate').val();

      if((OrderNumber == '') && (FaxID == '') && (FromFaxNumber == '') && (ToFaxNumber == '') && (TransmissionStatus == '') &&  (FaxStatus == '') && (FromDate == '') && (ToDate == '')){
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
        var formData = ({'OrderNumber':OrderNumber,'FaxID':FaxID,'FromFaxNumber':FromFaxNumber,'ToFaxNumber':ToFaxNumber,'TransmissionStatus':TransmissionStatus,'FaxStatus':FaxStatus,'FromDate':FromDate,'ToDate':ToDate}); 
      }

      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/WriteExcel',
        xhrFields: {
          responseType: 'blob',
        },
        data: {'formData':formData},
        beforeSend: function(){
        },
        success: function(data)
        {
          var filename = "FaxOrders.csv";
          if (typeof window.chrome !== 'undefined') {
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "FaxOrders.csv";
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

    $(document).off('click','.fax_exceldownload').on('click','.fax_exceldownload',function(){
      var transaction_id = $('#transaction_id').val();
      var transmission_status = $('#transmission_status').val();
      var fax_status = $('#fax_status').val();
      var image_downloaded = $('#image_downloaded').val();
      var originating_fax_number = $('#originating_fax_number').val();
      var destination_fax_number = $('#destination_fax_number').val();
      var error_code = $('#error_code').val();
      var min_pages = $('#min_pages').val();
      var max_pages = $('#max_pages').val();
      var search_text = $('#search_text').val();
      var min_completed_timestamp = $('#min_completed_timestamp').val();
      var max_completed_timestamp = $('#max_completed_timestamp').val();

      if((transaction_id == '') && (transmission_status == '') && (fax_status == '') && (image_downloaded == '') && (originating_fax_number == '') && (destination_fax_number == '') && (error_code == '') && (min_pages == '') && (max_pages == '') && (search_text == '') && (min_completed_timestamp == '') && (max_completed_timestamp == '')) {
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
        var formData = ({'transaction_id':transaction_id,'transmission_status':transmission_status,'fax_status':fax_status,'image_downloaded':image_downloaded,'originating_fax_number':originating_fax_number,'destination_fax_number':destination_fax_number,'error_code':error_code,'min_pages':min_pages,'max_pages':max_pages,'search_text':search_text,'min_completed_timestamp':min_completed_timestamp,'max_completed_timestamp':max_completed_timestamp});
      }

      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/FaxWriteExcel',
        xhrFields: {
          responseType: 'blob',
        },
        data: {'formData':formData},
        beforeSend: function(){
        },
        success: function(data)
        {
          var filename = "FaxOrders.csv";
          if (typeof window.chrome !== 'undefined') {
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "FaxOrders.csv";
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

    $(document).off('click','.AddAttachment').on('click','.AddAttachment',function()
    { 
      var imageURL = $('.fax-imageURL').val();
      var OrderUID = $('.FaxOrderUID').val();
      var FaxImageName = $('.FaxImageName').val();
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/StoreDocuments',
        data: {'OrderUID':OrderUID,'imageURL':imageURL,'FaxImageName':FaxImageName},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          if(data){
            $('#md-faximage').modal('hide');
            $.notify({icon:"icon-bell-check",message:'Fax Image is added'},{type:'success',delay:1000});
          } else {
            $.notify({icon:"icon-bell-check",message:'Not Added'},{type:'danger',delay:1000});
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

    $(document).off('click','.fax_search').on('click','.fax_search',function()
    {
      var transaction_id = $('#transaction_id').val();
      var transmission_status = $('#transmission_status').val();
      var fax_status = $('#fax_status').val();
      var image_downloaded = $('#image_downloaded').val();
      var originating_fax_number = $('#originating_fax_number').val();
      var destination_fax_number = $('#destination_fax_number').val();
      var error_code = $('#error_code').val();
      var min_pages = $('#min_pages').val();
      var max_pages = $('#max_pages').val();
      var search_text = $('#search_text').val();
      var min_completed_timestamp = $('#min_completed_timestamp').val();
      var max_completed_timestamp = $('#max_completed_timestamp').val();

      if((transaction_id == '') && (transmission_status == '') && (fax_status == '') && (image_downloaded == '') && (originating_fax_number == '') && (destination_fax_number == '') && (error_code == '') && (min_pages == '') && (max_pages == '') && (search_text == '') && (min_completed_timestamp == '') && (max_completed_timestamp == '')) {
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

        var formData = ({'transaction_id':transaction_id,'transmission_status':transmission_status,'fax_status':fax_status,'image_downloaded':image_downloaded,'originating_fax_number':originating_fax_number,'destination_fax_number':destination_fax_number,'error_code':error_code,'min_pages':min_pages,'max_pages':max_pages,'search_text':search_text,'min_completed_timestamp':min_completed_timestamp,'max_completed_timestamp':max_completed_timestamp});
        sent_efax_initialization(formData);
      }
      return false;
    });

    function sent_efax_initialization(formdata)
    {
      $('#eFaxLiveTable').css('display','table');
      var eFaxLiveTable = $('#eFaxLiveTable').DataTable( {
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        scrollY: 360,
        paging:  false,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
        fixedColumns: {
          leftColumns: 1,
          rightColumns: 2
        },
        searchDelay:1000,
        initComplete: function(settings, json) {
          $($.fn.dataTable.tables( true ) ).css('width', '100%');
        },
        "ajax": {
          "url": "<?php echo base_url('EfaxOrders/live_efax_ajax_list')?>",
          "type": "POST",
          "data" : {'formData':formdata}  
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }]
      });
    }

    /**
    * Function to retrive the fax images from e-Fax Integration
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @return List of Fax in Array
    * @since July 14th 2020
    * @version E-Fax Integration
    *
    */

    $(document).off('click','.fax_receive_search').on('click','.fax_receive_search',function()
    {
      var transaction_id = $('#receive_transaction_id').val();
      var image_downloaded = $('#receive_image_downloaded').val();
      var originating_fax_number = $('#receive_originating_fax_number').val();
      var destination_fax_number = $('#receive_destination_fax_number').val();
      var error_code = $('#receive_error_code').val();
      var min_pages = $('#receive_min_pages').val();
      var max_pages = $('#receive_max_pages').val();
      var search_text = $('#receive_search_text').val();
      var min_completed_timestamp = $('#receive_min_completed_timestamp').val();
      var max_completed_timestamp = $('#receive_max_completed_timestamp').val();

      if((transaction_id == '') && (image_downloaded == '') && (originating_fax_number == '') && (destination_fax_number == '') && (error_code == '') && (min_pages == '') && (max_pages == '') && (search_text == '') && (min_completed_timestamp == '') && (max_completed_timestamp == '')) {
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

        var formData = ({'transaction_id':transaction_id,'image_downloaded':image_downloaded,'originating_fax_number':originating_fax_number,'destination_fax_number':destination_fax_number,'error_code':error_code,'min_pages':min_pages,'max_pages':max_pages,'search_text':search_text,'min_completed_timestamp':min_completed_timestamp,'max_completed_timestamp':max_completed_timestamp});

        receive_efax_initialization(formData);
      }
      return false;
    });

    function receive_efax_initialization(formdata)
    {
      $('#eFaxReceiveTable').css('display','table');
      var eFaxReceiveTable = $('#eFaxReceiveTable').DataTable( {
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        scrollY: 360,
        paging:  false,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
        fixedColumns: {
          leftColumns: 1,
          rightColumns: 2
        },
        searchDelay:1000,
        initComplete: function(settings, json) {
          $($.fn.dataTable.tables( true )).css('width', '100%');
        },
        "ajax": {
          "url": "<?php echo base_url('EfaxOrders/receive_efax_ajax_list')?>",
          "type": "POST",
          "data" : {'formData':formdata}  
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }]
      });
    }

    /**
    * Function to list the receive fax list into the table
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */

    function NonMappedOrders(formdata)
    {
      $('#eFaxTableReceive').css('display','table');
      var eFaxTableReceive = $('#eFaxTableReceive').DataTable( {
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
          "url": "<?php echo base_url('EfaxOrders/efax_receive_ajax_list')?>",
          "type": "POST",
          "data" : {'formData':formdata}  
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }]
      });
    }

    /**
    * Click function to filter for receive fax list
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */

    $(document).off('click','.faxReceivesearch').on('click','.faxReceivesearch',function()
    {
      var OrderNumber = $('#ReceiveOrderNumber').val();
      var FaxID = $('#ReceiveFaxID').val();
      var FromFaxNumber = $('#ReceiveFromFaxNumber').val();
      var ToFaxNumber = $('#ReceiveToFaxNumber').val();
      var TransmissionStatus = $('#ReceiveTransmissionStatus').val();
      var FaxStatus = $('#ReceiveFaxStatus').val();
      var FromDate = $('#ReceiveFromDate').val();
      var ToDate = $('#ReceiveToDate').val();

      if((OrderNumber == '') && (FaxID == '') && (FromFaxNumber == '') && (ToFaxNumber == '') && (TransmissionStatus == '') &&  (FaxStatus == '') && (FromDate == '') && (ToDate == ''))
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
        var formData = ({'OrderNumber':OrderNumber,'FaxID':FaxID,'FromFaxNumber':FromFaxNumber,'ToFaxNumber':ToFaxNumber,'TransmissionStatus':TransmissionStatus,'FaxStatus':FaxStatus,'FromDate':FromDate,'ToDate':ToDate}); 
        NonMappedOrders(formData);
      }
      return false;
    });

    /**
    * Click function to reset the filter for receive fax list
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */

    $(document).off('click','.faxReceiveReset').on('click','.faxReceiveReset',function()
    {
      $('#ReceiveTransmissionStatus').val('');
      $('#ReceiveFaxStatus').val('');
      $('#ReceiveOrderNumber').val('');
      $('#ReceiveFaxID').val('');
      $('#ReceiveFromFaxNumber').val('');
      $('#ReceiveToFaxNumber').val('');
      $("#ReceiveFromDate").val('<?php echo date('m/d/Y',strtotime("-30 days")); ?>');
      $("#ReceiveToDate").val('<?php echo date('Y-m-d'); ?>');
      NonMappedOrders('false');
      callselect2();
    });

    $('.faxreceiveclose').click(function(){
      $(".faxReceiveReset").trigger('click');
    });

    /**
    * Click function to get the fax image from efax
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */
  
    $(document).off('click','.receive_btn_faximage').on('click','.receive_btn_faximage',function() { 
      var faxid = $(this).attr('data-faxid');
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/GetReceiveFaxImageByFaxID',
        data: {'faxid':faxid},
        dataType:'JSON',
        beforeSend: function(){
        },
        success: function(data){
          $('#md-faximage-receive').modal('show');
          $('#md-faximage-receive #SearchResults').html('');
          $('#SearchOrderDetails').val('');
          if(data.status == 1){
            $('#md-faximage-receive .fax-imageURL').val('');
            $('#md-faximage-receive .fax-imageURL').val(data.filepath);
            $('#md-faximage-receive .FaxID').val('');
            $('#md-faximage-receive .FaxID').val(faxid);
            $('#md-faximage-receive .FaxImageName').val('');
            $('#md-faximage-receive .FaxImageName').val(data.filename);
            $('#md-faximage-receive .fax-faximage').html('');
            $('#md-faximage-receive #iFramePDF').css('display','block');
            $('#md-faximage-receive #iFramePDF').attr('src', '<?php echo base_url()?>/uploads/Efax_files/'+data.filename+'?<?php echo microtime(1); ?>');
          } else {
            $('#md-faximage-receive .modal-dialog').css('max-width', "50%");
            $('#md-faximage-receive .fax-faximage').html('');
            $('#md-faximage-receive .fax-faximage').html(data.error);
            $('#md-faximage-receive #iFramePDF').css('display','none');
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

    /**
    * Click function to search the order details for mao fax image manually
    *
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */

    $(document).off('click','#BtnSearchOrderDetails').on('click','#BtnSearchOrderDetails',function() { 
      var SearchText = $('#SearchOrderDetails').val();
      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Search/SearchOrders',
        data: {'SearchText':SearchText},
        beforeSend: function(){
        },
        success: function(data){
          if(data){
            $('#md-faximage-receive #SearchResults').html('');
            $('#md-faximage-receive #SearchResults').html(data);
          } else {
            $('#md-faximage-receive #SearchResults').html('');
            $('#md-faximage-receive #SearchResults').html('<h5 class="panel-heading mt-20 tbheading">No Records Found</h5>');
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

    /**
    * Click function to add fax image to the order
    *
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @since July 20th 2020
    * @version E-Fax Integration
    *
    */
    
    $(document).off('click','.AddToAttachment').on('click','.AddToAttachment',function() { 

      var select_radio = $('.copy_select_radio:checked').length;
      var filepath = $('#md-faximage-receive #fax-imageURL').val();
      var faxID = $('#md-faximage-receive #FaxID').val();
      var filename = $('#md-faximage-receive #FaxImageName').val();
      var orderuid = [];
      if(select_radio>0) {
        $('.copy_select_radio:checked').each(function(){
          orderuid.push($(this).closest('tr').attr('data-id'));
        });
      } else {
        $.notify({icon:"icon-bell-check",message:'No choosen Order'},{type:'danger', delay:1000});
        return false;
      }

      $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>EfaxOrders/AddFaxImageToOrder',
        data: {'filename':filename,'filepath':filepath,'orderuid':orderuid,'faxID':faxID},
        dataType:'JSON',
        success: function(data){
          if(data.status == 1){
            $.notify({icon:"icon-bell-check",message:'Fax Image Added'},{type:'success', delay:1000});
          } else {
            $.notify({icon:"icon-bell-check",message:data.msg},{type:'danger', delay:1000});
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
});
</script>