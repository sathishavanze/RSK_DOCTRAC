
<!--ENABLE  WORKUP POPUP CONTENT -->                
<div id="ordersloanmodal" tabindex="-1" role="dialog" aria-hidden="true"  class="modal ordersloanmodal fade">
	<div class="modal-dialog modal-lg mt-10 ml-20"> 
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title ordersloanmodal-title text-bold"></h5>
				<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"> <i class="icon-x"></i></button>
				<button class="btn btn-default btn-xs btn-link excelorderlist" title="Excel Export" style="color:#0B781C;cursor: pointer;"><i class=" icon-file-excel"></i></button>
				<button class="btn btn-link btn-danger btn-xs orderclose" title="Close"><i class="icon-cross2
					"></i></button> 
				</div>
				<div class="modal-body">
					<input type="hidden" id="orderlist_orderuids">
					<input type="hidden" id="orderlist_workflowmoduleuid">
					<input type="hidden" id="OrderUID">



					<div class="row">
						<div class="col-md-12">
							<div class="material-datatables">
								<?php
								$QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
								?>
								<table id="orderslist" class="table table-striped display nowrap abortprocesstable dataTable Dashboardordertable" cellspacing="0" width="100%"  style="width:100%;">
									<thead>
										<tr>

											<?php 
											foreach ($QueueColumns as $ReportColumn) 
											{ 
												$sort = ($ReportColumn->NoSort == 1 && empty($ReportColumn->SortColumnName)) ? 'class="no-sort"' : '';
												if(!empty($ReportColumn->IsChecklist)) {

													echo '<th '.$sort.'>'.$ReportColumn->HeaderName.'</th>';

												}
												elseif(!empty($ReportColumn->FieldUID))
												{
													if(!empty($ReportColumn->ExpirationDuration))
													{
														echo '<th '.$sort.'>'.$ReportColumn->FieldLabel.'<br><span class="ml-30"> ('.$ReportColumn->ExpirationDuration.' Days) </span></th>';
													}
													else
													{
														echo '<th '.$sort.'>'.$ReportColumn->FieldLabel.'</th>';
													}
												} else if ($ReportColumn == 'KickbackAssociate') {
													echo '<th>'.$ReportColumn->HeaderName.'</th>';
												} else if ($ReportColumn == 'KickbackRemarks') {
													echo '<th>'.$ReportColumn->HeaderName.'</th>';
												} else if ($ReportColumn == 'KickbackDate') {
													echo '<th>'.$ReportColumn->HeaderName.'</th>';
												}
												elseif (!empty($ReportColumn->WorkflowUID))
												{
													echo '<th '.$sort.'>'.$ReportColumn->SystemName.'</th>';
												}
												else
												{
													echo '<th '.$sort.'>'.$ReportColumn->HeaderName.'</th>';
												}
											}
											?>
											<th class="no-sort">Actions</th>
										</tr>
									</thead>
									<tbody id="append-data"></tbody>

								</table>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

