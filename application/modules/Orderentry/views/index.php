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
		<div class="card-icon"> <i class="icon-upload4"></i>
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
				<a class="nav-link ajaxload active" href="<?php echo base_url(); ?>Orderentry" role="tablist">
					Single Entry
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/bulkentry" role="tablist">
					Bulk Entry 
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload" href="<?php echo base_url(); ?>Orderentry/bulkAssign" role="tablist">
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
				<a class="nav-link ajaxload bulkAssign" href="<?php echo base_url(); ?>Orderentry/PayOffBulkUpdate" role="tablist">
					PayOff 
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link ajaxload bulkAssign" href="<?php echo base_url(); ?>Orderentry/BulkWorkflowEnable" role="tablist">
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

			<div class="tab-pane active" id="singleentry">
				<form action="#"  name="orderform" id="order_frm" novalidate>
					<div class="col-md-12 pd-0">
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="Customer" class="bmd-label-floating">Client<span class="mandatory"></span></label>
								<select class="select2picker form-control"  id="Customer" name="Customer" required>
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

						<div class="col-md-3 productuidrow">
							<div class="form-group bmd-form-group">
								<label for="Single-ProductUID" class="bmd-label-floating"> Product<span class="mandatory"></span></label>
								<select class="select2picker form-control ProductUID"  id="Single-ProductUID" name="ProductUID">
									<option value=""></option>
								</select>
							</div> 
						</div>

						<div class="col-md-3 projectuidrow">
							<div class="form-group bmd-form-group">
								<label for="Single-ProjectUID" class="bmd-label-floating"> Project<span class="mandatory"></span></label>
								<select class="select2picker form-control ProjectUID"  id="Single-ProjectUID" name="ProjectUID">
								</select>
							</div> 
						</div>

						<div class="col-md-3 priorityuidrow">
							<div class="form-group bmd-form-group">
								<label for="Single-PriorityUID" class="bmd-label-floating"> Priority<span class="mandatory"></span></label>
								<select class="select2picker form-control PriorityUID"  id="Single-PriorityUID" name="PriorityUID">
									<?php foreach ($OrderPriority as $key => $value) { ?>
										<option value="<?php echo $value->PriorityUID; ?>"><?php echo $value->PriorityName; ?></option>
									<?php } ?>								
								</select>
							</div> 
						</div>




					</div>


					<div class="row mt-10">


						<div class="col-md-3" id="divLoanNumber">
							<div class="form-group bmd-form-group">
								<label for="LoanNumber" class="bmd-label-floating">Loan Number</label>
								<input type="text" class="form-control" id="LoanNumber" name="LoanNumber">
							</div> 
						</div>

						<div class="col-md-3" id="divLoanAmount">
							<div class="form-group bmd-form-group">
								<label for="LoanAmount" class="bmd-label-floating">Loan Amount</label>
								<input type="text" class="form-control" id="LoanAmount" name="LoanAmount" data-type="currency">
							</div> 
						</div>
						<?php if(!in_array($this->parameters['DefaultClientUID'],$this->config->item('Loantypeexcludedclients'))) { ?>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="LoanType" class="bmd-label-floating">Loan Type <span class="mandatory"></span></label>
								<select class="select2picker form-control LoanType"  id="LoanType" name="LoanType">
									<option value=""></option>
									<?php 
									foreach ($GetLoanTypeDetails as $key => $value) { ?>
										<option value="<?php echo $value->LoanTypeName; ?>"><?php echo $value->LoanTypeName; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php } ?>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="CustomerReferenceNumber" class="bmd-label-floating">Customer Reference Number</label>
								<input type="text" class="form-control" id="CustomerReferenceNumber" name="CustomerReferenceNumber">
							</div> 
						</div>



					</div>

					<div class="row mt-10">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="APN" class="bmd-label-floating">APN</label>
								<input type="text" class="form-control" id="APN" name="APN">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyAddress1" class="bmd-label-floating">Address Line 1</label>
								<input type="text" class="form-control" id="PropertyAddress1" name="PropertyAddress1" >
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyAddress2" class="bmd-label-floating">Address Line 2</label>
								<input type="text" class="form-control" id="PropertyAddress2" name="PropertyAddress2">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyZipcode" class="bmd-label-floating">Zipcode</label>
								<input type="text" class="form-control" id="PropertyZipcode" name="PropertyZipcode" >
								<span data-modal="zipcode-form" class="label label-success label-zip md-trigger" id="zipcodeadd" style="display: none;">Add Zipcode</span>
							</div>
						</div>



					</div>


					<div class="row mt-10">
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyCityName" class="bmd-label-floating">City</label>
								<input type="text" class="form-control" id="PropertyCityName" name="PropertyCityName" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
								<ul class="dropdown-menu dropdown-style PropertyCityName"></ul>
							</div>											
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyCountyName" class="bmd-label-floating">County</label>
								<input type="text" class="form-control" id="PropertyCountyName" name="PropertyCountyName" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
								<ul class="dropdown-menu dropdown-style PropertyCountyName"></ul>
							</div>											
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group">
								<label for="PropertyStateCode" class="bmd-label-floating">State<!-- <span class="mandatory"></span> --></label>
								<input type="text" class="form-control" id="PropertyStateCode" name="PropertyStateCode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
								<ul class="dropdown-menu dropdown-style PropertyStateCode"></ul>
							</div>											
						</div>
					</div>

					<div class="col-md-12 pd-0">
						<h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Property Roles</h4>                   
					</div>


					<div class="Borrower">
						<i class="fa fa-plus-circle fa-2x material-icons mdl-textfield__label__icon productadd_button pull-right" style="margin-top: -5px;"></i>
						<div class="productfield_add delete">
							<div class="row  mt-10">
								<div class="col-md-3">
									<div class="form-group bmd-form-group" >
										<label for="BorrowerFirstName" class="bmd-label-floating">Borrower Name</label>
										<input type="text" class="form-control" id="BorrowerFirstName" name="BorrowerFirstName[]" value="">
									</div>
								</div>
					
								<div class="col-md-3">
									<div class="form-group bmd-form-group" >
										<label for="BorrowerMailAddress" class="bmd-label-floating">Borrower Mail Address</label>
										<input type="text" class="form-control" id="BorrowerMailAddress" name="BorrowerMailAddress[]" value="">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group bmd-form-group" >
										<label for="BorrowerContactNumber" class="bmd-label-floating">Borrower Contact Number</label>
										<input type="text" class="form-control" id="BorrowerContactNumber" name="BorrowerContactNumber[]" value="">
									</div>
								</div>

					
								<div class="col-md-3">
									<div class="form-group bmd-form-group" >
										<label for="BorrowerSSN" class="bmd-label-floating">Borrower SSN</label>
										<input type="text" class="form-control" id="BorrowerSSN" name="BorrowerSSN[]" value="">
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="col-sm-12 text-right pd-0">

						<button type="submit" class="btn btn-space btn-social btn-color btn-save single_submit" id="saveandnew" value="1">Save &amp; New Order</button>
						<button type="submit" class="btn btn-space btn-social btn-color btn-dribbble single_submit"  id="saveandexit" value="3">Save &amp; Exit</button>

					</div>

				</form>
			</div>


		</div>

	</div>
</div>


<script src="assets/js/formatcurrency.js?reload=1.0.1"></script>


<script type="text/javascript">

	$(document).ready(function(){

		/*-- removing hash from url --*/
		$(function(){
			var hash = window.location.hash;
			hash && $('ul.nav.entrytab a[href="' + hash + '"]').tab('show');
			var noHashURL = window.location.href.replace(/#.*$/, '');
			window.history.replaceState('', document.title, noHashURL) 
		})

		$(document).off('click', '.productadd_button').on('click', '.productadd_button', function()
		{
			var appendrow=` <div class="productfield_add delete">
			<i class="fa fa-minus-circle fa-2x material-icons mdl-textfield__label__icon productremove_button Deleterow" id="Deleterow" style="float:right;" ></i>
			<div class="row  mt-10">
			<div class="col-md-3">
			<div class="form-group bmd-form-group" >
			<label for="BorrowerFirstName" class="bmd-label-floating">Borrower Name</label>
			<input type="text" class="form-control" id="BorrowerFirstName" name="BorrowerFirstName[]" value="">
			</div>
			</div>
			
			<div class="col-md-3">
			<div class="form-group bmd-form-group" >
			<label for="BorrowerMailAddress" class="bmd-label-floating">Borrower Mail Address</label>
			<input type="text" class="form-control" id="BorrowerMailAddress" name="BorrowerMailAddress[]" value="">
			</div>
			</div>
			<div class="col-md-3">
			<div class="form-group bmd-form-group" >
			<label for="BorrowerContactNumber" class="bmd-label-floating">Borrower Contact Number</label>
			<input type="text" class="form-control" id="BorrowerContactNumber" name="BorrowerContactNumber[]" value="">
			</div>
			</div>
			
			<div class="col-md-3">
			<div class="form-group bmd-form-group" >
			<label for="BorrowerSSN" class="bmd-label-floating">Borrower SSN</label>
			<input type="text" class="form-control" id="BorrowerSSN" name="BorrowerSSN[]" value="">
			</div>
			</div>
			</div>
			</div>`;
			$('.Borrower').append(appendrow);
		});

		$('#order_frm').off('click','.Deleterow').on('click','.Deleterow',function(){
			var whichtr = $(this).closest('.delete');
			whichtr.remove();  
		});

	});

</script>







