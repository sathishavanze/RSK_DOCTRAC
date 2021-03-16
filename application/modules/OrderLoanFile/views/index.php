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


	.white-box {
		background: #ffffff;
		padding: 25px;
		margin-bottom: 15px;
	}
	.btn-outline {
		color: #fb9678;
		background-color: transparent;
	}
	.btn-danger.disabled {
		background: #fb9678;
		border: 1px solid #fb9678;
	}
	.exception{
		border: 1px dotted black;
		border-radius: 5px;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.table-bordered>thead>tr>th {
		min-width: 0px ! important;
	}
	.productadd_button{
		margin-top: -40px;
		font-size: 23px;
	}
	table{
		width:100% !important;
	}
	.danger {
		padding: 0px 6px 0px 6px;
		background: #f9cdcd !important;
		color: #f30606;
		font-weight: 500;
		font-size: 18px; 
	}
	.CompanyDetailsDiv {
		/*float: right;*/
	}
	.CompanyDetailsDiv div {
		line-height: 2.4;
	}
	a[href*=".pdf"]:before {
		display:none!important;
	}
	.table-borderless tr td{
		border:0;
	}
	.table-borderless tr td:first-child{
		font-weight:bold;
		width:150px;
	}
</style>
<?php
$HeaderColor = $this->db->select('SideBar_NavColor')->from('mUsers')->where('UserUID',$this->loggedid)->get()->row();
if(!empty($hoi_company)){
	if($hoi_company->Comments != ''){
		$CompanyName = $hoi_company->Comments;	
	}else{
		$CompanyName = $hoi_company->SelectIn;	
	}
}else{
	$CompanyName = '';
}

?>
<div class="col-md-12 pd-0" >
	<div class="card mt-0" id="Orderentrycard">
		<div class="card-header tabheader" id="">
			<div class="col-md-12 pd-0">
				<div id="headers" style="color: #ffffff;" class="<?php echo $HeaderColor->SideBar_NavColor ?:'#333' ?>">
					<!-- Order Info Header View -->	
					<?php $this->load->view('orderinfoheader/orderinfo'); ?>
				</div>
			</div>
		</div>
		<div class="card-body pd-0">
			<!-- Workflow Header View -->	
			<?php $this->load->view('orderinfoheader/workflowheader'); 
			$OrderUID = $this->uri->segment(3);
			?>
			<div class="tab-content tab-space">
				<div class="tab-pane active" id="loanfile">
					<form action="#"  name="orderform" id="frmLoanFile">

						<input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderUID;?>">
						

						<div class="order_expand_div" style="display: none">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group bmd-form-group">
									<label for="HOICompanyName" class="bmd-label-floating">Company Name<span class="mandatory"></span></label>
										
										<!-- <input type="text" name="CompanyName" class="form-group " id="HOICompanyName" value="<?php echo $Documents->CompanyName; ?>">		 -->
										<!-- <input type="text" name="CompanyName"  class="form-control form-check-input1 pre_select"> -->
										<select class="form-control select2picker form-check-input1 pre_select" name="CompanyName" id="HOICompanyName">
											<option value=""> </option>
											<?php  
											foreach($CompanyDetails as $Company) { 
												if($CompanyName == $Company->CompanyName){	
												echo '<option value="'.$Company->CompanyName.'" selected>'.$Company->CompanyName.'</option>'; 
												} else {													
												echo '<option value="'.$Company->CompanyName.'">'.$Company->CompanyName.'</option>';
												}
											} 
											?>
										</select>
										<?php /*$tags= array(); 
											foreach($CompanyDetails as $Company) {  
												$tags[] = $Company->CompanyName;
											}*/ 
											?>
										</div>									
									</div>
									<input type="hidden" name="email" id="CompanyEmail" value="">
									
								</div>
								<div class="row">
								<div class="col-md-6 fileentry">
										<form  name="formfile"  class="formfile">
											<input type="file" name="file" id="filebulk_entry" class="dropify" value="">
										</form>						
								</div>
								<div class="CompanyDetailsDiv col-md-6">
									<table class="table table-borderless">	
										<tbody>
											<tr>
												<td>Company Name</td>
												<td> : </td>
												<td><?php echo $CompanyName; ?></td>
												<td></td>
											</tr>
											<tr>
												<td>Policy Number</td>
												<td> : </td>
												<td><?php if(!empty($hoi_policyNo)){ echo $hoi_policyNo->Comments; } ?></td>
												<td></td>
											</tr>
											<tr>
												<td>Policy Expiration Date</td>
												<td> : </td>
												<td><?php if(!empty($hoi_policyEx)){ echo $hoi_policyEx->Comments; } ?></td>
												<td></td>
											</tr>
											<tr>
												<td>Company Email</td>
												<td> : </td>
												<td>
													<?php if(!empty($hoi_email)){ echo $hoi_email->Comments; } ?>

													<input type="hidden" name="CompanyEmail" class="form-group " id="CompanyEmail" value="">
												</td>
												<td></td>
											</tr>
											<tr>
												<td>Company Contact</td>
												<td> : </td>
												<td>
													<?php if(!empty($hoi_ContactNo)){ echo $hoi_ContactNo->Comments; } ?>
													
													<input type="hidden" name="CompanyContact" class="form-group " id="CompanyContact" value="">
												</td>
											</tr>
											<tr>
												<td>Company Fax</td>
												<td> : </td>
												<td>
													<?php if(!empty($hoi_efax)){ echo $hoi_efax->Comments; } ?>
													<input type="hidden" name="CompanyFax" class="form-group " id="CompanyFax" value="">
												</td>
												<td></td>
											</tr>
											<tr>
												<td>Company Website</td>
												<td> : </td>
												<td>
													<?php if(!empty($hoi_web)){ echo $hoi_web->Comments; } ?>
													<input type="hidden" name="CompanyWeb" class="form-group " id="CompanyWeb" value="">
												</td>
												<td></td>
											</tr>
										</tbody>
									</table>
									<div class="row" style="display:none;"> 
									<div class="col-md-12 cSub">
										<label class="bmd-label-floating">Company Name : </label>
										<span id="Cname"> <?php echo $CompanyName; ?> </span>									
									</div>
									<div class="col-md-12 cSub">
										<label class="bmd-label-floating">Policy Number : </label>
										<span id="CPolicyname">
											<?php if(!empty($hoi_policyNo)){ echo $hoi_policyNo->Comments; } ?>
										</span>									
									</div>
									<div class="col-md-12 cSub">
										<label class="bmd-label-floating">Policy Expiration Date : </label>
										<span id="CPolicyExp"><?php if(!empty($hoi_policyEx)){ echo $hoi_policyEx->Comments; } ?></span>									
									</div>
									<div class="col-md-12 cSub">
										<label for="CompanyEmail" class="bmd-label-floating">Company Email : </label>
										<span id="Cemail"><?php if(!empty($hoi_email)){ echo $hoi_email->Comments; } ?></span>
										<input type="hidden" name="CompanyEmail" class="form-group " id="CompanyEmail" value="">		
										
									</div>
									<div class="col-md-12 cSub">
										<label for="CompanyContact" class="bmd-label-floating">Company Contact :</label>
										<span id="cNo"> <?php if(!empty($hoi_ContactNo)){ echo $hoi_ContactNo->Comments; } ?> </span>										
										<input type="hidden" name="CompanyContact" class="form-group " id="CompanyContact" value="">											
									</div>
									<div class="col-md-12 cSub">
										<label for="CompanyFax" class="bmd-label-floating">Company Fax : </label>
										<span id="cFax"> <?php if(!empty($hoi_efax)){ echo $hoi_efax->Comments; } ?> </span>										
										<input type="hidden" name="CompanyFax" class="form-group " id="CompanyFax" value="">											
									</div>
									<div class="col-md-12 cSub">
										<label for="CompanyWeb" class="bmd-label-floating">Company Website : </label>
										<span id="cWeb"> <?php if(!empty($hoi_web)){ echo $hoi_web->Comments; } ?> </span>										
										<input type="hidden" name="CompanyWeb" class="form-group " id="CompanyWeb" value="">											
									</div>
								</div>
								</div>
								</div>
								
							</div>
							<div class="row form-group" style="margin-top: 0px">
								<div class="col-md-6 text-right">
									<?php if ($OrderSummary->StatusUID != $this->config->item('keywords')['Cancelled']) { ?>
										<button type="submit" class="btn btn-space btn-social btn-color btn-update loanfile_update pull-right" value="1"  style="display: none;"> Upload File </button>
										<button type="submit" class="btn btn-space btn-social btn-color btn-update loanfile_sendcron pull-right" value="0"> Upload </button>
									<?php } ?>							
								</div>
							</div>
						</form>
						<?php if(!empty($Documents)){ ?>
							<h4 class="card-title" style="padding-left:5px;padding-bottom:5px;">Loan Files</h4>
							<!-- <label class="bmd-label-floating"> Order Files </label> -->
							<table class="table">
								<thead>
									<th>Document Name</th>
									<th>Document Type</th>
									<th>Uploaded Date Time</th>
									<th>Uploaded User</th>
									<th>OCR Status</th>
									<th style="width:80px;text-align:center;">Action</th>
								</thead>	
								<tbody>
									<?php
								// if(empty($Documents)){
								// 	echo '<td colspan="4" class="text-center">No Documents Found</td>';
								// }
									foreach($Documents as $Document){ ?>
										<tr>
											<td>
												<?php echo $Document->DocumentName; ?>
											</td>
											<td>
												<?php echo $Document->TypeofDocument; ?>
											</td>
											<td>
												<?php echo $Document->UploadedDateTime; ?>
											</td>
											<td>
												<?php echo $Document->UploadedUser; ?>
											</td>
											<td>
												<?php 
												if($Document->IsStacking == '4'){
													echo "Failed";
												}else if($Document->IsStacking == '2'){ 
													echo "Success";
												}else if($Document->IsStacking == '1'){
													echo '-';
												}
												?>
											</td>
											<td style="text-align:center;">
												<a target="_blank" href="<?php echo base_url().$Document->DocumentURL; ?>" type="button" class=" viewFile" title="View" style=""><img src="assets/img/icon.png" style="max-width:23px;"></a>

												<!-- <a target="_blank" title="" href="<?php echo base_url().$Document->DocumentURL; ?>" class="btn btn-sm btn-xs viewFile" style="background-color: #f2f2f2;color: #000;"><span class="mdi mdi-eye"></span>  View</a> -->
												<button class="removeFile btn btn-sm btn-xs btn-danger" data-documentuid="<?php echo $Document->DocumentUID; ?>">X</button>									  
											</td>
										</tr>
									<?php } ?>
								</tbody>		
							</table>
						<?php }
				/**
				Desc: Showoff automation logs & status for the Order
				**/ 
				$OrderLogs = $this->Common_Model->GetAutoLogs($OrderUID);
				if(!empty($OrderLogs) ){ $i = 0; ?>
                                <h4 class="card-title" style="padding-left:5px;padding-bottom:5px;">Automation Log</h4>
					<!-- <label class="bmd-label-floating"> Automation Log </label> -->
					<table class="table">
						<thead>
							<th> S.No </th>
							<th> Automation Type </th>
							<th> Automation Status </th>
							<th> Date Time </th>
							<th> Action </th>
						</thead>
						<tbody>
							<?php foreach ($OrderLogs as $key => $LastLog) {
						# code...
								if($LastLog->CreatedDate != ''){
									$i++;
									$Actiondata='';
									if($LastLog->EFaxDataUID){						
										$Actiondata = '<a href="javascript:void(0)" class="viewFile btn_metadata" title="Get Meta Data" data-logid="'.$LastLog->EFaxDataUID.'" data-orderid="'.$LastLog->OrderUID.'" title="View Details"> <img src="assets/img/icon.png" style="max-width:23px;"> </a>';
									} else if($LastLog->EmailUID || $LastLog->EmailUID == '0'){
										$Actiondata = '<a href="javascript:void(0)" class="viewFile btn_emaildata" data-logid="'.$LastLog->EmailUID.'" data-orderid="'.$LastLog->OrderUID.'"  title="View Details"> <img src="assets/img/icon.png" style="max-width:23px;"> </a>';
									} else if($LastLog->DocumentUID){
										$Actiondata = '<a href="javascript:void(0)" class="viewFile btn_loandata" data-logid="'.$LastLog->DocumentUID.'" data-orderid="'.$LastLog->OrderUID.'"  title="View Details"> <img src="assets/img/icon.png" style="max-width:23px;"> </a>';
									} else if($LastLog->AutomationType == 'Bot Receive'){

									}
									?>

									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $LastLog->AutomationType; ?></td>
										<td> <?php echo $LastLog->AutomationStatus; ?> </td>
										<td> <?php echo $LastLog->CreatedDate; ?> </td>
										<td>  <?php echo $Actiondata; ?> <!-- <a href="<?php echo base_url('AutomationLog').'?search='.$LastLog->OrderUID; ?>" class="btn btn-sm btn-xs" target="_blank" style="background-color: #f2f2f2;color: #000;">View More</a> --> </td>
									</tr>

									<?php  
								} 
							}
							?>
						</tbody>
					</table>
					<?php
				}
				?>

			</div>
		</div>
	</div>
</div>
</div>

<div id="LogResponse" tabindex="-1" role="dialog"  class="modal fade custommodal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center" style="background-color: #1d4870;">
				<h5 style="color: #fff;"> Response Data </h5>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div id="append_history"></div>
					<form id="FrmLogResponse">
						<div class="col-md-12">
							<div class="form-group">
								<span class="LogGetResponse"></span>
							</div>
						</div>
					</form>
				</div>
				<div class="text-right">
					<button class="btn btn-space btn-social btn-color btn-danger" data-dismiss="modal" style="" id="Close">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('orderinfoheader/workflowpopover'); ?>

<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>

<script type="text/javascript">

	$('.order_expand_div').show();
	function updateDetails(value){
		var OrderUID = $('#OrderUID').val();
		button = $('.loanfile_update');
		button_text = $('.loanfile_update').html();
		$.ajax({
			url:'OrderLoanFile/GetCompanyDetails',
			data:{'name': value,'OrderUID': OrderUID},
			type:'POST',
			dataType:'json',
			beforeSend: function(){					
				button.attr("disabled", true);
				button.html('Loading ...'); 
			},
			success: function(data){
				
					//$('.loanfile_sendcron').hide();
					$('.loanfile_update').show();
					if(data.Status == 1){
						$('#Cname').html(value);
						$('#Cemail')
						.html(data.message)
						.addClass('danger');
						$('#CompanyEmail').val('');
						$('#cNo').html('');
						$('#cFax').html('');
						$('#cWeb').html('');
						$('#CPolicyname').html('');
						$('#CPolicyExp').html('');
						button.removeAttr("disabled", true);
						button.html('Upload File & Move to HOI Exception Queue'); 
					}else{
						var details = JSON.stringify(data.details);
						//console.log(data.details.CompanyName);
						if(data.OrderImportDetails && data.OrderImportDetails.Email != '' && data.OrderImportDetails.Email != null){
							$('#Cemail')
							.html(data.OrderImportDetails.Email)
							.removeClass('danger');
							$('#CompanyEmail').val(data.OrderImportDetails.Email);
						}else{
							$('#Cemail')
							.html(data.details.Email)
							.removeClass('danger');
							$('#CompanyEmail').val(data.details.Email);
						}
						if(data.OrderImportDetails && data.OrderImportDetails.InsuranceCompany != '' && data.OrderImportDetails.InsuranceCompany != null){
							$('#Cname').html(data.OrderImportDetails.InsuranceCompany);
						}else{
							$('#Cname').html(data.details.CompanyName);
						}
						$('#cNo').html(data.details.ContactNo);
						if(data.OrderImportDetails && data.OrderImportDetails.Efax != ''&& data.OrderImportDetails.Efax != null){
							$('#cFax').html(data.OrderImportDetails.Efax);
						}else{
							$('#cFax').html(data.details.FaxNo);
						}
						if(data.OrderImportDetails && data.OrderImportDetails.WebUrl != ''&& data.OrderImportDetails.WebUrl != null){
							$('#cWeb').html(data.OrderImportDetails.WebUrl);
						}else{
							$('#cWeb').html(data.details.Website);	
						}
						if(data.OrderImportDetails == false){
							$('#CPolicyname').html('');
							$('#CPolicyExp').html('');
						}else{
							$('#CPolicyname').html(data.OrderImportDetails.PolicyNumber);
							$('#CPolicyExp').html(data.OrderImportDetails.PolicyExpDate);
						}						
						button.removeAttr("disabled", true);
						button.html('Upload File & Send Email'); 
					}
					
				},
				error: function (jqXHR, textStatus, errorThrown) {					
					button.html('Upload File');
					button.removeAttr("disabled");
				},
			});
	}
	$(document).ready(function(){
	// var tag = '<?php echo $tags; ?>';
    // $('.pre_select').select2({tags: [tag]});
    $('.pre_select').select2();

    $('#HOICompanyName').on('change', function(){

    	var value = $(this).val();
    	if(value){
    		updateDetails(value);
    	}
    });

    $('#filebulk_entry').dropify();	
    
    $(document).off('click',  '.loanfile_update').on('click',  '.loanfile_update', function (event) {
    	event.preventDefault();
    	button = $(this);
    	button_text = $(this).html();
    	var OrderUID = $('#OrderUID').val();
    	var name = $('#HOICompanyName').val();
    	var email = $('#CompanyEmail').val();
			/*var web = $('#CompanyWebsite').val();
			var contact = $('#CompanyContact').val();
			var fax = $('#CompanyFax').val();*/

			var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (name == "") {
				$.notify({icon:"icon-bell-check",message:"Fill the required fields"},{type:"danger",delay:3000 });
				return false;
			}
			// if(!regex.test(email)){
			// 	$.notify({icon:"icon-bell-check",message:"Please enter valid email"},{type:"danger",delay:3000 });
			// 	return false;				
			// }
			var Docs = '<?php echo $Documents->DocumentUID ?>';
			if($('#filebulk_entry')[0].files.length === 0) {
				$.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
				return false;
			}

			var file_data = $('#filebulk_entry').prop('files')[0];
			var form_data = new FormData();
			form_data.append('file', file_data);
			form_data.append('OrderUID',OrderUID);
			form_data.append('name',name );
			form_data.append('email',email );
			/*form_data.append('web',web );
			form_data.append('contact',contact );
			form_data.append('fax',fax );*/

			$.ajax({
				type: "POST",
				url: 'OrderLoanFile/UpdateLoanFile',
				data: form_data,
				processData: false,
				contentType: false,
				cache:false,
				dataType:'json',
				beforeSend: function(){					
					// $('.loanfile_update').addClass("be-loading-active");
					button.attr("disabled", true);
					button.html('Loading ...'); 
				},
				success: function(data)
				{
					button.html(button_text);
					button.removeAttr("disabled");

					if(data.Status==1){
						$.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
					}else if(data.Status==2){
						$.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
					}else{
						$.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:3000 });
					}

					setTimeout(function(){location.href=location.href} , 5000);   									
				},
				error: function (jqXHR, textStatus, errorThrown) {					
					button.html(button_text);
					button.removeAttr("disabled");
				},
			});
		});

    $(document).off('click',  '.loanfile_sendcron').on('click',  '.loanfile_sendcron', function (event) {
    	event.preventDefault();
    	button = $(this);
    	button_text = $(this).html();
    	var OrderUID = $('#OrderUID').val();

    	var Docs = '<?php echo $Documents->DocumentUID ?>';
    	if($('#filebulk_entry')[0].files.length === 0) {
    		$.notify({icon:"icon-bell-check",message:"No file selected"},{type:"danger",delay:3000 });
    		return false;
    	}

    	var file_data = $('#filebulk_entry').prop('files')[0];
    	var form_data = new FormData();
    	form_data.append('file', file_data);
    	form_data.append('OrderUID',OrderUID);


    	$.ajax({
    		type: "POST",
    		url: 'OrderLoanFile/init_ocr',
    		data: form_data,
    		processData: false,
    		contentType: false,
    		cache:false,
    		dataType:'json',
    		beforeSend: function(){					
					// $('.loanfile_update').addClass("be-loading-active");
					button.attr("disabled", true);
					button.html('Loading ...'); 
				},
				success: function(data)
				{
					button.html('Upload');
					button.removeAttr("disabled");

					if(data.Status==1){
						$.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
					}else if(data.Status==2){
						$.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
					}else{
						$.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:3000 });
					}

					setTimeout(function(){location.href=location.href} , 5000);   									
				},
				error: function (jqXHR, textStatus, errorThrown) {					
					button.html('Upload');
					button.removeAttr("disabled");
				},
			});
    });
});

