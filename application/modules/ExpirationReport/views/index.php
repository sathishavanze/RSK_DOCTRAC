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
  
}
</style>
<div class="card mt-20" id="Exceptionorders">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Expiration Report</h4>
      </div>
    </div>
  </div>
    <div class="text-right"> 
      <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
      <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
    </div>


    <div id="advancedFilterForReport">
      <fieldset class="advancedsearchdiv">
        <legend>Advanced Search</legend>
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row " >

              <!-- <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_ProjectUID" class="bmd-label-floating">Project </label>
                  <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
                    <option value="All">All</option>
                    <?php foreach ($Projects as $key => $value) { ?>

                      <option value="<?php echo $value->ProjectUID; ?>" ><?php echo $value->ProjectName; ?></option>
                    <?php } ?>                     
                  </select>
                </div>
              </div> -->

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
                  <select class="select2picker form-control" id="WorkflowModuleUID"  name="WorkflowModuleUID">   
                    <option value="All">All</option>
                    <?php foreach ($Modules as $key => $value) { ?>
                      <option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->SystemName; ?></option>
                    <?php } ?>  
                    <option value="FHA_MONTH">FHA Month</option>                    
                  </select>
                </div>
              </div>

              <div class="col-md-3 FilterContainer">
                <div class="form-group bmd-form-group">
                  <label for="filterexpiredays" class="bmd-label-floating">Expiry Day </label>
                  <select class="select2picker form-control" id="filterexpiredays"  name="filterexpiredays">   
                    <option value="All">All</option>
                    <?php 
                    // how many days filtering
                    $num_days = 30;
                    // start date
                    // $date = date(date("Y-m-d"));
                    for ($days=1; $days <= $num_days; $days++) { ?>
                      <option value="<?php echo $days; ?>" ><?php echo 'Day '.$days; ?></option>
                    <?php
                    // $date = date('Y-m-d', strtotime($date . " +1 days"));
                    }
                    ?>                    
                  </select>
                </div>
              </div>

              <div class="col-md-3 FilterContainer">
                <div class="form-group bmd-form-group">
                  <label for="FilterStatus" class="bmd-label-floating">Status </label>
                  <select class="select2picker form-control" id="FilterStatus"  name="FilterStatus">  
                    <option value="All">All</option> 
                    <option value="ExpiredOrders">Expired Orders</option>
                    <option value="ExpiryOrders">Expiry Orders</option>
                  </select>
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
  <table class="table table-hover table-striped" id="ExpirationReport">
    <thead>
     <tr>
      <!-- Loan No,Borrower Name ,Milestone,State,Workflow,Associate ,Processor, Document Expiry Date , Days To Expire -->
      <th>Loan Number</th>
      <th>Loan Type</th>
      <th>Borrower Name</th>
      <th>Milestone</th>
      <th>State</th>
      <th>Workflow</th>
      <th>Associate</th>
      <th>Processor</th>
      <th>Checklist</th>
      <th>Document Date</th>
      <th>Document Expiry Date</th>
      <th>Days To Expire</th>
      <th>Actions</th>
    </tr>
  </thead>

  <tbody>

  </tbody>
</table>
</div>

</div>


<script type="text/javascript">
  var ExpirationReport = false;
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
      ExpirationReport = $('#ExpirationReport').DataTable( {
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
            "url": "<?php echo base_url('ExpirationReport/FetchExpirationReport')?>",
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
      var WorkflowModuleUID = $('#WorkflowModuleUID').val();
      var filterexpiredays = $('#filterexpiredays').val();
      var FilterStatus = $('#FilterStatus').val();
      if((WorkflowModuleUID == '') && (filterexpiredays == '') && (FilterStatus == ''))
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
      } else if((filterexpiredays != 'All') && (FilterStatus == 'All')) {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'Please Choose Filter Status OR Choose Expiry Day All'
        },
        {
          type:'danger',
          delay:2000 
        });
        return false;
      } else {
       var formData = ({'WorkflowModuleUID':WorkflowModuleUID,'filterexpiredays':filterexpiredays,'FilterStatus':FilterStatus}); 
       dailypendinginitialize(formData);
     }

     return false;
   });

  $(document).off('click','.reset').on('click','.reset',function(){
    $("#WorkflowModuleUID").val('All');
    $("#filterexpiredays").val('All');
    $("#FilterStatus").val('All');
    dailypendinginitialize('false');
    callselect2();

  });
});


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var WorkflowModuleUID = $('#WorkflowModuleUID').val();
  var filterexpiredays = $('#filterexpiredays').val();
  var FilterStatus = $('#FilterStatus').val();

  if((WorkflowModuleUID == '') && (filterexpiredays == '') && (FilterStatus == ''))
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
  } else if((filterexpiredays != 'All') && (FilterStatus == 'All')) {
    $.notify(
    {
      icon:"icon-bell-check",
      message:'Please Choose Filter Status OR Choose Expiry Day All'
    },
    {
      type:'danger',
      delay:2000 
    });
    return false;
  } else {
      var formData = ({'WorkflowModuleUID':WorkflowModuleUID,'filterexpiredays':filterexpiredays,'FilterStatus':FilterStatus});
  }  

  $.ajax({
    type: "POST",
    url: '<?php echo base_url();?>ExpirationReport/WriteExcel',
    xhrFields: {
    responseType: 'blob',
  },
  data: {'formData':formData},
  beforeSend: function(){

  },
  success: function(data)
  {
    var filename = 'ExpirationReport.xlsx';
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

$(document).off('change').on('change', '#WorkflowModuleUID', function(event) {
  event.preventDefault();
  if ($(this).val() == "FHA_MONTH") {
    $('.FilterContainer').hide();
  } else {    
    $('.FilterContainer').show();
  }
});;

</script>







