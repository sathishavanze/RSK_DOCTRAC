<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />


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
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon"> <i class="icon-file-text"></i>
		</div>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Order Upload</h4>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="col-md-12 mt-10">
      <ul class="nav nav-pills nav-pills-danger customtab entrytab" role="tablist">
        <li class="nav-item">
          <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry" role="tablist">
            Single Entry
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/bulkentry" role="tablist">
            Bulk Entry 
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/BulkWorkflow" role="tablist">
            Bulk Workflow Complete
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active ajaxload" href="<?php echo base_url(); ?>Orderentry/MilestoneUpdate" role="tablist">
            Milestone Update
          </a>
        </li>
      </ul>
    </div>

    <div class="col-md-12 mt-10">
      <button type="button" id="milestone_import" class="btn btn-space btn-primary  btn-sm btn-custom-sm" title="Import Milestone"><i class="fa fa-upload"></i> Milestone Import</button>

      <button type="button" id="milestone_export" class="btn btn-space btn-success btn-color btn-sm btn-custom-sm" title="Export Milestone"><i class="fa fa-download"></i>Milestone Export</button>
    </div>

    <div class="tab-content tab-space bulkmilestone">
     <div class="tab-pane active">
      <div class="col-md-12">
       <div class="row fileentry mt-10"  >
        <div class="col-sm-6">
          <form  name="formfile"  class="formfile">
            <input type="file" name="excelfile" id="filebulk_milestoneUpdate" class="dropify">
          </form>
        </div>

        <div class="col-sm-6">
          <ul class="bulk-notes">

            <li >Please follow the below steps to upload milestone.</li>
            <li>Download the available Excel Template XLSX sheet.<br/>
              <a href="javascript:void(0);" class="btn btn-primary"  id="MileStoneUpdateExcel">EXCEL TEMPLATE</a>
            </li>
            <li>Fill in your Milestone details into the downloaded XLSX.</li>
            <li>Upload file size max 5MB </li>  
          </ul>
        </div> 

        <div class="col-md-12" id="preview-table"></div>          
        <div class="col-md-12" id="imported-table"></div>

        <div class="text-right form-group pull-right" id="file-standard-import">
          <button type="submit" class="btn btn-save" id="milestone-bulksave"  data-type="standard">Save</button>
          <button type="button" class="btn btn-space btn-social btn-color btn-preview" id="milestone-preview" data-type="standard">Preview</button>
        </div> 

      </div>
    </div>
  </div>
</div>


