<style type="text/css">
	.textbold
	{
		font-weight: bold;
		color: #000;
	}
	.processinflow > tbody > tr:last-child > td > a ,.processinflow > tbody > tr:last-child
	{
		font-weight : bold;
		color : #000 !important;
	}
</style>
<?php if($inflowType=='Team'){ ?>
	<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar inflowReport" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		<thead>
			<tr>
				<th><b>Dates</b></th>
				<th><b>FHA</b></th>
				<th><b>VA</b></th>
				<th><b>Total</b></th>
			</tr>	
		</thead>
		<tbody>
			<?php
			$FHAtotal = 0;
			$VAtotal = 0;
			$TotalCount = 0; 
			foreach ($ProcessInflowReportCounts as $ReportCounts) 
			{ 

				$fha_infloworderuid = '';
				$va_infloworderuid = '';
				$total_infloworderuid = '';
				if($ReportCounts['TotalOrders'] > 0) {

					$ReportCounts['FHACount'] = ($ReportCounts['FHAOrders'] != '') ? count(explode(',', $ReportCounts['FHAOrders'])) : NULL;
					$ReportCounts['VACount'] = ($ReportCounts['VAOrders'] != '') ? count(explode(',', $ReportCounts['VAOrders'])) : NULL;
					$ReportCounts['TotalCount'] = ($ReportCounts['TotalOrders'] != '') ? count(explode(',', $ReportCounts['TotalOrders'])) : NULL;


					$FHAtotal +=$ReportCounts['FHACount'];
					$VAtotal +=$ReportCounts['VACount'];
					$TotalCount +=$ReportCounts['TotalCount'];


					$fha_infloworderuid =  ($ReportCounts["FHAOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$fha_infloworderuid),explode(",", $ReportCounts["FHAOrders"]))))) : NULL;
					$va_infloworderuid =  ($ReportCounts["VAOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$va_infloworderuid),explode(",", $ReportCounts["VAOrders"]))))) : NULL;
					$total_infloworderuid =  ($ReportCounts["TotalOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$total_infloworderuid),explode(",", $ReportCounts["TotalOrders"]))))) : NULL;

					?>
					<tr>

						<td><b><?php echo $ReportCounts['Date']; ?></b></td>

						<td><a href="javascript:;" title="View Loans" data-heading="FHA Inflows - <?php echo $ReportCounts["Date"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['FHAOrders']; ?>"><?php echo $ReportCounts["FHACount"]; ?></a></td>
						<td><a href="javascript:;" title="View Loans" data-heading="VA Inflows - <?php echo $ReportCounts["Date"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['VAOrders']; ?>"><?php echo $ReportCounts["VACount"]; ?></a></td>
						<td><a href="javascript:;" title="View Loans" data-heading="Total Inflows - <?php echo $ReportCounts["Date"]; ?>" class="listorders textbold" data-orderid="<?php echo $ReportCounts['TotalOrders']; ?>"><?php echo $ReportCounts["TotalCount"]; ?></a></td>

					</tr>        
				<?php } 
			} ?>
			<tr>
				<td><span class="text-bold">Total</span></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total FHA Inflows" class="listorders textbold" data-orderid="<?php echo $fha_infloworderuid; ?>" class="text-bold"><?php echo $FHAtotal; ?></a></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total VA Inflows" class="listorders textbold" data-orderid="<?php echo $va_infloworderuid; ?>" class="text-bold"><?php echo $VAtotal; ?></a></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total Inflows" class="listorders textbold" data-orderid="<?php echo $total_infloworderuid; ?>" class="text-bold"><?php echo $TotalCount; ?></a></td>
			</tr> 
		</tbody>  
	</table>

<?php }else{ ?>

	<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar inflowReport" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		<thead>
			<tr>
				<th><b>UserName</b></th>
				<th><b>FHA</b></th>
				<th><b>VA</b></th>
				<th><b>Total</b></th>
			</tr>  

		</thead>

		<tbody>
			<?php
			$FHAtotal = 0;
			$VAtotal = 0;
			$TotalCount = 0;
			foreach ($ProcessInflowReportCounts as $ReportCounts) 
			{   
				$fha_infloworderuid = '';
				$va_infloworderuid = '';
				$total_infloworderuid = '';
				if($ReportCounts['TotalOrders'] > 0){


					$ReportCounts['FHACount'] = ($ReportCounts['FHAOrders'] != '') ? count(explode(',', $ReportCounts['FHAOrders'])) : NULL;
					$ReportCounts['VACount'] = ($ReportCounts['VAOrders'] != '') ? count(explode(',', $ReportCounts['VAOrders'])) : NULL;
					$ReportCounts['TotalCount'] = ($ReportCounts['TotalOrders'] != '') ? count(explode(',', $ReportCounts['TotalOrders'])) : NULL;

					$FHAtotal +=$ReportCounts['FHACount'];
					$VAtotal +=$ReportCounts['VACount'];
					$TotalCount +=$ReportCounts['TotalCount'];


					$fha_infloworderuid =  ($ReportCounts["FHAOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$fha_infloworderuid),explode(",", $ReportCounts["FHAOrders"]))))) : NULL;
					$va_infloworderuid =  ($ReportCounts["VAOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$va_infloworderuid),explode(",", $ReportCounts["VAOrders"]))))) : NULL;
					$total_infloworderuid =  ($ReportCounts["TotalOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$total_infloworderuid),explode(",", $ReportCounts["TotalOrders"]))))) : NULL;

					?>
					<tr>
						<td><b><?php echo $ReportCounts['UserName']; ?></b></td>
						<td><a href="javascript:;" title="View Loans" data-heading="FHA Inflows - <?php echo $ReportCounts["UserName"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['FHAOrders']; ?>"><?php echo $ReportCounts["FHACount"]; ?></a></td>
						<td><a href="javascript:;" title="View Loans" data-heading="VA Inflows - <?php echo $ReportCounts["UserName"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['VAOrders']; ?>"><?php echo $ReportCounts["VACount"]; ?></a></td>
						<td><a href="javascript:;" title="View Loans" data-heading="Total Inflows - <?php echo $ReportCounts["UserName"]; ?>" class="listorders textbold" data-orderid="<?php echo $ReportCounts['TotalOrders']; ?>"><?php echo $ReportCounts["TotalCount"]; ?></a></td>
					</tr>  
				<?php  }
			}   ?>       
			<tr>
				<td><span class="text-bold">Total</span></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total FHA Inflows" class="listorders textbold" data-orderid="<?php echo $fha_infloworderuid; ?>" class="text-bold"><?php echo $FHAtotal; ?></a></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total VA Inflows" class="listorders textbold" data-orderid="<?php echo $va_infloworderuid; ?>" class="text-bold"><?php echo $VAtotal; ?></a></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total Inflows" class="listorders textbold" data-orderid="<?php echo $total_infloworderuid; ?>" class="text-bold"><?php echo $TotalCount; ?></a></td>
			</tr>         

		</tbody>  
	</table>
	<?php } ?>