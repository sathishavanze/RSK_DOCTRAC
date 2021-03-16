<style type="text/css">
th,tr{
	font-size: 11px;
}
.select2-container--bootstrap .select2-selection {
	font-size: 11px;
}

.select2-container--bootstrap .select2-results__option--highlighted[aria-selected] {
	font-size: 11px;
}

.select2-container--bootstrap .select2-results__option {
	font-size: 11px;
}

.pointer:hover{
	cursor: pointer;
}

.disabled:hover {
	opacity: 0.65;
	cursor: not-allowed !important;
}
a.viewFile {
	color: #2196f3;
	border: 1px solid #2196f3;
	padding: 5px;
	border-radius: 5px;
}

.calendar-icon {
	position: absolute;
	right: -10px !important;
	top: 18px;
	border-bottom: 0px solid #bfbfbf !important;
}

</style>
<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />


<!-- content start -->
<div class="panel panel-default panel-border-color panel-border-color-primary">
	<div class="panel-body" style="padding: 0px;">
		<div class="row">
			<form action="#" name="frm_abstractor" id="frm_abstractor" accept-charset="multipart/form-data" onsubmit="return false">
				<input  type="hidden" id="AbstractorUID" name="AbstractorUID" value="<?php echo $AbstractorDetails->AbstractorUID;?>">
				<div class="col-sm-12">

					<div class="panel-heading" style="padding: 1px 1px 1px 1px; background-color: #eee ; margin-top: 20px; padding-left: 0px;">
						<p style="margin: 5px; font-size: 16px; color: #000; font-weight: bold">&nbsp; Abstractor Details </p>
					</div>

					<div class="col-sm-12 form-group bmd-form-group">
						<div class="row">
							<div class="col-sm-4 form-group bmd-form-group">
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
									<label class="mdl-textfield__label" for="FirstName">First Name <span style="color: red">*</span></label>
									<input class="form-control" type="text" id="FirstName" name="FirstName" value="<?php echo $AbstractorDetails->AbstractorFirstName;?>">
									<span class="mdl-textfield__error form_error"></span>
								</div>
							</div>

							<div class="col-sm-4 form-group bmd-form-group">
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
									<label class="mdl-textfield__label" for="LastName">Last Name </label>
									<input class="form-control" type="text" id="LastName" name="LastName" value="<?php echo $AbstractorDetails->AbstractorLastName;?>">
									<span class="mdl-textfield__error form_error"></span>
								</div>
							</div>

							<div class="col-sm-4 form-group bmd-form-group">
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
									<label class="mdl-textfield__label" for="ContactNumber">Contact Number <span style="color: red">*</span></label>
									<input class="form-control" type="text" id="ContactNumber" name="ContactNumber" data-mask="phone" value="<?php echo $AbstractorDetails->Mobile;?>">
									<span class="mdl-textfield__error form_error"></span>
								</div>
							</div></div>

						</div>
						<div class="col-sm-12 form-group bmd-form-group">
							<div class="row">
								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorEmail">Contact Email Id <span style="color: red">*</span></label>
										<input class="form-control" type="email" id="AbstractorEmail" name="AbstractorEmail" value="<?php echo $AbstractorDetails->Email;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorPaypal">Paypal Email Id </label>
										<input class="form-control" type="email" id="AbstractorPaypal" name="AbstractorPaypal" value="<?php echo $AbstractorDetails->PaypalEmailID;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Address Details -->

					<div class="col-sm-12">

						<div class="panel-heading" style="padding: 1px 1px 1px 1px; background-color: #eee ; margin-top: 20px; padding-left: 0px;">
							<p style="margin: 5px; font-size: 16px; color: #000; font-weight: bold">&nbsp; Address Details </p>
						</div>
						<div class="col-sm-12 form-group bmd-form-group">
							<div class="row">
								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorAddress1">Address 1 <span style="color: red">*</span></label>
										<input class="form-control" type="text" id="AbstractorAddress1" name="AbstractorAddress1" value="<?php echo $AbstractorDetails->AddressLine1;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorAddress2">Address 2</label>
										<input class="form-control" type="text" id="AbstractorAddress2" name="AbstractorAddress2" value="<?php echo $AbstractorDetails->AddressLine2;?>">
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorZipCode">Zipcode <span style="color: red">*</span></label>
										<input class="form-control" type="text" id="AbstractorZipCode" name="AbstractorZipCode" value="<?php echo $AbstractorDetails->ZipCode;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorCityUID">City <span style="color: red">*</span></label>
										<select class="select2picker" id="AbstractorCityUID" name="AbstractorCityUID">
											<?php
											foreach ($Cities as $row) {
												if($row->CityUID==$AbstractorDetails->CityUID)
													echo "<option value='".$row->CityUID."' selected>".$row->CityName."</option>";
											}
											?>
										</select>
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorCountyUID">County <span style="color: red">*</span></label>
										<select class="select2picker" id="AbstractorCountyUID" name="AbstractorCountyUID">
											<?php
											foreach ($Counties as $row) {
												if($row->CountyUID==$AbstractorDetails->CountyUID)
													echo "<option value='".$row->CountyUID."' selected>".$row->CountyName."</option>";
											}
											?>
										</select>
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="AbstractorStateUID">State <span style="color: red">*</span></label>
										<select class="select2picker" name="AbstractorStateUID" id="AbstractorStateUID">
											<?php
											foreach ($States as $row) {
												if($row->StateUID==$AbstractorDetails->StateUID)
													echo "<option value='".$row->StateUID."' selected>".$row->StateName."</option>";
											}
											?>
										</select>
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!-- Address Details -->

					<!-- Contact Details -->

					<div class="col-sm-12">

						<div class="panel-heading" style="padding: 1px 1px 1px 1px; background-color: #eee ; margin-top: 20px; padding-left: 0px;">
							<p style="margin: 5px; font-size: 16px; color: #000; font-weight: bold">&nbsp; Contact Details </p>
						</div>
						<div class="col-sm-12 form-group bmd-form-group">
							<div class="row">
								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="OfficePhoneNo">Office Phone Number</label>
										<input class="form-control" type="text" id="OfficePhoneNo" name="OfficePhoneNo" data-mask="phone" value="<?php echo $AbstractorDetails->OfficePhoneNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="OfficeExt">Office Extension Number</label>
										<input class="form-control" type="text" id="OfficeExt" name="OfficeExt" data-mask="phone" value="<?php echo $AbstractorDetails->OfficeExt;?>">
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="BeeperPhoneNo">Beeper Phone Number</label>
										<input class="form-control" type="text" id=" BeeperPhoneNo" name="BeeperPhoneNo" data-mask="phone" value="<?php echo $AbstractorDetails->BeeperPhoneNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="BeeperPhoneExt">Beeper Phone Extension Number</label>
										<input class="form-control" type="text" id="BeeperPhoneExt" name="BeeperPhoneExt" data-mask="phone" value="<?php echo $AbstractorDetails->BeeperPhoneExt;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="CarPhoneNo">Car Phone Number</label>
										<input class="form-control" type="text" id="CarPhoneNo" name="CarPhoneNo" data-mask="phone" value="<?php echo $AbstractorDetails->CarPhoneNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="HomePhoneNo">Home Phone Number</label>
										<input class="form-control" type="text" id="HomePhoneNo" name="HomePhoneNo" data-mask="phone" value="<?php echo $AbstractorDetails->HomePhoneNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="CourtHousePhoneNo">Court House Phone Number</label>
										<input class="form-control" type="text" id="CourtHousePhoneNo" name="CourtHousePhoneNo" data-mask="phone" value="<?php echo $AbstractorDetails->CourtHousePhoneNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="CourtHouseExt">Court House Extension Number</label>
										<input class="form-control" type="text" id="CourtHouseExt" name="CourtHouseExt" data-mask="phone" value="<?php echo $AbstractorDetails->CourtHouseExt;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="Comments">Comments</label>
										<input class="form-control" type="text" id="Comments" name="Comments" value="<?php echo $AbstractorDetails->Comments;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="ALTVendorNo">ALT Vendor Number</label>
										<input class="form-control" type="text" id="ALTVendorNo" name="ALTVendorNo" value="<?php echo $AbstractorDetails->ALTVendorNo;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div>

								<div class="col-sm-4 form-group bmd-form-group">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
										<label class="mdl-textfield__label" for="Contact">Contact</label>
										<input class="form-control" type="text" id="Contact" name="Contact" data-mask="phone" value="<?php echo $AbstractorDetails->Contact;?>">
										<span class="mdl-textfield__error form_error"></span>
									</div>
								</div></div>

							</div>

						</div>
						<!-- Contact Details -->

						<!-- Bank Details -->
						<div class="col-sm-12">

							<div class="panel-heading" style="padding: 1px 1px 1px 1px; background-color: #eee ; margin-top: 20px; padding-left: 0px;">
								<p style="margin: 5px; font-size: 16px; color: #000; font-weight: bold">&nbsp; Bank Details </p>
							</div>
							<div class="col-sm-12 form-group bmd-form-group">
								<div class="row">
									<div class="col-sm-4 form-group bmd-form-group">
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
											<label class="mdl-textfield__label" for="BankAccountnumber">Bank Account Number</label>
											<input class="form-control" type="text" id="BankAccountnumber" name="BankAccountnumber" value="<?php echo $AbstractorDetails->BankAccountNo;?>">
											<span class="mdl-textfield__error form_error"></span>
										</div>
									</div>

									<div class="col-sm-4 form-group bmd-form-group">
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
											<label class="mdl-textfield__label" for="BankName">Bank Name</label>
											<input class="form-control" type="text" id="BankName" name="BankName" value="<?php echo $AbstractorDetails->BankName;?>">
										</div>
									</div>

									<div class="col-sm-4 form-group bmd-form-group">
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
											<label class="mdl-textfield__label" for="BankAddress">Bank Address</label>
											<input class="form-control" type="text" id="BankAddress" name="BankAddress" value="<?php echo $AbstractorDetails->BankAddress;?>">
											<span class="mdl-textfield__error form_error"></span>
										</div>
									</div>

									<div class="col-sm-4 form-group bmd-form-group">
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
											<label class="mdl-textfield__label" for="RoutingNumber">Routing Number</label>
											<input class="form-control" type="text" id="RoutingNumber" name="RoutingNumber" value="<?php echo $AbstractorDetails->RoutingNum;?>">
											<span class="mdl-textfield__error form_error"></span>
										</div>
									</div>

									<div class="col-sm-4 form-group bmd-form-group">
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
											<label class="mdl-textfield__label" for="BankAccountType">Bank Account Type</label>
											<input class="form-control" type="text" id="BankAccountType" name="BankAccountType" value="<?php echo $AbstractorDetails->BankAccountType;?>">
											<span class="mdl-textfield__error form_error"></span>
										</div>
									</div> </div>

								</div>
							</div>
							<!-- Bank Details -->

							<!-- Document Details -->
							<div class="col-sm-12">
								<div class="panel-heading" style="padding: 1px 1px 1px 1px; background-color: #eee ; margin-top: 20px;">
									<p style="margin: 5px; font-size: 16px; color: #000; font-weight: bold">&nbsp; Documents</p>
								</div>

								<br>

								<div class="col-sm-12  bmd-form-group">
									<div class="row">
										<div class="col-sm-4">

											<input type="file" id="upload_file" name="upload_file" class="dropify" multiple accept="application/pdf"/>
										</div>

										<div class="col-sm-8 form-group bmd-form-group">
											<div class="row">
												<div class="col-sm-4 form-group bmd-form-group">
													<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
														<label class="mdl-textfield__label" for="EOStatus">E&O Status <span style="color: red">*</span></label>
														<select class="select2picker EOStatus" id="EOStatus" name="EOStatus">
															<option></option>
															<?php
															if($AbstractorDetails->EAndOStatus == 1)
																{ ?>
																	<option value = "1" selected>Yes</option>
																	<option value = "2">No</option>
																<?php } else{ ?>
																	<option value = "1">Yes</option>
																	<option value = "2" selected>No</option>
																<?php } ?>
															</select>
															<span class="mdl-textfield__error form_error"></span>
														</div>
													</div>

													<div class="col-sm-4 form-group bmd-form-group" style="margin-top: 34px;">
														<label style="color: rgb(0, 150, 136); font-size: 12px;" for="EORecoveryAmount">E&O Recovery Amount</label>
														<div class="input-group"><span class="input-group-addon input-xs mt-5">$</span>
															<input type="text" class="form-control currency  EORecoveryAmount input-xs" id="EORecoveryAmount" placeholder="0.00" name="EORecoveryAmount" value="<?php echo $AbstractorDetails->EAndORecoveryAmt;?>">
														</div>
													</div>
												</div>

												<div class="row">

													<div class="col-sm-4 form-group bmd-form-group">
														<div class='input-group date mdl-textfield mdl-js-textfield DtTmPickerNew ch_expiry_dt mdl-textfield--floating-label' data-hidden='HidDate1'>
															<label class="mdl-textfield__label date-label" for="ExpiryDate" style="color: rgb(0, 150, 136);">E&O Expiry Date</label>

															<?php if($AbstractorDetails->EAndOExpiryDate == "0000-00-00") : ?>

																<input type="text" class="mdl-textfield__input form-control input-xs input-group date-entry1" id="ExpiryDate" name="ExpiryDate" />
																<input type='hidden' class="form-control DtHidden" id="HidDate1"/>

																<?php else: ?>

																	<input type="text" class="mdl-textfield__input form-control input-xs input-group date-entry1" id="ExpiryDate" name="ExpiryDate" value="<?php echo $AbstractorDetails->EAndOExpiryDate; ?>"/>
																	<input type='hidden' class="form-control DtHidden" id="HidDate1"/>

																<?php endif; ?>

																<span class="input-group-addon calendar-icon ExpiryCalendar">
																	<i class="fa fa-calendar"></i>
																</span>
															</div>
														</div>

														<div class="col-sm-4 form-group bmd-form-group">
															<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
																<label class="mdl-textfield__label" for="Insurance">E&O Insurance</label>
																<input class="form-control" type="text" id="Insurance" name="Insurance" value="<?php echo $AbstractorDetails->EAndOInsurance;?>">
																<span class="mdl-textfield__error form_error"></span>
															</div>
														</div>

														<div class="col-sm-4 form-group bmd-form-group">
															<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
																<label class="mdl-textfield__label" for="Policy">E&O Policy Limit</label>
																<input class="form-control" type="text" id="Policy" name="Policy" value="<?php echo $AbstractorDetails->EAndOPolicyLimit;?>">
																<span class="mdl-textfield__error form_error"></span>
															</div>
														</div>
													</div>

													<div class="row">

														<div class="col-sm-4 form-group bmd-form-group">
															<div class='input-group date mdl-textfield mdl-js-textfield DtTmPickerNew ch_license_dt mdl-textfield--floating-label' data-hidden='HidDate2'>
																<label class="mdl-textfield__label date-label" for="LicenseExpiryDate" style="color: rgb(0, 150, 136);">License Expiry Date</label>

																<?php if($AbstractorDetails->LicenseExpiryDate == "0000-00-00") : ?>

																	<input type="text" class="mdl-textfield__input form-control input-xs input-group date-entry1" id="LicenseExpiryDate" name="LicenseExpiryDate" />
																	<input type='hidden' class="form-control DtHidden" id="HidDate2"/>

																	<?php else: ?>

																		<input type="text" class="mdl-textfield__input form-control input-xs input-group date-entry1" id="LicenseExpiryDate" name="LicenseExpiryDate" value="<?php echo $AbstractorDetails->LicenseExpiryDate; ?>"/>
																		<input type='hidden' class="form-control DtHidden" id="HidDate2"/>

																	<?php endif; ?>

																	<span class="input-group-addon calendar-icon">
																		<i class="fa fa-calendar"></i>
																	</span>
																</div>
															</div>

															<div class="col-sm-4 form-group bmd-form-group">
																<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
																	<label class="mdl-textfield__label" for="FaxNo">Fax No</label>
																	<input class="form-control" type="text" id="FaxNo" name="FaxNo" value="<?php echo $AbstractorDetails->FaxNo;?>">
																	<span class="mdl-textfield__error form_error"></span>
																</div>
															</div>

															<div class="col-sm-4 form-group bmd-form-group">
																<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
																	<label class="mdl-textfield__label" for="LicenseNo">Lisense No</label>
																	<input class="form-control" type="text" id="LicenseNo" name="LicenseNo" value="<?php echo $AbstractorDetails->LicenseNo;?>">
																	<span class="mdl-textfield__error form_error"></span>
																</div>
															</div>
														</div>

													</div></div>
												</div>

												<div class="col-sm-12">

													<div class="col-sm-6 form-group bmd-form-group">
														<div class="col-sm-8" style="padding-left: 0px;">
															<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
																<label class="mdl-textfield__label" for="DocumentTypeSelect">Document Type <span style="color: red">*</span></label>
																<select class="select2picker" id="DocumentTypeSelect" name="DocumentTypeSelect">
																	<option></option>

																	<?php   foreach($subdocument as $type)
																	{
																		echo '<option value="'.$type->subDocumentTypeName.'">'.$type->subDocumentTypeName.'</option>';
																	} ?>

																</select>
																<span class="mdl-textfield__error form_error"></span>
															</div>

														</div>
														<div class="col-sm-4 form-group bmd-form-group" id="displaychecklist" style="display:none;margin-top: 30px;">

															<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
																<input type="radio" id="WebTax" class="mdl-radio__button" value="1" name="ContractPackage" >
																<span class="mdl-radio__label">Yes</span>
															</label>
															<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
																<input type="radio" id="WebTax" class="mdl-radio__button" value="0" name="ContractPackage" >
																<span class="mdl-radio__label">No</span>
															</label>
														</div>
													</div>

													<div class="col-sm-12">
														<div id="upload_preview" class="table-responsive">
															<table id = "upload-preview-table" class="table table-striped table-inverse">
																<thead class="thead-inverse">
																	<tr>
																		<th style="display: none;">SNo</th>
																		<th width="15%" style="text-align:left;">Document Type</th>
																		<th style="text-align:left;">Document Name</th>
																		<th width="25%" style="text-align:left;">Uploaded DateTime</th>
																		<th width="25%" style="text-align:left;">Uploaded User</th>
																		<th width="10%" style="text-align:left;">Status</th>
																		<th width="10%" style="text-align:left;">Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach($AbstractorDocumentDetails as $row): ?>
																		<tr data-filepath="<?php echo $row->url?>" data-document="<?php echo $row->document?>" data-created_dt="<?php echo $row->created_dt?>" data-created_by="<?php echo $row->created_by?>" data-abstractor_doc_uid="<?php echo $row->abstractor_doc_uid?>">
																			<td style="text-align: left;"><?php echo $row->document; ?></td>
																			<td style="text-align: left;"> <?php echo str_replace('uploads/abstractordocuments/', '', $row->url); ?></td>
																			<td style="text-align: left;"> <?php echo $row->created_dt; ?></td>
																			<td style="text-align: left;"> <?php echo $row->UserName; ?></td>

																			<td> <span style="text-align: left;width:100%;" >
																				<div class="switch-button  switch-button-xs ">
																					<?php  if($row->is_active==1): ?>
																						<input type="checkbox" name="OrderEntry<?php echo $row->abstractor_doc_uid; ?>" id="<?php echo $row->abstractor_doc_uid;?>" class="status" value="1" checked="true">
																						<?php elseif($row->is_active==0): ?>
																							<input type="checkbox" name="OrderEntry<?php echo $row->abstractor_doc_uid; ?>" id="<?php echo $row->abstractor_doc_uid;?>" class="status" value="0">
																						<?php endif; ?>
																						<span><label for="<?php echo $row->abstractor_doc_uid; ?>"></label></span>
																					</div>
																				</span></td>
																				<td style="text-align: left;">

																					<a class="viewFile " style="margin-left: 5px;" href="<?php echo base_url().$row->url; ?>" target="blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
																				</td>
																			</tr>
																		<?php endforeach; ?>
																	</tbody>
																</table>
															</div>
														</div>
													</div>

													<div class="col-sm-12 text-right">
														<div class="col-sm-12 form-group bmd-form-group">
															<a href="<?php echo base_url(); ?>" class="btn btn-default btn-space" >Cancel</a>
															<button type="button" class="btn btn-primary btn-space" id="BtnSaveAbstractor"  value="1" >Update</button>
														</div>
													</div>
												</div>
											</form>

										</div>

