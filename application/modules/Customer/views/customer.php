
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
	.card-wizard .tab-content {
		/* min-height: 340px; */
		padding: 10px 0px;
	}
	.card-header p{
		margin: 0px !important;
	}
	.card-description{
		margin: 0px !important;
	}

</style>



<div class="col-md-12 pd-0" id="customerpaneldiv"> 

	<!-- product setup -->

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
				<div class="card-body">
					<div class="setupcontent col-md-12 pd-0">
						<div class="addproduct col-md-12 mt-20" style="display:none;">
							<form id="Productform" name="Productform">
								<input type="hidden" id="Productform_ProductUID" name="Productform_ProductUID" value="">
								<input type="hidden" id="Productform_PreviousProductCode" name="Productform_PreviousProductCode" value="">
								<input type="hidden" id="Productform_PreviousProductName" name="Productform_PreviousProductName" value="">
								<div class="row">
									<div class="col-md-6">      
										<div class="form-group">
											<label for="productcode" class="bmd-label-floating">Product Code </label>
											<input type="text" class="form-control" id="Productform_ProductCode" name="Productform_ProductCode" />
										</div>                     
									</div>
									<div  class="col-md-6">     
										<div class="form-group">
											<label for="productname" class="bmd-label-floating">Product Name</label>
											<input type="text" class="form-control" id="Productform_ProductName" name="Productform_ProductName" />
										</div>  
									</div>
								</div>
								<div class="col-md-12">
									<div  class="row">      
										<div class="col-md-12 checkbox-radios mt-20 pd-0">
											<span style="padding-right: 10px;color: #AAA;">Multiple Pricing   : </span>  
											<div class="form-check form-check-inline">
												<label class="form-check-label">
													<input class="form-check-input" type="checkbox" value="" name="Productform_Active" id="Productform_Active"> Active
													<span class="form-check-sign">
														<span class="check"></span>
													</span>
												</label>
											</div>
										</div>
									</div>
								</div>        
								<div class="col-md-12 col-xs-12 pd-0 text-right">
									<button class="btn btn-fill btn-success btnaction" id="SaveProduct"><i class="icon-floppy-disk"></i>Save</button>
									<button type="button" class="btn btn-fill btn-default closeaddproduct"  id="cancelbtn"><i class="icon-cancel-square"></i>Cancel</button>
								</div>
							</form>
						</div> 

						<div class="col-md-12 viewproduct mt-20" style="display: none;">
							<div class="col-md-12 text-right">
								<button class="btn btn-outline-success btn-sm" id="addproduct">Add Product</button>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered" id="product_datatable">
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
										<?php $sno = 1; foreach ($AllProducts as $key => $Product) { ?>
											<tr>
												<td><?php echo $sno; ?></td>
												<td><?php echo $Product->ProductCode; ?></td>
												<td><?php echo $Product->ProductName; ?></td>
												<td>  
													<div class="togglebutton">
														<label>
															<input data-type="<?php echo $Product->ProductUID;?>"  name="product_status" class="product_status" type="checkbox" <?php echo ($Product->Active == 1) ? 'checked value="1" ' : 'value="0"'; ?>>
															<span class="toggle"></span>               
														</label>
													</div>
												</td>
												<td>
													<a href="javascript:void(0);" data-type="<?php echo $Product->ProductUID;?>" class="btn btn-link btn-info btn-just-icon btn-xs editproduct"><i class="icon-pencil"></i>
														<div class="ripple-container"></div>
													</a>
												</tr>
												<?php $sno++; } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!--Sub product setup -->

			<div class="subproduct col-md-12"  style="display:none">
				<div class="card">
					<div class="card-header mastersetupheader">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-8">
									<p>Sub Product</p>
								</div>
								<div class="col-md-4 text-right">
									<span class="icon-close2 closesubproduct"></span>
								</div>
							</div>
						</div>
					</div>

					<div class="card-body">
						<div class="addsubproduct"  style="display:none">
							<form id="SubProductform" name="SubProductform">
								<input type="hidden" id="SubProductform_PreviousSubProductName" name="SubProductform_PreviousSubProductName" value="">
								<input type="hidden" id="SubProductform_SubProductUID" name="SubProductform_SubProductUID" value="">
								<div class="row">
									<div class="col-md-4">      
										<div class="form-group">
											<label for="SubProductCode" class="bmd-label-floating">Sub Product Code</label>
											<input type="text" class="form-control" id="SubProductform_SubProductCode" name="SubProductform_SubProductCode"/>
										</div>                     
									</div>
									<div  class="col-md-4">     
										<div class="form-group">
											<label for="SubProductName" class="bmd-label-floating">Sub Product Name <span style="color: red"> *</span></label>
											<input type="text" class="form-control" id="SubProductform_SubProductName" name="SubProductform_SubProductName"/>
										</div>  
									</div>
									<div  class="col-md-4">     
										<div class="form-group bmd-form-group">
											<label for="ProductUID" class="bmd-label-floating">Product <span style="color: red"> *</span></label>
											<select class="form-control select2picker"  id="SubProductform_ProductUID"  name="SubProductform_ProductUID">
												<option></option>
												<?php foreach ($ProductsDet as $row) { ?>
													<option value="<?php echo $row->ProductUID; ?>"><?php echo $row->ProductName; ?></option>
												<?php } ?>  
											</select>
										</div>  
									</div>
								</div>
						<!-- <div class="row mt-20">
							<div class="col-md-4">      
								<div class="form-group bmd-form-group">
									<label for="OrderTypeUID" class="bmd-label-floating">Default Assignment Type</label>
									<select class="form-control select2picker"  id="SubProductform_OrderTypeUID"  name="SubProductform_OrderTypeUID">
										<option></option>
										<?php foreach ($OrderTypeDetails as $row) { ?>
											<option value="<?php echo $row->OrderTypeUID;?>"><?php echo $row->OrderTypeName;?></option>
										<?php } ?>  
									</select>
								</div>                     
							</div>
							<div class="col-md-4">      
								<div class="form-group bmd-form-group">
									<label for="PriorityUID" class="bmd-label-floating">Default Priority </label>
									<select class="form-control select2picker"  id="SubProductform_PriorityUID"  name="SubProductform_PriorityUID">
										<option></option>
										<?php foreach ($Prioritys as $row) { ?>
											<option value="<?php echo $row->PriorityUID;?>"><?php echo $row->PriorityName;?></option>
										<?php } ?>  
									</select>
								</div>                     
							</div>
							<div class="col-md-4">      
								<div class="form-group">
									<label for="ReportHeading" class="bmd-label-floating">Report Heading</label>
									<input type="text" class="form-control" id="SubProductform_ReportHeading" name="SubProductform_ReportHeading" />
								</div>                     
							</div>
						</div> -->
						<div class="col-md-12 col-xs-12 pd-0 text-right">
							<div class="row mt-10">
								<div class="col-md-1 text-left">
									<p>Active</p>
								</div>
								<div class="col-md-3 text-left">
									<div class="togglebutton">
										<label>
											<input type="checkbox" checked="" name="SubProductform_Active" id="SubProductform_Active">
											<span class="toggle"></span>On
										</label>
									</div>
								</div>
								<div class="col-md-8 text-right">
									<button class="btn btn-success" id="savesubproduct">Save</button>
									<button class="btn btn-default" id="cancelsubproduct">Cancel</button>
								</div>
							</div>
						</div>								
					</form>
				</div>
				<div class="subproductlist" style="display:none">
					<div class="col-md-12 text-right">
						<button class="btn btn-outline-success btn-sm" id="addsubproduct">Add Sub Product</button>
					</div>
					<div class="col-md-12 pd-0 mt-10">
						<div class="material-datatables">
							<table class="table table-bordered" id="subproduct_datatable">
								<thead>
									<tr>
										<th>S.No</th>
										<th>Sub Products Code</th>
										<th>Sub Products Name</th>
										<th>Products Name</th>
										<!-- <th>Report Heading</th> -->
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>  
								<tbody>
									<?php $sno = 1; foreach ($AllSubProducts as $Subkey => $SubProduct) { ?>
										<tr>
											<td><?php echo $sno; ?></td>
											<td><?php echo $SubProduct->SubProductCode; ?></td>
											<td><?php echo $SubProduct->SubProductName; ?></td>
											<td><?php echo $SubProduct->ProductName; ?></td>
											<!-- <td><?php echo $SubProduct->ReportHeading; ?></td> -->
											<td>  
												<div class="togglebutton">
													<label>
														<input data-type="<?php echo $SubProduct->SubProductUID;?>"  name="SubProduct_status" class="SubProduct_status" type="checkbox" <?php echo ($SubProduct->Active == 1) ? 'checked value="1" ' : 'value="0"'; ?>>
														<span class="toggle"></span>               
													</label>
												</div>
											</td>
											<td>
												<a href="javascript:void(0);" data-type="<?php echo $SubProduct->SubProductUID;?>" class="btn btn-link btn-info btn-just-icon btn-xs editsubproduct"><i class="icon-pencil"></i>
													<div class="ripple-container"></div>
												</a>
											</tr>
											<?php $sno++; } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!--customer onboarding -->




			<div class="wizard-container col-md-12">
				<div class="card card-wizard" data-color="rose" id="wizardProfile">

					<div class="card-header text-center">    
						<div class="row">
							<div class="col-md-12">
								<input type="hidden" name="CustomerUID" id="CustomerUID" value="<?php echo $CustomerUID; ?>">  
								<h4 class="card-title" style="font-weight:500;">
									<?php echo $GetCustomer->CustomerName; ?> <span style="font-size:15px;font-weight:600;"></span>
								</h4>
								<p class="mt-10" style="margin:2px 0 10px 0px!important;">
									<i class="icon-location4" style="padding-right: 2px"></i>
									<span style="margin-right:20px;" id="customeraddress"><?php echo $GetCustomer->AddressLine1; ?></span>
									<i class="icon-phone-wave" style="padding-right: 2px"></i>
									<span style="margin-right:20px;" id="customernumber"><?php echo $GetCustomer->OfficeNo; ?></span>
									<i class="icon-envelop4" style="padding-right: 2px"></i>
									<span id="customeremailid"><?php echo $GetCustomer->CustomerEmail; ?></span>
								</p>
								<h5 class="card-description"></h5>
							</div>
						</div>
					</div>

					<div class="wizard-navigation">
						<ul class="nav nav-pills">
							<li class="nav-item">
								<a class="nav-link active" id="customerinfo" href="#info" data-toggle="tab" role="tab">
									Info
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  id="customerproduct" href="#products" data-toggle="tab" role="tab">
									Workflow
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link "  id="customerworkflowdependent" href="#workflowdependent" data-toggle="tab" role="tab">
									workflow metrics
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link "  id="customermilestonematrics" href="#milestonematrics" data-toggle="tab" role="tab">
									Milestone Metrics
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link "  id="customerworkflowmetric" href="#workflowmetric" data-toggle="tab" role="tab">
									Loan Prioritization Metrics
								</a>
							</li>
					<!-- <li class="nav-item">
						<a class="nav-link" id="customerpricing"  href="#pricing" data-toggle="tab" role="tab">
							Pricing
						</a>
					</li>  -->
					<!-- <li class="nav-item">
						<a class="nav-link" id="customerworkflow"  href="#workflow" data-toggle="tab" role="tab">
							Workflows
						</a>
					</li>   -->
				<!-- 	<li class="nav-item">
						<a class="nav-link"  id="customertat"  href="#tat" data-toggle="tab" role="tab">
							TAT
						</a>
					</li>

					<li class="nav-item">
						<a class="nav-link"  id="customerusers"  href="#tat" data-toggle="tab" role="tab">
							Users
						</a>
					</li> -->
					<!-- <li class="nav-item">
						<a class="nav-link"  id="customerabstractor" href="#abstractor" data-toggle="tab" role="tab">
							Abstractor
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="customeruploads" href="#uploaded" data-toggle="tab" role="tab">
							Uploads
						</a>
					</li> -->
				</ul>
			</div>


			<div class="card-body">					


				<div class="tab-content">
					<div class="tab-pane active" id="info">						

					</div>
					<div class="tab-pane" id="products" style="display: none">

					</div>
					<div class="tab-pane" id="workflowdependent" style="display: none">

					</div>
					<div class="tab-pane" id="milestonematrics" style="display: none">

					</div>
					<div class="tab-pane" id="workflowmetric" style="display: none">

					</div>
					<div class="tab-pane" id="workflow"  style="display: none">

					</div>
					<div class="tab-pane" id="priority" style="display: none">

					</div>
					<div class="tab-pane" id="users" style="display: none">												

					</div>
				</div>
			</div>
		</div>


		<!--Customer script -->

		<script src="<?php echo base_url(); ?>assets/js/multi-form.js" type="text/javascript"></script>
		<script src="<?php echo base_url(); ?>assets/js/plugins/jquery.bootstrap-wizard.js"  type="text/javascript"></script>
		<script src="<?php echo base_url(); ?>assets/js/customer.js" type="text/javascript"></script>
		<script src="<?php echo base_url(); ?>assets/js/load.js" type="text/javascript"></script>
		<script type="text/javascript">
			var CustomerUID  ='<?php echo $this->uri->segment(3); ?>';
			if(CustomerUID == ''){
				CustomerUID = $('#CustomerUID').val();
			}

			function removebycallback(){
				removecardspinner("#customerpaneldiv");
			}

			$(document).ready(function(){
				$("#info").load('<?php echo base_url("Customer/LoadcustomerInfo")?>', {"CustomerUID":CustomerUID},removebycallback);
				$("#customerinfo").click(function(){
					addcardspinner("#customerpaneldiv");
					$(".tab-pane").hide();
					$("#info").load('<?php echo base_url("Customer/LoadcustomerInfo")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#info").show();
					callselect2();
					
				});
				$("#customerproduct").click(function(){
					addcardspinner("#customerpaneldiv");					
					$(".tab-pane").hide();
					$("#products").load('<?php echo base_url("Customer/LoadcustomerProduct")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#products").show();
					
				});

				$("#customerworkflowdependent").click(function(){
					addcardspinner("#customerpaneldiv");					
					$(".tab-pane").hide();
					$("#workflowdependent").load('<?php echo base_url("Customer/LoadcustomerDependentWorkflow")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#workflowdependent").show();
					
				});

				$("#customermilestonematrics").click(function(){
					addcardspinner("#customerpaneldiv");					
					$(".tab-pane").hide();
					$("#milestonematrics").load('<?php echo base_url("Customer/LoadcustomerMilestone")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#milestonematrics").show();
					
				});

				$("#customerworkflowmetric").click(function(){
					addcardspinner("#customerpaneldiv");					
					$(".tab-pane").hide();
					$("#workflowmetric").load('<?php echo base_url("Customer/LoadcustomerDependentMetricWorkflow")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#workflowmetric").show();
					
				});
				
				$("#customerworkflow").click(function(){
					addcardspinner("#customerpaneldiv");
					$(".tab-pane").hide();
					$("#workflow").load('<?php echo base_url("Customer/LoadcustomerWorkflow")?>', {"CustomerUID":CustomerUID},removebycallback);		
					$("#workflow").show();
					
				});
				$("#customertat").click(function(){	
					addcardspinner("#customerpaneldiv");
					$(".tab-pane").hide();
					$("#priority").load('<?php echo base_url("Customer/LoadcustomerTat")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#priority").show();
					
				});
				
				$("#customerusers").click(function(){						
					addcardspinner("#customerpaneldiv");
					$(".tab-pane").hide();
					$("#users").load('<?php echo base_url("Customer/LoadcustomerUsers")?>', {"CustomerUID":CustomerUID},removebycallback); 		
					$("#users").show();
					
				});
			});




			$(document).ready(function(){


				$(".closesubproduct").click(function(){
					$(".subproduct").slideUp();			
				});

				$(document).off("click" , "#viewproductmodules").on("click" , "#viewproductmodules" , function(e){
					e.preventDefault();
					$(".setupdiv").slideDown();  
					$(".viewproduct").slideDown();  
					$(".addproduct").slideUp(); 
					$(".subproduct").slideUp(); 
				});


				$(document).off("click" , "#viewsubproductmodule").on("click" , "#viewsubproductmodule" , function(e){
					e.preventDefault();
					$(".subproduct").slideDown();
					$(".addsubproduct").slideUp();
					$(".subproductlist").slideDown();
					$(".setupdiv").slideUp();
				});

				$("#addproduct").click(function(){
					$('#Productform_ProductUID').val('');
					$('#Productform_ProductCode').val('');
					$('#Productform_ProductName').val('');
					$('#Productform_PreviousProductName').val('');
					$('#Productform_PreviousProductCode').val('');
					$('#Productform_ProductUID,#Productform_ProductCode,#Productform_ProductName').closest('.form-group').removeClass('is-filled has-danger');
					$('#Productform_ProductUID,#Productform_ProductCode,#Productform_ProductName').removeClass('is-invalid');

					$(".viewproduct").slideUp();
					$(".productlist").slideUp();
					$(".addproduct").slideDown();
				});

				$("#addsubproduct").click(function(){


					$('#SubProductform_SubProductUID').val('');
					$('#SubProductform_ProductUID').val('').trigger('change');
					$('#SubProductform_SubProductName').val('');
					$('#SubProductform_PreviousSubProductName').val('');
					$('#SubProductform_SubProductCode').val('');
					$('#SubProductform_OrderTypeUID').val('').trigger('change');
					$('#SubProductform_PriorityUID').val('').trigger('change');
					$('#SubProductform_ReportHeading').val('');
					$('#SubProductform_ProductUID,#SubProductform_SubProductName,#SubProductform_PreviousSubProductName,#SubProductform_SubProductCode,#SubProductform_OrderTypeUID,#SubProductform_PriorityUID,#SubProductform_ReportHeading').closest('.form-group').removeClass('is-filled has-danger');
					$('#SubProductform_ProductUID,#SubProductform_SubProductName,#SubProductform_PreviousSubProductName,#SubProductform_SubProductCode,#SubProductform_OrderTypeUID,#SubProductform_PriorityUID,#SubProductform_ReportHeading').removeClass('is-invalid');

					$(".subproduct").slideDown();
					$(".addsubproduct").slideDown();
					$(".subproductlist").slideUp();
				});
				

			});

			function callselect2(){
				$("select.select2picker").select2({
					//tags: false,
					theme: "bootstrap",
				});
			}

			$(document).on('click', '.click_completed', function(event) {
				event.preventDefault();
				/* Act on the event */
				CustomerUID = $('#CustomerUID').val();
				
				$('#myModal').modal('show');


			});



// function get_percent(CustomerUID){

// 	$.ajax({
// 		type: "POST",
// 		url: '<?php echo base_url();?>Customer/get_completedpercent',
// 		data: {"CustomerUID":CustomerUID}, 
// 		dataType:'json',
// 		beforeSend: function(){
// 		},
// 		success: function(data)
// 		{
	
// 			data.TAT_percent == "100" ?  $( "#TAT_percent_checkbox" ).prop( "checked", true ) :  $( "#TAT_percent_checkbox" ).prop( "checked", false );
// 			data.Workflowpercentage == "100" ?  $( "#Workflowpercentage_checkbox" ).prop( "checked", true ) :  $( "#Workflowpercentage_checkbox" ).prop( "checked", false );
// 			data.infocompleted == "100" ?  $( "#infocompleted_checkbox" ).prop( "checked", true ) :  $( "#infocompleted_checkbox" ).prop( "checked", false );
// 			data.pricingcompleted == "100" ?  $( "#pricingcompleted_checkbox" ).prop( "checked", true ) :  $( "#pricingcompleted_checkbox" ).prop( "checked", false );
// 			data.productcompleted == "100" ?  $( "#productcompleted_checkbox" ).prop( "checked", true ) :  $( "#productcompleted_checkbox" ).prop( "checked", false );

// 			$('#TAT_percent').html(parseFloat(data.TAT_percent).toFixed(2)+"%");
// 			$('#Workflowpercentage').html(parseFloat(data.Workflowpercentage).toFixed(2)+"%");
// 			$('#infocompleted').html(parseFloat(data.infocompleted).toFixed(2)+"%");
// 			$('#pricingcompleted').html(parseFloat(data.pricingcompleted).toFixed(2)+"%");
// 			$('#productcompleted').html(parseFloat(data.productcompleted).toFixed(2)+"%");
// 			$('#totalpercent').html(parseFloat(data.totalpercent).toFixed(2)+"%");
// 			$('#totalpercent_progressbar').css('width', parseFloat(data.totalpercent).toFixed(2)+"%");
// 			$('#TAT_percent_progressbar').css('width', parseFloat(data.TAT_percent).toFixed(2)+"%");
// 			$('#Workflowpercentage_progressbar').css('width', parseFloat(data.Workflowpercentage).toFixed(2)+"%");
// 			$('#infocompleted_progressbar').css('width', parseFloat(data.infocompleted).toFixed(2)+"%");
// 			$('#pricingcompleted_progressbar').css('width', parseFloat(data.pricingcompleted).toFixed(2)+"%");
// 			$('#productcompleted_progressbar').css('width', parseFloat(data.productcompleted).toFixed(2)+"%");
// 		},
// 		error: function(jqXHR){
// 			console.log(jqXHR);
// 		}
// 	});
// }

// $(document).off('click','#SaveProduct').on('click','#SaveProduct',function(e){
//  var ProductCode =$('#Productform_ProductCode').val();
//  var ProductName =$('#Productform_ProductName').val();
// });

</script>

