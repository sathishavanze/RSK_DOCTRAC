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
 .DTFC_RightBodyLiner {
    overflow-y: hidden !important;
}

  th, td { text-align: center; }
</style>
<div class="card mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">On Hold Report 
		</div>
	</div>
    <div class="card-body ">
 <div class="text-right" > 
      <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
      <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
    </div>
    <div class="col-md-12 pd-0">
  <div id="advancedsearch"  style="display: none;">
  <fieldset class="advancedsearchdiv">
    <legend>Advanced Search</legend>
    <form id="advancedsearchdata">
      <div class="col-md-12 pd-0">
        <div class="row " >
          <div class="col-md-3 ">
           <div class="form-group bmd-form-group">
            <label for="adv_CustomerUID" class="bmd-label-floating">Client Name <span class="mandatory"></span></label>
            <select class="select2picker form-control" id="adv_CustomerUID"  name="CustomerUID">  
              <?php if (count($Clients) > 1) { ?>
                <option value="All">All</option>
              <?php } ?>
              <?php 
              foreach ($Clients as $key => $value) { ?>
                <option value="<?php echo $value->CustomerUID; ?>" ><?php echo $value->CustomerName; ?></option>
              <?php } ?>              
            </select>
          </div>
        </div>
        <div class="col-md-3 ">
          <div class="form-group bmd-form-group">
            <label for="adv_ProductUID" class="bmd-label-floating">Product <span class="mandatory"></span></label>
            <select class="select2picker form-control" id="adv_ProductUID"  name="ProductUID">   
              <option value="All">All</option>                  
            </select>
          </div>
        </div>
        <div class="col-md-3 ">
          <div class="form-group bmd-form-group">
            <label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
            <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
              <option value="All">All</option>                  
            </select>
          </div>
        </div>
        <div class="col-md-3 ">
          <div class="form-group bmd-form-group">
            <label for="adv_PackageUID" class="bmd-label-floating">Package Number <span class="mandatory"></span></label>
            <select class="select2picker form-control" id="adv_PackageUID"  name="PackageUID">      
              <option value="All">All</option>             
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="row " >
        <div class="col-md-3 datadiv">
          <div class="bmd-form-group row">
            <div class="col-md-3 pd-0 inputprepand" >
              <p class="mt-5"> From </p>
            </div>
            <div class=" col-md-9 " style="padding-left: 0px">
              <div class="datediv">
                <input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-90 days")); ?>">
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 datadiv">
          <div class="bmd-form-group row">
            <div class="col-md-3 pd-0 inputprepand" >
              <p class="mt-5"> To </p>
            </div>
            <div class=" col-md-9 " style="padding-left: 0px">
              <div class="datediv">
                <input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
    <div class="col-md-12  text-right pd-0 mt-10">
      <button type="button" class="btn btn-fill btn-facebook search" >Submit</button>
      <button type="button" class="btn btn-fill btn-tumblr reset">Reset</button>
    </div>
  </form>
</fieldset>
</div>



    <div class="col-md-12 col-xs-12 pd-0">
    <div class="material-datatables">
         <table class="table table-hover table-striped nowrap" id="OrderOnHoldTable" cellspacing="0" width="100%"  style="width:100%">
       <thead>
        <tr>
         <th class="text-center">Order Number</th>
         <th class="text-center">Pack No</th>
         <th class="text-center">Client</th>
         <th class="text-center">Product</th>
         <th class="text-center">Project</th>         
         <th class="text-center">Status</th>
         <th class="text-center">Property Address</th>
         <th class="text-center">Property City</th>
         <th class="text-center">Property State</th>
         <th class="text-center">Zip Code</th>
         <th class="text-center">OrderEntryDateTime</th>
         <!-- <th class="text-center">OnHold Type</th> -->
         <th class="text-center">OnHold Status</th>
         <th class="text-center">Remarks</th>
         <th class="text-center">Comments</th>
         <th class="text-center">Assigned User</th>
         <th class="text-center">OnHoldDateTime</th>
         <th class="text-center">ReleaseDateTime</th>
         <th  class="text-center">Action</th>
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


<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

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

   $('.fa-filter').click(function(){
    $("#advancedsearch").slideToggle();
  });


OrderOnHoldTableinitialize();

});


function OrderOnHoldTableinitialize(formdata){
  
  OrderOnHoldTable = $('#OrderOnHoldTable').DataTable({
     destroy: true,
          scrollX:        true,
          scrollCollapse: true,
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
            "url": "<?php echo base_url('OnHoldReport/OnHold_ajax_list')?>",
            "type": "POST",
            "data" : {'formData':formdata}
            // "columnDefs": [ {
            //   "targets": 'no-sort',
            //   "orderable": false,
            // } ],

          }

        });
    }

