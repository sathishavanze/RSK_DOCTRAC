	<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
	<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>

	<div class="row">
		<div class="col-md-12 pd-0">
			<h5 style="color:#3b5998;font-weight: 500;">Generic Upload</h5>
		</div>

		<div class="col-md-12 pd-0">
			<form>
				<input type="file" id="customerdocument" name="customerdocument[]" class="dropify"   accept=".pdf"  multiple />
			</form> 

			<div class="progress progress-line-info" id="progressupload" style="display: none; height: 22px;">
				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 21px;">
					<span class="sr-only">0% Complete</span>
				</div>
			</div>
		</div>

		<div class="col-md-12 instructiondivider pd-0 mt-20">     
			<h5><strong>Customer Documents Detail</strong></h5> 
			<div class="uploadedfile">
				<?php if (!empty($Customers->CustomerInformation) && is_file(FCPATH . $Customers->CustomerInformation)) {
					$fname=basename(FCPATH . $Customers->CustomerInformation);
					$fsize=$this->Common_Model->filesize_formatted(FCPATH . $Customers->CustomerInformation); 
					?>
					<div class='row filediv'>
						<div class='col-md-4'>
							<p class='mb-0'>
								<strong><?php echo $fname; ?></strong>
							</p>
							<p style="font-style: italic;">
								<?php echo $fsize; ?>
							</p>
						</div>
						<div class='col-md-8'>
							<a href="<?php echo base_url($Customers->CustomerInformation); ?>" target='_blank' class='btn btn-sm btn-outline-info defaultfileview'><i class='icon-eye'></i></a>
							<button class='btn btn-outline-danger btn-sm customerdocumentremove_server'><i class='icon-x'></i></button>
						</div>
					</div>
				<?php } else{ ?>
					<p>No files were uploaded</p>       
				<?php }?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 pd-0 mt-10">
			<h5 style="color:#3b5998;font-weight: 500;">Abstractor Instruction</h5>
		</div>

		<div class="col-md-12 borderright dynamicdiv">
			<div class="row" style="background:#f7f7f7;">
				<div class="col-md-4 dyndivhead">
					<strong>Order Type</strong>
				</div>
				<div class="col-md-4 dyndivhead">
					<strong>Document Upload ( PDF ) </strong>
				</div>
				<div class="col-md-4 dyndivhead">
					<strong>Action</strong>
				</div>
			</div>
			<form method="post" enctype="multipart/form-data" class="frmabstractordoc">
				<div class="row abstractordoc">															
					<div class="col-md-4 dyndiv">
						<div class="form-group bmd-form-group">
							<label for="filter_ordertypeuid" class="bmd-label-floating">Order Types</label>
							<select class="select2picker ordertypes_select" name="ordertypes[]" multiple >  
								<?php foreach ($OrderTypes as $key => $type) { ?>
									<option value="<?php echo $type->OrderTypeUID; ?>"><?php echo $type->OrderTypeName; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4 dyndiv">
						<center style="padding:5px;">
							<span class="btn btn-default fileinput-button-upload">      
								<h6>Browse</h6>   
								<input type="file" name="ordertypedocs" id="ordertypedocs1" class="abstractorordertypedoc" accept=".pdf">
							</span>
						</center> 
						<p class="showfilename"></p>
						<div class="progress progress-line-info" id="documentupload1" style="display: none; height: 22px;">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 21px;">
								<span class="sr-only" style="height: 21px;">0% Complete</span>
							</div>
						</div>


					</div>
					<div class="col-md-4 dyndiv">
						<div class="row">
							<div class="col-6 viewdocumentcontainer">
							</div>
							<div class="col-6">
								<button type="button" class="btn btn-github btn-sm addabstractordoc"><i class="icon-plus22"></i></button>
								<button type="submit" class="btn btn-sm btn-success upload hide"><i class="icon-upload"></i> Upload</button>
							</div>
						</div>
					</div>																
				</div>
			</form>
			<?php foreach ($OrderTypeDocuments as $key => $value) {
				$selectedordertypes=explode(',', $value->OrderTypeUID);

				?>
				<form method="post" enctype="multipart/form-data" class="frmabstractordoc">
					<div class="row abstractordoc">															
						<div class="col-md-4 dyndiv">
							<div class="form-group bmd-form-group">
								<label for="filter_ordertypeuid" class="bmd-label-floating">Order Types</label>
								<select class="select2picker ordertypes_select" name="ordertypes[]" multiple >  
									<?php foreach ($OrderTypes as $key => $type) { ?>
										<?php if (in_array($type->OrderTypeUID, $selectedordertypes)) { ?>

											<option value="<?php echo $type->OrderTypeUID; ?>" selected><?php echo $type->OrderTypeName; ?></option>
										<?php }	else{ ?>
											<option value="<?php echo $type->OrderTypeUID; ?>" ><?php echo $type->OrderTypeName; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-4 dyndiv">
							<center style="padding:5px;">
								<span class="btn btn-default fileinput-button-upload">      
									<h6>Browse</h6>   
									<input type="file" name="ordertypedocs" id="ordertypedocs1" class="abstractorordertypedoc" accept=".pdf">
								</span>
							</center> 
							<p class="showfilename"></p>
							<div class="progress progress-line-info" id="documentupload1" style="display: none; height: 22px;">
								<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 21px;">
									<span class="sr-only" style="height: 21px;">60% Complete</span>
								</div>
							</div>


						</div>
						<div class="col-md-4 dyndiv">
							<div class="row">
								<div class="col-6 viewdocumentcontainer">
									<a class="btn btn-link btn-dribbble" href="<?php echo base_url($value->DocumentName); ?>" target="_blank"><i class="icon-eye"></i> View Document</a>
								</div>
								<div class="col-6">
									<button type="button" class="btn btn-github btn-sm removeabstractordocserver"><i class="icon-minus3"></i></button>
									<button type="submit" class="btn btn-sm btn-success upload hide"><i class="icon-upload"></i> Upload</button>
								</div>
							</div>
						</div>																
					</div>
				</form>
			<?php } ?>
		</div>
	</div>




	<form id="Default_ProductForm">
		<div class="row">		
			<div class="col-md-12 pd-0 mt-20">
				<h5 style="color:#3b5998;font-weight: 500;">Default Product & Sub Product</h5>
			</div>
			<div class="col-md-12 mt-10 pd-0">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group bmd-form-group">
							<label for="DefaultProductSubProduct" class="bmd-label-floating">Default Product & Sub Product</label>
							<select class="select2picker form-control" name="DefaultProductSubProduct" id="DefaultProductSubProduct" >

								<option></option>
								<?php $DefaultProductSubCode = $this->Customer_Model->GetmcustomerdefaultProduct($Customers->CustomerUID);	
							//echo '<pre>';print_r($DefaultProductSubCode);exit;						
								if($DefaultProductSubCode == 1){?>
									<option value = "1" selected>Default Subproduct</option>
									<option value = "2" >Most Processed Subproduct for the Month</option>
									<option value = "3">Most Processed Subproduct So far</option>								

								<?php } else if($DefaultProductSubCode == 2) {?>
									<option value = "1" >Default Subproduct</option>
									<option value = "2" selected>Most Processed Subproduct for the Month</option>
									<option value = "3">Most Processed Subproduct So far</option>
								<?php } else { ?>
									<option value = "1" >Default Subproduct</option>
									<option value = "2" >Most Processed Subproduct for the Month</option>
									<option value = "3" selected>Most Processed Subproduct So far</option>
								<?php }?>

							</select>
						</div>
					</div>

					<?php $DefaultProductSubCode = $this->Customer_Model->GetmcustomerdefaultProduct($Customers->CustomerUID);
					if($DefaultProductSubCode == 1) {?>
						<div class="col-md-8"  id="DefaultSubProductdiv">
							<select  class="form-control select2picker" id="DefaultSubProduct" name="DefaultSubProduct" multiple>
								<option></option>
								<?php 	
								foreach ($SubProducts as $key => $subproduct) {
									$res = $this->Customer_Model->getCustomerDefaultProduct($Customers->CustomerUID);
									foreach ($res as $key => $value) {
										foreach ($value as $key => $data) {
											if($subproduct->SubProductUID == $data)
												{?>
													<option value="<?php echo $subproduct->SubProductUID; ?>" selected><?php echo $subproduct->SubProductName;?></option>
													<?php 
												}
											}
										} ?>
									<?php } ?>
									<?php
									foreach ($unSelectProducts as $key => $value) { ?>
										<option value="<?php echo $value['SubUID']; ?>"><?php echo $value['SubPName']; ?></option>
									<?php }	?>
								</select>
							</div>
						<?php } else { ?>							
							<div class="col-sm-8" id="DefaultSubProductdiv"">
								<div class="form-group">
									<select  class="form-control select2picker" id="DefaultSubProduct" name="DefaultSubProduct" 
									multiple>
									<option></option>
								</select>
							</div>
						</div>
					<?php  } ?>
				</div>
			</div>

			<div class="col-md-12 col-xs-12 text-right">
				<button type="submit" class="btn btn-fill btn-dribbble btn-wd" name="Update_DefaultProductsetup" id="Update_DefaultProductsetup"><i class="icon-floppy-disk pr-10"></i>Update</button>
			</div>

		</div>
	</form>





	<script type="text/javascript">

		$(function() {
			$('.dropify').dropify();
			$(".selectpicker").selectpicker();
			$("select.select2picker").select2({
				//tags: false,
				theme: "bootstrap",
			});



			$('#DefaultProductSubProduct').change(function()
			{					
				var CustomerUID = $('#CustomerUID').val();
				var id = $('#DefaultProductSubProduct').val();
				var SubProductUID_Select_Table = [];
				$.ajax({
					type : "POST",
					url : "<?php echo base_url();?>/Customer/getSubproductList",
					data : {"CustomerUID" : CustomerUID},
					async :  false,
					dataType:'html',
					success : function(data){							
						if(id == 1)
						{
							$('#DefaultSubProduct').empty();
							$("#DefaultSubProductdiv").show();
							$('#DefaultSubProduct').append(data);
						}
						else{
							$("#DefaultSubProductdiv").hide();
						}
					}
				});
			});


			$("#Update_DefaultProductsetup").click(function(e){
				e.preventDefault();
				var CustomerUID = $('#CustomerUID').val();
				var DefaultProductSubProduct =  $("#DefaultProductSubProduct").val();
				var DefaultSubProduct =  $("#DefaultSubProduct").val();			
				var button = $('#Update_DefaultProductsetup');
				var button_text = $('#Update_DefaultProductsetup').html();		
				if(DefaultProductSubProduct != 1){
					DefaultSubProduct = '';
				}

				if(DefaultSubProduct == ''){
					DefaultSubProduct = '';
					DefaultProductSubProduct = '3';
				}

				$.ajax({
					type : "POST",
					url : "<?php echo base_url();?>Customer/UpdateDefaultSubProductSetup",
					data : {"CustomerUID" : CustomerUID , "DefaultProductSetup" : DefaultProductSubProduct , "DefaultSubProductUIDs" : DefaultSubProduct},
					beforeSend: function(){
						button.attr("disabled", true);
						button.html('Loading ...'); 
					},
					success : function(data){						
						parsedata  = JSON.parse(data);
						colortxt = '';
						if(parsedata.Status == 1){
							colortxt = 'success';
						}else{
							colortxt = 'danger';
						}
						$.notify(
						{
							icon:"icon-bell-check",
							message:parsedata.Message
						},
						{
							type:colortxt,
							delay:1000 
						});
						button.html(button_text);
						button.removeAttr("disabled");
					}
				});
			});


			/*-----Document Uploads Tab STARTS----*/
			$(document).off('change', '#customerdocument').on('change', '#customerdocument', function (e) {
				var postdata=new FormData();
				if($(this).prop('files').length > 0)
				{
					var file =$(this).prop('files')[0];
					postdata.append("customerfiles", file);
				}
				postdata.append('CustomerUID',$('#CustomerUID').val());
				var progress=$('.progress-bar');
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/CustomerUpload',
					data: postdata, 
					processData:false,
					contentType: false,
					dataType:'json',
					beforeSend: function(){
						$("#progressupload").show();
					},
					xhr: function () {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function (evt) {
							if (evt.lengthComputable) {
								var percentComplete = evt.loaded / evt.total;
								percentComplete = parseInt(percentComplete * 100);
								$(progress).width(percentComplete + '%');
								$(progress).text('Uploading ' + percentComplete + '%');
							}
						}, false);
						return xhr;
					},
					success: function(data)
					{
						if(data.validation_error == 0){
							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"success",
								delay:1000 
							})

							ChangeCustomerFileDetails(file, data);
						}else if(data.validation_error == 1){

							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"danger",
								delay:1000 
							})
						}
						ResetProgress(progress);

					},
					error: function(jqXHR){
						console.log(jqXHR);
						ResetProgress(progress);
					}
				});
			})

			$(document).off('change', '.ordertypes_select').on('change', '.ordertypes_select', function (e) {
				var currentordertypeval=$(this).find(':selected').map(function(key, value){
					return value.value;
				}).get();

				var other_ordertypes_select=$('.ordertypes_select').not(this);

				$.each(other_ordertypes_select, function (k, elem) {

					$(currentordertypeval).each(function (key, v) {
						$(elem).find('option[value="'+v+'"]').prop('selected', false);
					})

					$(elem).select2({
						//tags: false,
						theme: "bootstrap",
					});

				});

				var parent=getParentByClass($(this)[0], 'abstractordoc');

				var ele =$(parent).find('input[type="file"]');


				if ($(this).val().length && $(ele).prop('files').length) {
					$(parent).find('button.upload').removeClass('hide');
				}else{
					$(parent).find('button.upload').addClass('hide');
				}
			})

			$(document).off('change', '.abstractorordertypedoc').on('change', '.abstractorordertypedoc',function (e) {
				var ordertypepostdata=new FormData();
				if($(this).prop('files').length > 0)
				{
					var file =$(this).prop('files')[0];
					ordertypepostdata.append("customerfiles", file);
				}

				var parent=getParentByClass($(this)[0], 'abstractordoc');

				var elem =$(parent).find('select');

				if ($(elem).val().length && $(this).prop('files').length) {
					$(parent).find('button.upload').removeClass('hide');
				}else{
					$(parent).find('button.upload').addClass('hide');
				}

			})

			$(document).off('submit', '.frmabstractordoc').on('submit', '.frmabstractordoc', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var formdata=new FormData($(this)[0]);
				formdata.append('CustomerUID', $('#CustomerUID').val());

				var progressbar=$(e.target).find(".progress");
				var progress=$(progressbar).find(".progress-bar");
				var parentform=$(this)[0];
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/OrderTypeDocUpload',
					data: formdata, 
					processData:false,
					contentType: false,
					dataType:'json',
					beforeSend: function(){
						$(progressbar).show();
						$(e.target).find('.upload').addClass('hide');
					},
					xhr: function () {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function (evt) {
							if (evt.lengthComputable) {
								var percentComplete = evt.loaded / evt.total;
								percentComplete = parseInt(percentComplete * 100);
								$(progress).width(percentComplete + '%');
								$(progress).text('Uploading ' + percentComplete + '%');
							}
						}, false);
						return xhr;
					},
					success: function(data)
					{
						if(data.validation_error == 0){
							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"success",
								delay:1000 
							})
							ShowViewDocumentLink(data.URL, parentform);

						}else if(data.validation_error == 1){

							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"danger",
								delay:1000 
							})
						}
						ResetProgress(progress);

					},
					error: function(jqXHR){
						console.log(jqXHR);
						ResetProgress(progress);
					}
				});

			})


			$(document).off('click', '.removeabstractordocserver').on('click', '.removeabstractordocserver', function (e) {
				e.preventDefault();

				var parentform=getParentForm($(this)[0]);

				swal({
					title: "<i class='icon-warning iconwarning'></i>",     
					html: '<p>Are you sure you want to delete this record ?</p>',   
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					buttonsStyling: false,
					closeOnClickOutside: false,
					allowOutsideClick: false,
					showLoaderOnConfirm: true,
					position: 'top-end'
				}).then(function(confirm) {

					var remformdata=new FormData();

					remformdata=$(parentform).serialize() + '&CustomerUID=' + $('#CustomerUID').val();

					console.log(remformdata);

					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>Customer/RemoveOrderTypeDoc',
						data: remformdata, 
						dataType:'json',
						beforeSend: function(){
						},
						success: function(data)
						{
							if(data.validation_error == 0){

								swal({
									title: "<i class='icon-checkmark2 iconsuccess'></i>", 
									html: "<p>Record Deleted Successfully</p>",
									confirmButtonClass: "btn btn-success",
									allowOutsideClick: false,
									width: '300px',
									buttonsStyling: false
								}).catch(swal.noop)

								$(parentform).remove();

							}else if(data.validation_error == 1){

								$.notify(
								{
									icon:"icon-bell-check",
									message:data.message
								},
								{
									type:"danger",
									delay:1000 
								})
							}

						},
						error: function(jqXHR){
							console.log(jqXHR);
						}
					});

				},
				function(dismiss) {              

				})


			})

			$(document).off('click', '.customerdocumentremove_server').on('click', '.customerdocumentremove_server', function (e) {
				e.preventDefault();

				swal({
					title: "<i class='icon-warning iconwarning'></i>",     
					html: '<p>Are you sure you want to delete customer file ?</p>',   
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					buttonsStyling: false,
					closeOnClickOutside: false,
					allowOutsideClick: false,
					showLoaderOnConfirm: true,
					position: 'top-end'
				}).then(function(confirm) {
					$.ajax({
						type: "POST",
						url: '<?php echo base_url();?>Customer/RemoveCustomerFile',
						data: {'CustomerUID': $('#CustomerUID').val()}, 
						dataType:'json',
						beforeSend: function(){
					// $(progressbar).show();
					// $(e.target).find('.upload').addClass('hide');
				},
				success: function(data)
				{
					if(data.validation_error == 0){
						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"success",
							delay:1000 
						})
						$('.uploadedfile').html('<p>No files were uploaded</p>');

						swal({
							title: "<i class='icon-checkmark2 iconsuccess'></i>", 
							html: "<p>Record Deleted Successfully</p>",
							confirmButtonClass: "btn btn-success",
							allowOutsideClick: false,
							width: '300px',
							buttonsStyling: false
						}).catch(swal.noop)
						
					}else if(data.validation_error == 1){

						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"danger",
							delay:1000 
						});
					}
				},
				error: function(jqXHR){
					console.log(jqXHR);
				}
			});
				},
				function(dismiss) {              

				})


			})

		});
	/*-----Document Uploads Tab ENDS----*/

</script>