<style type="text/css">

</style>

<div class="card mt-20" id="Exceptionorders">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-reload-alt"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Exception</h4>
      </div>
    </div>
  </div>
	<div class="card-body" id="filter-bar">
    <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active current" data-toggle="tab" href="#orderslist" role="tablist">
            Indexing Exceptions <span class="badge badge-pill badge-primary" style="background-color: #fff;color: #000;"><?php echo $this->Exceptionordersmodel->count_all('indexing');?></span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link current" data-toggle="tab" href="#orderslistnonfatal" role="tablist">
            Audit Exceptions- Non Fatal <span class="badge badge-pill badge-primary" style="background-color: #fff;color: #000;"><?php echo $this->Exceptionordersmodel->count_all('nonfatal');?></span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link current" data-toggle="tab" href="#orderslistfatal" role="tablist">
            Audit Exceptions- Fatal <span class="badge badge-pill badge-primary" style="background-color: #fff;color: #000;"><?php echo $this->Exceptionordersmodel->count_all('fatal');?></span>
        </a>
      </li>
      <?php
      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        ?>

      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#workinprogresslist" role="tablist">
          Work In Progress
          <span class="badge badge-pill badge-primary" id="completed-count" style="background-color: #fff;color: #000;"><?php echo $this->Exceptionordersmodel->in_progress_count_all(); ?></span>

        </a>
      </li>
      <?php } ?>
    </ul>
		<?php $this->load->view('common/advancesearch'); ?>

    <div class="tab-content tab-space customtabpane">
      <div class="tab-pane active" id="orderslist">
        <div class="col-md-12 col-xs-12 pd-0">
          <div class="material-datatables" id="myordertable_parent">
              <table class="table table-hover table-striped" id="exception">
                  <thead>
                     <tr>
                          <th>Order No</th>
                          <th>Package No</th>
                          <th>Client </th>
                          <th>Product</th>
                          <th>Project</th>
                          <th>Doc Type</th>
                          <th>Loan No</th>                          
                          <th>Current Status</th>
                          <th>Property Address</th>   
                          <th>Property City</th>    
                          <th>Property State</th> 
                          <th>Zip Code</th>     
                          <th>OrderEntryDateTime</th>
                          <th>OrderDueDateTime</th>
                          <th>Exception Remarks</th>
                          <th class="no-sort">Actions</th>
                     </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="orderslistnonfatal">
        <div class="col-md-12 col-xs-12 pd-0">
          <div class="material-datatables" id="myordertable_parent">
              <table class="table table-hover table-striped" id="exceptionnonfatal">
                  <thead>
                     <tr>
                          <th>Order No</th>
                          <th>Package No</th>
                          <th>Client </th>
                          <th>Product</th>
                          <th>Project</th>
                          <th>Doc Type</th>
                          <th>Loan No</th>                          
                          <th>Current Status</th>
                          <th>Property Address</th>   
                          <th>Property City</th>  
                          <th>Property State</th> 
                          <th>Zip Code</th>     
                          <th>OrderEntryDateTime</th>
                          <th>OrderDueDateTime</th>                          
                          <th>Exception Remarks</th>                          
                          <th class="no-sort">Actions</th>
                     </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="orderslistfatal">
        <div class="col-md-12 col-xs-12 pd-0">
          <div class="material-datatables" id="myordertable_parent">
              <table class="table table-hover table-striped" id="exceptionfatal">
                  <thead>
                     <tr>
                          <th>Order No</th>
                          <th>Package No</th>
                          <th>Client </th>
                          <th>Product</th>
                          <th>Project</th>
                          <th>Doc Type</th>
                          <th>Loan No</th>                          
                          <th>Current Status</th>
                          <th>Property Address</th>   
                          <th>Property City</th>    
                          <th>Property State</th> 
                          <th>Zip Code</th>     
                          <th>OrderEntryDateTime</th>
                          <th>OrderDueDateTime</th>
                          <th>Exception Remarks</th>                          
                          <th class="no-sort">Actions</th>
                     </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="workinprogresslist">
        <div class="col-md-12 col-xs-12 pd-0">
          <div class="material-datatables" id="myordertable_parent">
          <table class="table table-striped display nowrap" id="exceptionworkingprogresstable"  cellspacing="0" width="100%"  style="width:100%">
            <thead>
              <tr>
                <th>Order No</th>
                <th>Pack No</th>
                <th>Client </th>
                <th>Product</th>
                <th>Project</th>
                <th>Doc Type</th>
                <th>Loan No</th>
                <th>Assigned Username</th>
                <th>Current Status</th>
                <th>Property Address</th>
                <th>Property City</th>
                <th>Property State</th>
                <th>Zip Code</th>
                <th>OrderEntryDateTime</th>
                <th>OrderDueDateTime</th>
                <th>Exception Remarks</th>                          
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
	</div>
