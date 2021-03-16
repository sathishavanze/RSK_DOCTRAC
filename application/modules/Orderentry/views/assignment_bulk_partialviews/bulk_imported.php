<ul class="nav nav-pills nav-pills-rose" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small>
			Imported&nbsp;<i class="fa fa-check-circle"></i></small>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#error-data" role="tablist"><small>
			Not Imported&nbsp;<i class="fa fa-times-circle-o"></i></small>
		</a>
	</li>
</ul>
<div class="tab-content tab-space">
	<div id="success-table" class="tab-pane active cont">

		<div class="">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="importdata">
					<thead>
						<tr>
							<th>OrderNumber</th>
							<th>Client</th>
							<th>Product</th>
							<th>Lender</th>
							<th>Property County Name</th>
							<th>Servicer Loan Number</th>
							<th>Alternate Ref No</th>
							<th>Loan Number</th>
							<th>Borrower Name 1</th>
							<th>Borrower Name 2</th>
							<th>Property Address</th>
							<th>Property City</th>
							<th>Property State</th>
							<th>Property Zip Code</th>

							<th>Origination Date</th>
							<th>Loan Amount</th>
							<th>Mod Balance</th>
							<th>Modification Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($SuccessData as $key => $a) { ?>
							<tr>
								<td> <?php echo $a['result']['OrderNumber']; ?> </td>
								<td> <?php echo $a[0]; ?> </td>
								<td> <?php echo $a[1]; ?> </td>
								<td> <?php echo $a[2]; ?> </td>
								<td> <?php echo $a[3]; ?> </td>
								<td> <?php echo $a[4]; ?> </td>
								<td> <?php echo $a[5]; ?> </td>
								<td> <?php echo $a[6]; ?> </td>
								<td> <?php echo $a[7] . ' ' . $a[8]; ?> </td>
								<td> <?php echo $a[9] . ' ' .$a[10]; ?> </td>
								<td> <?php echo $a[11]; ?> </td>
								<td> <?php echo $a[12]; ?> </td>
								<td> <?php echo $a[13]; ?> </td>
								<td> <?php echo $a[14]; ?> </td>
								<td> <?php echo $a[15]; ?> </td>
								<td> <?php echo $a[16]; ?> </td>
								<td> <?php echo $a[17]; ?> </td>
								<td> <?php echo $a[18]; ?> </td>
							</tr>
						<?php } ?>
					</tbody>

				</table>
			</div>
		</div>
	</div>

	<div id="error-data" class="tab-pane cont">

		<div class="col-sm-12">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable">
					<thead>
						<tr>
							<th>Servicer Loan Number</th>
							<th>Alternate Ref No</th>
							<th>Loan Number</th>
							<th>Borrower Name 1</th>
							<th>Borrower Name 1</th>
							<th>Borrower Name 2</th>
							<th>Borrower Name 2</th>
							<th>Property Address</th>
							<th>Property City</th>
							<th>Property State</th>
							<th>Property Zip Code</th>
							<th>Origination Date</th>
							<th>Loan Amount</th>
							<th>Mod Balance</th>
							<th>Modification Date</th>
						</tr>
					</thead>
					<?php 
					foreach ($FailedData as $key => $value) { ?>
						<tbody>
							<tr>
								<td> <?php echo $a[3]; ?> </td>
								<td> <?php echo $a[4]; ?> </td>
								<td> <?php echo $a[5]; ?> </td>
								<td> <?php echo $a[6]; ?> </td>
								<td> <?php echo $a[7]; ?> </td>
								<td> <?php echo $a[8]; ?> </td>
								<td> <?php echo $a[9]; ?> </td>
								<td> <?php echo $a[10]; ?> </td>
								<td> <?php echo $a[11]; ?> </td>
								<td> <?php echo $a[12]; ?> </td>
								<td> <?php echo $a[13]; ?> </td>
								<td> <?php echo $a[14]; ?> </td>
								<td> <?php echo $a[15]; ?> </td>
								<td> <?php echo $a[16]; ?> </td>
								<td> <?php echo $a[17]; ?> </td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<input type="hidden" value="<?php echo $InsertedOrderUID; ?>" name="InsertedOrderUID[]" id="InsertedOrderUID">