<div class="fixed-plugin">
  <div class="dropdown show-dropdown pd-10">
    <a href="#" data-toggle="dropdown">
      <i class="ion-android-settings" style="color:#fff"> </i>
    </a>
    <ul class="dropdown-menu">
      <li class="header-title"> Sidebar Filters</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger active-color">
          <div class="badge-colors ml-auto mr-auto">
            <span class="badge filter badge-purple" data-color="purple"></span>
            <span class="badge filter badge-azure" data-color="azure"></span>
            <span class="badge filter badge-green" data-color="green"></span>
            <span class="badge filter badge-warning" data-color="orange"></span>
            <span class="badge filter badge-danger" data-color="danger"></span>
            <span class="badge filter badge-rose active" data-color="rose"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>


      <li class="header-title">Sidebar Background</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="ml-auto mr-auto">
            <span class="badge filter badge-black active" data-background-color="black"></span>
            <span class="badge filter badge-white" data-background-color="white"></span>
            <span class="badge filter badge-red" data-background-color="red"></span>
            <span class="badge filter badge-blue" data-background-color="blue"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Mini</p>
          <label class="ml-auto">
            <div class="togglebutton switch-sidebar-mini">
              <label>
                <input type="checkbox">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Images</p>
          <label class="switch-mini ml-auto">
            <div class="togglebutton switch-sidebar-image">
              <label>
                <input type="checkbox" checked="">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="header-title">Images</li>

      <li class="active">
        <a class="img-holder switch-trigger" href="javascript:void(0)">a
          <img src="<?php echo base_url(); ?>assets/img/sidebar-1.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-2.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-3.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-4.jpg" alt="">
        </a>
      </li>

    </ul>
  </div>