</div>

<script type="text/javascript">
          var exception = false;
        $(function() {
          $("select.select2picker").select2({
            //tags: false,
            theme: "bootstrap",
          });
          $('#exception').DataTable().destroy();
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
indexinitialize('false');
function indexinitialize(formdata)
{
          exception = $('#exception').DataTable( {
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
            "url": "<?php echo base_url('Exceptionorders/exceptionorders_ajax_list')?>",
            "data" : {'Status': 'indexing','formData':formdata},
            "type": "POST" 
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]

        });
  
}
fatalinitialize('false');
function fatalinitialize(formdata)
{

        exceptionfatal = $('#exceptionfatal').DataTable( {
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
            "url": "<?php echo base_url('Exceptionorders/exceptionorders_ajax_list')?>",
            "data" : {'Status': 'fatal','formData':formdata},
            "type": "POST" 
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]

        });
}
nonfatalinitialize('false');
function nonfatalinitialize(formdata)
{

        exceptionnonfatal = $('#exceptionnonfatal').DataTable( {
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
            "url": "<?php echo base_url('Exceptionorders/exceptionorders_ajax_list')?>",
            "data" : {'Status': 'nonfatal','formData':formdata},
            "type": "POST" 
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]
        });
}


		<?php
  if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
    ?>
workingprogressinitialize('false');
function workingprogressinitialize(formdata)
{
        exceptionworkingprogresstable = $('#exceptionworkingprogresstable').DataTable( {
            destroy: true,
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            scrollY: '100vh',
            paging:  true,
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
            "url": "<?php echo base_url('Exceptionorders/exceptionordersworkinprogress_ajax_list')?>",
             "data" : {'formData':formdata},
            "type": "POST" 
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]
        });
}
  <?php } ?>

          $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
          });

          $(window).resize(function() {
            $($.fn.dataTable.tables( true ) ).css('width', '100%');
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
          });


      $(document).off('click','.exceptionPickNewOrder').on('click','.exceptionPickNewOrder',function(){

        var OrderUID = $(this).attr('data-orderuid');
        var ProjectUID = $(this).attr('data-projectuid');
        

          $.ajax({

          type: "POST",
          url: '<?php echo base_url(); ?>Exceptionorders/PickExistingOrderCheck',
          data:{'OrderUID':OrderUID,'ProjectUID':ProjectUID},
          dataType:'json',
          beforeSend: function(){

          },
          success: function(data)
          {
              if(data.validation_error == 1)
              {
                    $.notify(
                    {
                      icon:"icon-bell-check",
                      message:data.message
                    },
                    {
                      type:data.color,
                      delay:1000 
                    });
                    
                   setTimeout(function(){ 

                      triggerpage('<?php echo base_url();?>Ordersummary/index/'+OrderUID);

                   }, 3000);
              }
              else
              {
                    $.notify(
                    {
                      icon:"icon-bell-check",
                      message:data.message
                    },
                    {
                      type:data.color,
                      delay:1000 
                    });

                   setTimeout(function(){ 

                      triggerpage('<?php echo base_url();?>Ordersummary/index/'+OrderUID);

                   }, 3000);
               }
            
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
            
          },
          failure: function (jqXHR, textStatus, errorThrown) {
            
            console.log(errorThrown);
            
          },
        });
      });

          $(document).off('click','.search').on('click','.search',function()
          {

                var filterlist = $("#filter-bar .active").attr("href");

                var ProductUID = $('#adv_ProductUID option:selected').val();
                var ProjectUID = $('#adv_ProjectUID option:selected').val();
                var PackageUID = $('#adv_PackageUID option:selected').val();
                var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
                var CustomerUID = $('#adv_CustomerUID option:selected').val();
                var FromDate = $('#adv_FromDate').val();
                var ToDate = $('#adv_ToDate').val();
                if((ProjectUID == '') && (PackageUID == '') &&  (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID == ''))
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


                 var formData = ({ 'ProductUID':ProductUID, 'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate}); 
                 if(filterlist == '#orderslist')
                 {
                    indexinitialize(formData);
                 }
                 else if(filterlist == '#orderslistnonfatal')
                 {
                    nonfatalinitialize(formData);
                 }
                 else if(filterlist == '#orderslistfatal')
                 {
                    fatalinitialize(formData);
                 }
                 else if(filterlist == '#workinprogresslist')
                 {
                    workingprogressinitialize(formData);
                 }

                
                }
               return false;
          });

          $(document).off('click','.reset').on('click','.reset',function()
          {
            $('#adv_ProjectUID').html('<option value = "All">All</option>');
            $('#adv_PackageUID').html('<option value = "All">All</option>');
           $('#adv_InputDocTypeUID').val('All').attr("selected", "selected");
            $('#adv_ProductUID').html('<option value = "All">All</option>');

            $("#adv_ProductUID").val('All');
            $("#adv_ProjectUID").val('All');
            $("#adv_PackageUID").val('All');
            $("#adv_InputDocTypeUID").val('All');
            $("#adv_CustomerUID").val('All');
            $("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
            $("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
             var filterlist = $("#filter-bar .active").attr("href");
             if(filterlist == '#orderslist')
             {
                indexinitialize('false');
             }
             else if(filterlist == '#orderslistnonfatal')
             {
                nonfatalinitialize('false');
             }
             else if(filterlist == '#orderslistfatal')
             {
                fatalinitialize('false');
             }
             else if(filterlist == '#workinprogresslist')
             {
                 workingprogressinitialize('false');
             }

             callselect2();
          });

	});

$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var filterlist = $("#filter-bar .active").attr("href");
  if(filterlist == '#orderslist')
   {
      var filter = 'indexing';
   }
   else if(filterlist == '#orderslistnonfatal')
   {
      var filter = 'nonfatal';
   }
   else if(filterlist == '#orderslistfatal')
   {
      var filter = 'fatal';
   }
   else if(filterlist == '#workinprogresslist')
   {
    var filter= 'workinprogress';
   }

  var ProductUID = $('#adv_ProductUID option:selected').val();
  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  var PackageUID = $('#adv_PackageUID option:selected').val();
  var InputDocTypeUID = $('#adv_InputDocTypeUID option:selected').val();
  var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '') && (PackageUID == '') &&  (InputDocTypeUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
  {
    var formData = 'All';
  } 
  else 
  {
    var formData = ({ 'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'InputDocTypeUID': InputDocTypeUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'Status':filter}); 
  }
  

  $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Exceptionorders/WriteExcel',
          xhrFields: {
      responseType: 'blob',
    },
     data: {'formData':formData},
    beforeSend: function(){

        
    },
    success: function(data)
    {
        if(filterlist == '#orderslist')
        {
         var filename = "IndexingException.csv";
        }
        else if(filterlist == '#orderslistnonfatal')
        {
          var filename = "NonFatalException.csv";
        }
        else if(filterlist == '#orderslistfatal')
        {
          var filename = "FatalException.csv";
        }
          else if(filterlist == '#workinprogresslist')
        {
          var filename = "WorkinProgressList.csv";
        }


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

          
	</script>







