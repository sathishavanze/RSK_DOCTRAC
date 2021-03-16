<?php
$GetLoanTypeDetails = $this->Common_Model->GetLoanTypeDetails();
?>
<div class="order_expand_div" style="display: none">
	<div class="row">
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="Customer" class="bmd-label-floating">Client<span class="mandatory"></span></label>
				<select class="select2picker form-control"  id="Customer" name="Customer"  required>

					<?php foreach ($Customers as $key => $value) { 
						if($value->CustomerUID == $OrderSummary->CustomerUID) { ?>
							<option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
						<?php  } ?>

					<?php } ?>

				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="CustomerRefNum" class="bmd-label-floating">Client Ref Number</label>
				<input type="text" class="form-control" id="CustomerRefNum" name="CustomerRefNum" value="<?php echo $OrderSummary->CustomerReferenceNumber; ?>" />
			</div>
		</div>

		<div class="col-md-3 priorityrow">
			<div class="form-group bmd-form-group">
				<label for="PriorityUID" class="bmd-label-floating">Priority<span class="mandatory"></span></label>
				<select class="select2picker form-control PriorityUID"  id="PriorityUID" name="PriorityUID" required>
					<!--   <option value=""></option> -->
					<option value="<?php echo $OrderDetails->PriorityUID;?>" selected><?php echo $OrderDetails->PriorityName;?>  </option>

				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="AltORderNumber" class="bmd-label-floating">Alternate Order Number</label>
				<input type="text" class="form-control" id="AltORderNumber" name="AltORderNumber" value="<?php echo $OrderSummary->AltOrderNumber; ?>">
			</div>
		</div>


	</div>

	<div class="row productfield_add mt-10">



		<div class="col-md-3" id="divLoanNumber">
			<div class="form-group bmd-form-group">
				<label for="LoanNumber" class="bmd-label-floating">Loan Number</label>
				<input type="text" class="form-control" id="LoanNumber" name="LoanNumber" value="<?php echo $OrderSummary->LoanNumber; ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyAddress1" class="bmd-label-floating">Address Line 1</label>
				<input type="text" class="form-control" id="PropertyAddress1" name="PropertyAddress1" value="<?php echo $OrderSummary->PropertyAddress1; ?>">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyAddress2" class="bmd-label-floating">Address Line 2</label>
				<input type="text" class="form-control" id="PropertyAddress2" name="PropertyAddress2" value="<?php echo $OrderSummary->PropertyAddress2; ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyZipcode" class="bmd-label-floating">Zipcode</label>
				<input type="text" class="form-control" id="PropertyZipcode" name="PropertyZipcode" value="<?php echo $OrderSummary->PropertyZipCode; ?>">
				<span data-modal="zipcode-form" class="label label-success label-zip md-trigger" id="zipcodeadd" style="display: none;">Add Zipcode</span>
			</div>
		</div>


	</div>

	<div class="row ">

		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyCityName" class="bmd-label-floating">City</label>
				<input type="text" class="form-control" id="PropertyCityName" name="PropertyCityName" value="<?php echo $OrderDetails->PropertyCityName; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<ul class="dropdown-menu dropdown-style PropertyCityName"></ul>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyCountyName" class="bmd-label-floating">County</label>
				<input type="text" class="form-control" id="PropertyCountyName" name="PropertyCountyName"  value="<?php echo $OrderDetails->PropertyCountyName; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<ul class="dropdown-menu dropdown-style PropertyCountyName"></ul>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="PropertyStateCode" class="bmd-label-floating">State<!-- <span class="mandatory"></span> --></label>
				<input type="text" class="form-control" id="PropertyStateCode" name="PropertyStateCode"  value="<?php echo $OrderDetails->PropertyStateCode; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<ul class="dropdown-menu dropdown-style PropertyStateCode"></ul>
			</div>
		</div>
		<?php if(!in_array($OrderDetails->CustomerUID,$this->config->item('Loantypeexcludedclients'))) { ?>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="LoanType" class="bmd-label-floating">Loan Type</label>
				<select class="select2picker form-control LoanType"  id="LoanType" name="LoanType">
					<option value=""></option>
					<?php 
					// $titleWorkflow = array('VA','FHA','FHA/VA');
					foreach ($GetLoanTypeDetails as $key => $value) 
					{
						
						if($OrderDetails->LoanType == $value->LoanTypeName)
						{
							echo '<option value="'.$value->LoanTypeName.'" selected>'.$value->LoanTypeName.'</option>';
						}
						else
						{
							echo '<option value="'.$value->LoanTypeName.'">'.$value->LoanTypeName.'</option>';
						}
					}
					?>
					
				</select>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="col-md-12 pd-0">
		<h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Property Roles <i class="fa fa-plus-circle fa-2x material-icons mdl-textfield__label__icon productadd_button pull-right" style="margin-top: -5px;"></i></h4>                   
	</div>



	<div class="Borrower">
		<?php 
		$sno = 1;
		if(sizeof($BorrowerName) == 0)
		{
			?>
			<div class="productfield_add delete">
				<div class="row  mt-10">
					<div class="col-md-3">
						<div class="form-group bmd-form-group" >
							<label for="BorrowerFirstName" class="bmd-label-floating">Borrower FirstName</label>
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


			<?php

		}
		foreach ($BorrowerName as $row) {
			if($sno == 1)
			{
				?>
				<div class="productfield_add delete">
					<div class="row  mt-10">
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerFirstName" class="bmd-label-floating">Borrower Name</label>
								<input type="hidden" class="form-control" id="SNO" name="SNO[]" value="<?php echo $row->SNO; ?>">
								<input type="text" class="form-control" id="BorrowerFirstName" name="BorrowerFirstName[]" value="<?php echo $row->BorrowerFirstName; ?>">
							</div>
						</div>
	
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerMailAddress" class="bmd-label-floating">Borrower Mail Address</label>
								<input type="text" class="form-control" id="BorrowerMailAddress" name="BorrowerMailAddress[]" value="<?php echo $row->BorrowerMailingAddress1; ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerContactNumber" class="bmd-label-floating">Borrower Contact Number</label>
								<input type="text" class="form-control" id="BorrowerContactNumber" name="BorrowerContactNumber[]" value="<?php echo $row->BorrowerContactNumber; ?>">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerSSN" class="bmd-label-floating">Borrower SSN</label>
								<input type="text" class="form-control" id="BorrowerSSN" name="BorrowerSSN[]" value="<?php echo $row->BorrowerSSN; ?>">
							</div>
						</div>
					</div>
				</div>

				<?php
			}
			else
			{
				?>

				<div class="productfield_add delete">
					<i class="fa fa-minus-circle fa-2x material-icons mdl-textfield__label__icon productremove_button Deleterow" id="Deleterow" style="float:right;" ></i>
					<div class="row  mt-10">
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerFirstName" class="bmd-label-floating">Borrower Name</label>
								<input type="hidden" class="form-control" id="SNO" name="SNO[]" value="<?php echo $row->SNO; ?>">
								<input type="text" class="form-control" id="BorrowerFirstName" name="BorrowerFirstName[]" value="<?php echo $row->BorrowerFirstName; ?>">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerMailAddress" class="bmd-label-floating">Borrower Mail Address</label>
								<input type="text" class="form-control" id="BorrowerMailAddress" name="BorrowerMailAddress[]" value="<?php echo $row->BorrowerMailingAddress1; ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerContactNumber" class="bmd-label-floating">Borrower Contact Number</label>
								<input type="text" class="form-control" id="BorrowerContactNumber" name="BorrowerContactNumber[]" value="<?php echo $row->BorrowerContactNumber; ?>">
							</div>
						</div>

	
						<div class="col-md-3">
							<div class="form-group bmd-form-group" >
								<label for="BorrowerSSN" class="bmd-label-floating">Borrower SSN</label>
								<input type="text" class="form-control" id="BorrowerSSN" name="BorrowerSSN[]" value="<?php echo $row->BorrowerSSN; ?>">
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			$sno++;
		}

		?>
	</div>

</div>


<div class="field_add">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
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

	$('#frmordersummary').off('click','.Deleterow').on('click','.Deleterow',function(){
		var whichtr = $(this).closest('.delete');
		whichtr.remove();  
	});
</script>