$('.removeFile').on('click',function(e){
	var DocUID = $(this).attr('data-documentuid');
	console.log(DocUID);
	var button = $(this);

	$.ajax({
		url: '<?php echo base_url('OrderLoanFile/RemoveDocument');?>',
		data: {'DocUID':DocUID},
		type:"POST",
		cache: false,
		beforeSend: function(){
			$('.spinnerclass').addClass('be-loading-active');
		},
		success: function(data)
		{
			$.notify({icon:"icon-bell-check",message:'Document Deleted Successfully'},{type:"success",delay:3000 });
			$('.spinnerclass').removeClass('be-loading-active');
			setTimeout(function(){location.href=location.href} , 5000);   									
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			$('.spinnerclass').removeClass('be-loading-active');

		},
	});

});
$(document).off('click','.btn_metadata').on('click','.btn_metadata',function() { 
	var EFaxDataUID = $(this).attr('data-logid');
	$.ajax({
		type: "POST",
		url: '<?php echo base_url();?>EfaxOrders/GetMetaDataByFaxID',
		data: {'EFaxDataUID':EFaxDataUID},
		dataType:'JSON',
		beforeSend: function(){
		},
		success: function(data){
			$('#LogResponse').modal('show');
			$('.LogGetResponse').html('');
			$('.LogGetResponse').html(data);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
		},
		failure: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
	});
});

