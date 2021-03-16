<?php // echo '<pre>';print_r($holidayDates);exit; ?>
<style type="text/css">
	.tblcol1 {
		width: 10%;
		text-align: left;
	}
	.tblcol2 {
		width: 15%;
	}
	.tblcol3 {
		width: 4%;
		text-align: left;
	}
	.tblcol4 {
		width: 70%%;
		text-align: left;
	}
	#HolidayListLabel {
		font-size: 21px;
		font-weight: bold;
		margin-bottom: -10px;
		margin-top: 5px;
	}
	.LabelHolidayDate {
		font-size: 14px;
	}
	.LabelHolidayHeading {
		font-size: 14px;
	}
</style>
<!-- ORDER REVERSE POPUP CONTENT STARTS -->                

<div class="fulltopmodal modal fade" id="modal-HolidayList" tabindex="-1" role="dialog" aria-labelledby="HolidayListLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<span class="modal-title" id="HolidayListLabel">Holiday List</span>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table style="width:100%" class="table table-striped display nowrap">
							<tr>
								<th class="tblcol1">S.No</th>
								<th class="tblcol2">Date</th>
								<th class="tblcol3"></th>
								<th class="tblcol4">Description</th>
							</tr>
							<?php 
							$SNo = 0;
							foreach ($HolidayDetails as $value) { ?>
							    <tr>
							    	<td><?php echo ++$SNo.'.'; ?></td>
									<td>
										<span class="LabelHolidayDate"><?php echo date('m/d/Y', strtotime($value->HolidayDate)); ?></span></td>
									<td> : </td>
									<td>
										<span class="LabelHolidayHeading">
											<?php echo $value->HolidayDescription; ?>
										</span>
									</td>
								</tr>
							<?php } ?>							
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- ORDER REVERSE POPUP CONTENT ENDS -->  