$(document).off('click','.exceldownload').on('click','.exceldownload',function(){

            var ProductUID = $('#adv_ProductUID option:selected').val();
            var ProjectUID = $('#adv_ProjectUID option:selected').val();
            var PackageUID = $('#adv_PackageUID option:selected').val();
            var CustomerUID = $('#adv_CustomerUID option:selected').val();
            var OnHoldStatus = $('#adv_OnHoldStatus option:selected').val();
            var OnHoldType =$('#adv_OnHoldType option:selected').val();
            var FromDate = $('#adv_FromDate').val();
            var ToDate = $('#adv_ToDate').val();
            if((ProjectUID == '') && (PackageUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='') && (OnHoldStatus == '') && (OnHoldType == ''))
            {
              var formData = 'All';
            } 
            else 
            {
              var formData = ({ 'ProductUID':ProductUID,'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'OnHoldStatus':OnHoldStatus,'OnHoldType':OnHoldType}); 
            }
            

            $.ajax({
               type: "POST",
               url: "<?php echo base_url('OnHoldReport/WriteExcel')?>",
                    xhrFields: {
                responseType: 'blob',
              },
               data: {'formData':formData},
              beforeSend: function(){

                  
              },
              success: function(data)
              {
                  var filename = "OrderOnHoldDetails.csv";
                  if (typeof window.chrome !== 'undefined') {
                      //Chrome version
                      var link = document.createElement('a');
                      link.href = window.URL.createObjectURL(data);
                      link.download = "OrderOnHoldDetails.csv";
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

$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
      e.preventDefault();
      var $dataobject = {'CustomerUID': $(this).val()};
      if($(this).val() == 'All')
      {
        $('#adv_ProductUID').html('<option value="All">All</option>');
        $('#adv_ProjectUID').html('<option value="All">All</option>');
        $('#adv_PackageUID').html('<option value="All">All</option>');
        callselect2();
      }
      else
      {
        SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {
        // addcardspinner($('#AuditCard'));
      }).then(function (data) {
        if (data.validation_error == 0) {
          var Product_Select = data.Products.reduce((accumulator, value) => {
            
            return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
          }, '<option value="All">All</option>');         
          $('#adv_ProductUID').html(Product_Select);
          $('#adv_ProductUID').trigger('change');
        }
        callselect2();

      }).catch(function (error) {
        
        console.log(error);
      });
    }
  });

    $(document).off('change', '#adv_ProductUID').on('change', '#adv_ProductUID', function (e) {  
      e.preventDefault();
      var $dataobject = {'ProductUID': $(this).val()};
      if($(this).val() == 'All')
      {
        $('#adv_ProjectUID').html('<option value="All">All</option>');
        $('#adv_PackageUID').html('<option value="All">All</option>');
        callselect2();
      }
      else
      {
        SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProjects', $dataobject, 'json', true, true, function () {
        // addcardspinner($('#AuditCard'));
      }).then(function (data) {
        if (data.validation_error == 0) {
          var Project_Select = data.Projects.reduce((accumulator, value) => {
            
            return accumulator + '<Option value="' + value.ProjectUID + '">' + value.ProjectName + '</Option>';
          }, '<option value="All">All</option>');         
          $('#adv_ProjectUID').html(Project_Select);
          $('#adv_ProjectUID').trigger('change');
        }
        callselect2();

      }).catch(function (error) {
        
        console.log(error);
      });
    }
  });

    $(document).off('change', '#adv_ProjectUID').on('change', '#adv_ProjectUID', function (e) {  
      e.preventDefault();
      
      var $dataobject = {'ProjectUID': $(this).val()};
      SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchPackNo', $dataobject, 'json', true, true, function () {
        // addcardspinner($('#AuditCard'));
        
      }).then(function (data) {
        if (data.validation_error == 0) {
          var Package_Select = data.Package.reduce((accumulator, value) => {
            
            return accumulator + '<Option value="' + value.PackageUID + '">' + value.PackageNumber + '</Option>';
          }, '<option value="All">All</option>');   
          $('#adv_PackageUID').html(Package_Select);
          $('#adv_PackageUID').trigger('change');

        }
        callselect2();

      }).catch(function (error) {
        
        console.log(error);
      });

      
    });

    $(document).off('click','.reset').on('click','.reset',function(){
        $("#adv_ProductUID").val('All');
        $("#adv_ProjectUID").val('All');
        $("#adv_PackageUID").val('All');
        $("#adv_CustomerUID").val('All');
        $("#adv_OnHoldStatus").val('All');
        $("#adv_OnHoldType").val('All');
        $("#adv_FromDate").val('<?php echo date('m/d/Y',strtotime("-90 days")); ?>');
        $("#adv_ToDate").val('<?php echo date('Y-m-d'); ?>');
        callselect2();
        initialize('false');

      });

      if ($('#adv_CustomerUID option').length == 1) {
        $('#adv_CustomerUID').trigger('change');
      }


      $(document).off('click','.search').on('click','.search',function()
          {
            var ProductUID = $('#adv_ProductUID option:selected').val();
            var ProjectUID = $('#adv_ProjectUID option:selected').val();
            var PackageUID = $('#adv_PackageUID option:selected').val();
            var CustomerUID = $('#adv_CustomerUID option:selected').val();
            var OnHoldStatus = $('#adv_OnHoldStatus option:selected').val();
             var OnHoldType = $('#adv_OnHoldType option:selected').val();
            var FromDate = $('#adv_FromDate').val();
            var ToDate = $('#adv_ToDate').val();
            if((ProjectUID == '') && (PackageUID == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='') && (OnHoldStatus == '') && (OnHoldType == ''))
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


             var formData = ({ 'ProductUID':ProductUID, 'ProjectUID': ProjectUID ,'PackageUID': PackageUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'OnHoldStatus':OnHoldStatus,'OnHoldType':OnHoldType}); 

             OrderOnHoldTableinitialize(formData);
            
            }
           return false;
          });

 </script>







