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
		padding: 4px 5px;
	}

	.mt-0{
		margin-bottom: 0px !important;
	}

	.width-100{
		width:100%;
	}
	#tbl-singledoctype td{
		padding: 0px !important;
		margin: 0px;
	}
	.bulk-notes li{
		line-height: 30px;
	}
</style>
<div class="card mt-40" id="Orderentrycard">
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
				<a class="nav-link ajaxload bulkAssign" href="<?php echo base_url(); ?>Orderentry/bulkAssign" role="tablist">
					Bulk Assign 
				</a>
			</li>
			<!-- <li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/BulkWorkflow" role="tablist">
					Bulk Workflow Complete
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/MilestoneUpdate" role="tablist">
					Milestone Update
				</a>
			</li> -->
			<li class="nav-item">
				<a class="nav-link ajaxload bulkAssign" href="<?php echo base_url(); ?>Orderentry/PayOffUpdate" role="tablist">
					PayOff 
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload bulkAssign active" href="<?php echo base_url(); ?>Orderentry/BulkWorkflowEnable" role="tablist">
					Bulk Workflow Enable 
				</a>
			</li>
			
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/DocsOut" role="tablist">
					Docs Out 
				</a>
			</li>
		</ul>

		<div class="tab-content tab-space customtabpane">


			<div class="tab-pane active" id="bulkentry">

				<div class="row">

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="bulk_Customers" class="bmd-label-floating">Client<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="bulk_Customers" name="bulk_Customers">
								<option value=""></option>
								<?php foreach ($Customers as $key => $value) { 
									if ($this->parameters['DefaultClientUID'] == $value->CustomerUID) { ?>
						                <option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName; ?></option>    
						            <?php } else { 
						            	if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
						            		<option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName; ?></option>
					            		<?php }
						            }
						        } ?>							
							</select>		
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="bulk_products" class="bmd-label-floating">Product<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="bulk_products" name="bulk_products">
							</select>		
						</div>
					</div>


					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="bulk_ProjectUID" class="bmd-label-floating">Project<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="bulk_ProjectUID" name="bulk_ProjectUID">
							</select>		
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="WorkflowModuleUID" class="bmd-label-floating">Workflow<span class="mandatory"></span></label>
							<select class="select2picker form-control WorkflowModuleUID" id="WorkflowModuleUID"  name="WorkflowModuleUID">  
								<option></option>
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

				</div>


				<div class="row fileentry mt-10">
					<div class="col-sm-6">
						<form  name="formfile"  class="formfile">
							<input type="file" name="excelfile" id="filebulk_entry" class="dropify">
						</form>
					</div>

					<div class="col-sm-6">
						<ul class="bulk-notes">
							
							<li >Please follow the below steps to upload Order.</li>
							<li>Download the available Excel Template XLSX sheet.<br/>
								<a href="javascript:void(0);" class="btn btn-primary changeentryfilename disabled"  id="standardpreviewfile" disabled>EXCEL TEMPLATE</a>
							</li>
							<li>Fill in your Order details into the downloaded XLSX.</li>
							<li>Upload file size max 5MB </li>
							<li>Upload back the XLSX.</li>  
						</ul>
					</div>	
					
					<div class="col-md-12" id="preview-table">

					</div>
					
					<div class="col-md-12" id="imported-table">


					</div>

					
					<div class="text-right form-group" id="file-standard-import">
						<button type="submit" class="btn btn-save" id="bulk_save"  data-type="standard">Save</button>
						<button type="button" class="btn btn-space btn-social btn-color btn-preview" id="preview"  data-type="standard">Preview</button>
					</div>


				</div>


			</div>

		</div>

	</div>
</div>



<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>

<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>


