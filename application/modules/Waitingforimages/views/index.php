<style>
	.DTFC_RightBodyWrapper{
		z-index: 9;
	}

	.labelinfo{
		color: #3e3e3e;
		font-weight: 600;
	}

	.notification-right{
		position: absolute;
		top: 10px;
		border: 1px solid #FFF;
		right: 46px;
		font-size: 9px;
		background: #f44336;
		color: #FFFFFF;
		min-width: 20px;
		padding: 0px 5px;
		height: 20px;
		border-radius: 10px;
		text-align: center;
		line-height: 19px;
		vertical-align: middle;
		display: block;
	}
</style>

<div class="card">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-images2"></i>
		</div>
		<div class="row">
			<div class="col-md-6">
				<h4 class="card-title">Waiting for Images</h4>
			</div>
		</div>
	</div>
	<div class="card-body">
		<ul class="nav nav-pills nav-pills-rose" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					Waiting for Images
				</a>
			</li>
		</ul>


		<div class="tab-content tab-space">
			<div class="tab-pane active" id="orderslist">
						<div class="col-md-12 col-xs-12">
							<div class="material-datatables" id="waitingforimages_parent">
								<table class="table table-striped display nowrap" id="waitingforimages"  cellspacing="0" width="100%"  style="width:100%">
									<thead>
										<tr>
											<th>Order No</th>
											<th>Client </th>
											<th>Current Status</th>
											<th>Property Address</th>
											<th>Property City</th>
											<th>Property County</th>
											<th>Property State</th>
											<th>Zip Code</th>
											<th>Project</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
			    	</div>

				</div>
			</div>




			<script type="text/javascript">
				var waitingforimages = false;
				$(function() {
					$("select.select2picker").select2({
						//tags: false,
						theme: "bootstrap",
					});
					$('#waitingforimages').DataTable().destroy();
				});
				$(document).ready(function(){

					waitingforimages = $('#waitingforimages').DataTable( {
						scrollX:        true,
						scrollCollapse: true,
						fixedHeader: false,
						paging:  true,
						"autoWidth": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.
					"pageLength": 50, // Set Page Length
					"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
					language: {
						sLengthMenu: "Show _MENU_ Orders",
						emptyTable:     "No Orders Found",
						info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
						infoEmpty:      "Showing 0 to 0 of 0 Orders",
						infoFiltered:   "(filtered from _MAX_ total Orders)",
						zeroRecords:    "No matching Orders found",
						processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
					},
					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo base_url('Waitingforimages/waitingforimages_ajax_list')?>",
						"type": "POST"
					}

				});



					$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
					});

					$(window).resize(function() {
						$($.fn.dataTable.tables( true ) ).css('width', '100%');
						$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
					});


					new $.fn.dataTable.FixedColumns(waitingforimages, {
						leftColumns: 1,
						rightColumns: 1,
						heightMatch: 'auto'
					} );






				});


			</script>
