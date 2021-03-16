
<div class="card" id="orderstablediv" style="display: none;">

	<div class="card-header card-header-icon card-header-rose">
		<div class="card-icon title-heading"><i class="icon-file-check"></i><span id="orderlisttitle"></span>
		</div> 					
		<div class="text-right"> 
			<button class="btn btn-default btn-xs btn-link excelorderCountlist" title="Excel Export" style="font-size: 13px;color:#0B781C;cursor: pointer;"><i class=" icon-file-excel"></i></button>
			<button class="btn btn-link btn-danger btn-xs orderclose" title="Close"><i class="icon-cross2
				"></i></button> 
			</div>
		</div>
		
		<div class="card-body">
			<div class="row"> 
				<div class="col-md-12 pd-0">
					<div class="material-datatables">
						<table id="orderslist" class="table table-striped table-no-bordered table-hover wraptable" cellspacing="0" width="100%"  style="width:100%;margin:0;">
							<thead>
								<tr>
									<th>Loan Number</th>
									<th>Borrower Name</th>
									<th>Milestone</th>
									<th>Loan Type</th>
									<th>State</th>
									<th>Processor</th>
									
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
		<input type="hidden" id="OrderUID">
	</div>

	<div id="appendmodal" class=""  role="dialog">

	</div>