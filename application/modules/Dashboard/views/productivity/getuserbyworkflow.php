		

<table class="table table-striped table-hover table-actions-bar productivity-individual" style="border-collapse: collapse; border-spacing: 0;width: 100%;">
	<thead>
		<tr>
			<th>UserName</th>
			<th>Completed</th>
			<th>Productivity</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i=0; 
		$totalCount=0;
		$TotalTarget=0;
		$tot_completeorderuid = '';

		$TotalTarget=$daycount*$Target;
		
		foreach($ReportCounts as $row): 
			if($row['CompletedOrders']>0){
				$row['CompleteCount'] = ($row['CompletedOrders'] != '') ? count(explode(',', $row['CompletedOrders'])) : NULL;

				$totalCount+=$row['CompleteCount'];
				$tot_completeorderuid =  ($row["CompletedOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$tot_completeorderuid),explode(",", $row["CompletedOrders"]))))) : NULL;

				?>
				<tr>
					<td><?php echo $row['UserName']; ?></td>
					<td><a href="javascript:;" title="View Loans" data-heading="Productivity - <?php echo $row["UserName"]; ?>" class="listorders" data-orderid="<?php echo $row['CompletedOrders']; ?>"><?php echo $row["CompleteCount"]; ?></a></td>
					<td><?php echo $row['CompleteCount'] && $TotalTarget > 0 ? round(($row['CompleteCount']/$TotalTarget)*100 , 2) : 0; ?>%</td>
				</tr>

				<?php 
				$i++;
			}
		endforeach; 
		$Target=$TotalTarget*$i; ?>
		<tr>
			<td><span class="text-bold"><?php echo "Total"; ?></span></td>
			<td><a href="javascript:;" title="View Loans" data-heading="Productivity - Individual Total" class="listorders text-bold" data-orderid="<?php echo $tot_completeorderuid; ?>"><?php echo $totalCount; ?></a></td>
			<td><span class="text-bold"><?php echo ($totalCount>0) && $Target > 0 ? round(($totalCount/$Target)*100 , 2) : 0; ?>%</span></td>
		</tr>
	</tbody>
</table>
