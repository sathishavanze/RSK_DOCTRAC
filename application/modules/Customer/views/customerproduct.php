			<style type="text/css">
			.bootstrap-select .dropdown-menu{
				z-index: 9999 !important;
			}
			.WorkflowmoduleWrapper .select2-container {
				width:100%;
				border-bottom:1px solid #eee;
			}
		</style>


		<form class="Customer_prod_subproduct" name="Customer_prod_subproduct" id="Customer_prod_subproduct">
			<input type="hidden" name="AppendCount" class="form-control" id="AppendCount" value="<?php echo count($Prod_SubProd); ?>" />
			<div class="col-md-12 selectproductdiv dynamicdiv">
				<div class="row" style="background:#f7f7f7;"> 
					<!-- <div class="col-md-3 dyndivhead"> -->
						<!-- <div class="row"> -->
							<!-- <div class="col-5 mt-5">
								<strong>Product</strong>
							</div> -->
							<div class="col-md-3 dyndivhead">       
								<strong>Product</strong> 
							</div>
							<div class="col-md-9 dyndivhead">
								<strong>Workflows</strong>         
							</div>
							<!-- <div class="col-md-4 dyndivhead">
								<strong>Optional Workflow</strong>
							</div> -->
							<!-- <div class="col-md-1 dyndivhead text-center">
								<strong>Action</strong>
								<button type="button" class="btn btn-github btn-sm addproductrow">
							<i class="icon-plus22  btnicon "></i></button>
							</div> -->
							<!-- <div class="col-7 text-right">
								<button type="button" class="btn  btn-outline-info btn-sm btn-xs" id="viewproductmodules" ><i class="icon-eye"></i></button>      
							</div> -->
						<!-- </div> -->
					<!-- </div> -->
					<!-- <div class="col-md-7 dyndivhead">
						<div class="row">
							<div class="col-5 mt-5">
								<strong>Sub Product</strong>
							</div>
							<div class="col-7 text-right">
								<button type="button" class="btn btn-outline-info btn-sm btn-xs" id="viewsubproductmodule"><i class="icon-eye"></i></button>      
							</div>
						</div>
					</div> -->
					<!-- <div class="col-md-2 dyndivhead text-center">
						<button type="button" class="btn btn-github btn-sm addproductrow">
							<i class="icon-plus22  btnicon "></i></button>
						</div> -->
					</div>
					<?php $i=1; 
						for ($x = 1; $x == 1; $x++) {
						// foreach ($Prod_SubProd as $key => $value) { 
						// $SubProductDetails = $this->Common_Model->GetSubproductByProduct($value['ProductUID']);
						?>
						<div class="row WorkflowmoduleWrapper" id="apimaindiv<?php echo $i; ?>" data-id="<?php echo $i; ?>">
							<div class="col-md-3 dyndiv">
								<div class="form-group">
									<select class="select2picker ProductUID"  id="ProductUID<?php echo $i; ?>" name="ProductUID[<?php echo $i; ?>]" data-hidden="ProductUID<?php echo $i; ?>" title="Select Product" data-live-search="true" data-header="<p class='addbtnval addprovision' onclick='myFunction()'><i class='icon-plus22 pr-10'></i> Add Product</p>">                   
										<!-- <option value="0" selected="selected"> -- Select Product -- </option> -->
										<?php foreach ($Products as $key => $prod) {
// print_r($Products);exit;
											if($prod->ProductUID == $Prod_SubProd[0]['ProductUID']){  ?>

												<option value="<?php echo $prod->ProductUID; ?>" selected><?php echo $prod->ProductName;?></option>

											<?php }else{  ?>

												<option value="<?php echo $prod->ProductUID; ?>"><?php echo $prod->ProductName;?></option>

											<?php } 											
										} ?>  
										</select>
									</div>
								</div>
								<!-- <div class="col-md-7 dyndiv">
									<div class="form-group">
										<select class="select2picker SubProductUID" id="SubProductUID<?php echo $i; ?>" name="SubProductUID[<?php echo $i; ?>][]" data-hidden="SubProductUID<?php echo $i; ?>" multiple="multiple">  
											<?php foreach ($SubProductDetails as $key => $subprod) {
												$selected = 0;
												foreach ($value['Customer_Subproducts'] as $keys => $values) {
													if($subprod->SubProductUID == $values['SubProductUID']){  ?>
														<option value="<?php echo $subprod->SubProductUID; ?>" selected><?php echo $subprod->SubProductName;?></option>
														<?php 
														$selected = 1; 
													}
												} if($selected == 0){ ?>
													<option value="<?php echo $subprod->SubProductUID; ?>" ><?php echo $subprod->SubProductName;?></option>
												<?php } ?>
											<?php } ?>    
										</select>
									</div>
								</div>  -->
							
								<div class="col-md-9 dyndiv">
									<div class="form-group">
										<select class="select2picker WorkflowModuleUID" id="WorkflowModuleUID_<?php echo $key; ?>" name="WorkflowModuleUID[<?php echo $key; ?>][]" multiple="multiple" >  
											<option value=""></option>

											<?php foreach ($WorkflowDetaiils as $wkey => $Workflow) {

												$selected = 0;

												foreach ($Prod_SubProd[0]['Customer_Workflow'] as $cwkeys => $values) {

													if($Workflow->WorkflowModuleUID == $values['WorkflowModuleUID']){  ?>
														<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" selected><?php echo $Workflow->WorkflowModuleName;?></option>
														<?php 
														$selected = 1; 
													}

												} if($selected == 0){ ?>

													<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" ><?php echo $Workflow->WorkflowModuleName;?></option>

												<?php } ?>
											<?php } ?>
										</select>
									</div>   
								</div>
								
								<!-- <div class="col-md-1 dyndiv text-center">
									<div class="mt-10">
										<button type="button" class="btn btn-xs btn-dribbble copyworkflow"><i class="icon-copy4"></i></button>
										<button class="btn btn-github btn-sm removeproductrow">
										<i class="icon-minus3 btnicon "></i>	</button>
									</div>
								</div> -->

						<!-- 		<div class="col-md-2 dyndiv text-center"> -->
									
								
							<!-- 	</div> -->

			</div>
			<?php $i++; } ?>
		</div>
		<div class="ml-auto text-right">
			<a href="Customer" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
			<button type="submit" class="btn btn-fill btn-update btn-wd " name="Update_Productsetup" id="Update_Productsetup"><i class="icon-floppy-disk pr-10"></i>Update</button>
		</div>
		<div class="clearfix"></div>
	</form>


	<script type="text/javascript">

	  // json products and subproducts
	  var JSON_Products = JSON.parse('<?php echo $JSON_Products; ?>');
	  var JSON_WorkflowModules = JSON.parse('<?php echo $JSON_WorkflowModules; ?>');
	  var product_options = '<option></option>';
	  console.log(JSON_Products);

	  JSON_Products.forEach(function (value, key) {
	  	product_options += '<option value="'+value.ProductUID+'">'+value.ProductName+'</option>';
	  });


	  function callselect2() {
	  	$("select.select2picker").select2({
	  		//tags: false,
	  		theme: "bootstrap",
	  	});
	  }

		$(function() {
			$("select.select2picker").select2({
				//tags: false,
				theme: "bootstrap",
			});
		});


		$(document).ready(function() {

			$("div.inner.show").addClass("perfectscrollbar");
			$('.perfectscrollbar').perfectScrollbar();

			var Workflow_options='<?php foreach ($WorkflowDetaiils as $key => $WorkflowDet) {?><option value="<?php echo $WorkflowDet->WorkflowModuleUID; ?>"><?php echo $WorkflowDet->WorkflowModuleName;?></option><?php } ?>';

			var Product_options='<?php foreach ($Products as $key => $prod) { ?><option value="<?php echo $prod->ProductUID; ?>"><?php echo $prod->ProductName;?></option><?php } ?>';



			$("body").off("click", ".addproductrow").on("click" , ".addproductrow" , function(e){
				e.preventDefault();
				var elem_count = $('.WorkflowmoduleWrapper').length;
				var appenddiv   = '';
				appenddiv  =  '<div class="row productsetupdiv WorkflowmoduleWrapper" id="apimaindiv'+elem_count+'" data-id="'+elem_count+'"><div class="col-md-3 dyndiv"> <div class="form-group">';
				appenddiv = appenddiv + '<select class="select2picker form-control ProductUID" id="ProductUID'+elem_count+'" ata-hidden="ProductUID'+elem_count+'" name="ProductUID['+elem_count+']" ><option value="0" selected="selected"> -- Select Product -- </option>'+Product_options;
				appenddiv =  appenddiv + '</select></div></div>';
				
				// appenddiv  =  '<div class="col-md-6 dyndiv"> <div class="form-group">';
				// appenddiv = appenddiv + '<select class="select2picker form-control WorkflowModuleUID" id="WorkflowModuleUID'+elem_count+'" ata-hidden="WorkflowModuleUID'+elem_count+'" name="WorkflowModuleUID['+elem_count+']" ><option value="0" selected="selected"> -- Select Product -- </option>'+Workflow_options;
				// appenddiv =  appenddiv + '</select></div></div>';

				appenddiv += '<div class="col-md-6 dyndiv"><select class="select2picker form-control WorkflowModuleUID" id="WorkflowModuleUID'+elem_count+'" name="WorkflowModuleUID['+elem_count+']" data-hidden="WorkflowModuleUID'+elem_count+'" multiple="multiple" >'+Workflow_options; 

				 appenddiv =  appenddiv + '</select></div>';

				appenddiv += '<div class="col-md-1 dyndiv text-center"><button type="button" class="btn btn-xs btn-dribbble copyworkflow"><i class="icon-copy4"></i></button><button class="btn btn-github btn-sm removeproductrow"><i class="icon-minus3 btnicon "></i></button></div>';
				$(".selectproductdiv").append(appenddiv); 

				callselect2();
			});

			$("body").on("click" , ".removeproductrow" , function(e){
				e.preventDefault();
				$(this).closest(".WorkflowmoduleWrapper").remove();
				return false;
			});



			// Product DDL change event
			$(document).off('change','select.ProductUID').on('change','select.ProductUID', function (e) {

				e.preventDefault();


				console.log(this);
				var ALL_ProductUIDs = [];

				console.log($('select.ProductUID').not(this));

				$('select.ProductUID').not(this).each(function (key, value) {
					ALL_ProductUIDs.push($(value).find('option:selected').val());
				});

				console.log(ALL_ProductUIDs);
				if (ALL_ProductUIDs.length > 0 && ALL_ProductUIDs.indexOf($(this).val()) !== -1) {
					$(this).val('');
					callselect2();
					$('.main-panel').perfectScrollbar('update');

					return;
				}
				var $ProductUID = $(this).val();
				var current_elem = $(this).closest('.dyndiv').next().find('select.SubProductUID');
				console.log(current_elem);
				var SubProducts = '<option></option>';


      			// iterate and find Current Product inn predefined json in JSON_Products.
		      JSON_Products.forEach(function (value, key) {
		      	// if (value.ProductUID == $ProductUID) {
		      	// 	value.SubProducts.forEach(function (subproduct, skey) {
		       //      // Generate dynamix SubProducts.
		       //      SubProducts += '<option value="'+subproduct.SubProductUID+'">'+subproduct.SubProductName+'</option>';
		       //  })
		      	// }
		      })

		      // Apppend Current Subproduct to current row.
		      $(current_elem).html(SubProducts);
		      callselect2();
		      $('.main-panel').perfectScrollbar('update');
		  })




			$('#Customer_prod_subproduct').off('submit').on('submit',function(event)
			{
				event.preventDefault();
				var button = $('#Update_Productsetup');
				var button_text = $('#Update_Productsetup').html();
				var Prod_WorkflowModule = [];

				/*Products & Sub Products Starts*/
				$('.WorkflowmoduleWrapper').each(function(key,value) {
					//var ivalue = $(value).attr('data-id');
					Prod_WorkflowModule.push( {
						ProductUID : $(value).find('select.ProductUID').val(),
						WorkflowModuleUID :$(value).find('select.WorkflowModuleUID').val(),
					});
				});
				/*Products & Sub Products Ends*/


				console.log(Prod_WorkflowModule);
				$.ajax({
					type: "POST",
					url: base_url+'Customer/Update_Productsetup',
					data: {"CustomerUID":CustomerUID,'Prod_WorkflowModule': Prod_WorkflowModule}, 
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

					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					complete:function(){
						button.html(button_text);
						button.removeAttr("disabled");
					}
				});


			});
			/*---------- PRODUCT TAB ---------*/


		});

	</script>
