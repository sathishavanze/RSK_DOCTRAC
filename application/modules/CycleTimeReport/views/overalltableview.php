<table class="table table-hover table-striped listtable">
	<thead>
		<tr>
			<!-- total order -->
					<th><b>Date </b></th>
					<th><b>Cycle Time </b></th>
					<!-- <th><b>Total Loans </b></th> -->
					<!-- <th><b>Total Average</b></th> -->

					<!-- pending order -->
					<!-- <th><b>Pending Loans</b></th> -->
					<!-- <th><b>Pending Average</b></th> -->

					<!-- funded order -->
					<!-- <th><b>Funded Loans</b></th> -->
					<!-- <th><b>Funded Average</b></th> -->
		</tr>	

	</thead>

	<tbody>
		
			<!-- total order -->
				<?php 
				foreach ($CycleTimeReportrows as $key => $value) 
				{	
					foreach ($value as $key => $cycletimeCount) 
					{
						// if($cycletimeCount->PendingAvg != '')
						// {

						// 	$PendingAvg = round($cycletimeCount->PendingAvg/$cycletimeCount->PendingOrderCount);
						// }
						// else
						// {
						// 	$PendingAvg = 0;
						// }
						if($cycletimeCount->FundedAvg != '')
						{

							$FundedAvg = round($cycletimeCount->FundedAvg/$cycletimeCount->FundedOrderCount);
						}
						else
						{
							$FundedAvg = 0;
						}
					?>
				<tr>
					<td><b><?php echo $cycletimeCount->date; ?></b></td>			
					<!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->TotalOrderUID; ?>" data-title="CycleTime LIST" ><?php  echo $cycletimeCount->PendingOrderCount+$cycletimeCount->FundedOrderCount;?></a></td> -->
					<!-- <td><?php  echo $PendingAvg+$FundedAvg ;?> -->
					<!-- Pending order -->
					<!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->PendingOrderUID; ?>" data-title="CycleTime LIST" ><?php  echo $cycletimeCount->PendingOrderCount;?></a></td> -->
					<!-- <td><?php  echo $PendingAvg ;?></td> -->

					<!-- Funded order -->
					<!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->FundedOrderUID; ?>" data-title="CycleTime LIST" ><?php  echo $cycletimeCount->FundedOrderCount;?></a></td> -->
					<td><?php  echo $FundedAvg ;?></td>
				</tr>	
					
				 <?php } } ?>

	</tbody>  
</table>