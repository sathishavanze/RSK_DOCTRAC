


<style>
.progress {
	height: 6px;
	margin-bottom: 5px !important;
}
.viewproduct   #accordion p{
	font-size: 13px !important;
	margin-bottom: 0rem  !important;
}
.viewproduct  .card-collapse .card-header {
	border-bottom: none !important;
	padding: 7px 10px 5px 0 !important;
}
.viewproduct  .addproduct{
	border:1px solid #ddd;
	padding:20px;
}
.viewproduct  .icon-pencil{
	cursor: pointer;
}
.viewproduct  .icon-close2{
	cursor: pointer;
}
.viewproduct .buttons-excel{
	background: #3b5998 !important;
	color: #fff !important;
}
.viewproduct  .buttons-pdf{
	background: #6f6f6f !important;
	color: #fff !important;
}
.viewproduct  .pagination>.page-item>.page-link, .pagination>.page-item>span {
	border-radius: 5px!important;
}

.viewproduct  .navdiv{
	padding: 5px 20px;
	border: 1px solid #e6e6e6;
	background: #fff;
}
.viewproduct  .breadcrumb {
	display: flex;
	flex-wrap: wrap;
	padding: 5px 0px;   
	background-color: #fff;
	margin-bottom: 0px;
}
.viewproduct  .breadcrumb>li>a {
	color: #333;
	font-size: 14px;
	font-weight: 400;
}
.viewproduct  .breadcrumb-item.active {
	color: #6c757d;
	font-size: 13px;
	font-weight: 400;
	padding-top: 2px;
}
.viewproduct .card{
	border-radius: 0px !important;
}


