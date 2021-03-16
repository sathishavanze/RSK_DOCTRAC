<table class="table table-hover table-striped listtable">
	<thead>
		<tr>
			<th></th>
			<?php
			foreach ($ProcessUsers as $value) 
				{ ?>
					<th><?php echo $value->UserName;?></th>
				<?php } ?>
			</tr>

			<tr>
				<th><b>Dates</b></th>
				<?php
				foreach ($ProcessUsers as $value) 
					{ ?>
						<th><b>Cycle Time</b></th>
						<!-- <th><b>Funded Average</b></th> -->
					<?php } ?>	
				</tr>	

			</thead>

			<tbody>
				<!-- total order -->
				<?php 
				foreach ($CycleTimeReportrows as $key => $cycletimeCount) 
				{	 ?>
							<tr>
								<td><b><?php echo $cycletimeCount->date; ?></b></td>	
								<?php foreach ($ProcessUsers as $key => $Process) {
									if($cycletimeCount->{'FundedAvg'.$Process->UserUID} != '')
									{

										$FundedAvg = round($cycletimeCount->{'FundedAvg'.$Process->UserUID}/$cycletimeCount->{'FundedOrderCount'.$Process->UserUID});
									}
									else
									{
										$FundedAvg = 0;
									} ?>

									<!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->{'FundedOrderUID'.$Process->UserUID}; ?>" data-title="CycleTime Agent LIST" ><?php  echo $cycletimeCount->{'FundedOrderCount'.$Process->UserUID};?></a></td> -->
									<td><?php  echo $FundedAvg ;?></td>
								<?php } ?>		
								<!-- Funded order -->
							</tr>	

						<?php  } ?>

					</tbody>  
				</table>