<div class="tab-content tab-space bulmilestoneexport" style="display: none;">
  <div class="tab-pane active">
    <div class="col-md-12">
      <div class="col-md-12 text-right">
        <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
        <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
      </div>
      <div id="advancedMilestone"  style="display: none;">
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
              <label for="adv_MilestoneUID" class="bmd-label-floating">Milestone </label>
              <select class="select2picker form-control" id="adv_MilestoneUID"  name="WorkflowModuleUID">   
                <option value="All">All</option>
                <?php foreach ($Milestone as $key => $value) { ?>
                  <option value="<?php echo $value->MilestoneUID; ?>" ><?php echo $value->MilestoneName; ?></option>
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
      </div>

      <div class="col-md-12  text-right pd-0 mt-10">
        <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
        <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
      </div>

    </form>
  </fieldset>
</div>

<div class="material-datatables">
  <table class="table table-hover table-striped" id="MilestoneTable">
    <thead>
     <tr>
      <th>Order Number</th>
      <th>Loan Number</th>
      <th>MileStone</th>
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




<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>


<script type="text/javascript">
	$(document).ready(function()
  {
     $(window).resize(function() {
        $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
      }
      );

       $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
      }
      );


		/* --- Dropify initialization starts */

		$('.dropify').dropify();

		// Used events
		var drEvent = $('.dropify').dropify();

		drEvent.on('dropify.beforeClear', function(event, element){
			// return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
		});

		drEvent.on('dropify.afterClear', function(event, element){
			// alert('File deleted');
		});

		drEvent.on('dropify.errors', function(event, element){
			console.log('Has Errors');
		}); 

		/* --- Dropify initialization ends */
    $(document).off('click','#milestone_import').on('click','#milestone_import',function()
    { 
      $('.bulmilestoneexport').hide();
      $('.bulkmilestone').show();
    }); 

    $(document).off('click','#milestone_export').on('click','#milestone_export',function()
    { 
       $('.bulkmilestone').hide();
       $('.bulmilestoneexport').show();
        milestoneinitialize('false')
      });
      
    $('.fa-filter').click(function(){
      $("#advancedMilestone").slideToggle();
    });

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

     var MilestoneExport = false;

     
    function milestoneinitialize(formdata)
    {
      MilestoneExport = $('#MilestoneTable').DataTable( {
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
            "url": "<?php echo base_url('Orderentry/MilestoneUpdate/MilestoneExport')?>",
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
              var ProjectUID = $('#adv_ProjectUID option:selected').val();
              // var CustomerUID = $('#adv_CustomerUID option:selected').val();
              var Milestone = $('#adv_MilestoneUID').val();
              var FromDate = $('#adv_FromDate').val();
              var ToDate = $('#adv_ToDate').val();
              if((ProjectUID == '')  && (Milestone == '') && (CustomerUID == '') && (FromDate == '')&& (ToDate == ''))
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
               var formData = ({'Milestone':Milestone,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate}); 
               milestoneinitialize(formData);
             }

             return false;
           });

  

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_MilestoneUID").val('All');
      $("#adv_ProjectUID").val('All');
      // $("#adv_CustomerUID").val('All');
      $("#adv_FromDate").val('');
      $("#adv_ToDate").val('');
      milestoneinitialize('false');
      callselect2();

    });


    $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
      var ProjectUID = $('#adv_ProjectUID option:selected').val();
      // var CustomerUID = $('#adv_CustomerUID option:selected').val();
      var Milestone = $('#adv_MilestoneUID option:selected').val();
      var FromDate = $('#adv_FromDate').val();
      var ToDate = $('#adv_ToDate').val();
      if((ProjectUID == '')  && (Milestone == '') && (FromDate == '')&& (ToDate == ''))
      {
        var formData = 'All';
      } 
      else 
      {
        var formData = ({'Milestone':Milestone,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate}); 
      }

      $.ajax({
       type: "POST",
       url: '<?php echo base_url();?>Orderentry/MilestoneUpdate/MilestoneExcelExport',
       xhrFields: {
        responseType: 'blob',
      },
      data: {'formData':formData},
      beforeSend: function(){

      },
      success: function(data)
      {
        var filename = "MileStoneOrders.xlsx";
        if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "MileStoneOrders.xlsx";
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

    $(document).off('click','#MileStoneUpdateExcel').on('click','#MileStoneUpdateExcel',function()
    { 
       $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Orderentry/MilestoneUpdate/ExcelFormat',
        xhrFields: {
         responseType: 'blob',
       },
       data: {'formData':''},
       beforeSend: function(){

       },
       success: function(data)
       {	
         var filename = "doctrac-milestone.xlsx";
         if (typeof window.chrome !== 'undefined') 
         {
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

      //preview bulk milestone update
      $(document).off('click',  '#milestone-preview').on('click', '#milestone-preview', function (event) 
      {
        event.preventDefault();
        button = $(this);
        button_val = $(this).val();
        button_text = $(this).html(); 

        if($('#filebulk_milestoneUpdate')[0].files.length === 0){
          $.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
          return false;
        }

        var file_data = $('#filebulk_milestoneUpdate').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);  
        $.ajax({
          type: "POST",
          url: '<?php echo base_url('Orderentry/MilestoneUpdate/preview_MilestoneUpdate'); ?>',
          data: form_data,
          processData: false,
          contentType: false,
          cache:false,
          dataType:'json',

          beforeSend: function(){
            button.attr("disabled", true);
            button.html('Loading ...'); 
          },
          success: function(data)
          { 
            $('#preview-table').html('');

            if (data.error==1) {
              $.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
            }
            else if (data.error==0) {
              $('#imported-table').html('');

              var addtext = '<div class="tab-content tab-space"><div id="success-table" class="tab-pane active cont">';

              addtext += data.html;

              addtext += '</div><div id="error-data" class="tab-pane cont">';

              addtext += data.filehtml;

              addtext += '</div></div></div></div>'
              $('#text-preview-table').html('');
              $('#preview-table').html('');
                
                  $('#preview-table').html(addtext);

                  /* Datatable initialization for excel, pdf export */
                  if ( $.fn.DataTable.isDataTable( '#table-bulkmilestone' ) ) {
                    $('#table-bulkmilestone').dataTable().fnClearTable();
                    $('#table-bulkmilestone').dataTable().fnDestroy();
                  }
                  
                  $('#table-bulkmilestone').DataTable( {
                    "scrollX": true,
                    "autoWidth": true,
                    "processing": true, //Feature control the processing indicator.
                    language: {
                      sLengthMenu: "Show _MENU_ Orders",
                      emptyTable:     "No Records Found",
                      info:           "Showing _START_ to _END_ of _TOTAL_ Records",
                      infoEmpty:      "Showing 0 to 0 of 0 Records",
                      infoFiltered:   "(filtered from _MAX_ total Records)",
                      zeroRecords:    "No matching Records found",
                      processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
                    },
                    ajax: data.filelink,
                    "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                      var currentindex = aData.length;
                      if (aData[currentindex - 1] && isColor(aData[currentindex - 1])) {
                        $(nRow).css('color', aData[currentindex - 2]);
                        $(nRow).css('background-color', aData[currentindex - 1]);
                      }
                    }
                  });
                }           
                button.html('Preview');
                button.removeAttr("disabled");
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
              },
              failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
              },
            });
    });

   //save bulk milestone update
   $(document).off('click',  '#milestone-bulksave').on('click',  '#milestone-bulksave', function (event) 
   {
     event.preventDefault();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();

     if($('#filebulk_milestoneUpdate')[0].files.length === 0){
       $.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
       return false;
     }

     var file_data = $('#filebulk_milestoneUpdate').prop('files')[0];
     var form_data = new FormData();
     form_data.append('file', file_data);

     $.ajax({
       type: "POST",
       url: '<?php echo base_url('Orderentry/MilestoneUpdate/save_MilestoneUpdate'); ?>',
       data: form_data,
       processData: false,
       contentType: false,
       cache:false,
       dataType:'json',
       beforeSend: function(){
         $('.spinnerclass').addClass("be-loading-active");
         button.attr("disabled", true);
         button.html('Loading ...'); 
       },
       success: function(data)
       {
         button.html('save'); 
         button.removeAttr('disabled');

         if (data.error==1) {
           $.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
         }
         else if (data.error==0) 
         {
           $('#imported-table').html(data.html);
           $('#preview-table').html('');

           /* Datatable initialization for excel, pdf export */
           if ( $.fn.DataTable.isDataTable( '.datatable' ) ) {
             $('.datatable').dataTable().fnClearTable();
             $('.datatable').dataTable().fnDestroy();
           }

           $('.datatable').DataTable( {
             dom: 'Bfrtip',
             buttons: [
             'excel',  {
               extend : 'pdfHtml5',
               orientation : 'landscape',
               pageSize : 'A0',
               customize: function (doc) { 
                 doc.defaultStyle.fontSize = 4; 
                 doc.styles.tableHeader.fontSize = 4; 
               }
             } 
             ],
             "scrollX": true
           });
           $('.dropify-clear').click();
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

});
</script>







