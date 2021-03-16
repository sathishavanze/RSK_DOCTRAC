<style>
  th, td { text-align: center; }

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
      <h4 class="card-title">DocChase Report</h4>
    </div>
<!--     <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">DocChase Report</h4>
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
              <label for="adv_Status" class="bmd-label-floating">Status </label>
              <select class="select2picker form-control" id="adv_Status"  name="StatusUID">   
                <option value="All">All</option>
                <?php foreach ($Status as $key => $value) { ?>
                  <option value="<?php echo $value->StatusUID; ?>" ><?php echo $value->StatusName; ?></option>
                <?php } ?>                      
              </select>
            </div>
          </div>
           
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

          <div class="col-md-12  text-right pd-0 mt-10">
            <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
            <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
          </div>

        </div>
      </form>
    </fieldset>
  </div>

<div class="material-datatables">
  <table class="table table-hover table-striped" id="DocChaseRepot">
    <thead>
     <tr>
      <th>Order Number</th>
      <th>Loan Number</th>
      <th>StatusName</th>
      <?php foreach ($Modules as $key => $value) { 
       if($this->config->item('Workflows')['DocChase'] == $value->WorkflowModuleUID)
       {

       }
       else
         {   ?>

          <th><?php echo $value->SystemName; ?></th>
        <?php }} ?> 
      <th class="no-sort">Actions</th>
    </tr>
  </thead>

  <tbody>

  </tbody>
</table>
</div>

</div>


<script type="text/javascript">
  var DocChaseRepot = false;
  $(function() {
    $("select.select2picker").select2({
      //tags: false,
      theme: "bootstrap",
    });
    $('#DocChaseRepot').DataTable().destroy();
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
    DocChaseReport('false')
    function DocChaseReport(formdata)
    {
      DocChaseRepot = $('#DocChaseRepot').DataTable( {
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
            "url": "<?php echo base_url('DocChaseReport/FetchDocChaseReportReport')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ],
        });
    }


    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
              var ProjectUID = $('#adv_ProjectUID option:selected').val();
              var Status = $('#adv_Status').val();
              var FromDate = $('#adv_FromDate').val();
              var ToDate = $('#adv_ToDate').val();
              if((ProjectUID == '')  && (Status == '')&& (FromDate == '')&& (ToDate == ''))
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
               var formData = ({'ProjectUID': ProjectUID, 'Status':Status,'FromDate':FromDate,'ToDate':ToDate}); 
               DocChaseReport(formData);
             }

             return false;
           });

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_ProjectUID").val('All');
      $("#adv_Status").val('All');
      $("#adv_FromDate").val('');
      $("#adv_ToDate").val('');
      DocChaseReport('false');
      callselect2();

    });
  });


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  var Status = $('#adv_Status option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '')  && (Status == '')&& (FromDate == '')&& (ToDate == ''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({'ProjectUID': ProjectUID ,'Status':Status,'FromDate':FromDate,'ToDate':ToDate}); 
  }
  

  $.ajax({
   type: "POST",
   url: '<?php echo base_url();?>DocChaseReport/WriteExcel',
   xhrFields: {
    responseType: 'blob',
  },
  data: {'formData':formData},
  beforeSend: function(){


  },
  success: function(data)
  {
    var filename = "DocChaseReportReportOrders.csv";
    if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "DocChaseReportReportOrders.csv";
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

</script>







