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
      <h4 class="card-title">Escalation</h4>
    </div>
<!--     <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Esclation</h4>
      </div>
    </div> -->
  </div>


  <div class="card-body">

    <div class="text-right"> 
      <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
      <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
    </div>

    <div id="advancedFilterForReport">
      <fieldset class="advancedsearchdiv">
        <legend>Advanced Search</legend>
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row" >

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
                  <select class="select2picker form-control WorkflowModuleUID" id="WorkflowModuleUID"  name="WorkflowModuleUID">  
                    <?php foreach ($Customer_Workflow as $key => $workflow) { ?>
                      <?php if ($key === 0) { ?>
                        <option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>
                      <?php } else { ?>
                        <option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="QueueUID" class="bmd-label-floating">Sub Queues </label>
                  <select class="select2picker form-control" id="QueueUID"  name="QueueUID">
                    <option value="">Select Sub Queues</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3 pd-0 mt-10">
                <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
                <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
              </div>

            </div>
          </div>         

        </form>
      </fieldset>
    </div>

    <?php
    $QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
    ?>

    <div  class="material-datatables" >
      <?php if ( !empty($QueueColumns) ) { ?>
      <table class="table table-hover table-striped" id="Esclation">
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
    <table class="table table-hover table-striped" id="Esclation">
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

<script type="text/javascript">
  var Esclation = false;
  $(function() {
    $("select.select2picker").select2({
      //tags: false,
      theme: "bootstrap",
    });
    $('#Esclation').DataTable().destroy();
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
    // Esclationinitialize('false')
    function Esclationinitialize(formdata)
    {
      Esclation = $('#Esclation').DataTable( {
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
            "url": "<?php echo base_url('Esclation/Esclationorders_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ],
          createdRow:function(row,data,index){


           // Highlight esclation order row
           if($(row).find('.HighlightEsclationOrder').length > 0){
            $(row).addClass('HighlightEsclationOrderRow');
          }


          },
        });
    }

    $(document).off('click','.search').on('click','.search',function()
    {
      var WorkflowModuleUID = $('#WorkflowModuleUID option:selected').val();
      var QueueUID = $('#QueueUID option:selected').val();
      if(WorkflowModuleUID == '')
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


       var formData = ({ 'WorkflowModuleUID':WorkflowModuleUID,'QueueUID':QueueUID }); 

       Esclationinitialize(formData);

     }
     return false;
   });

    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
      var WorkflowModuleUID = $('#WorkflowModuleUID option:selected').val();
      var QueueUID = $('#QueueUID option:selected').val();
      if(WorkflowModuleUID == '')
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


       var formData = ({ 'WorkflowModuleUID':WorkflowModuleUID,'QueueUID':QueueUID }); 

       Esclationinitialize(formData);

     }
     return false;
   });

    // Default trigger 
    $('.filterreport').trigger('click');

    $(document).off('click','.reset').on('click','.reset',function()
    {
      $("#WorkflowModuleUID").val($(".WorkflowModuleUID").find("option:first").val()).trigger("change");
      var WorkflowModuleUID = $('#WorkflowModuleUID option:selected').val();
      var formData = ({ 'WorkflowModuleUID':WorkflowModuleUID});
      Esclationinitialize(formData);
      callselect2();

    });
  });


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var WorkflowModuleUID = $('#WorkflowModuleUID option:selected').val();
  var QueueUID = $('#QueueUID option:selected').val();
  if(WorkflowModuleUID == '')
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

   var formData = ({ 'WorkflowModuleUID':WorkflowModuleUID,'QueueUID':QueueUID }); 
 }

 $.ajax({
   type: "POST",
   url: '<?php echo base_url();?>Esclation/WriteExcel',
   xhrFields: {
    responseType: 'blob',
  },
  data: {'formData':formData},
  beforeSend: function(){

  },
  success: function(data)
  {
    var filename = 'Escalation.xlsx';
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

$(document).off('click', '.ExportDocument').on('click', '.ExportDocument', function (e) {  
  e.preventDefault();
  var currentrow = $(this).closest('tr');
  var OrderUID = $(this).attr('data-OrderUID');
  var OrderNumber = $(this).attr('data-OrderNumber');
  var LoanNumber = $(this).attr('data-LoanNumber');

  SwalConfirmExport(OrderUID, OrderNumber, currentrow, Esclation, LoanNumber);
})

$(document).off('change', '.WorkflowModuleUID').on('change', '.WorkflowModuleUID', function(e) {
  var WorkflowModuleUID = $(this).val();
  $QueueUID = $('#QueueUID');

  $.ajax({
    url: 'CommonController/FetchWorkflowSubQueues',
    type: 'POST',
    dataType: 'JSON',
    data: {WorkflowModuleUID: WorkflowModuleUID},
  })
  .done(function(data) {
    console.log("success");
    // reset queueuid
    $QueueUID.empty();
    // append queues
    $QueueUID.append($("<option></option>").attr("value", "").text(""));
    $.each(data, function(key, value) {   
     $QueueUID.append($("<option></option>").attr("value", value.QueueUID).text(value.QueueName)); 
   });             
     $QueueUID.select2("val", "Select Sub Queues");
  })
  .fail(function() {
    console.log("error");
  })
  .always(function() {
    console.log("complete");
  });


});

//
$('.WorkflowModuleUID').trigger("change");

</script>







