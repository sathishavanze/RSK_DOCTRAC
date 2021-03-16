		<div class="text-right"> 
			<i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
			<i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
		</div>


		<div id="advancedFilterForReport"  style="display: none;">
	<fieldset class="advancedsearchdiv">
		<legend>Advanced Search</legend>
	<form id="advancedsearchdata">
		<div class="col-md-12 pd-0">
			<div class="row " >
				<div class="col-md-3 ">
					   <div class="form-group bmd-form-group">
						<label for="adv_CustomerUID" class="bmd-label-floating">Client Name <span class="mandatory"></span></label>
						<select class="select2picker form-control" id="adv_CustomerUID"  name="CustomerUID">  
							<?php if (count($Clients) > 1) { ?>
						    	<option value="All">All</option>
							<?php } ?>
							<?php 
							foreach ($Clients as $key => $value) { ?>

								<option value="<?php echo $value->CustomerUID; ?>" ><?php echo $value->CustomerName; ?></option>
							<?php } ?>
							
						</select>
				  </div>
				</div>
				<div class="col-md-3 datadiv">
					<div class="form-group bmd-form-group">
              		<label for="adv_ProductUID" class="bmd-label-floating">Product <span class="mandatory"></span></label>
              		<select class="select2picker form-control" id="adv_ProductUID"  name="ProductUID">   
              			<option value="All">All</option>                  
              		</select>
              	</div>
				</div>
				<div class="col-md-3 datadiv">
				   <div class="form-group bmd-form-group">
						<label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
						<select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
						<option value="All">All</option>                  
						</select>
				  </div>
				</div>
				<div class="col-md-3 datadiv">
				   <div class="form-group bmd-form-group">
						<!-- <label for="adv_DateSelecter" class="bmd-label-floating">Project <span class="mandatory"></span></label> -->
						<select class="select2picker form-control chartview" id="adv_DateSelecter"  name="DateSelecterID">   
						<option value="Default">-- Select Filter --</option> 
                        <option value="C" data-filter="All" selected>All</option>
                        <option value="C" data-filter="Today">Today</option>
                        <option value="C" data-filter="3Month">Last 3 months</option>
                        <option value="C" data-filter="6Month">Last 6 months</option>
                        <option value="C" data-filter="LYear">Last 12 months</option>
                        <option value="C" data-filter="LMonth">Last 30 days</option>
                        <option value="M" data-filter="CMonth">Current month</option>
                        <option value="Y" data-filter="CYear">Current Year</option>                
						</select>
				  </div>
				</div>


			</div>
		<!-- 	<div class="row " >
				<p style="margin: 0px;color: #266596;font-weight: 600;">Date Filter</p>

			</div>
		-->
		<div class="col-md-12">
			<div class="row " >
				<div class="col-md-3 datadiv">
					<div class="bmd-form-group row">
						<div class="col-md-3 pd-0 inputprepand" >
							<p class="mt-5"> From </p>
						</div>
						<div class=" col-md-9 " style="padding-left: 0px">
							<div class="datediv">
							<input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php //echo date('m/d/Y',strtotime("-90 days")); ?>">
							</div>
						</div>
					</div>
				</div>

					<div class="col-md-3 datadiv">
					<div class="bmd-form-group row">
						<div class="col-md-3 pd-0 inputprepand" >
							<p class="mt-5"> To </p>
						</div>
						<div class=" col-md-9 " style="padding-left: 0px">
							<div class="datediv">
								<input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php //echo (date("m/d/Y")); ?>"/>
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



	</div>
