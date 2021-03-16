<style type="text/css">
	.textbold
	{
		font-weight: bold;
		color: #000;
	}
	.processFollowUp > tbody > tr:last-child > td > a ,.processFollowUp > tbody > tr:last-child
	{
		font-weight : bold;
		color : #000 !important;
	}
</style>
<?php if($FollowUpType=='Team'){ ?>
	<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar FollowUpReport" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		<thead>
			<tr>
				<th><b>Dates</b></th>
				<th><b>Total Cleared</b></th>
			</tr>	
		</thead>
		<tbody>
			<?php
			$TotalClearedtotal = 0;
			$TotalCleared_FollowUporderuid = '';
			foreach ($ProcessFollowUpReportCounts as $ReportCounts) 
			{ 

				if($ReportCounts['TotalCleared'] > 0) {

					$ReportCounts['TotalClearedCount'] = ($ReportCounts['TotalCleared'] != '') ? count(explode(',', $ReportCounts['TotalCleared'])) : NULL;

					$TotalClearedtotal +=$ReportCounts['TotalClearedCount'];

					$TotalCleared_FollowUporderuid =  ($ReportCounts["TotalCleared"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$TotalCleared_FollowUporderuid),explode(",", $ReportCounts["TotalCleared"]))))) : NULL;

					?>
					<tr>

						<td><b><?php echo $ReportCounts['Date']; ?></b></td>

						<td><a href="javascript:;" title="View Loans" data-heading="FollowUp Cleared - <?php echo $ReportCounts["Date"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['TotalCleared']; ?>"><?php echo $ReportCounts["TotalClearedCount"]; ?></a></td>

					</tr>        
				<?php } 
			} ?>
			<tr>
				<td><span class="text-bold">Total Cleared</span></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total FollowUp Cleared" class="listorders textbold" data-orderid="<?php echo $TotalCleared_FollowUporderuid; ?>" class="text-bold"><?php echo $TotalClearedtotal; ?></a></td>
			</tr> 
		</tbody>  
	</table>

<?php }else{ ?>

	<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar FollowUpReport" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		<thead>
			<tr>
				<th><b>UserName</b></th>
				<th><b>Total Cleared</b></th>
			</tr>  

		</thead>

		<tbody>
			<?php
			$TotalClearedtotal = 0;
			$TotalCleared_FollowUporderuid = '';
			foreach ($ProcessFollowUpReportCounts as $ReportCounts) 
			{   
				if($ReportCounts['TotalCleared'] > 0){


					$ReportCounts['TotalClearedCount'] = ($ReportCounts['TotalCleared'] != '') ? count(explode(',', $ReportCounts['TotalCleared'])) : NULL;

					$TotalClearedtotal +=$ReportCounts['TotalClearedCount'];


					$TotalCleared_FollowUporderuid =  ($ReportCounts["TotalCleared"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$TotalCleared_FollowUporderuid),explode(",", $ReportCounts["TotalCleared"]))))) : NULL;

					?>
					<tr>
						<td><b><?php echo $ReportCounts['UserName']; ?></b></td>
						<td><a href="javascript:;" title="View Loans" data-heading="FollowUp Cleared - <?php echo $ReportCounts["UserName"]; ?>" class="listorders" data-orderid="<?php echo $ReportCounts['TotalCleared']; ?>"><?php echo $ReportCounts["TotalClearedCount"]; ?></a></td>
					</tr>  
				<?php  }
			}   ?>       
			<tr>
				<td><span class="text-bold">Total</span></td>
				<td><a href="javascript:;" title="View Loans" data-heading="Total FollowUp Cleared" class="listorders textbold" data-orderid="<?php echo $TotalCleared_FollowUporderuid; ?>" class="text-bold"><?php echo $TotalClearedtotal; ?></a></td>
			</tr>         

		</tbody>  
	</table>
	<?php } ?>