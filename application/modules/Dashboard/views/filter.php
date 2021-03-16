
<?php 
//$Customers = $this->Dashboard_Model->GetCustomers('all');
$Customers = $this->Common_Model->GetClients();

?>

<style>
.datepicker{
	border: 1px solid #ddd;
	background: #fff;
	cursor: pointer;
	letter-spacing: 1px;
	text-align: center;
	font-weight: 700 !important;
	height: 32px;
	width: 100%;

}
.form-control{
	height:31px;
}

input:focus {
	box-shadow: none !important;
	outline: 0 !important; 
}

input {
	color: transparent !important;
	text-shadow: 0 0 0 #3C4858 !important;
}
input:focus {
	outline: none !important;
}

.date-placeholder{
	font-size: 12px;
	padding: 10px 10px 10px 10px;
}
.select2-container--open {
    z-index: 9999999 !important;
}
.multiselect-container {

  z-index: 9999999 !important;
}
</style>

<!-- FOR SLA INLINE FILTER -->
<input type="hidden" id="chartfilter_fdate" name="chartfilter_fdate" value=""/>
<input type="hidden" id="chartfilter_tdate" name="chartfilter_tdate" value=""/>
<!-- FOR SLA INLINE FILTER -->


<div class="col-md-12 mb-20">
		
	<div class="row">
		<div class="col-md-4">
			<select class="selectCustomer" multiple="multiple" name="Customer" id="DashboardCustomer" placeholder="Select Customer(s)">				
				<?php 
					foreach($Customers as $cust)
					{ 
						echo '<option value="'.$cust->CustomerUID.'">'.$cust->CustomerName.'</option>'; 
					}
				?> 				
			</select>
		</div>
		<div class="col-md-4">
			<select class="selectProduct" multiple="multiple" placeholder="Select Product(s)" name="Product" id="DashboardProduct">
				<?php	foreach($Products as $cust)
					{ 
						echo '<option value="'.$cust->ProductUID.'">'.$cust->ProductName.'</option>'; 
					}
					?>				
			</select>
		</div>
		<div class="col-md-4">
			<select class="selectProject" multiple="multiple" placeholder="Select Project(s)" name="Project" id="DashboardProject">	
				<?php	foreach($Projects as $cust)
					{ 
						echo '<option value="'.$cust->ProjectUID.'">'.$cust->ProjectName.'</option>'; 
					}
					?>

			</select>
		</div> 
	</div>


	<div class="row mt-10">
		<div class="col-md-4">
			<select class="select2picker datefilter_type" id="datefilter_type" name="datefilter_type">			

				<?php 
					$Today = date('Y-m-d');
				?>
				<!-- Last 7 Days -->
				<?php 
					// Last 7 Days
					$Last7Days = date('Y-m-d', strtotime('-7 days'));

					// Last 30 Days
					$Last30Days = date('Y-m-d', strtotime('-30 days'));

					$Last90Days = date('Y-m-d', strtotime('-90 days'));

					// Current Month
					$CurrentMonth = date('Y-m-01');

					// Current Year
					$CurrentYear = date('Y-01-01');

					// Last 6 Months
					$Last6Months = date('Y-m-d', strtotime('-6 months'));

					// Last 3 Months
					$Last3Months = date('Y-m-d', strtotime('-3 months'));

					// Last 12 Months
					$Last12Months = date('Y-m-d', strtotime('-12 months'));
				?>
				<option data-groupby = "MONTH" data-from="<?php echo $Last90Days; ?>" data-to="<?php echo $Today; ?>" value="0" selected>-- Select Filter --</option>
				<option data-groupby = "DATE" data-from="<?php echo $Last7Days; ?>" data-to="<?php echo $Today; ?>">Last 7 Days</option>
				<option data-groupby = "DATE" data-from="<?php echo $Last30Days; ?>" data-to="<?php echo $Today; ?>" >Last 30 days</option>
				<option data-groupby = "DATE" data-from="<?php echo $CurrentMonth; ?>" data-to="<?php echo $Today; ?>">Current Month</option>
				<option data-groupby = "MONTH" data-from="<?php echo $CurrentYear; ?>" data-to="<?php echo $Today; ?>">Current Year</option>
				<option data-groupby = "MONTH" data-from="<?php echo $Last3Months; ?>" data-to="<?php echo $Today; ?>" >Last 3 Months</option>     
				<option data-groupby = "MONTH" data-from="<?php echo $Last6Months; ?>" data-to="<?php echo $Today; ?>">Last 6 Months</option>
				<option data-groupby = "MONTH" data-from="<?php echo $Last12Months; ?>" data-to="<?php echo $Today; ?>">Last 12 Months</option>	

			</select>
		</div>
		<div class="col-md-3">	
			<input type="text" class="datepicker date-placeholder date" id="chartfdate" name="chartfdate" placeholder="Select From Date" value="<?php echo date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));?>"/>
		</div>
		<div class="col-md-2 text-center pd-0">	
			<button class="btn btn-white mt-0">TO</button>
		</div>

		<div class="col-md-3">
			<input type="text" class="datepicker date-placeholder date" id="charttdate" name="charttdate" placeholder="Select To Date" value="<?php echo date('Y-m-d');?>"/>
		</div>
	</div>
</div>


<script>
	$ (document).ready (function () {

		$('.datepicker').datetimepicker({
			icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-chevron-up",
				down: "fa fa-chevron-down",
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-screenshot',
				clear: 'fa fa-trash',
				close: 'fa fa-remove'
			},
			format: 'MM/DD/YYYY'
		});

		$(".datefilter_type").select2({
			//tags: false,
			theme: "bootstrap",
		});

	});
</script>
