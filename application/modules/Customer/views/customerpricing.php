		<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
			<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>
		<style>
			.pricingpreview{
				border: 1px solid #efefef;
				background: #f9f9f9;
				box-shadow: 1px 1px 3px 0px #b5b1b1;
			}
			.pricingpreview .table{
				background: #fff;
			}
			#closepricingprev{
				cursor: pointer;
			}
			.modal-header {
				background: #36528c;
			}
			.close{
    cursor: pointer;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}
		</style>
	
		<div class="modal fade modal-lg custommodal" id="feeupload" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" >
			<div class="modal-dialog">
				<div class="modal-content perfectscrollbar">
					<div class="modal-header" style="    min-height: 60px;">
						<h4 class="modal-title" style="color: #fff;">Not Imported Data</h4>  
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<i class="icon-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-striped table-bordered defaultfontsize pricingimportfailed"></table>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default close" data-dismiss="modal">Close</button>

					</div>
				</div>
			</div>
		</div>

		<div class="addnewpricing col-md-12 pd-0" >
			<div class="row divborder">
				<div class="col-md-4">
					<div class="form-group">
						<label for="pricingname" class="bmd-label-floating">Pricing Name</label>
						<input type="hidden" class="form-control" id="CustomerPricingUID" name="CustomerPricingUID"  value="<?php echo $Customers->PricingUID; ?>" required />
						<input type="text" class="form-control" id="PricingName" name="PricingName"  value="<?php echo $Customers->PricingName; ?>" />
					</div> 
				</div>
				<div class="col-md-2">
					<button class="btn btn-tumblr btn-sm mt-10" id="update_pricingname"> <i class="icon-checkmark pr-10"></i>Update <span class="pr-10"></span></button>
				</div>
				<div class="col-md-12 mt-10">
					<div class="row">
						<div class="col-md-4">  
							<div class="form-group"> 
								<label for="selectdefaultpricing" class="bmd-label-floating">Pricing Type</label> 
								<select  class="select2picker form-control"  id="selectdefaultpricing" name="selectdefaultpricing">
									<?php 
									foreach ($PricingDetails as $row) {
										if($row->PricingUID == $Customers->PricingUID)
											echo "<option value='".$row->PricingUID."' selected>".$row->PricingName."</option>";
										else
											echo "<option value='".$row->PricingUID."'>".$row->PricingName."</option>";
									} ?>         
								</select>
							</div>
						</div>
						<div class="col-md-2 mt-20">
							<div class="form-group">
								<button class="btn btn-sm btn-github <?php if($Customers->PricingUID == '' || $Customers->PricingUID == '0' ): ?> disabled <?php endif; ?>" id="copypricing"><i class="icon-copy4 pr-10"></i>Copy</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="row">						
					<div class="col-md-6 pd-0">
						<button class="btn btn-dribbble btn-sm <?php if($Customers->PricingUID == '' || $Customers->PricingUID == '0' ): ?> disabled <?php endif; ?>" id="pricingimport"><i class="pr-10 icon-download4"></i>Pricing Import</button>

						<a id="excel_exportlink" href="<?php echo base_url();?>Customer/ExcelPricingExport/<?php echo $Customers->CustomerUID; ?>/<?php echo $Customers->PricingUID; ?>" class="btn btn-dribbble btn-sm  <?php if($Customers->PricingUID == '' || $Customers->PricingUID == '0' ): ?> disabled <?php endif; ?>"><i class="pr-10 icon-upload4"></i>Pricing Export</a>
						
					</div>
				</div>
			</div>

			<div class="row importdiv" style="display: none;">

				<input type="file" id="CustomerPricingimport"  name="CustomerPricingimport[]" class="dropify"  accept=".xlsx" />

				<!-- 	<center class="col-md-12" style="padding:5px;">
						<span class="btn btn-default fileinput-button">
							<i class="icon-cloud-upload" style="display:block;font-size:45px;"></i>
							<h6 class="mt-20">Drag And Drop Files Here</h6>
							<span>or</span>
							<h6>Browse</h6>
							<input type="file"   >
						</span>
					</center>  -->
					<div class="col-md-12 text-right">
						<button class="btn btn-facebook" id="pricing_bulk_preview">Preview</button>
						<button class="btn btn-success" id="bulk_save">Upload</button>
					</div>
				</div>


				<div class="col-md-12 mt-20 pricingpreview" style="display: none">
					
					<div class="row" style="border-bottom:1px solid #ddd">
						<div class="col-md-8">
							<h5 class="mt-10">Import Data Preview</h5>						
						</div>
						<div class="col-md-4 text-right">
							<button class="btn btn-fab btn-round btn-pinterest" id="closepricingprev"><i class="icon-x"></i></button>
						</div>
					</div>
					<p class="mt-10 text-right">
						<span class="label label-rose">Pricing</span>
						<span class="label label-success">State</span>
						<span class="label label-info">County</span>
						<span class="label label-warning">Sub Product</span>
					</p>

					<div class="col-md-12 mt-10">
						<div class="table-responsive">
							<table class="table table-bordered" id="append_preview">

							</table>
						</div>
					</div>

				</div>

				<div class="row">		

					<div class="col-md-12 mt-20">
						<div class="material-datatables">
							<table class="table table-striped" id="pricingtable" cellspacing="0" width="100%" style="width:100%"
							>
							<thead>
								<tr>
									<th>ProductCode</th>
									<th>SubProduct</th>
									<th>State Code</th>
									<th>County</th>         
									<th>Pricing</th>
									<th>CancellationFee</th>
									<th>Action</th> 
								</tr>
							</thead>
							<tbody class="addpricelist">


							</tbody>
						</table>
					</div>
					<div class="updatediv text-right col-md-12 pd-0">
						<button class="btn btn-tumblr" id="BtnUpdateFees">Update</button>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">


			var Product_options='<?php foreach ($Products as $key => $prod) { ?><option value="<?php echo $prod->ProductUID; ?>"><?php echo $prod->ProductName;?></option><?php } ?>';
			var State_options='<?php foreach ($States as $state) {  echo '<option value="' . $state->StateUID .'">' . $state->StateName .'</option>';  } ?>';
   $('.dropify').dropify();

			function pricingtable_init(){
				var pricingtable = $('#pricingtable').DataTable({
					responsive  :  true,
					scrollCollapse: true,
					paging:  true,
					"processing": true, /*Feature control the processing indicator.*/
					"serverSide": true, /*Feature control DataTables' server-side processing mode.*/
					"order": [], /*Initial no order.*/
					"pageLength": 10, /*Set Page Length*/
					"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
					"language": {
						processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
					},
					/*Load data for the table's content from an Ajax source*/
					"ajax": {
						"url": "<?php echo base_url('Customer/fee_list')?>",
						"type": "POST",
						data: function(d) {
							d.CustomerUID = $('#CustomerUID').val();
						},
					}, 
					drawCallback: function( settings ) {
						$("select.select2picker").select2({
							//tags: false,
							theme: "bootstrap",
						});
					},

					/*Set column definition initialisation properties.*/
					"columnDefs": [
					{
						"targets": [6], /*first, Fourth, seventh column*/
						"orderable": false /*set not orderable*/
					}
					],

				});
			}

			$(function() {
				$(".selectpicker").selectpicker();
			});

			$(document).ready(function(){
				pricingtable_init();
				$("#pricingimport").click(function(){
					$(".importdiv").slideToggle();
				});
			});	



			var i = 0;
			$("body").off("click" , ".addpricing").on("click" , ".addpricing" , function(){      
				i = i + 1;    
				appenddiv = "<tr>"
				appenddiv  = appenddiv + '<td><input type="hidden" name="PricingProductUID" value=""><select class="select2picker form-control ProductUIDFee" data-size="7" name="ProductUID" ><option value="" selected="selected"></option>'+Product_options+'</select> </td><td><select class="select2picker form-control SubProductUIDFee"  name="SubProductUID" ><option value="" selected="selected"></option></select></td><td><select class="select2picker form-control StateCodeFee" name="StateCode" ><option value="" selected="selected"></option>'+State_options+'</select></td><td><select class="select2picker form-control CountyUIDFee" name="CountyUID"><option value="" selected="selected"></option></select></td><td><div class="form-group"><input type="text" class="form-control" name="Pricing" placeholder="Pricing"></div></td><td><div class="form-group"><input type="text" class="form-control" name="CancellationFee"  placeholder="CancellationFee"></div></td><td><a href="javascript:void(0);" class="btn btn-link btn-danger btn-just-icon btn-xs removepricing"><i class="icon-x"></i></a></td>';     
				appenddiv = appenddiv + "</tr>";
				$(".addpricelist").append(appenddiv);
				$(".updatediv").slideDown();
				callselect2();
			});


			$("body").on("click" , ".removepricing" , function(){
				$(this).closest("tr").remove();
			});

			/*PRICING TAB START*/

			$("#copypricing").click(function(e){


				swal({
					title: "<i class='icon-unlocked2 iconverify'></i><br><h5 class='pswverify'>Verify Password !</h5>",     
					html: '<div class="form-group">' +
					'<input name="pricingpassword" id="pricingpassword" type="password" class="form-control" placeholder="Enter Password" required/>' +
					'</div>',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					buttonsStyling: false,
					showLoaderOnConfirm: true,
					preConfirm: function() {
						return new Promise(function(resolve, reject) {
							var Password = $('#pricingpassword').val();
							var CopyPricingUID  =  $('#selectdefaultpricing').val();
							var PricingUID  =  $('#CustomerPricingUID').val();

							if(CopyPricingUID == '' && PricingUID == ''){
								reject('Pricing not selected');
							}

							if(Password == ''){
								$('#pricingpassword').closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
								reject('Enter Password');
							}else{
								$.ajax({
									type: "POST",
									url: '<?php echo base_url();?>Customer/update_copypricing',
									data: {'CopyPricingUID':CopyPricingUID,'PricingUID':PricingUID,'Password':Password},
									dataType:'json',
									success: function(data)
									{

										if(data['validation_error'] == '1'){


											reject(data['message']);

										}else{
											$('#pricingtable').DataTable().destroy();
											pricingtable_init();
											swal({
												title: data['message'],
												type : "success",
												confirmButtonColor: "#A5DC86",
												timer: 3000
											}, function(){
												resolve();
											});
										}


									}
								});


							}


						});
					}
				}).then(function () {
					console.log('modal is closed');
				});


			});


			$("#closepricingprev").click(function(){
				$(".pricingpreview").slideUp();
			});
			$("#pricing_bulk_preview").click(function(){				
				$(".pricingpreview").slideDown();
			})

			$('#update_pricingname').click(function(event){

				event.preventDefault();

				var PricingName = $('#PricingName').val();
				var CustomerUID = $('#CustomerUID').val();
				if(PricingName == ''){
					$('#PricingName').closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
					return false;
				}

				var button = $(this);
				var button_text = $(this).html();
				var PricingUID=$('#CustomerPricingUID').val();
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/update_pricingname',
					data: {'PricingName':PricingName,'PricingUID':PricingUID,'CustomerUID':CustomerUID}, 
					dataType:'json',
					cache: false,
					beforeSend: function(){
						button.attr("disabled", true);
						button.html('Please Wait ...'); 
					},
					success: function(data)
					{
						button.html(button_text);						
						if(data['validation_error'] == 0)
						{
							if(data['PricingUID']){
								$('#CustomerPricingUID').val(data['PricingUID']);
								$('#excel_exportlink').attr('href', base_url+'Customer/ExcelPricingExport/'+CustomerUID+'/'+data['PricingUID']);
								$('#pricingimport').removeClass("disabled");
								$('#excel_exportlink').removeClass("disabled");
								$('#copypricing').removeClass("disabled");
							}
							$('#pricingtable').DataTable().destroy();
							pricingtable_init();
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:3000 });
						} else { 
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
							$.each(data, function(k, v) {
								$('#'+k).closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
							});
						}
						button.removeAttr("disabled"); 
					} 
				});
				return false;
			});





			$('#BtnUpdateFees').on('click',function(event)
			{

				var CustomerUID = $('#CustomerUID').val();
				var CustomerPricingUID = $('#CustomerPricingUID').val();
				var button = $(this);
				var button_text = $(this).html();

				var data = new Array();
				$('#pricingtable > tbody  > tr').each(function(tablekey,tablevalue){  
					obj = new Object();
					obj['PricingProductUID'] = $(this).find('input[name=PricingProductUID]').val(); 
					obj['ProductUID'] = $(this).find('select[name=ProductUID]').val(); 
					obj['SubProductUID'] = $(this).find('select[name=SubProductUID]').val(); 
					obj['StateCode'] = $(this).find('select[name=StateCode]').val(); 
					obj['CountyUID'] = $(this).find('select[name=CountyUID]').val(); 
					obj['Pricing'] = $(this).find('input[name=Pricing]').val(); 
					obj['CancellationFee'] = $(this).find('input[name=CancellationFee]').val(); 
					data.push(obj);
				});

				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/UpdateFees',
					data: {"data":data,"CustomerUID":CustomerUID,"CustomerPricingUID":CustomerPricingUID}, 
					dataType:'json',
					cache: false,
					beforeSend: function(){
						button.attr("disabled", true);
						button.html('Please Wait ...'); 
					},
					success: function(data)
					{
						if(data.validation_error == 1)
						{
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
						}else{
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:3000 });
						}
						$('#pricingtable').DataTable().destroy();
						pricingtable_init();

					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					complete:function(data){
						button.html(button_text);
						button.removeAttr("disabled"); 
					}
				});
			});


			$('.addpricelist').on('change','.ProductUIDFee',function(event) {
				ProductUID  =  $(this).val();
				var ele = $(this);
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/get_subproduct',
					data: {'ProductUID':ProductUID},
					dataType:'json',
					beforeSend: function(){
					},
					success: function(data)
					{
						$(ele).parent().next().find('.SubProductUIDFee').empty();
						$(ele).parent().next().find('.SubProductUIDFee').append('<option value=""></option>');
						if(data['success'] == 1){
							$.each(data['SubProducts'], function(k, v) {
								$(ele).parent().next().find('.SubProductUIDFee').append('<option value="' + v['SubProductUID'] + '">' + v['SubProductName'] + '</option>');
							});
						}else{
							$(ele).parent().next().find('.SubProductUIDFee').parent().removeClass('is-filled');
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

			$('.addpricelist').on('change','.StateCodeFee',function(event) {
				StateUID  =  $(this).val();
				var ele = $(this);
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/getcounty',
					data: {'StateUID':StateUID},
					dataType:'json',
					beforeSend: function(){
					},
					success: function(data)
					{
						$(ele).parent().next().find('.CountyUIDFee').empty();
						$(ele).parent().next().find('.CountyUIDFee').append('<option value=""></option>');
						if(data['success'] == 1){
							$.each(data['Counties'], function(k, v) {
								$(ele).parent().next().find('.CountyUIDFee').append('<option value="' + v['CountyUID'] + '">' + v['CountyName'] + '</option>');
							});
						}else{
							$(ele).parent().next().find('.CountyUIDFee').parent().removeClass('is-dirty');
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


			$('.addpricelist').on('click','.deletepricingproduct',function(event) {
				PricingProductUID  =  $(this).attr('data-PricingProductUID');
				var ele = $(this);
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/deletepricingproduct',
					data: {'PricingProductUID':PricingProductUID},
					dataType:'json',
					beforeSend: function(){
					},
					success: function(data)
					{
						if(data['validation_error'] == 0){
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:3000 });
							$('#pricingtable').DataTable().destroy();
							pricingtable_init();
						}else{
							$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:3000 });
							$('#pricingtable').DataTable().destroy();
							pricingtable_init();
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


			$('.addnewpricing ').off('click','#pricing_bulk_preview').on('click','#pricing_bulk_preview',function(event) {

				event.preventDefault();
				button = $(this);
				button_val = $(this).val();
				button_text = $(this).html();
				var file_data = $('#CustomerPricingimport').prop('files')[0];
				var CustomerPricingUID = $('#CustomerPricingUID').val();

				var form_data = new FormData();
				form_data.append('file', file_data);
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/preview_excel_records/'+CustomerPricingUID,
					data: form_data,
					dataType:'JSON',
					processData: false,
					contentType: false,
					cache:false,
					beforeSend: function(){
						$('.loading').show() 
					},
					success: function(data)
					{
						if(data['Error'] == '0')
						{
							$('#append_preview').html(data.objdata);
							$('#append_preview').fadeIn();
							$('.pricingpreview').fadeIn();

						}else{
							$('#append_preview').html('');
							$('#append_preview').hide();
							$('.pricingpreview').hide();
							$.notify({icon:"icon-bell-check",message:data['Message']},{type:"danger",delay:3000 });

						}
						$('.loading').hide();
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
				});
			});

			$('#bulk_save').click(function(event) {
				event.preventDefault();
				button = $(this);
				button_val = $(this).val();
				button_text = $(this).html();
				var CustomerPricingUID = $('#CustomerPricingUID').val();
				var file_data = $('#CustomerPricingimport').prop('files')[0];
				var form_data = new FormData();
				form_data.append('file', file_data);
				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/save_excel_records/'+CustomerPricingUID,
					data: form_data,
					dataType:'json',
					processData: false,
					contentType: false,
					cache:false,
					beforeSend: function(){
					},
					success: function(data)
					{
						if(data.error == '0')
						{
							$.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:3000 });
						}
						else if(data.error == '1')
						{
							$('#feeupload').modal('show');
							$('.pricingimportfailed').html(data.data);
							$.notify({icon:"icon-bell-check",message:'Pricing Not Imported'},{type:"danger",delay:3000 });
						}
						else if(data.error == '2')
						{
							$.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:3000 });
						}
						$('#pricingtable').DataTable().destroy();
						pricingtable_init();
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
				});
			});

			/*PRICING TAB END*/

		</script>