$(document).off('click','.btn_emaildata').on('click','.btn_emaildata',function() { 
	var EmailUID = $(this).attr('data-logid');
	var OrderUID = $(this).attr('data-orderid');
	$.ajax({
		type: "POST",
		url: '<?php echo base_url();?>AutomationLog/GetEmailResponse',
		data: {'EmailUID':EmailUID,'OrderUID':OrderUID},
		dataType:'JSON',
		beforeSend: function(){
		},
		success: function(data){
			if(data.status == 1) {            
				$('#LogResponse').modal('show');
				$('.LogGetResponse').html('');
				$('.LogGetResponse').html(data.html);
			} else {
				$.notify(
				{
					icon:"icon-bell-check",
					message:'No Data Found'
				},
				{
					type:'danger',
					delay:1000 
				});
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

$(document).off('click','.btn_loandata').on('click','.btn_loandata',function() { 
	var DocumentUID = $(this).attr('data-logid');
	var OrderUID = $(this).attr('data-orderid');
	$.ajax({
		type: "POST",
		url: '<?php echo base_url();?>AutomationLog/GetOCRResponse',
		data: {'DocumentUID':DocumentUID,'OrderUID':OrderUID},
		dataType:'JSON',
		beforeSend: function(){
		},
		success: function(data){
			if(data.status == 1){
				$('#LogResponse').modal('show');
				$('.LogGetResponse').html('');
				$('.LogGetResponse').html(data.response);
			} else {
				$.notify(
				{
					icon:"icon-bell-check",
					message:'No Response Found'
				},
				{
					type:'danger',
					delay:1000 
				});
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
$(document).off('click','.btn_botdata').on('click','.btn_botdata',function() { 
	var DocumentUID = $(this).attr('data-logid');
	var OrderUID = $(this).attr('data-orderid');
	$.ajax({
		type: "POST",
		url: '<?php echo base_url();?>CommonController/GetBotResponse',
		data: {'OrderUID':OrderUID},
		dataType:'JSON',
		beforeSend: function(){
		},
		success: function(data){
			if(data.status == 1){
				$('#LogResponse').modal('show');
				$('.LogGetResponse').html('');
				$('.LogGetResponse').html(data);
			} else {
				$.notify(
				{
					icon:"icon-bell-check",
					message:'No Response Found'
				},
				{
					type:'danger',
					delay:1000 
				});
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








