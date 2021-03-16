
<div class="card" id="orderstablediv" style="display: none;">

	<div class="card-header card-header-icon card-header-rose">
		<div class="card-icon title-heading"><i class="icon-file-check"></i><span id="orderlisttitle"></span>
		</div> 					
		<div class="text-right"> 
			<button class="btn btn-default btn-xs btn-link excelorderlist" title="Excel Export" style="font-size: 13px;color:#0B781C;cursor: pointer;"><i class=" icon-file-excel"></i></button>
			<button class="btn btn-link btn-danger btn-xs orderclose" title="Close"><i class="icon-cross2
				"></i></button> 
			</div>
		</div>
		
		<div class="card-body">
			<div class="row"> 
				<div class="col-md-12 pd-0">
					<div class="material-datatables">
						<?php
						$QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
						?>
						<table id="orderslist" class="table table-striped table-no-bordered table-hover wraptable datepickertable fetch-notescountstable" cellspacing="0" width="100%"  style="width:100%;margin:0;">
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
	<div class="" style="display: none;">
		<input type="hidden" id="orderlist_orderuids">
		<input type="hidden" id="orderlist_workflowmoduleuid">
		<input type="hidden" id="OrderUID">
	</div>

	<div id="appendmodal" class=""  role="dialog">

	</div>

	<!--ENABLE  WORKUP POPUP CONTENT -->                
	<?php $this->load->view('orderinfoheader/workupmodal'); ?>

	<!--WORKFLOW COMMENTS POPUP CONTENT -->                
	<?php $this->load->view('orderinfoheader/workflownotesmodal'); ?>

	<!-- SCRIPTS FOR PRIORITY REPORTS -->	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.counterup.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
	<script src="assets/js/app/priorityreport.js?reload=1.0.6"></script>
	<script src="assets/js/formatcurrency.js?reload=1.0.1"></script>
