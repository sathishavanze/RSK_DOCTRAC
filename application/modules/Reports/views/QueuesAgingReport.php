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
   content: "*";
   color: red;
   font-size: 15px;
 }

 .nowrap{
   white-space: nowrap
 }

 .table-format > thead > tr > th{
   font-size: 12px;
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
.bold{
    font-weight: bold;
  }
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">

<div class="card mt-40" >
    <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Queues Aging Report </div>
    </div>
<div class="card-body">
 <div class="col-md-12">

    
<div id="Queues-div">
<div class="card-header card-header-icon card-header-rose">         
    </div>
    <div class="col-md-12 pd-0 filters_div">
          <div class="row" >

            <div class="col-md-3 ">
              <div class="form-group bmd-form-group">
      <select class="form-control select2picker"  id="WorkflowModule"  name="WorkflowModule">
        <option value="0">Please Select Workflow</option>
                <?php 
                  //print_r($WorkflowModules);
                foreach($WorkflowModules as $Module){ ?>
                <option value="<?php echo $Module->WorkflowModuleUID;?>"><?php echo $Module->WorkflowModuleName;?></option>
                <?php }?>
              </select>
            </div>
            </div>
            <div class="form-group bmd-form-group col-md-3">
               <label for="aging" class="bmd-label-floating">Please Select Queue </label>
                <select class="form-control mdb-select"  id="mQueue"  name="mQueue" multiple="true" placeholder="Please Select Queue">

                </select>
            
          </div>
              <div class="form-group bmd-form-group col-md-3">
                  <label for="aging" class="bmd-label-floating">Please Select Aging </label>
                  <select class="form-control mdb-select" id="aging"  name="aging" multiple="true" placeholder="Please Select Aging">   
                    <?php foreach ($AgingHeader as $key => $value) {
                      if($key != 'total'){
                      ?>
              
                    <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                  <?php }}  ?>                      
                  </select>
                </div>
                <div class="col-md-3 " <?php if($this->RoleType == $this->config->item('Internal Roles')['Agent']){?>style="visibility: hidden;"<?php }?>>
                <div class="form-group bmd-form-group is-filled">
          <!--      <label for="UserList" class="bmd-label-floating">Please Select Users</label>
           -->      <select class="form-control mdb-select"  id="UserList"  name="UserList" multiple="true" placeholder="Please Select Users">

                    </select>
                </div>
              </div>
                <div class="col-md-3 ">
                     <button type="button" class="btn btn-fill btn-facebook submit" >Submit</button>
                <button type="button" class="btn  reseting">Reset</button> 
                </div>
            </div>
          </div>
                

      <div class="material-datatables">
        <div id="Workflow-Userslist">

        </div> 
      </div>
</div>
</div>
</div>
</div>
<input type="hidden" name="screen_name" id="screen_name">
 <script src="assets/js/app/inflowReport.js?reload=1.0.1"></script>
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
$('#WorkflowModule').change(function(event) {
        WorkflowModule = $(this).val();
        if(WorkflowModule != '0'){
          $.ajax({
              type: "POST",
              url: '<?php echo base_url();?>Reports/GetQueuesByWorkflow',
              data: ({'WorkflowModule':WorkflowModule}),
              dataType: 'JSON',
              beforeSend: function()
              {
                addcardspinner($('#Queues-div'));
              },
              success: function(data)
              {
               $('#mQueue').html(data.options);
               $('#mQueue').fSelect('destroy').fSelect('create');
                $('#UserList').html(data.usr_options);
               $('#UserList').fSelect('destroy').fSelect('create');
                removecardspinner($('Queues-div'));
               $("#screen_name").val(data.screen_name);
              }
            });
        }
        else
        {
          $.notify(
            {
              icon:"icon-bell-check",
              message:"Please Select the Workflow"
            },
            {
              type:'danger',
              delay:1000 
            });

        }
});
$(document).on('click','.reseting',function()
  {
      addcardspinner($('#Queues-div'));
      $('#WorkflowModule').val(0);
      callselect2();
      $('#mQueue').html('');
      $('#mQueue').fSelect('destroy').fSelect('create');
       $('#aging').html('');
      $('#aging').fSelect('destroy').fSelect('create');
      $('#UserList').html('');
      $('#UserList').fSelect('destroy').fSelect('create');
       $('#Workflow-Userslist').html('');
       removecardspinner($('Queues-div'));
  });
$(document).on('click','.submit',function()
  {
        WorkflowModule = $('#WorkflowModule').val();
                Aging= $('#aging').val();
         mQueue=$('#mQueue').val();
          UserList=$('#UserList').val();
        if((WorkflowModule != '0') && (Aging != '') && (mQueue != '0')){
         $.ajax({
              type: "POST",
              url: '<?php echo base_url();?>Reports/GetQueuesAgingUsersByWorkflow',
              data: ({'WorkflowModule':WorkflowModule,'Aging':Aging,'mQueue':mQueue,'UsrList':UserList}),
              dataType: 'JSON',
              beforeSend: function()
              {
                addcardspinner($('#Queues-div'));
              },
              success: function(data)
              {
               $('#Workflow-Userslist').html(data);
                removecardspinner($('Queues-div'));
               // $('#PreScreen').hide();
              }
            });
        }
        else
        {
          $.notify(
            {
              icon:"icon-bell-check",
              message:"Please Select values"
            },
            {
              type:'danger',
              delay:1000 
            });

        }
});
$(document).off('click','.orderclose').on('click','.orderclose',function() {
  //$("#MaritalTableList").fadeIn('fast');
      $("#MaritalTableList_wrapper").fadeIn('fast');
      $(".filters_div").fadeIn('fast');
       $("#orderstablediv").fadeOut('fast');
        $("#orderslist").fadeOut('fast');

});
$(document).off('click','.listorders').on('click','.listorders',function() {
      var td = $(this).closest('td');
      var OrderUID = $(this).attr('data-orderid');
      var QueueUID = $(this).attr('data-queueid');
      var UID= $(this).attr('data-uid');
      var WorkflowModuleUID =  $('#WorkflowModule').val();
      var title = $(this).attr('title');
      var Orderlistname = 'Orders - '+title;
      if(!OrderUID || OrderUID.length === 0) {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'No Orders Available'
        },
        {
          type:'danger',
          delay:1000 
        });
        return false;
      }

      fetchorders(OrderUID,WorkflowModuleUID,Orderlistname,QueueUID)
    });
