<table  class="display dataTable table table-striped table-hover table-actions-bar queue-aging" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
	<thead>
		<tr>
			<th></th>
			<?php $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

			foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
				<th><?php echo $AgingHeadervalue; ?></th>
			<?php } ?>
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
				<td><b><?php echo $row["CategoryName"]; ?></b></td>
				<?php foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
					<?php $row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; ?>
					<td><a href="javascript:;" title="View Loans" data-heading="Aging - <?php echo $row["CategoryName"]; ?>" class="listorders" data-orderid="<?php echo $row[$AgingHeaderkey]; ?>"><?php echo $row[$AgingHeaderkey.'count']; ?></a></td>

					<?php 
					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;  ?>
					<?php 
					//$finaltotalsum[$AgingHeaderkey] = $finaltotalsum + $totalsum;
					//$finalnoofvalues[$AgingHeaderkey] = $noofvalues + $finalnoofvalues;

					?>
				<?php } ?>

			</tr>        
			<?php 
		}
		?>

	</tbody>  


</table>