</div> 
										<div id="document-modal" class="modal fade custommodal" role="dialog" >
											<div class="modal-dialog" style="width: 750px;">
												<div class="modal-content">
													<div class="modal-header" style=" background-color: #1d4870;">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title" style="color: #fff"><center>Uploaded Documents</center></h4>
													</div>

													<div class="modal-body" >
														<table class="table table-striped table-bordered defaultfontsize">
															<thead>
																<tr>
																	<th style="text-align:center" width="15%">S.No</th>
																	<th style="text-align:center" width="15%">Documents</th>
																</tr>
															</thead>
															<tbody class="document-data">

															</tbody>
														</table>
													</div>

													<div class="modal-footer" style=" background-color: #e6f6fe;">
														<button type="button"  data-dismiss="modal" class="btn btn-default Close">Close</button>
													</div>
												</div>

												<script type="text/javascript">

													$(document).ready(function() {
														datetimepicker_init();
													});

													var uploaded={};

													var documents="";
													var updates = [];
													var fieldcounter = 0;

													var filetoupload = [];
													var o = [];
													var selectoption = [];
													var filename = [];
													var $inputs = $('#document-form').find("input, select, button, textarea");
													var progresswidget = $('.progress-widget'); var progressvalue = $('.progress-value');
													var bar = $('#bar'); var progressdata = $('.progress-data');
													progresswidget.show();

													$('#upload_file').change(function(event){

														var DocumentTypeSelect = $('#DocumentTypeSelect').val();

														if(DocumentTypeSelect != ''){

															var output = [];
															fieldcounter = $('#upload-preview-table tbody').find('tr:last').attr('data-postion');
															var documenttype = $('#documenttype').find(":Selected").text();
															var documenttype = "Others";
															var documenttypeuid = $('#documenttype').find(":Selected").val();

															$.ajax({
																url:'<?php echo base_url('Abstractor_Order_Search/GetUser'); ?>',
																type: 'POST',
																data: {OrderUID: 1},
																dataType: 'json',
																beforeSend: function(){
																	$('.spinnerclass').addClass('be-loading-active');
																}
															})
															.done(function(data)
															{
																console.log(data);
																$('#upload-preview-table').show();
																if(isNaN(fieldcounter))
																{
																	fieldcounter =0;
																}
																for(var i = 0; i < event.target.files.length; i++)
																{
																	fieldcounter++;
																	var file = event.target.files[i];
																	var fieldid = fieldcounter;
																	filetoupload.push({ id: fieldid, file: file, filename: file.name });


																	var subdocumenttypecombo = $('#DocumentTypeSelect').find(":Selected").text();

																	var select = selectoption;
																	uploaded.username=data.UserName;
																	uploaded.userid=data.UserUID;
																	uploaded.datetime=data.datetime;

																	var removelink = " <button class=\"removeFile btn btn-danger\" data-fileid=\"" + fieldid + "\"><span class=\"glyphicon glyphicon-trash\"></button>";

																	var viewlink = " <a class=\" viewFile btn btn-success\" href=\"#\" data-fileid=\"" + fieldid + "\"><span class=\"glyphicon glyphicon-search\"></span></div></a>";



																	output.push("<tr data-postion =\""+ fieldcounter +"\" data-filename =\""+ file.name +"\" data-documenttypeuid = \"" + documenttypeuid + "\"><td class = \"text-left SubDocumentTypeName\"><strong>", subdocumenttypecombo, "</strong></td><td class = \"text-left\"><strong>", escape(file.name), "</strong></td></td><td class = \"text-left\"><strong>", uploaded.datetime, "</strong></td><td class = \"text-left\"><strong>", uploaded.username, "</strong></td><td class = \"text-left\">", removelink,"</td></tr>");

																}

																$('#upload-preview-table').find('tbody').append(output.join(""));
																updatePosition();
																select_mdl();
																$('.select2').select2({
																	theme: "bootstrap",
																});


																$('#mergePDF').show();
																$('#upload_preview').show();
																$('.spinnerclass').removeClass('be-loading-active');

															})
															.fail(function(jqXHR, textStatus, errorThrown){
																$('.spinnerclass').removeClass('be-loading-active');
																console.log(jqXHR.responseText);
															})

														}
														else{
															$.notify({icon:"icon-bell-check",message:'Please Select Document Type'},{type:"danger",delay:1000 });


															$('#DocumentTypeSelect').addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
															$('#DocumentTypeSelect.select2picker').next().find('span.select2-selection').addClass('errordisplay');

														}

													});



													$('#DocumentTypeSelect').change(function(){



														var DocumentTypeSelect = $('#DocumentTypeSelect').val();
														if(DocumentTypeSelect!='Contract Package'){
															$('#displaychecklist').hide();
														}else{
															$('#displaychecklist').show();
														}

														if(DocumentTypeSelect != ''){
															var subdocumenttypecombo = $('#DocumentTypeSelect').find(":Selected").text();
															$('#upload-preview-table tbody').find('.SubDocumentTypeName').text(subdocumenttypecombo);
														}
														else{
															$.notify({icon:"icon-bell-check",message:'Please Select Document Type'},{type:"danger",delay:1000 });


															$('#DocumentTypeSelect').addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
															$('#DocumentTypeSelect.select2picker').next().find('span.select2-selection').addClass('errordisplay');

														}

													});


													$('.EOStatus').change(function(){
														var EOStatus = $('#EOStatus option:selected').val();
														if(EOStatus == 1)
														{
															$("#ExpiryDate").attr( "disabled", false );
															$("#ExpirySignature").attr( "disabled", false );
															$("#Insurance").attr( "disabled", false );
															$("#Policy").attr( "disabled", false );
															$(".ExpiryCalendar").show();

															$('.ch_expiry_dt ').closest('div').find('.jq-dte-day').attr( "disabled", false );
															$('.ch_expiry_dt ').closest('div').find('.jq-dte-month').attr( "disabled", false );
															$('.ch_expiry_dt ').closest('div').find('.jq-dte-year').attr( "disabled", false );
														}
														else
														{
															$("#ExpiryDate").attr( "disabled", true );
															$("#ExpirySignature").attr( "disabled", true );
															$("#Insurance").attr( "disabled", true );
															$("#Policy").attr( "disabled", true );
															$(".ExpiryCalendar").hide();

															$("#Insurance").val('');
															$("#Policy").val('');

															$('.ch_expiry_dt ').find('.jq-dte-day').attr( "disabled", true );
															$('.ch_expiry_dt ').find('.jq-dte-month').attr( "disabled", true );
															$('.ch_expiry_dt ').find('.jq-dte-year').attr( "disabled", true );

															$('.ch_expiry_dt ').find('.jq-dte-day').val('');
															$('.ch_expiry_dt ').find('.jq-dte-month').val('');
															$('.ch_expiry_dt ').find('.jq-dte-year').val('');
														}
													});

													$("#upload-preview-table").on('click', '.RemoveAbstractorFile', function(e){
														e.preventDefault();

														var currentrow = $(this);
														var abstractor_doc_uid = $(this).closest('tr').attr('data-abstractor_doc_uid');
														var FilePath = $(this).closest('tr').attr('data-filepath');

														$.ajax({
															url: '<?php echo base_url('abstractors/DeleteAbstractorFile')?>',
															data: {"abstractor_doc_uid": abstractor_doc_uid,"FilePath": FilePath},
															type: 'POST',
															dataType: 'json',
															beforeSend: function(){
																$('.spinnerclass').addClass('be-loading-active');
															},
															success: function(data){
																$inputs.prop('disabled', false);
																if(data.status == 'success')
																{
																	if(data.action == 'delete')
																	{
																		$(currentrow).closest('tr').remove();
																		updatePosition();
																		submitPositions();
																		$.notify({icon:"icon-bell-check",message:'File is Deleted Successfully.'},{type:"success",delay:1000 });
																		setTimeout(function(){
																			triggerpage(CurrentURL);
																		}, 3000);

																	}
																}
																else
																{
																	$.notify({icon:"icon-bell-check",message:'Internal Error'},{type:"danger",delay:1000 });
																	setTimeout(function(){
																		triggerpage(CurrentURL);
																	}, 3000);

																}

																$('.spinnerclass').removeClass('be-loading-active');
															},
															error: function(response){
																console.log(response.responseText);
																$('.spinnerclass').removeClass('be-loading-active');

															},
														});
														fieldcounter--;
													});

  									//Attach Documents Ends



  									$('#AbstractorZipCode').change(function(event) {

  										AbstractorZipCode = $(this).val();

  										$.ajax({
  											type: "POST",
  											url: '<?php echo base_url();?>Abstractor/getzip',
  											data: {'AbstractorZipCode':AbstractorZipCode},
  											dataType:'json',
  											cache: false,
  											success: function(data)
  											{
  												console.log(data);
  												$('#AbstractorCityUID').empty();
  												$('#AbstractorStateUID').empty();
  												$('#AbstractorCountyUID').empty();

  												if(data != ''){

  													$('#AbstractorCityUID').append('<option value="' + data['CityUID'] + '" selected>' + data['CityName'] + '</option>').trigger('change');

  													$('#AbstractorStateUID').append('<option value="' + data['StateUID'] + '" selected>' + data['StateName'] + '</option>').trigger('change');
  													$('#AbstractorCountyUID').append('<option value="' + data['CountyUID'] + '" selected>' + data['CountyName'] + '</option>').trigger('change');
  													$('#AbstractorStateUID').parent().addClass('is-filled');
  													$('#AbstractorCityUID').parent().addClass('is-filled');
  													$('#AbstractorCountyUID').parent().addClass('is-filled');
  												}

  											},
  											error: function (jqXHR, textStatus, errorThrown) {

  												console.log(errorThrown);

  											},
  											failure: function (jqXHR, textStatus, errorThrown) {

  												console.log(errorThrown);

  											},
  										});
  									});


  									$('#BtnSaveAbstractor').on('click',function(event) {


  										var data = new FormData();
									    //Form Data
									    //
									    var disabled = $('#frm_abstractor').find(':input:disabled').removeAttr('disabled');

									    var form_data = $('#frm_abstractor').serializeArray();

									    disabled.attr('disabled','disabled');

									    $.each(form_data, function (key, input) {
									    	data.append(input.name, input.value);
									    });
									    //Form Data
									    //File data
									    var abs_upload_file =  $('input[name="upload_file"]')[0].files;
									    for (var i = 0; i < abs_upload_file.length; i++) {
									    	data.append("upload_file[]", abs_upload_file[i]);
									    }

									    //File Data
									    //Custom Data
									    data.append('key', 'value');
									    //Custom Data

									    $.ajax({
									    	type: "POST",
									    	url: '<?php echo base_url();?>Profile/SaveAbstractor',
									    	processData: false,
									    	contentType: false,
									    	dataType:'json',
									    	data: data,
									    	beforeSend: function()
									    	{
									    		$('#BtnSaveAbstractor').attr("disabled", true);
									    		$('#BtnSaveAbstractor').html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
									    	},
									    	success: function(data)
									    	{


									    		$('#BtnSaveAbstractor').attr("disabled", false);
									    		$('#BtnSaveAbstractor').html('Update');

									    		if(data.validation_error == 1)
									    		{
									    			$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:1000 });
									    			setTimeout(function(){
									    				triggerpage(CurrentURL);
									    			}, 3000);

									    		}
									    		else{
									    			$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });
									    			setTimeout(function(){
									    				triggerpage(CurrentURL);
									    			}, 3000);

									    			$.each(data, function(k, v) {
									    				$('#'+k).closest('div.is_error').addClass('is-invalid');
									    			});
									    		}

									    	},
									    	error: function (jqXHR, textStatus, errorThrown) {

									    		console.log(errorThrown);

									    	},
									    	failure: function (jqXHR, textStatus, errorThrown) {

									    		console.log(errorThrown);

									    	},
									    });
									  });


  									var date_init = new Date();
  									var current_date = date_init.getFullYear() + "-" + ( '0' + (date_init.getMonth()+1) ).slice( -2 ) + "-" + date_init.getDate();


  									$('.ch_license_dt .jq-dte-year').on("focusout", function() {

  										var LicenseExpiryDate = $('#LicenseExpiryDate').val();

  										if(LicenseExpiryDate<1970){

  											$.notify({icon:"icon-bell-check",message:'Year Should Be More than 1970'},{type:"danger",delay:1000 });
  											setTimeout(function(){
  												triggerpage(CurrentURL);
  											}, 3000);

  										}
  									});

  									$(document).off('change','.status').on('change','.status',function(){

  										var abstractor_doc_uid = $(this).attr('id');
  										if($(this).val()==1)
  										{
  											var status = 0;
  											$('#'+abstractor_doc_uid).val('0');
  										} else {
  											var status = 1;
  											$('#'+abstractor_doc_uid).val('1');
  										}
  										$.ajax({
  											type: "POST",
  											url: "<?php echo base_url()?>Abstractor/abstractor_changestatus",
  											dataType: "JSON",
  											data: {'abstractor_doc_uid':abstractor_doc_uid,'status':status},
  											cache: false,
  											success: function(data)
  											{
  												if(data['error']==0)
  												{
  													$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:1000 });
  													setTimeout(function(){
  														triggerpage(CurrentURL);
  													}, 3000);

  												} else {

  													$.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });
  													setTimeout(function(){
  														triggerpage(CurrentURL);
  													}, 3000);
  												}
  											}
  										});
  									});

  									$(document).ready(function(){
  										$('.EOStatus').trigger('change');
  									});



  								</script>



