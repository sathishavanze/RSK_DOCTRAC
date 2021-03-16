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
          <a class="nav-link active ajaxload" href="<?php echo base_url(); ?>Orderentry/BulkWorkflow" role="tablist">
            Bulk Workflow Complete
          </a>
        </li>
         <li class="nav-item">
          <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/MilestoneUpdate" role="tablist">
            Milestone Update
          </a>
        </li>
      </ul>
    </div>

		<div class="tab-content tab-space">

			<div class="tab-pane active">
				<div class="col-md-12">
         <div class="row fileentry mt-10"  >
          <div class="col-sm-6">
            <form  name="formfile"  class="formfile">
              <input type="file" name="excelfile" id="filebulk_entry" class="dropify">
            </form>
          </div>

          <div class="col-sm-6">
            <ul class="bulk-notes">
              
              <li >Please follow the below steps to upload Order.</li>
              <li>Download the available Excel Template XLSX sheet.<br/>
                <a href="javascript:void(0);" class="btn btn-primary"  id="BulkworkflowExcel">EXCEL TEMPLATE</a>
               </li>
              <li>Fill in your Order details into the downloaded XLSX.</li>
              <li>Upload file size max 5MB </li>
              <li>Upload back the XLSX.</li>  
            </ul>
          </div> 
          
          <div class="col-md-12" id="preview-table"></div>          
          <div class="col-md-12" id="imported-table"></div>
 
          <div class="text-right form-group" id="file-standard-import">
            <button type="submit" class="btn btn-save" id="workflow-bulksave"  data-type="standard">Save</button>
            <button type="button" class="btn btn-space btn-social btn-color btn-preview" id="workflow-preview" data-type="standard">Preview</button>
          </div> 

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
   
    $(document).off('click','#BulkworkflowExcel').on('click','#BulkworkflowExcel',function()
    { 
       $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Orderentry/BulkWorkflow/ExcelFormat',
        xhrFields: {
         responseType: 'blob',
       },
       data: {'formData':''},
       beforeSend: function(){

       },
       success: function(data)
       {	
         var filename = "LOP-Workflow_Complete.xlsx";
         if (typeof window.chrome !== 'undefined') 
         {
           //Chrome version
           var link = document.createElement('a');
           link.href = window.URL.createObjectURL(data);
           link.download = "LOP-Workflow_Complete.xlsx";
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

      //preview bulk entry
      $(document).off('click',  '#workflow-preview').on('click', '#workflow-preview', function (event) 
      {
        event.preventDefault();
        button = $(this);
        button_val = $(this).val();
        button_text = $(this).html(); 

        if($('#filebulk_entry')[0].files.length === 0){
          $.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
          return false;
        }

        var file_data = $('#filebulk_entry').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);  
        $.ajax({
          type: "POST",
          url: '<?php echo base_url('Orderentry/BulkWorkflow/preview_bulkworkflow'); ?>',
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
                  if ( $.fn.DataTable.isDataTable( '#table-bulkorder' ) ) {
                    $('#table-bulkorder').dataTable().fnClearTable();
                    $('#table-bulkorder').dataTable().fnDestroy();
                  }
                  
                  $('#table-bulkorder').DataTable( {
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

   //save bulk entry
   $(document).off('click',  '#workflow-bulksave').on('click',  '#workflow-bulksave', function (event) 
   {
     event.preventDefault();
     button = $(this);
     button_val = $(this).val();
     button_text = $(this).html();

     if($('#filebulk_entry')[0].files.length === 0){
       $.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
       return false;
     }

     var file_data = $('#filebulk_entry').prop('files')[0];
     var form_data = new FormData();
     form_data.append('file', file_data);

     $.ajax({
       type: "POST",
       url: '<?php echo base_url('Orderentry/BulkWorkflow/save_bulkworkflow'); ?>',
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







