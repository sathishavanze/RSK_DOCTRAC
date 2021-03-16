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
		<ul class="nav nav-pills nav-pills-danger customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#singleentry" data-href="#singleentry" role="tablist">
					Single Entry
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#bulkentry" data-href="#bulkentry" role="tablist">
					Bulk Entry 
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry#fileuploadtab" data-href="#fileuploadtab" role="tablist">
					Doc Upload
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload active" href="<?php echo base_url(); ?>Orderentry/exceptionimport" role="tablist">
					Data Exception
				</a>
			</li>
					<?php
			if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
				?>
				<li class="nav-item">
					<a class="nav-link ajaxload " href="<?php echo base_url(); ?>Orderentry/DocumentTracking/missingOrders" role="tablist">
						Missing Orders
					</a>
				</li>
			<?php }?>
      <li class="nav-item">
        <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/BulkWorkflow" role="tablist">
          Bulk Workflow Complete
        </a>
      </li>
		</ul>
	</div>

		<div class="tab-content tab-space">

			<div class="tab-pane active">
				<div class="col-md-12">

				<div class="row fileentry ">
					<div class="col-sm-12 pd-0">
						<form  name="formfile"  class="formfile">
							<input type="file" name="excelfile" id="fileexceptionimport_entry" class="dropify">
						</form>
					</div>

					<div class="col-md-12 pd-0">
						<div class="text-left">									
							<button type="button" class="btn btn-dribbble btn-sm btn-github" id="btn_multiplefile_upload_toggle"><i class="icon-upload4 pr-10"></i>Upload Image(s)</button>
						</div>
						<div class="mt-10" style="display: none;" id="multiplefile_upload">
							<input type="file" id="multiplefileupload"  class="dropify" name="multiplefileupload" webkitdirectory mozdirectory msdirectory odirectory directory multiple>
							<div class="progress progress-line-info" id="bulk_file_orderentry-progressupload" style="display:none; height: 22px;">
								<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 21px;">
									<span class="sr-only">0% Complete</span>
								</div>
							</div>

							<div class="col-md-12 pd-0 mt-5">
								<h4 class="sectionhead"><i class="icon-checkmark4 headericon"></i>File Preview</h4>	
							</div>

							<div class="table-responsive">
								<table class="table table-bordered" id="ExcelDocumentPreviewTable">
									<thead class="text-primary">
										<th>
											Document Name	
										</th>
										<th>
											Uploaded DateTime
										</th>
										<th>
											Action
										</th>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>

						</div>
					</div>	


					
					<div class="col-md-12 text-right pd-0" id="file-standard-import">
						<button type="submit" class="btn btn-save" id="exceptionimport_save"  data-type="standard">Save</button>
						<button type="button" class="btn btn-preview btn-social btn-color " id="exceptionimport_preview"  data-type="standard">Preview</button>
					</div>

					<div class="col-md-12 pd-0" id="exceptionimport-previewtable"></div>
					<div class="col-md-12 pd-0" id="exceptionimported-table"></div>

					<div class="form-group" id="exception-table">
						<div class="col-md-12 pd-0">
							<h4 class="sectionhead"><i class="icon-checkmark4 headericon"></i>Data Exception</h4>	
						</div>
						<div class="form-group" id="exceptionimport-table">
							<div class="text-right">
								<span class="badge badge-pill" style="background-color: #757575;">Invalid</span>
								<span class="badge badge-pill" style="background-color: #168998;">Client Name</span>
								<span class="badge badge-pill" style="background-color: #8123e8;">Client Code</span>
								<span class="badge badge-pill" style="background-color: #67941e;">Project Name</span>
								<span class="badge badge-pill" style="background-color: #BF6105;">Project Code</span>
								<span class="badge badge-pill" style="background-color: #ff04ec;">Loan Number</span>
								<!-- <span class="badge badge-pill" style="background-color: #7a0082;">Borrower Name</span>
								<span class="badge badge-pill" style="background-color: #e20000;">Property Address Line 1</span>
								<span class="badge badge-pill" style="background-color: #074D1E;">Property City</span>
								<span class="badge badge-pill" style="background-color: #02BD5A;">Property State</span>
								<span class="badge badge-pill" style="background-color: #BDA601;">Property Zip Code</span>
								<span class="badge badge-pill" style="background-color: #3502BD;">Property County</span>
								<span class="badge badge-pill" style="background-color: #0055ad;">Loan Amount</span>
								<span class="badge badge-pill" style="background-color: #2196f3;">TPO Company Name</span>-->
								<span class="badge badge-pill" style="background-color: #ff5c33;">Document Type</span> 
								<span class="badge badge-pill" style="background-color: #bd302a;">Product Name</span>
							</div>

							<div class="mt-10">
								<div class="defaultfontsize">
									<div class="col-md-12 pd-0"  style="clear: :both">
									<a  href="<?php echo base_url(); ?>Orderentry/exception_excel" class="btn btn-info excel-btn"><i class="fa fa-download"></i> Excel</a>
								</div>
									<div class="col-md-12 pd-0" style="clear: both">
									<div class="scrollmenu">	
									<table class=" table table-striped table-format nowrap"  id="exception-orderstable" style="width:100%" >
										<thead>
											<tr>
												<th>Action</th>
												<th>Exception ID</th>
												<th>Client Name</th>
												<th>Client Code</th>
												<th>Project Name</th>
												<th>Project Code</th>
												<th>Loan Number</th>
												<th>BussinessChannel</th>
												<th>SIMO Loan Number</th>
												<th>Loan Reference Number</th>
												<th>Seller Loan Number</th>
												<th>Title order Number</th>
												<th>Servicer Loan Number</th>
												<th>Loan Amount</th>
												<th>Loan Type</th>
												<th>Loan Purpose</th>
												<th>MOM Flag</th>
												<th>SIMO MOM Flag</th>
												<th>MIN</th>
												<th>SIMO MIN</th>
												<th>Loan Priority Flag</th>
												<th>Loan Priority Score</th>
												<th>Borrower Name</th>
												<th>Property Address Line 1</th>
												<th>Property Address Line 2</th>
												<th>Property Address Line 3</th>
												<th>Property Address Line 4</th>
												<th>Property City</th>
												<th>Property State</th>
												<th>Property Zip Code</th>
												<th>Property County</th>
												<th>Closing Date</th>
												<th>Disbursement Date</th>
												<th>Funding Date</th>
												<th>Payoff Date</th>
												<th>Servicer Name</th>
												<th>Sub Servicer name</th>
												<th>Purchase Date</th>
												<th>Purchased Loan Transferred date</th>
												<th>Trade Commitment Date</th>
												<th>Loan Pool Number</th>
												<th>Loan Pool Certification Date</th>
												<th>Loan Pool Due Date</th>
												<th>Loan Pool Recertification Date</th>
												<th>Case Number</th>
												<th>Certificate Number</th>
												<th>Certificate Issued Date</th>
												<th>TPO Company Name</th>
												<th>TPO Company Reference</th>
												<th>TPO Company Phone #</th>
												<th>TPO Company Fax #</th>
												<th>TPO Company Email</th>
												<th>TPO Contact First Name</th>
												<th>TPO Contact Last Name</th>
												<th>TPO Contact Phone #</th>
												<th>TPO Contact Email</th>
												<th>Settlement Agent Name</th>
												<th>Settlement Agent Phone #</th>
												<th>Settlement Agent Fax #</th>
												<th>Settlement Agent Email</th>
												<th>Settlement Agent Contact First Name</th>
												<th>Settlement Agent Contact Last Name</th>
												<th>Settlement Agent Contact Phone #</th>
												<th>Settlement Agent Contact Email</th>
												<th>Agent #</th>
												<th>Agent CPL#</th>
												<th>Investor #</th>
												<th>Investor Name</th>
												<th>Investor loan Number</th>
												<th>Custodian Name</th>
												<th>Custodian Code</th>
												<th>Custodian Loan Number</th>
												<th>Title Underwriter Company Name</th>
												<th>Document Reference Number</th>
												<th>Document Type</th>
												<th>Document Name</th>
												<th>Document Status</th>
												<th>Document Status Date</th>
												<th>Document eRecorded Flag</th>
												<th>Document Sent to County Date</th>
												<th>Document Returned from County Date</th>
												<th>Gap Mortgage Amount</th>
												<th>Client Processing Rule</th>
												<th>Second Lien Flag</th>
												<th>eLoan</th>
												<th>Custom Field 1</th>
												<th>Custom Field 2</th>
												<th>Custom Field 3</th>
												<th>Custom Field 4</th>
												<th>Custom Field 5</th>
												<th>Custom Field 6</th>
												<th>Custom Field 7</th>
												<th>Custom Field 8</th>
												<th>CD Recording Fee - Mortgage</th>
												<th>CD Recording Fee- Deed</th>
												<th>Product</th>
												<th>Lender</th>
												
											</tr>
										</thead>

										
									</table>
								</div>
								
								</div>
								</div>
							</div>

						</div>

					</div>

				</div>
			</div>
			</div>

		</div>

	</div>