$(document).off('click','.listassignedorders').on('click','.listassignedorders',function() {
      var td = $(this).closest('td');
      var UID= $(this).attr('data-uid');
      var WorkflowModuleUID =  $('#WorkflowModule').val();
      var title = $(this).attr('title');
      var Orderlistname = 'Orders - '+title;
      if(!OrderUID || OrderUID.length === 0) {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'No Orders Available'
        },
        {
          type:'danger',
          delay:1000 
        });
        return false;
      }

      fetchassignedorders(WorkflowModuleUID,Orderlistname,UID)
    });
function hide_data()
    {
      $("#MaritalTableList_wrapper").fadeOut('fast');
      $(".filters_div").fadeOut('fast'); 
    }
function fetchassignedorders(WorkflowModuleUID,Orderlistname,UID)
  {
    var WorkflowModuleUID =  $('#WorkflowModule').val();
    hide_data();
    var screen_url="<?php echo base_url();?>"+ $("#screen_name").val()+'/workinginprogress_ajax_list';
    $("#orderstablediv").fadeIn('fast');
    $('#orderlisttitle').text(Orderlistname);
    $('#orderlist_orderuids').val(OrderUID);
    $('#orderlist_workflowmoduleuid').val(WorkflowModuleUID);
    $("#tblQueue").fadeOut('fast');
    $("#tblQueue_wrapper").fadeOut('fast');
    $("#workingprogresstable").fadeIn('fast');
    Workin_Progress = $('#workingprogresstable').DataTable( {
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
        "url": screen_url,
        "type": "POST",
        "data" : {'ReportType':"Queues_Report",'UserUID':UID}   
      },
      "columnDefs": [ {
        "targets": 'no-sort',
        "orderable": false,
      } ],
    });
  }
 function fetchorders(OrderUID,WorkflowModuleUID,Orderlistname,QueueUID)
  {
    var WorkflowModuleUID =  $('#WorkflowModule').val();
    hide_data();
    var formData = ({'QueueUID':QueueUID,'OrderUID':OrderUID});
    $("#orderstablediv").fadeIn('fast');
    $('#orderlisttitle').text(Orderlistname);
    $('#orderlist_orderuids').val(OrderUID);
    $('#orderlist_workflowmoduleuid').val(WorkflowModuleUID);
    $("#workingprogresstable").fadeOut('fast');
    $("#workingprogresstable_wrapper").fadeOut('fast');
    $("#tblQueue").fadeIn('fast');
    orderslist = $('#tblQueue').DataTable( {
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
        "url": "<?php echo base_url('ExceptionQueue_Orders/ExceptionQueue_Orders_ajax')?>",
        "type": "POST",
        "data" : {'formData':formData, 'QueueUID':QueueUID,'ReportType':"Queues_Report"}   
      },
      "columnDefs": [ {
        "targets": 'no-sort',
        "orderable": false,
      } ],
    });
  }
</script>

