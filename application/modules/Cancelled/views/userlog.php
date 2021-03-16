<style type="text/css">

</style>
<div class="card" id="Exceptionorders">

	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">User History</h4>
      </div>
    </div>
  </div>

  <div class="card-body">
    <ul class="nav nav-pills nav-pills-rose" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#UserLog" role="tablist">
          User Log
        </a>
      </li>
    </ul>

		<div class="row">
                  <div class="col-sm-12 pd-0">
                     <div id="accordion4" class="panel-group accordion accordion-color">
                        <div class="panel md-editor-no-dashed">
                           <div class="panel-heading panel-full-primary" style="background-color: #fff;">
  <h4 class="panel-title text-right" id="Ad_button">
        <button class="btn btn-link btn-warning btn-xs advancedfiltericon" data-toggle="collapse" data-parent="#accordion4" href="#collapse-4" class="search-size" aria-expanded="true"><i class="icon-filter3 filtericon font20"></i></button>
        <!--              <a ><i id="Ad_icon" class="fa fa-chevron-right"></i> Advanced Search &nbsp;&nbsp;&nbsp;<i class="icon-search4"></i></a> -->
      </h4>    

                           </div>
                           <div id="collapse-4" class="panel-collapse collapse in advancedfilter" aria-expanded="true" style="">
                              <div class="panel-body panel-body-br pd-0">
                                 <div class="col-md-12 pd-0">
                                    <div>
                                       <div class="card-body pd-10">
                                          <form action="#" id="Frm_User_Search_Filter" name="Frm_User_Search_Filter">
                                             <div class="row">
                                                <div class="form-group col-md-2">
                                                   <div class="form-group bmd-form-group">
                                                      <label for="UserName" class="bmd-label-floating">User Name</label>
                                                      <input type="text" name="UserName" class="form-control" id="UserName">
                                                   </div>
                                                </div>
                                             
                                           
                                                <div class="form-group col-md-2">
                                                   <div class="form-group bmd-form-group">
                                                      <label for="IpAddreess" class="bmd-label-floating">Ip Address</label>
                                                      <input type="text" class="form-control" name="IpAddreess" id="IpAddreess">
                                                   </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                  <div class="form-group bmd-form-group">
                                                    <label for="adv_FromDate" class="bmd-label-floating">From Date</label>
                                                    <input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-90 days")); ?>">
                                                  </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                  <div class="form-group bmd-form-group">
                                                    <label for="adv_ToDate" class="bmd-label-floating">To Date</label>
                                                    <input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
                                                  </div>
                                                </div>
                                                <div class="col-md-2" style="margin-top: 10pt;">
                                            <button type="button" class="btn btn-fill btn-danger btn-sm search" >Submit</button>
                                            <button type="button" class="btn btn-fill btn-danger btn-sm reset">Reset</button>
                                          </div>
                                           </div>
                                           
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>


	<div class="card-body">
         <table class="table table-hover table-striped" id="UserLog"  style="font-size: 12px;white-space: nowrap;">
                                    <thead>
                                       <tr>
                                          <th>Date & Time  </th>
                                          <th>Operations</th>
                                          <th>IP</th>                            
                                          <th>Browser</th>                            
                                          <th>Browser Version</th>                            
                                          <th>Platform</th>                            
                                          <th>Platform Version</th>                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                 </table>
	</div>



  </div>
</div>

<script type="text/javascript">
          var review = false;
        $(function() {
          $("select.select2picker").select2({
            //tags: false,
            theme: "bootstrap",
          });
          $('#UserLog').DataTable().destroy();
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
userloginitialize('false')
    function userloginitialize(formdata)
    {
          review = $('#UserLog').DataTable( {
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
          "pageLength": 50, // Set Page Length
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
            "url": "<?php echo base_url('UserLog/userlog_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ]

        });
    }
           
    $(document).off('click','.search').on('click','.search',function(e)
    {
      //alert()
      e.preventDefault();
      var UserName = $('#UserName').val();
      var IpAddreess = $('#IpAddreess').val();
      var FromDate = $('#adv_FromDate').val();
      var ToDate = $('#adv_ToDate').val();
      if((UserName == '') && (IpAddreess == '') && (FromDate == ''))
      {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'Please Choose Keywords'
        },
        {
          type:'danger',
          delay:3000 
        });
      } else {

       var formData = ({ 'UserName': UserName ,'IpAddreess': IpAddreess,'FromDate':FromDate,'ToDate':ToDate}); 
       console.log(formData);
       userloginitialize(formData);

     }
     return false;
   });

    $(document).off('click','.reset').on('click','.reset',function(e)
    {
     e.preventDefault();
     $('#Frm_User_Search_Filter')[0].reset();
     $('.select2picker').val('').trigger('change');  
     userloginitialize('All');
     return false;
   });
	});


$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

  var ProjectUID = $('#adv_ProjectUID option:selected').val();
  var LenderUID = $('#adv_LenderUID option:selected').val();
  var CustomerUID = $('#adv_CustomerUID option:selected').val();
  var FromDate = $('#adv_FromDate').val();
  var ToDate = $('#adv_ToDate').val();
  if((ProjectUID == '') && (LenderUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == ''))
  {
    var formData = 'All';
  }
  else
  {
    var formData = ({ 'ProjectUID': ProjectUID ,'LenderUID': LenderUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate});
  }


  $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Cancelled/WriteExcel',
          xhrFields: {
      responseType: 'blob',
    },
     data: {'formData':formData},
    beforeSend: function(){


    },
    success: function(data)
    {
        var filename = "CancelledOrders.csv";
        if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "CancelledOrders.csv";
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