</div>




<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>


<script type="text/javascript">
	$(document).ready(function(){


		filetoupload=[];
		excelmultiplefileupload_Obj=[];
		textmultiplefileupload_Obj=[];
		$('#fileexceptionimport_entry').dropify();	
		jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
            	var items=[];var result1='';
                 $.ajax({
                    url: 'Orderentry/exception_ajax',
                    type:'POST',
                    data: {length:'-1'},
                    success: function (result) {
                    	
                    	 result1=JSON.parse(result);
                     
                       
                    },
                    async: false
                });
               
                return {
			      body: result1, 
			      // skip actions header
			      header: $("#exception-orderstable thead tr th").map(function() { 
			        if(this.innerHTML!='Actions')
			          return this.innerHTML; 
			      }).get()
			    };
            }
        });

		$('#exception-orderstable').DataTable( {
			dom: 'Blfrtip',
        buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'none'     // 'none',    'applied', 'removed'
                }
            }
        } ],
			
			 'processing': true,
			 "destroy":true,
		      'serverSide': true,
		      'serverMethod': 'post',
		      "lengthMenu":[[ 10,100,1000,2000, -1], [10,100,1000,2000 , "ALL"]],
		      //"pageLength": 10,
		      'ajax': {
		          'url':'Orderentry/exception_ajax'
		      },
		      'columns': [
						       	{
						            "targets": 1,
						            "data": null,
						           
       							 	"mRender": function (o) { return "<button class='btn btn-danger delete_dataexception' data-dataexceptionuid='"+o.DataExceptionUID+"'><i class='fa fa-times'></i></button>" ; }
          
       							 },
						       	{ data : 'DataExceptionUID' },
								{ data : 'CustomerName' },
								{ data : 'CustomerCode' },
								{ data : 'ProjectName' },
								{ data : 'ProjectCode' },
								{ data : 'LoanNumber' },
								{ data : 'BussinessChannel' },
								{ data : 'SIMOLoanNumber' },
								{ data : 'LoanReferenceNumber' },
								{ data : 'SellerLoanNumber' },
								{ data : 'TitleorderNumber' },
								{ data : 'ServicerLoanNumber' },
								{ data : 'LoanAmount' },
								{ data : 'LoanType' },
								{ data : 'LoanPurpose' },
								{ data : 'MOMFlag' },
								{ data : 'SIMOMOMFlag' },
								{ data : 'MIN' },
								{ data : 'SIMOMIN' },
								{ data : 'LoanPriorityFlag' },
								{ data : 'LoanPriorityScore' },
								{ data : 'BorrowerName' },
								{ data : 'PropertyAddress1' },
								{ data : 'PropertyAddress2' },
								{ data : 'PropertyAddress3' },
								{ data : 'PropertyAddress4' },
								{ data : 'PropertyCityName' },
								{ data : 'PropertyStateCode' },
								{ data : 'PropertyZipCode' },
								{ data : 'PropertyCountyName' },
								{ data : 'ClosingDateTime' },
								{ data : 'DisbursementDate' },
								{ data : 'FundingDate' },
								{ data : 'PayoffDate' },
								{ data : 'ServicerName' },
								{ data : 'SubServicerName' },
								{ data : 'PurchaseDate' },
								{ data : 'PurchasedLoanTransferredDate' },
								{ data : 'TradeCommitmentDate' },
								{ data : 'LoanPoolNumber' },
								{ data : 'LoanPoolCertificationDate' },
								{ data : 'LoanPoolDueDate' },
								{ data : 'LoanPoolRecertificationDate' },
								{ data : 'CaseNumber' },
								{ data : 'CertificateNumber' },
								{ data : 'CertificateIssuedDate' },
								{ data : 'CorrespondentLenderName' },
								{ data : 'CorrespondentLenderRefNo' },
								{ data : 'CorrespondentLenderPhoneNo' },
								{ data : 'CorrespondentLenderFaxNo' },
								{ data : 'CorrespondentLenderEmail' },
								{ data : 'CorrespondentContactFirstName' },
								{ data : 'CorrespondentContactLastName' },
								{ data : 'CorrespondentContactPhoneNo' },
								{ data : 'CorrespondentContactEmail' },
								{ data : 'SettlementAgentName' },
								{ data : 'SettlementAgentPhone' },
								{ data : 'SettlementAgentFax' },
								{ data : 'SettlementAgentEmail' },
								{ data : 'SettlementAgentContactFirstName' },
								{ data : 'SettlementAgentContactLastName' },
								{ data : 'SettlementAgentContactPhoneNo' },
								{ data : 'SettlementAgentContactEmail' },
								{ data : 'AgentNo' },
								{ data : 'AgentCPL' },
								{ data : 'InvestorNo' },
								{ data : 'InvestorName' },
								{ data : 'InvestorLoanNumber' },
								{ data : 'CustodianName' },
								{ data : 'CustodianCode' },
								{ data : 'CustodianLoanNumber' },
								{ data : 'UnderwriterName' },
								{ data : 'DocRefNo' },
								{ data : 'DocType' },
								{ data : 'DocName' },
								{ data : 'DocStatus' },
								{ data : 'DocStatusDate' },
								{ data : 'DoceRecordedFlag' },
								{ data : 'DocSentToCountyDate' },
								{ data : 'DocReturnedFromCountyDate' },
								{ data : 'GAPMortgageAmount' },
								{ data : 'Comments' },
								{ data : 'SecondLienFlag' },
								{ data : 'eLoan' },
								{ data : 'CustomField1' },
								{ data : 'CustomField2' },
								{ data : 'CustomField3' },
								{ data : 'CustomField4' },
								{ data : 'CustomField5' },
								{ data : 'CustomField6' },
								{ data : 'CustomField7' },
								{ data : 'CustomField8' },
								{ data : 'CDRecordingFeeMortgage' },
								{ data : 'CDRecordingFeeDeed' },
								{ data : 'ProductName' },
								{ data : 'LenderName' },
								

						      ],
						      
		});

		$('.changeentry').click(function()
		{
			$(this).find('i').toggleClass('fa-toggle-on fa-toggle-off')
			$('.textentry').toggle();
			$('.fileentry').toggle();
			$('#exceptionimport-previewtable').html('');
			$('#exceptionimported-table').html('');
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

		$('input[name=UploadType]').off('change').on("change", function (e) {
			e.preventDefault();
			$('#upload-filecontent-div').html("");
			$('#btn-fileupload').hide();
			$('#filescount').html('');
			$('#localfileupload').val('');

			if ($(this).attr('id') == "localfolder") {
				$('#btn-choosefolder').show();
				$('#filescount').show();
			}
			else{
				$('#btn-choosefolder').hide();	
				$('#filescount').hide();	
			}

		});

		$('#btn-choosefolder').off('click').on('click', function (e) {
			$('#localfileupload').trigger('click');
		});

		$('#localfileupload').off('change').on('change', function (event) {

			var Filescount = event.target.files.length;

			$('#filescount').html(Filescount + ' Files Available.');

			$('#upload-filecontent-div').html("");

			$('#btn-fileupload').hide();


		});

		$('#btn-upload-preview').off('click').on('click', function (event) {
			event.preventDefault();

			var button = $(this);
			var button_text = $(this).html();

			var files = $('#localfileupload')[0].files;
			var filenames = [];
			for (var i = 0; i < files.length; i++) {
				filenames.push(files[i].name);
			}
			console.log(filenames);

			if ($('input[name=UploadType]:checked').attr('id') == "localfolder") {
				var UploadType = "LocalUpload";
			}
			else{
				var UploadType = "FTPUpload";
			}


			$.ajax({
				url: '<?php echo base_url(); ?>Orderentry/PreviewOrderUploadFiles',
				method: 'POST',
				data: {"Files":filenames, 'UploadType':UploadType},
				dataType: 'json',
				beforeSend: function () {
					$(button).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
					$(button).prop('disabled', true);
					$('#btn-fileupload').hide();

				}
			}).done(function(data, textStatus, jqXHR) {
				console.log(data);
				if (data.validation_error == 0) {
					$('#upload-filecontent-div').html(data.html);
					$('#btn-fileupload').show();
				}
				else if(data.validation_error == 1){
					$('#upload-filecontent-div').html('');
					$('#btn-fileupload').hide();
					$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });

				}

			}).fail(function(jqXHR, textStatus, errorThrown) {
				if (jqXHR.responseJSON) {
					console.log('failed with json data');
				}
				else {
					console.log('failed with unknown data'); 
				}
			}).always(function(dataOrjqXHR, textStatus, jqXHRorErrorThrown) {
				$(button).html(button_text);
				$(button).prop('disabled', false);

			});


		});


		$('#btn-fileupload').off('click').on('click', function (event) {
			event.preventDefault();
			var button = $(this);
			var button_text = $(this).html();

			var formdata = new FormData();

			var choosenorders = $('.choosen-orders:checked').map(function (key, value) {
				var obj ={};
				obj.orderuid = $(value).attr('data-orderuid');
				obj.filename = $(value).attr('data-filename');
				obj.filepath = $(value).attr('data-filepath');
				return obj;
			});

			console.log(choosenorders);

			var files = $('#localfileupload')[0].files;
			var filestoupload = [];


			if ($('input[name=UploadType]:checked').attr('id') == "localfolder") {
				var UploadType = "LocalUpload";
				formdata.append('UploadType', UploadType);
				for (var i = 0; i < choosenorders.length; i++) {
					for (var f = 0; f < files.length; f++) {
						if (choosenorders[i].filename == files[f].name) {

							formdata.append('Orders[OrderUID][]', choosenorders[i].orderuid);
							formdata.append('Orders[FileName][]', choosenorders[i].filename);
							formdata.append('Orders[File][]', files[f]);
						}
					}
				}

			}
			else{
				var UploadType = "FTPUpload";
				formdata.append('UploadType', UploadType);
				for (var i = 0; i < choosenorders.length; i++) {

					formdata.append('Orders[OrderUID][]', choosenorders[i].orderuid);
					formdata.append('Orders[FileName][]', choosenorders[i].filename);
					formdata.append('Orders[FilePath][]', choosenorders[i].filepath);
				}
			}



			console.log(formdata);
			$.ajax({
				url: '<?php echo base_url(); ?>Orderentry/UploadOrderFiles',
				method: 'POST',
				data: formdata,
				contentType: false,
				processData: false,
				cache:false,
				dataType: 'json',
				beforeSend: function () {
					$('#btn-upload-preview').hide();
					addcardspinner('#Orderentrycard');
					$(button).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
					$(button).prop('disabled', true);

				}
			}).done(function(data, textStatus, jqXHR) {
				console.log(data);
				if (data.validation_error == 0) {
					$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:1000 });
					$('#btn-fileupload').hide();
					$('#btn-upload-preview').show();
					$('#upload-filecontent-div').html('');

				}
				else if(data.validation_error == 1){
					$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });
					$('#btn-fileupload').show();
					$('#btn-upload-preview').show();
					$('#upload-filecontent-div').html('');

				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				$.notify({icon:"icon-bell-check",message:"Something Went Wrong. Unable to Upload Files."},{type:"danger",delay:1000 });

				if (jqXHR.responseJSON) {
					console.log('failed with json data');
				}
				else {
					console.log('failed with unknown data'); 
				}
			}).always(function(dataOrjqXHR, textStatus, jqXHRorErrorThrown) {
				console.log('always');
				$(button).html(button_text);
				$(button).prop('disabled', false);
				removecardspinner('#Orderentrycard');

			});


		});



		$("body").off('click', '.DeleteUploadDocument').on("click" , ".DeleteUploadDocument" , function(e){
			e.preventDefault();

			var currentrow = $(this);
			var fuid = $(currentrow).attr('data-fileuploadid');

			var context = $(this).attr('data-context');
			var tablename = '#DocumentPreviewTable';
			if (context == 'SingleInsertTable') {
				filetoupload.splice(fuid,1);
				tablename = '#DocumentPreviewTable';
			}
			else if (context == 'ExcelTable') {
				excelmultiplefileupload_Obj.splice(fuid,1);
				tablename = '#ExcelDocumentPreviewTable';				
			}
			else if (context == 'TextTable') {
				textmultiplefileupload_Obj.splice(fuid,1);
				tablename = '#TextDocumentPreviewTable';				
			}

			$(currentrow).closest('tr').remove();

			$(tablename + ' tr.DocumentFileRow').find('.DeleteUploadDocument').each(function(key, element){
				$(element).attr('data-fileuploadid', key);
			});
		});


		/* ---- BULK ORDER ENTRY STARTS ----*/



		/* ABSTRACTOR DOCUMENT SCRIPT SECTION STARTS */
		$(document).off('change', '#multiplefile_upload').on('change', '#multiplefile_upload', function(event){


			var output = [];


			for(var i = 0; i < event.target.files.length; i++)
			{
				var fileid=excelmultiplefileupload_Obj.length;
				var file = event.target.files[i];
				excelmultiplefileupload_Obj.push({file: file, filename: file.name , is_stacking: 1});
				console.log(excelmultiplefileupload_Obj);

				var datetime=calcTime('Caribbean', '-5');

				var documentrow='<tr class="DocumentFileRow">';
				documentrow+='<td>'+file.name+'</td>';
				documentrow+='<td>'+datetime+'</td>';
				documentrow+='<td style="text-align: left;"><button type="button" data-context = "ExcelTable" data-fileuploadid="'+fileid+'" class="DeleteUploadDocument btn btn-link btn-danger btn-just-icon btn-xs"><i class="icon-x"></i></button></td>';
				documentrow+='</tr>';

				output.push(documentrow);

			}

			$('#ExcelDocumentPreviewTable').find('tbody').append(output.join(""));

			/*Loader START To BE Added*/

		});





		/* Excel Bulk Import Order starts */

		//preview bulk entry
		$(document).off('click',  '#exceptionimport_preview').on('click',  '#exceptionimport_preview', function (event) {
			event.preventDefault();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();

			var file_data = $('#fileexceptionimport_entry').prop('files')[0];
			var form_data = new FormData();
			form_data.append('file', file_data);


			$.each(excelmultiplefileupload_Obj, function (key, value) {
				// form_data.append('DOCFILES[]', value.file);
				form_data.append('FILENAMES[]', value.filename);
			});


			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>Orderentry/preview_exceptionentry',
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
					$('#exceptionimport-previewtable').html('');

					if (data.error==1) {
						$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
					}
					else if (data.error==0) {
						$('#exceptionimported-table').html('');

						var addtext = '<ul class="nav nav-pills nav-pills-rose customtab mt-0" role="tablist"><li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small> Import Data Preview&nbsp;<i class="fa fa-check-circle"></i></small> </a></li><li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#error-data" role="tablist"><small> Import File Preview &nbsp;<i class="fa fa-times-circle-o"></i></small> </a></li></ul><div class="tab-content tab-space"><div id="success-table" class="tab-pane active cont">';

						addtext += data.html;

						addtext += '</div><div id="error-data" class="tab-pane cont">';

						addtext += data.filehtml;

						addtext += '</div></div></div></div>'
						$('#exceptionimport-previewtable').html('');


						$('#exceptionimport-previewtable').html(addtext);

						/* Datatable initialization for excel, pdf export */
						if ( $.fn.DataTable.isDataTable( '.datatable' ) ) {
							$('.datatable').dataTable().fnClearTable();
							$('.datatable').dataTable().fnDestroy();
						}

						$('.datatable').DataTable( {
							dom: 'Bfrtip',
							buttons: [
							'excel', 'pdf'
							],
							"scrollX": true
						} );


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


		/* Excel Bulk Import Order ends */

		/* ---- BULK ORDER ENTRY ENDS ----*/

		$(document).off('click', '.delete_dataexception').on('click', '.delete_dataexception', function(event) {
			event.preventDefault();
			/*Delete confirmation*/
			var DataExceptionUID = $(this).attr('data-dataexceptionuid');
			var button = $('.dataexception-delete');
			swal({
				type:'question',
				//title: "<i class='icon-warning icondanger'></i>",     
				html: '<p>Do you want to delete?</p>',   
				showCancelButton: true,
				confirmButtonClass: 'btn btn-success dataexception-delete',
				cancelButtonClass: 'btn btn-danger dataexception-deletecancel',
				buttonsStyling: false,
				allowOutsideClick: true,
				showLoaderOnConfirm: true,
				confirmButtonText:'Yes, delete it!'
			}).then(function(confirm) {

				console.log('success');

				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>Orderentry/delete_exceptiondata',
					data: {'DataExceptionUID':DataExceptionUID},
					cache:false,
					dataType:'json',
					beforeSend: function(){
						button.attr("disabled", true);
						button.html('Loading ...'); 
					},
					success: function(data)
					{

						if(data.error == 1){
							swal("", data.message, "error");
						}else{
							swal({
								html: data.message,
								type:data.type,
							}).then(function(dismiss) {
								addcardspinner('#Orderentrycard');
								triggerpage('<?php echo base_url(); ?>Orderentry/exceptionimport');
							},function(done){
								addcardspinner('#Orderentrycard');
								triggerpage('<?php echo base_url(); ?>Orderentry/exceptionimport');
							})

						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						swal("Error deleting!", "Please try again", "error");

					},
					failure: function (jqXHR, textStatus, errorThrown) {

						console.log(errorThrown);
						swal("Error deleting!", "Please try again", "error");
					}

				});

			},
			function(dismiss) {              
				console.log('dismiss');
			});
		});

		/*Exception import bulk entry*/
	});
$(document).off('click','.excel_download').on('click','.excel_download',function(){

				

					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>Orderentry/exception_excel',
						xhrFields: {
							responseType: 'blob',
						},
						data: {'formData':''},
						beforeSend: function(){


						},
						success: function(data)
						{	
							var filename = "DataException.xlsx";
							if (typeof window.chrome !== 'undefined') {
				            //Chrome version
				            var link = document.createElement('a');
				            link.href = window.URL.createObjectURL(data);
				            link.download = "DataException.xlsx";
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