.showfilename{
	font-weight: 400;
	font-size:13px;
	margin-bottom: 0px !important;
	text-align: right;
}
.headerdiv{
	border-bottom: 1px solid #ddd;
	background: #f3f3f3;
	padding: 10px;
}
.instructiondivider{
	border-bottom: 1px solid #ddd;
	padding: 15px;
	margin-bottom: 20px;
}
.fileinput-button-upload{
	background: #fff !important;
	border: 2px dashed #ddd;
	color: black !important;
	width: 100%;
	height: 40px;    
} 
.fileinput-button i{
	display: block;
	font-size: 45px;
	color: #3d5b99;
}
.fileinput-button-upload input{
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	opacity: 0;
	-ms-filter: 'alpha(opacity=0)';
	font-size: 20px;
	direction: ltr;
	height: 20px;
}
.fileinput-button input{
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	opacity: 0;
	-ms-filter: 'alpha(opacity=0)';
	font-size: 200px;
	direction: ltr;
	height: 200px;
}
.fileinput-button{
	background: #fff !important;
	border: 2px dashed #ddd;
	color: black !important;
	width: 100%;
	height: 230px; 
	line-height: 80px;
}
.abstractordiv h5 , .excludeabstractordiv h5{
	border-bottom: 1px solid #ddd;
	padding: 10px;
	font-size: 14px;
	margin: 0px;
	font-weight: 400; 
	color: #3b5998;
	font-weight: 500; 
}
.headericon{
	padding: 4px;
	border: 2px solid #8e8c8c;
	border-radius: 50%;
	margin-right: 10px;
	color: #8e8c8c;
	font-size: 10px;
}
.selectproductdiv{  
	border-right: 1px solid #ddd;
}
.selectedproductheader{
	border-bottom: 1px solid #ddd;
	padding: 10px;
	margin-bottom: 20px;
}	
.addmastersetups{
	cursor: pointer;
	color: #11b8cc;
}
.viewmastersetups{
	cursor: pointer;
	color: #e66b24;
}
.tab{
	display: none; 
	width: 100%;
	height: 50%;
	margin: 0px auto;
}
.current{
	display: block;
}
.step {
	height: 30px;
	width: 30px; 
	cursor: pointer;
	margin: 0 2px;
	color: #fff;
	background-color: #bbbbbb;
	border: none; 
	border-radius: 50%; 
	display: block; 
	opacity: 0.8;
	padding: 5px; 
	margin-top: 15px;
}
.step.active {
	opacity: 1;
	background-color: #69c769;
}
.step.finish {
	background-color: #4CAF50; 
}
.error {
	color: #f00;
}
#myForm{
	width:100%;
}
.card #pricingtable tr td{
	width: 160px;
}
.iconverify{
	padding: 10px;
	color: #00717f;
	border-radius: 50%;
	font-size: 26px;
}
.pswverify{
	font-size: 20px;
	font-weight: 500;
}
.is-invalid {
	background-size: 100% 100%, 100% 100%;
	transition-duration: .3s;
	box-shadow: none;
	background-image: linear-gradient(to top, #f44336 2px, rgba(244, 67, 54, 0) 2px), linear-gradient(to top, #d2d2d2 1px, rgba(210, 210, 210, 0) 1px);
}
.hide{
	display: none;
}
.modal-body {
    overflow-y: auto;
}
 .close:not(:disabled):not(.disabled) {
    cursor: pointer;
    padding-top: 10px;
    padding-bottom: 10px;
}

</style>




				<div class="col-md-12 pd-0"> 
					<div class="setupdiv col-md-12" style="display:none;"> 
						<div class="col-md-12 pd-0">
							<div class="card">
								<div class="card-header mastersetupheader">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-8">
												<p>Product</p>
											</div>
											<div class="col-md-4 text-right">
												<span class="icon-close2 closesetup"></span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body pd-0">
									<div class="setupcontent col-md-12 pd-0">
										<div class="addproduct col-md-12 mt-20" style="display:none;">
											<form>
												<div class="row">
													<div class="col-md-6">      
														<div class="form-group">
															<label for="productcode" class="bmd-label-floating">Product Code </label>
															<input type="text" class="form-control" id="productcode" name="productcode" />
														</div>                     
													</div>
													<div  class="col-md-6">     
														<div class="form-group">
															<label for="productname" class="bmd-label-floating">Product Name</label>
															<input type="text" class="form-control" id="productname" name="productname" />
														</div>  
													</div>
												</div>
												<div class="col-md-12">
													<div  class="row">      
														<div class="col-md-12 checkbox-radios mt-20 pd-0">
															<span style="padding-right: 10px;color: #AAA;">Multiple Pricing   : </span>  
															<div class="form-check form-check-inline">
																<label class="form-check-label">
																	<input class="form-check-input" type="checkbox" value="" name="pricingname"> Insurance Type
																	<span class="form-check-sign">
																		<span class="check"></span>
																	</span>
																</label>
															</div>
															<div class="form-check form-check-inline">
																<label class="form-check-label">
																	<input class="form-check-input" type="checkbox" value=""  name="pricingname"> Agent Pricing
																	<span class="form-check-sign">
																		<span class="check"></span>
																	</span>
																</label>
															</div>
															<div class="form-check form-check-inline">
																<label class="form-check-label">
																	<input class="form-check-input" type="checkbox" value=""  name="pricingname"> Under Writing Pricing
																	<span class="form-check-sign">
																		<span class="check"></span>
																	</span>
																</label>
															</div>
														</div>
													</div>
												</div>        
												<div class="col-md-12 text-right mt-10">
													<button type="button" class="btn btn-fill btn-success btnaction" id="savebtn"><i class="icon-floppy-disk"></i>Save</button>
													<button type="button" class="btn btn-fill btn-default btnaction"  id="cancelbtn"><i class="icon-cancel-square"></i>Cancel</button>
												</div>
											</form>
										</div> 

										<div class="col-md-12 viewproduct mt-20" style="display: none;">
											<div class="table-responsive">
												<table class="table" id="datatable">
													<thead>
														<tr>
															<th>S.No</th>
															<th>Products Code</th>
															<th>Products Name</th>
															<th>Status</th>
															<th>Action</th>
														</tr>
													</thead>  
													<tbody>
														<tr>
															<td>1</td>
															<td>P</td>
															<td>Property Report</td>
															<td>  
																<div class="togglebutton">
																	<label>
																		<input type="checkbox" checked="">
																		<span class="toggle"></span>               
																	</label>
																</div>
															</td>
															<td><i class="icon-pencil"></i><i class="icon-close2" style="padding-left: 10px;"></i></td>
														</tr>
														<tr>
															<td>2</td>
															<td>D</td>
															<td>Deed Report</td>
															<td>
																<div class="togglebutton">
																	<label>
																		<input type="checkbox" checked="">
																		<span class="toggle"></span>               
																	</label>
																</div>
															</td>
															<td><i class="icon-pencil"></i><i class="icon-close2" style="padding-left: 10px;"></i></td>
														</tr>
														<tr>
															<td>3</td>
															<td>T</td>
															<td>Title Commitment</td>
															<td>
																<div class="togglebutton">
																	<label>
																		<input type="checkbox" checked="">
																		<span class="toggle"></span>               
																	</label>
																</div>
															</td>
															<td><i class="icon-pencil"></i><i class="icon-close2" style="padding-left: 10px;"></i></td>
														</tr>
														<tr>
															<td>4</td>
															<td>F</td>
															<td>Flood Cert</td>
															<td>
																<div class="togglebutton">
																	<label>
																		<input type="checkbox" checked="">
																		<span class="toggle"></span>               
																	</label>
																</div>
															</td>
															<td><i class="icon-pencil"></i><i class="icon-close2" style="padding-left: 10px;"></i></td>
														</tr>
														<tr>
															<td>5</td>
															<td>Z</td>
															<td>Testing</td>
															<td>
																<div class="togglebutton">
																	<label>
																		<input type="checkbox" checked="">
																		<span class="toggle"></span>               
																	</label>
																</div>
															</td>
															<td><i class="icon-pencil"></i><i class="icon-close2" style="padding-left: 10px;"></i></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>



									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="wizard-container col-md-12">
					<div class="card card-wizard" data-color="rose" id="wizardProfile">
						<div class="card-header text-center">    
							<div class="row">
								<div class="col-md-8">  
									<h4 class="card-title">
										<?php echo $Customers->CustomerName; ?> - <span style="font-size:15px;font-weight:600;"><?php echo $Customers->CustomerNumber; ?></span>
									</h4>
									<p class="mb-0">
										<i class="icon-location4" style="padding-right: 2px"></i>
										<span id="customeraddress"><?php echo $Customers->CustomerAddress1.','.$Customers->CustomerAddress1; ?></span>,&nbsp;&nbsp; 
										<i class="icon-phone-wave" style="padding-right: 2px"></i>
										<span id="customernumber"><?php echo $Customers->CustomerPContactMobileNo; ?></span>,&nbsp;&nbsp;
										<i class="icon-envelop4" style="padding-right: 2px"></i>
										<span id="customeremailid"><?php echo $Customers->CustomerPContactEmailID; ?></span>
									</p>
									<h5 class="card-description"></h5>
								</div>
								<div class="col-md-4">							
									<div class="row checklist">
										<div class="col-md-8" style="background: #ffffff;padding: 0px 10px;">		
										<div class="row">
											<div class="col-md-8 text-left">
												<p class="mb-0">5656</p>												
											</div>
											<div class="col-md-4 text-right">
												<p class="mb-0 completedpercent"> 67%</p>	
											</div>
										</div>
										<div class="col-md-12 pd-0 mt-10">
											<div class="progress progress-line-default ">
												<div class="progress-bar progress-bar-default" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 30%;">
													<span class="sr-only">60% Complete</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-4 taskdiv text-center">
										<div class="mt-10">
											<a href="JavaScript:Void(0);"  style="color:#fff;" data-toggle="modal" data-target="#myModal">Details <i class="icon-circle-right2"></i></a>				
										</div>
									</div>										
								</div>
							</div>
						</div>
					</div>
					<div class="wizard-navigation">
						<ul class="nav nav-pills nav_theme_color">
							<li class="nav-item">
								<a class="nav-link active" href="#info" data-toggle="tab" role="tab">
									Info
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#products" data-toggle="tab" role="tab">
									Products
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#pricing" data-toggle="tab" role="tab">
									Pricing
								</a>
							</li> 
							<li class="nav-item">
								<a class="nav-link" href="#template" data-toggle="tab" role="tab">
									Workflows
								</a>
							</li>  
							<li class="nav-item">
								<a class="nav-link" href="#priority" data-toggle="tab" role="tab">
									TAT
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#abstractor" data-toggle="tab" role="tab">
									Abstractor
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#uploaded" data-toggle="tab" role="tab">
									Uploads
								</a>
							</li>
						</ul>
					</div>

						<div class="card-body">
							<div class="tab-content">
								<div class="tab-pane active" id="info">
									<!--   <h5 class="info-text"> Let's start with the basic information (with validation)</h5> -->
				
								</div>


								<div class="tab-pane" id="products">
					
		</div>
		<div class="tab-pane" id="pricing">

			</div>
									<!-- 	<div class="tab-pane" id="template">
											<form> 
												<div class="col-md-12 borderright dynamicdiv"> 
													<div class="row" style="background:#f7f7f7"> 
														<div class="col-md-2 dyndivhead">       
															<strong>Product / Subproduct</strong>

															<div class="col-md-12 text-right">
																<button class="btn btn-linkedin" id="updateprice">Update</button>
															</div>
														</div>
													</div>
												</div>
											</form>
										</div> -->
										<div class="tab-pane" id="template">
						
										</div>


										<div class="tab-pane" id="priority">
					
										</div>

										<div class="tab-pane" id="abstractor">
					
										</div>

										<div class="tab-pane" id="uploaded">												
					
										</div>
									</div>

				<!-- <div class="card-footer">       
					<div class="ml-auto">
						<button class="btn btn-next btn-fill btn-dribbble btn-wd" name="next"><i class="icon-floppy-disk pr-10"></i>Save</button>
						<button class="btn btn-finish btn-fill btn-success btn-wd" name="finish" style="display: none;"><i class="icon-thumbs-up2 
							pr-10" ></i>Complete</button>      
						</div>
						<div class="clearfix"></div>
					</div> -->
				</div>
			</div>




<div class="modal fade custommodal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">1ST STREET CREDIT UNION  </h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					<i class="icon-x"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="row"> 
					<div class="col-md-1">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" value=""  checked="checked" disabled>
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div>                     
					<div class="col-md-3">
						<p>Info</p>
					</div>
					<div class="col-md-4">
						<div class="progress progress-line-info mt-10">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
								<span class="sr-only">100% Complete</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<p>100%</p>
					</div>
				</div>
				<div class="row">  
					<div class="col-md-1">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" value=""  checked="checked" disabled>
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div>                           
					<div class="col-md-3">
						<p>Products</p>
					</div>
					<div class="col-md-4">
						<div class="progress progress-line-info mt-10">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
								<span class="sr-only">100% Complete</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<p>100 %</p>
					</div>
				</div>
				<div class="row"> 
					<div class="col-md-1">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" value=""  disabled>
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div>  
					<div class="col-md-3">
						<p>Pricing</p>
					</div>
					<div class="col-md-4">
						<div class="progress progress-line-info mt-10">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
								<span class="sr-only">0% Complete</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<p>0 %</p>
					</div>
				</div>
				<div class="row">   
					<div class="col-md-1">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" value=""   disabled>
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div> 
					<div class="col-md-3">
						<p>Workflow</p>
					</div>
					<div class="col-md-4">
						<div class="progress progress-line-info mt-10">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
								<span class="sr-only">0% Complete</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<p>0 %</p>
					</div>
				</div>

				<div class="row">   
					<div class="col-md-1">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" value=""   disabled>
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div> 
					<div class="col-md-3">
						<p>TAT</p>
					</div>
					<div class="col-md-4">
						<div class="progress progress-line-info mt-10">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
								<span class="sr-only">0% Complete</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<p>0 %</p>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>



<script src="<?php echo base_url(); ?>assets/js/multi-form.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jquery.bootstrap-wizard.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/sweetalert2.js"></script>
<script src="<?php echo base_url(); ?>assets/js/customer.js" type="text/javascript"></script>

<script type="text/javascript"> 
	//Global Variables.
	var ordertypes_options='<?php foreach ($OrderTypes as $key=> $type){echo '<option value="'.$type->OrderTypeUID.'">'.$type->OrderTypeName.'</option>';}?>';
	var SubProduct_options='<?php foreach ($SubProducts as $key => $subproduct) {?><option value="<?php echo $subproduct->SubProductUID; ?>"><?php echo $subproduct->SubProductName;?></option><?php } ?>';
	var apitype_options='<?php foreach ($SourceAPI as $key => $value) {?><option value="<?php echo $value->OrderSourceUID; ?>"><?php echo $value->OrderSourceName;?></option><?php } ?>';
	var Product_options='<?php foreach ($Products as $key => $prod) { ?><option value="<?php echo $prod->ProductUID; ?>"><?php echo $prod->ProductName;?></option><?php } ?>';
	function pricingtable_init(){
		var pricingtable = $('#pricingtable').DataTable({
			scrollCollapse: true,
			paging:  true,

			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			"language": {
				processing: '<span class="progrss"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Processing...</span>'
			},
			// Load data for the table's content from an Ajax source
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

			//Set column definition initialisation properties.
			"columnDefs": [
			{
				"targets": [6], //first, Fourth, seventh column
				 "orderable": false //set not orderable
				}
				],

			});
	}
	$(document).ready(function(){

		pricingtable_init();


		$(".selectpicker").selectpicker();
		$("#viewproductmodules").click(function(e){
			e.preventDefault();
			$(".setupdiv").slideDown();  
			$(".viewproduct").show();  
			$(".addproduct").hide(); 
		});



		

		var i = 0;
		$("body").on("click" , ".addpricing" , function(){      
			i = i + 1;    
			appenddiv = "<tr>"
			appenddiv  = appenddiv + '<td><input type="hidden" name="PricingProductUID" value=""><select class="select2picker form-control ProductUIDFee" data-size="7" name="ProductUID" ><option value="" selected="selected"> Select Product</option><?php foreach ($Products as $Product) {  echo '<option value="' . $Product->ProductUID .'">' . $Product->ProductName .'</option>';  } ?></select> </td><td><select class="select2picker form-control SubProductUIDFee"  name="SubProductUID" ><option value="" selected="selected"> Select SubProduct</option></select></td><td><select class="select2picker form-control StateCodeFee" name="StateCode" ><option value="" selected="selected">Select State</option><?php foreach ($States as $state) {  echo '<option value="' . $state->StateUID .'">' . $state->StateName .'</option>';  } ?></select></td><td><select class="select2picker form-control CountyUIDFee" name="CountyUID"><option value="" selected="selected">Select County</option></select></td><td><div class="form-group"><label for="Pricing" class="bmd-label-floating">Pricing</label><input type="text" class="form-control" name="Pricing" ></div></td><td><div class="form-group"><label for="CancellationFee" class="bmd-label-floating">Cancellation Fee</label><input type="text" class="form-control" name="CancellationFee"></div></td><td><a href="JavaScript:Void(0);" class="btn btn-link btn-danger btn-just-icon btn-xs removepricing"><i class="icon-x"></i></a></td>';     
			appenddiv = appenddiv + "</tr>";
			$(".addpricelist").append(appenddiv);
			$(".updatediv").slideDown();
			callselect2();
		});


		$("body").on("click" , ".removepricing" , function(){
			$(this).closest("tr").remove();
		});


		$('#datatable').DataTable({  

			"pagingType": "full_numbers",        
			"lengthMenu": [
			[10, 25, 50, -1],
			[10, 25, 50, "All"]
			],  
			buttons: [          
			{
				extend: 'excelHtml5',
				exportOptions: { orthogonal: 'export' }
			},
			{
				extend: 'pdfHtml5',
				exportOptions: { orthogonal: 'export' }
			}
			], 
			language: {    

				paginate: {
					next: '<i class="icon-arrow-right13"></i>',
					previous: '<i class="icon-arrow-left12"></i>' 
				}
			}
		});
	});

	function myFunction(){
		$(".setupdiv").slideDown();
		$(".addproduct").show();
		$(".viewproduct").hide();  
	}

	function Check() {
		var isChecked = $("#ParentCompany").is(":checked");
		if (isChecked) {
			$("#ShowParentCompany").css("visibility" , "hidden");
		} else {
			$("#ShowParentCompany").css("visibility" , "visible");
		}
	}

	$(document).ready(function(){

		$("#ShowParentCompany").css("visibility" , "hidden");

		var isChecked = $("#ParentCompany").is(":checked");
		if (isChecked) {
			$("#ShowParentCompany").css("visibility" , "hidden");
		} else {
			$("#ShowParentCompany").css("visibility" , "visible");
		}



		/*-----Update Customer Info details ----*/	
		$('#frm_customer').submit(function(event) {
			event.preventDefault();

			var formData = $('#frm_customer').serialize();
			var button = $('#UpdateCustomer');
			var button_text = $('#UpdateCustomer').html();
			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>Customer/UpdateCustomerDetails',
				data: formData, 
				dataType:'json',
				beforeSend: function(){
					button.attr("disabled", true);
					button.html('Loading ...'); 
				},
				success: function(data)
				{

					if(data.validation_error == 0){
						$.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:1000 });
					}else{

						$.each(data, function(k, v) 
						{
							$('#'+k).closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#'+k).addClass("is-invalid");;
						});
					}

					button.html(button_text);
					button.removeAttr("disabled");
				}
			});

		});
		/*-----Update Customer Info details ----*/


		$('#CustomerZipCode').change(function(event) {
			CustomerZipCode = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>Customer/getzip',
				data: {'CustomerZipCode':CustomerZipCode}, 
				dataType:'json',
				cache: false,
				success: function(data)
				{
					$('#CustomerCityUID').empty();
					$('#CustomerStateUID').empty();
					$('#CustomerCountyUID').empty();

					if(data != ''){

						$('#CustomerCityUID').append('<option value="' + data['CityUID'] + '" selected="">' + data['CityName'] + '</option>').trigger('change');

						$('#CustomerStateUID').append('<option value="' + data['StateUID'] + '" selected="">' + data['StateName'] + '</option>').trigger('change');
						$('#CustomerCountyUID').append('<option value="' + data['CountyUID'] + '" selected="">' + data['CountyName'] + '</option>').trigger('change');
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


		/*PRICING TAB START*/

		$("#copypricing").click(function(e){

			swal({
				title: "<i class='icon-unlocked2 iconverify'></i><br><h5 class='pswverify'>Verify Password !</h5>",     
				html: '<div class="form-group">' +
				'<input name="password" id="password" type="password" class="form-control" placeholder="Enter Password" required/>' +
				'</div>',
				showCancelButton: true,
				confirmButtonClass: 'btn btn-success',
				cancelButtonClass: 'btn btn-danger',
				buttonsStyling: false,
				showLoaderOnConfirm: true,
				preConfirm: function() {
					return new Promise(function(resolve, reject) {
						var Password = $('#password').val();
						var CopyPricingUID  =  $('#selectdefaultpricing').val();
						var PricingUID  =  $('#CustomerPricingUID').val();

						if(CopyPricingUID == '' && PricingUID == ''){
							reject('Pricing not selected');
						}

						if(Password == ''){
							$('#password').closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
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
			});


		});
		/*PRICING TAB END*/


		/*-----Document Uploads Tab STARTS----*/
		$('#customerdocument').change(function (e) {
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

		$(document).on('change', '.ordertypes_select', function (e) {
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

		$(document).on('change', '.abstractorordertypedoc',function (e) {
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

		$(document).on('submit', '.frmabstractordoc', function (e) {
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


		$(document).on('click', '.removeabstractordocserver', function (e) {
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

		$(document).on('click', '.customerdocumentremove_server', function (e) {
			e.preventDefault();

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

		})

		/*-----Document Uploads Tab ENDS----*/

		/*-----Private Abstractor and Exclude Abstractor JS Starts*/


      $('.Tbody_ExcludeAbstractorTable').find('tr').each(function () {
          if (!$(this).find(':checkbox').is(':checked'))
              $('.Tbody_ExcludeAbstractorTable').append(this);
      });

      $('.Tbody_PrivateAbstractorTable').find('tr').each(function () {
          if (!$(this).find(':checkbox').is(':checked'))
              $('.Tbody_PrivateAbstractorTable').append(this);
      });

      $('#privateabstractor').on('change', '[type=checkbox]', function () {
        var $this = $(this);
        var row = $this.closest('tr');

        if ( $this.prop('checked') ){ 
          row.insertBefore( row.parent().find('tr:first-child') )
        }
        else { 
          row.insertAfter( row.parent().find('tr:last-child') )
        }
      });

      $('#excludeabstractor').on('change', '[type=checkbox]', function () {
        var $this = $(this);
        var row = $this.closest('tr');

        if ( $this.prop('checked') ){ 
          row.insertBefore( row.parent().find('tr:first-child') )
        }
        else { 
          row.insertAfter( row.parent().find('tr:last-child') )
        }
      });


      $(document).on('change', '.excludecheck', function (e) {
      	var formdata={};
      	formdata.CustomerUID=$('#CustomerUID').val();
      	formdata.AbstractorUID=$(this).closest('tr').attr('data-id');
      	if ($(this).prop('checked')==false) {
      		formdata.Action='delete';

      	}
      	else{
      		formdata.Action='insert';
      	}

      	$.ajax({
      		type: "POST",
      		url: '<?php echo base_url();?>Customer/ExcludeAbstractor',
      		data: formdata, 
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

      })

      $(document).on('change', '.privatecheck', function (e) {
      	var formdata={};
      	formdata.CustomerUID=$('#CustomerUID').val();
      	formdata.AbstractorUID=$(this).closest('tr').attr('data-id');
      	if ($(this).prop('checked')==false) {
      		formdata.Action='delete';

      	}
      	else{
      		formdata.Action='insert';
      	}

      	$.ajax({
      		type: "POST",
      		url: '<?php echo base_url();?>Customer/PrivateAbstractor',
      		data: formdata, 
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

      })
		/*-----Private Abstractor and Exclude Abstractor JS Ends*/
		

	});
function callselect2(){
	$("select.select2picker").select2({
		//tags: false,
		theme: "bootstrap",
	});
}


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



</script>

