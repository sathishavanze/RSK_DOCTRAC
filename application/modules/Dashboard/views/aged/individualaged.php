<table  class="display dataTable table table-striped table-hover table-actions-bar queue-aging" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
	<thead>
		<tr>
			<th>UserName</th>
			<?php $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

			foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
				<th><?php echo $AgingHeadervalue; ?></th>
			<?php } ?>
			<th>Average</th>

		</tr>
	</thead>

	<tbody>
		<?php 
		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($ReportCounts as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;
			?>
			<tr>
				<td><b><?php echo $row["UserName"]; ?></b></td>
				<?php foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
					<?php $row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; ?>
					<td><a href="javascript:;" title="View Loans" data-heading="Aging - <?php echo $row["UserName"]; ?>" class="listorders" data-orderid="<?php echo $row[$AgingHeaderkey]; ?>"><?php echo $row[$AgingHeaderkey.'count']; ?></a></td>

					<?php 
					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;  ?>
					<?php 
					//$finaltotalsum[$AgingHeaderkey] = $finaltotalsum + $totalsum;
					//$finalnoofvalues[$AgingHeaderkey] = $noofvalues + $finalnoofvalues;

					?>
				<?php } ?>
				<td><span class="text-bold"><?php echo !empty($totalsum) && !empty($noofvalues) ? $totalsum/$noofvalues : ''; ?></span></td>

			</tr>        
			<?php 
		}
		?>

		<!-- <tr>
			<td><span class="text-bold">Total</span></td>
			<?php foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
				<td><?php echo isset($finalnoofvalues[$AgingHeaderkey]) ? $finalnoofvalues[$AgingHeaderkey] : 0; ?></td>
			<?php } ?>
			<td><span class="text-bold"><?php echo !empty($tot_complete) && !empty($tot_flow) ? percent($tot_complete,$tot_flow) : 0; ?>%</span></td>
		</tr> --> 

	</tbody>  


</table>



