<style type="text/css">
	.tblcol1 {
		width: 30%;
		text-align: left;
	}
	.tblcol2 {
		width: 5%;
	}
	.tblcol3 {
		width: 65%;
		text-align: left;
	}
	#LoanInfoLabel {
		font-size: 21px;
		font-weight: bold;
		margin-bottom: -10px;
		margin-top: 5px;
	}
	.lblheading {
		font-size: 14px;
	}
	.lblloaninfo {
		font-size: 14px;
	}
</style>
<!-- ORDER REVERSE POPUP CONTENT STARTS -->                

<div class="fulltopmodal modal fade" id="modal-LoanInfo" tabindex="-1" role="dialog" aria-labelledby="LoanInfoLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<span class="modal-title" id="LoanInfoLabel">Loan Info</span>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table style="width:100%" class="table table-striped display nowrap">
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Loan Number </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->LoanNumber; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Borrower Name </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->BorrowerName; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Zip Code </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->ZipCode; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">DOB </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->DOB; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Loan Officer- Name </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->MP; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Processor Contact Phone Number </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->PhoneNumber; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Hours of Operations </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->HoursofOperation; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Email Address </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->BwrEmail; ?>
									</span>
								</td>
							</tr>
							<!-- <tr>
								<td class="tblcol1">
									<span class="lblheading">Mr.Cooper.com- Access </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php // echo $LoanInfo-> ?>
									</span>
								</td>
							</tr> -->
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Term  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->Term; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Product  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->LoanType; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Rate </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->NoteRate; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Estimated Payment </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php // echo $LoanInfo-> ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">STC </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php // echo $LoanInfo-> ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Vesting  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php // echo $LoanInfo-> ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Pending Borrower Docs  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->PendingDocs; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">Title  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->TitleSubQueueIssue; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">HOI  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->HOISubQueueIssue; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="tblcol1">
									<span class="lblheading">FHA  </span></td>
								<td class="tblcol2"> : </td>
								<td class="tblcol3">
									<span class="lblloaninfo">
										<?php echo $LoanInfo->FHASubQueueIssue; ?>
									</span>
								</td>
							</tr>
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