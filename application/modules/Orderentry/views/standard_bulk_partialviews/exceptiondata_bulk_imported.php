<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small>
			Imported&nbsp;<i class="fa fa-check-circle"></i></small>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#error-data" role="tablist"><small>
			Not Imported&nbsp;<i class="fa fa-times-circle-o"></i></small>
		</a>
	</li>
</ul>
<div class="tab-content tab-space">
	<div id="success-table" class="tab-pane active cont">

		<!-- <div class="mb-20">
			<button type="button" class="btn btn-success" id="pdfimport">PDF</button>
			<button type="button" id="excelimport" class="btn btn-success">Excel</button>
		</div> -->
		<div class="">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="importdata">
					<thead>
						<tr>
							<th>Exception ID</th>
							<th>Client Name</th>
							<th>Client Code</th>
							<th>Project Name</th>
							<th>Project Code</th>
							<th>Loan Number</th>
							<th>BussinessChannel</th>
							<th>SIMO Loan Number</th>
							<th>Loan Reference Number</th>
							<th>Seller Loan Number</th>
							<th>Title order Number</th>
							<th>Servicer Loan Number</th>
							<th>Loan Amount</th>
							<th>Loan Type</th>
							<th>Loan Purpose</th>
							<th>MOM Flag</th>
							<th>SIMO MOM Flag</th>
							<th>MIN</th>
							<th>SIMO MIN</th>
							<th>Loan Priority Flag</th>
							<th>Loan Priority Score</th>
							<th>Borrower Name</th>
							<th>Property Address Line 1</th>
							<th>Property Address Line 2</th>
							<th>Property Address Line 3</th>
							<th>Property Address Line 4</th>
							<th>Property City</th>
							<th>Property State</th>
							<th>Property Zip Code</th>
							<th>Property County</th>
							<th>Closing Date</th>
							<th>Disbursement Date</th>
							<th>Funding Date</th>
							<th>Payoff Date</th>
							<th>Servicer Name</th>
							<th>Sub Servicer name</th>
							<th>Purchase Date</th>
							<th>Purchased Loan Transferred date</th>
							<th>Trade Commitment Date</th>
							<th>Loan Pool Number</th>
							<th>Loan Pool Certification Date</th>
							<th>Loan Pool Due Date</th>
							<th>Loan Pool Recertification Date</th>
							<th>Case Number</th>
							<th>Certificate Number</th>
							<th>Certificate Issued Date</th>
							<th>TPO Company Name</th>
							<th>TPO Company Reference</th>
							<th>TPO Company Phone #</th>
							<th>TPO Company Fax #</th>
							<th>TPO Company Email</th>
							<th>TPO Contact First Name</th>
							<th>TPO Contact Last Name</th>
							<th>TPO Contact Phone #</th>
							<th>TPO Contact Email</th>
							<th>Settlement Agent Name</th>
							<th>Settlement Agent Phone #</th>
							<th>Settlement Agent Fax #</th>
							<th>Settlement Agent Email</th>
							<th>Settlement Agent Contact First Name</th>
							<th>Settlement Agent Contact Last Name</th>
							<th>Settlement Agent Contact Phone #</th>
							<th>Settlement Agent Contact Email</th>
							<th>Agent #</th>
							<th>Agent CPL#</th>
							<th>Investor #</th>
							<th>Investor Name</th>
							<th>Investor loan Number</th>
							<th>Custodian Name</th>
							<th>Custodian Code</th>
							<th>Custodian Loan Number</th>
							<th>Title Underwriter Company Name</th>
							<th>Document Reference Number</th>
							<th>Document Type</th>
							<th>Document Name</th>
							<th>Document Status</th>
							<th>Document Status Date</th>
							<th>Document eRecorded Flag</th>
							<th>Document Sent to County Date</th>
							<th>Document Returned from County Date</th>
							<th>Gap Mortgage Amount</th>
							<th>Client Processing Rule</th>
							<th>Second Lien Flag</th>
							<th>eLoan</th>
							<th>Custom Field 1</th>
							<th>Custom Field 2</th>
							<th>Custom Field 3</th>
							<th>Custom Field 4</th>
							<th>Custom Field 5</th>
							<th>Custom Field 6</th>
							<th>Custom Field 7</th>
							<th>Custom Field 8</th>
							<th>CD Recording Fee - Mortgage</th>
							<th>CD Recording Fee- Deed</th>
							<th>Product</th>
							<th>Lender</th>
							<th>File Available</th>

						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($SuccessData as $key => $a) {
							?>
							<tr>
								<td><?php echo $a[$columnvariables['DataExceptionUID']]; ?> </td>
								<td><?php echo $a[$columnvariables['ClientName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ClientCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProjectName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProjectCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['BussinessChannel']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanReferenceNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['SellerLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['TitleorderNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['ServicerLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanAmount']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanType']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPurpose']]; ?> </td>
								<td><?php echo $a[$columnvariables['MOMFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOMOMFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['MIN']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOMIN']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPriorityFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPriorityScore']]; ?> </td>
								<td><?php echo $a[$columnvariables['BorrowerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress1']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress2']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress3']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress4']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyCityName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyStateCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyZipCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyCountyName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ClosingDateTime']]; ?> </td>
								<td><?php echo $a[$columnvariables['DisbursementDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['FundingDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['PayoffDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['ServicerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SubServicerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PurchaseDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['PurchasedLoanTransferredDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['TradeCommitmentDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolCertificationDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolDueDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolRecertificationDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['CaseNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CertificateNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CertificateIssuedDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderName']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderRefNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderFaxNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactFirstName']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactLastName']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentPhone']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentFax']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactFirstName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactLastName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['AgentNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['AgentCPL']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorName']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianName']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['UnderwriterName']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocRefNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['InputDocTypeUID']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocName']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocStatus']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocStatusDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['DoceRecordedFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocSentToCountyDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocReturnedFromCountyDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['GAPMortgageAmount']]; ?> </td>
								<td><?php echo $a[$columnvariables['Comments']]; ?> </td>
								<td><?php echo $a[$columnvariables['SecondLienFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['eLoan']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField1']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField2']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField3']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField4']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField5']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField6']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField7']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField8']]; ?> </td>
								<td><?php echo $a[$columnvariables['CDRecordingFeeMortgage']]; ?> </td>
								<td><?php echo $a[$columnvariables['CDRecordingFeeDeed']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProductName']]; ?> </td>
								<td><?php echo $a[$columnvariables['LenderName']]; ?> </td>
								<td><?php echo $a[$columnvariables['FileAvailable']]; ?> </td>

							</tr>
						<?php } ?>
					</tbody>

				</table>
			</div>
		</div>
	</div>

	<div id="error-data" class="tab-pane cont">
		<!-- <div class=mb-20">
			<button type="button" class="btn btn-success" id="pdferror">PDF</button>
			<button type="button" id="excelerror" class="btn btn-success">Excel</button>
		</div> -->
		<div class="">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable">
					<thead>
						<tr>
							<th>Order Number</th>
							<th>Client Name</th>
							<th>Client Code</th>
							<th>Project Name</th>
							<th>Project Code</th>
							<th>Loan Number</th>
							<th>BussinessChannel</th>
							<th>SIMO Loan Number</th>
							<th>Loan Reference Number</th>
							<th>Seller Loan Number</th>
							<th>Title order Number</th>
							<th>Servicer Loan Number</th>
							<th>Loan Amount</th>
							<th>Loan Type</th>
							<th>Loan Purpose</th>
							<th>MOM Flag</th>
							<th>SIMO MOM Flag</th>
							<th>MIN</th>
							<th>SIMO MIN</th>
							<th>Loan Priority Flag</th>
							<th>Loan Priority Score</th>
							<th>Borrower Name</th>
							<th>Property Address Line 1</th>
							<th>Property Address Line 2</th>
							<th>Property Address Line 3</th>
							<th>Property Address Line 4</th>
							<th>Property City</th>
							<th>Property State</th>
							<th>Property Zip Code</th>
							<th>Property County</th>
							<th>Closing Date</th>
							<th>Disbursement Date</th>
							<th>Funding Date</th>
							<th>Payoff Date</th>
							<th>Servicer Name</th>
							<th>Sub Servicer name</th>
							<th>Purchase Date</th>
							<th>Purchased Loan Transferred date</th>
							<th>Trade Commitment Date</th>
							<th>Loan Pool Number</th>
							<th>Loan Pool Certification Date</th>
							<th>Loan Pool Due Date</th>
							<th>Loan Pool Recertification Date</th>
							<th>Case Number</th>
							<th>Certificate Number</th>
							<th>Certificate Issued Date</th>
							<th>TPO Company Name</th>
							<th>TPO Company Reference</th>
							<th>TPO Company Phone #</th>
							<th>TPO Company Fax #</th>
							<th>TPO Company Email</th>
							<th>TPO Contact First Name</th>
							<th>TPO Contact Last Name</th>
							<th>TPO Contact Phone #</th>
							<th>TPO Contact Email</th>
							<th>Settlement Agent Name</th>
							<th>Settlement Agent Phone #</th>
							<th>Settlement Agent Fax #</th>
							<th>Settlement Agent Email</th>
							<th>Settlement Agent Contact First Name</th>
							<th>Settlement Agent Contact Last Name</th>
							<th>Settlement Agent Contact Phone #</th>
							<th>Settlement Agent Contact Email</th>
							<th>Agent #</th>
							<th>Agent CPL#</th>
							<th>Investor #</th>
							<th>Investor Name</th>
							<th>Investor loan Number</th>
							<th>Custodian Name</th>
							<th>Custodian Code</th>
							<th>Custodian Loan Number</th>
							<th>Title Underwriter Company Name</th>
							<th>Document Reference Number</th>
							<th>Document Type</th>
							<th>Document Name</th>
							<th>Document Status</th>
							<th>Document Status Date</th>
							<th>Document eRecorded Flag</th>
							<th>Document Sent to County Date</th>
							<th>Document Returned from County Date</th>
							<th>Gap Mortgage Amount</th>
							<th>Client Processing Rule</th>
							<th>Second Lien Flag</th>
							<th>eLoan</th>
							<th>Custom Field 1</th>
							<th>Custom Field 2</th>
							<th>Custom Field 3</th>
							<th>Custom Field 4</th>
							<th>Custom Field 5</th>
							<th>Custom Field 6</th>
							<th>Custom Field 7</th>
							<th>Custom Field 8</th>
							<th>CD Recording Fee - Mortgage</th>
							<th>CD Recording Fee- Deed</th>
							<th>Product</th>
							<th>Lender</th>
							<th>File Available</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						foreach ($FailedData as $key => $a) { ?>
							<tr>
								<td> <?php echo $a['result']['OrderNumber']; ?> </td>
								<td><?php echo $a[$columnvariables['ClientName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ClientCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProjectName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProjectCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['BussinessChannel']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanReferenceNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['SellerLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['TitleorderNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['ServicerLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanAmount']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanType']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPurpose']]; ?> </td>
								<td><?php echo $a[$columnvariables['MOMFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOMOMFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['MIN']]; ?> </td>
								<td><?php echo $a[$columnvariables['SIMOMIN']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPriorityFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPriorityScore']]; ?> </td>
								<td><?php echo $a[$columnvariables['BorrowerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress1']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress2']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress3']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyAddress4']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyCityName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyStateCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyZipCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['PropertyCountyName']]; ?> </td>
								<td><?php echo $a[$columnvariables['ClosingDateTime']]; ?> </td>
								<td><?php echo $a[$columnvariables['DisbursementDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['FundingDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['PayoffDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['ServicerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SubServicerName']]; ?> </td>
								<td><?php echo $a[$columnvariables['PurchaseDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['PurchasedLoanTransferredDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['TradeCommitmentDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolCertificationDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolDueDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['LoanPoolRecertificationDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['CaseNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CertificateNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CertificateIssuedDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderName']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderRefNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderFaxNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['CorrespondentLenderEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactFirstName']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactLastName']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['TPOContactEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentPhone']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentFax']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactFirstName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactLastName']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactPhoneNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['SettlementAgentContactEmail']]; ?> </td>
								<td><?php echo $a[$columnvariables['AgentNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['AgentCPL']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorName']]; ?> </td>
								<td><?php echo $a[$columnvariables['InvestorLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianName']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianCode']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustodianLoanNumber']]; ?> </td>
								<td><?php echo $a[$columnvariables['UnderwriterName']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocRefNo']]; ?> </td>
								<td><?php echo $a[$columnvariables['InputDocTypeUID']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocName']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocStatus']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocStatusDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['DoceRecordedFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocSentToCountyDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['DocReturnedFromCountyDate']]; ?> </td>
								<td><?php echo $a[$columnvariables['GAPMortgageAmount']]; ?> </td>
								<td><?php echo $a[$columnvariables['Comments']]; ?> </td>
								<td><?php echo $a[$columnvariables['SecondLienFlag']]; ?> </td>
								<td><?php echo $a[$columnvariables['eLoan']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField1']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField2']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField3']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField4']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField5']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField6']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField7']]; ?> </td>
								<td><?php echo $a[$columnvariables['CustomField8']]; ?> </td>
								<td><?php echo $a[$columnvariables['CDRecordingFeeMortgage']]; ?> </td>
								<td><?php echo $a[$columnvariables['CDRecordingFeeDeed']]; ?> </td>
								<td><?php echo $a[$columnvariables['ProductName']]; ?> </td>
								<td><?php echo $a[$columnvariables['LenderName']]; ?> </td>
								<td><?php echo $a[$columnvariables['FileAvailable']]; ?> </td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<input type="hidden" value="<?php echo $InsertedOrderUID; ?>" name="InsertedOrderUID[]" id="InsertedOrderUID">