</form>
</fieldset>
</div>



		
<script type="text/javascript">


	$(document).ready(function(){

		$('.fa-filter').click(function(){
				$("#advancedFilterForReport").slideToggle();
			});


		$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
			e.preventDefault();
			var $dataobject = {'CustomerUID': $(this).val()};
			if($(this).val() == 'All')
			{
				$('#adv_ProductUID').html('<option value="All">All</option>');
				$('#adv_ProjectUID').html('<option value="All">All</option>');
				/*$('#adv_LenderUID').html('<option value="All">All</option>');*/
				callselect2();
			}
			else
			{
				SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {
				// addcardspinner($('#AuditCard'));
			}).then(function (data) {
				if (data.validation_error == 0) {
					var Product_Select = data.Products.reduce((accumulator, value) => {
						
						return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
					}, '<option value="All">All</option>');					
					$('#adv_ProductUID').html(Product_Select);
					$('#adv_ProductUID').trigger('change');
				}
				callselect2();

			}).catch(function (error) {
				
				console.log(error);
			});
		}
	});

		$(document).off('change', '#adv_ProductUID').on('change', '#adv_ProductUID', function (e) {  
			e.preventDefault();
			var $dataobject = {'ProductUID': $(this).val()};
			if($(this).val() == 'All')
			{
				$('#adv_ProjectUID').html('<option value="All">All</option>');
				/*$('#adv_LenderUID').html('<option value="All">All</option>');*/
				callselect2();
			}
			else
			{
				SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProjects', $dataobject, 'json', true, true, function () {
				// addcardspinner($('#AuditCard'));
			}).then(function (data) {
				if (data.validation_error == 0) {
					var Project_Select = data.Projects.reduce((accumulator, value) => {
						
						return accumulator + '<Option value="' + value.ProjectUID + '">' + value.ProjectName + '</Option>';
					}, '<option value="All">All</option>');					
					$('#adv_ProjectUID').html(Project_Select);
					$('#adv_ProjectUID').trigger('change');
				}
				callselect2();

			}).catch(function (error) {
				
				console.log(error);
			});
		}
	});


$('.chartview').change(function(){ 
     var view = $(this).val(); 
     var option_filter = $(this).find('option:selected').attr('data-filter');

     if(view!='Default')
     {
       get_from_to_date(option_filter,view); 
     } else {
        $('#adv_FromDate').val("<?php echo date('Y-m-d',strtotime('-90 days'));?>");
        $('#adv_FromDate').attr('value',"<?php echo date('Y-m-d',strtotime('-90 days'));?>");
        $('#adv_ToDate').val("<?php echo date('Y-m-d');?>"); 
        $('#adv_FromDate').attr('value',"<?php echo date('Y-m-d');?>");        
     }

  });


function get_from_to_date(option_filter,view,product,customer)
  {
    $.ajax ({
      type:'POST',
      url:'<?php echo base_url();?>CommonController/Filter_from_to_date/',
      dataType: 'JSON',
      data: {'Filter':option_filter},
      beforeSend: function() {
        $('#loader').addClass('be-loading be-loading-active');
      },
      success:function(data)
      {  
        if(data!='')
        { 
          $('#adv_FromDate').val(data.From);
          $('#adv_FromDate').attr('value',data.From);
          $('#adv_ToDate').val(data.To); 
          $('#adv_FromDate').attr('value',data.To); 
          Ajax_Load_Functions(view,option_filter);
        }  
      },
      complete: function() {
        $('#loader').removeClass('be-loading be-loading-active');
      }
    });
  }



		$(document).off('change', '#adv_ProjectUID').on('change', '#adv_ProjectUID', function (e) {  
			e.preventDefault();
			
			var $dataobject = {'ProjectUID': $(this).val()};
			SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchLenders', $dataobject, 'json', true, true, function () {
				// addcardspinner($('#AuditCard'));
				
			}).then(function (data) {
				if (data.validation_error == 0) {
					var Lender_Select = data.Lenders.reduce((accumulator, value) => {
						
						return accumulator + '<Option value="' + value.LenderUID + '">' + value.LenderName + '</Option>';
					}, '<option value="All">All</option>');		
					/*$('#adv_LenderUID').html(Lender_Select);*/
					$('#adv_LenderUID').trigger('change');

				}
				callselect2();

			}).catch(function (error) {
				
				console.log(error);
			});

			
		});

  		if ($('#adv_CustomerUID option').length == 1) {
  			$('#adv_CustomerUID').trigger('change');
  		}



		});
</script>
	