<script type="text/javascript">

	var Priorty_options = '<?php foreach ($OrderPriority as $key => $value) { if($value->PriorityName =="Normal"){echo '<option value="'.$value->PriorityUID . '" selected>'.$value->PriorityName.'</option>';}else{echo '<option value="'.$value->PriorityUID . '">'.$value->PriorityName.'</option>';} } ?>';

	var current_doctype = {};

	/*-- removing hash from url --*/
	$(function(){
		var hash = window.location.hash;
		hash && $('ul.nav.entrytab a[href="' + hash + '"]').tab('show');
		var noHashURL = window.location.href.replace(/#.*$/, '');
		window.history.replaceState('', document.title, noHashURL) 
	})

	function bytesToSize(bytes) {
		if(bytes < 1024) return bytes + " Bytes";
		else if(bytes < 1048576) return(bytes / 1024).toFixed(3) + " KB";
		else if(bytes < 1073741824) return(bytes / 1048576).toFixed(3) + " MB";
		else return(bytes / 1073741824).toFixed(3) + " GB";
	}

	$(document).ready(function(){

		filetoupload=[];
		excelmultiplefileupload_Obj=[];
		textmultiplefileupload_Obj=[];
		$('#filebulk_entry').dropify();	

		$("select.select2picker").select2({
			theme: "bootstrap",
		});


		$('#btn_multiplefile_upload_toggle').off('click').on('click', function (e) {
			$('#multiplefile_upload').slideToggle('slow');
		});

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

    var drDestroy = $('#input-file-to-destroy').dropify();
    drDestroy = drDestroy.data('dropify')
    $('#toggleDropify').on('click', function(e){
    	e.preventDefault();
    	if (drDestroy.isDropified()) {
    		drDestroy.destroy();
    	} else {
    		drDestroy.init();
    	}
    });


    /* --- Dropify initialization ends */



    /* ---- BULK ORDER ENTRY ENDS ----*/


    /*Get subproduct by customer and product*/

    $(document).off('change', '#bulk_products').on('change', '#bulk_products', function (e) {

    	var CustomerUID = $('#bulk_Customers').val();
    	var ProductUID = $(this).val();
    	var Entrytype = $(this).find(':selected').attr('data-bulkworkflowenableformat');
    	var Entrytypefile = $(this).find(':selected').attr('data-bulkworkflowenabletemplatename');
    	var Entrytypexmlfile = $(this).find(':selected').attr('data-typexmlname');

    	$('.changeentryfilename').removeAttr('disabled').removeClass('disabled');				
    	$('.changeentryfilename').attr('data-filename',Entrytypefile);			

    	$.ajax({
    		type: "POST",
    		url: base_url + "Orderentry/Get_CustomerProjects",
    		data:{'CustomerUID':CustomerUID,'ProductUID':ProductUID},
    		dataType: 'json',
    		beforeSend: function () {
    			addcardspinner('#Orderentrycard');
    		},

    		success: function (response) {
    			$('#bulk_ProjectUID').empty();
    			var ProjectCustomer = response.CustomerProjects;
    			Project_select = ProjectCustomer.reduce((accumulator, value) => {
    				return accumulator + '<Option value="' + value.ProjectUID + '">' + value.ProjectName + '</Option>';
    			}, '');
    			$('#bulk_ProjectUID').html(Project_select);
    			$('#bulk_ProjectUID').val($('#bulk_ProjectUID').find('option:first').val()).trigger('change');
    			callselect2();
    			removecardspinner('#Orderentrycard');
    		}
    	});
    });



    /*download file format*/
    $(document).off('click', '.changeentryfilename').on('click', '.changeentryfilename', function (e) {
    	event.preventDefault();
    	var filename = $(this).attr('data-filename');
    	$.ajax({
    		type: "POST",
    		url:  "<?php echo base_url(); ?>Orderentry/bulkentrypreviewfile/"+filename,
    		xhrFields: {
    			responseType: 'blob',
    		},
    		beforeSend: function () {
    			addcardspinner('#Orderentrycard');
    		},

    		success: function (data) {
    			if(data){
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
        }
        removecardspinner('#Orderentrycard');
      },
      error: function(jqXHR, textStatus, errorThrown) {
      	removecardspinner('#Orderentrycard');
      	$.notify({icon:"icon-bell-check",message:'File not found'},{type:"danger",delay:3000 });

      }
    });

    });



		//preview bulk entry
		$(document).off('click',  '#preview').on('click',  '#preview', function (event) {
			event.preventDefault();
			button = $(this);
			button_text = $(this).html();
			var CustomerUID = $('#bulk_Customers').val();
			var ProductUID = $('#bulk_products').val();
			var ProjectUID = $('#bulk_ProjectUID').val();
			var WorkflowModuleUID = $('#WorkflowModuleUID').val();

			if (CustomerUID == "" || CustomerUID == null || ProjectUID == "" || ProjectUID == null || ProductUID == "" || ProductUID == null || WorkflowModuleUID == "" || WorkflowModuleUID == null) {
				$.notify({icon:"icon-bell-check",message:"Select the required fields"},{type:"danger",delay:3000 });
				return false;
			}


			if($('#filebulk_entry')[0].files.length === 0){
				$.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
				return false;
			}

			var file_data = $('#filebulk_entry').prop('files')[0];
			var form_data = new FormData();
			form_data.append('file', file_data);
			form_data.append('CustomerUID',CustomerUID);
			form_data.append('ProductUID',ProductUID );
			form_data.append('ProjectUID',ProjectUID );
			form_data.append('WorkflowModuleUID',WorkflowModuleUID );

			/*for update type*/
			url="Orderentry/preview_BulkWorkflowEnable";

			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>' + url,
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

						var addtext = '<ul class="nav nav-pills nav-pills-rose customtab mt-0" role="tablist"><li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small> Import Data Preview&nbsp;<i class="fa fa-check-circle"></i></small> </a></li></ul><div class="tab-content tab-space"><div id="success-table" class="tab-pane active cont">';

						addtext += data.html;

						addtext += '</div></div>'
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
						} );


					}						


					button.html(button_text);
					button.removeAttr("disabled");

				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
					button.html(button_text);
					button.removeAttr("disabled");

				},
				failure: function (jqXHR, textStatus, errorThrown) {

					console.log(errorThrown);

				},
			});
		});


		//save bulk entry
		$(document).off('click',  '#bulk_save').on('click',  '#bulk_save', function (event) {
			event.preventDefault();
			button = $(this);
			button_text = $(this).html();

			var CustomerUID = $('#bulk_Customers').val();
			var ProductUID = $('#bulk_products').val();
			var ProjectUID = $('#bulk_ProjectUID').val();
			var WorkflowModuleUID = $('#WorkflowModuleUID').val();

			if (CustomerUID == "" || CustomerUID == null || ProjectUID == "" || ProjectUID == null || ProductUID == "" || ProductUID == null || WorkflowModuleUID == "" || WorkflowModuleUID == null) {
				$.notify({icon:"icon-bell-check",message:"Select the required fields"},{type:"danger",delay:3000 });
				return false;
			}


			if($('#filebulk_entry')[0].files.length === 0) {
				$.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
				return false;
			}

			var file_data = $('#filebulk_entry').prop('files')[0];
			var form_data = new FormData();
			form_data.append('file', file_data);
			form_data.append('CustomerUID',CustomerUID);
			form_data.append('ProjectUID',ProjectUID );
			form_data.append('ProductUID',ProductUID );
			form_data.append('WorkflowModuleUID',WorkflowModuleUID );


			/*for update type*/
			url = 'Orderentry/UpdateBulkEnableWorkflow';

			$.ajax({
				type: "POST",
				url: base_url + url,
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
					else if (data.error==0) {

						$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:3000 });
						$('#imported-table').html(data.html);
						$('#preview-table').html('');

						/* Datatable initialization for excel, pdf export */
						if ( $.fn.DataTable.isDataTable( '.datatable' ) ) {
							$('.datatable').dataTable().fnClearTable();
							$('.datatable').dataTable().fnDestroy();
						}

						$('#tblsuccessentries').DataTable( {
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

							ajax: data.successfilelink,
							"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
									// if (aData[97] ) {
									// 	$(nRow).css('color', aData[96]);
									// 	$(nRow).css('background-color', aData[97]);
									// }
								}

							} );

						$('#tblfailedentries').DataTable( {
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
							ajax: data.failedfilelink,
							"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
								var currentindex = aData.length;
								if (aData[currentindex - 1] && isColor(aData[currentindex - 1])) {
									$(nRow).css('color', aData[currentindex - 2]);
									$(nRow).css('background-color', aData[currentindex - 1]);
								}
							}

						} );


					}						

				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
					button.html(button_text);
					button.removeAttr("disabled");

				},
				failure: function (jqXHR, textStatus, errorThrown) {

					console.log(errorThrown);

				},
			})
})

})

</script>







