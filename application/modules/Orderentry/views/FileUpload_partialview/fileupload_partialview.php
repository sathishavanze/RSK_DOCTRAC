			<div class="">
				<div class="tablescroll defaultfontsize">
					<table class="table table-striped table-hover table-format nowrap datatable table-fileupload" >
						<thead>
							<tr>
								<th>#</th>
								<th>Order No</th>
								<th>Loan No</th>
								<th>Client</th>
								<th>Project</th>
								<th>File Name</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($WaitingForImage as $key => $value) { ?>
							<tr>
								<td>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input choosen-orders" type="checkbox" value="" checked="checked" data-orderuid="<?php echo $value->OrderUID; ?>" data-filename="<?php echo $value->FileName; ?>" data-filepath="<?php echo isset($value->FilesPath) ? $value->FilesPath : ""; ?>">
											<span class="form-check-sign">
												<span class="check"></span>
											</span>
										</label>
									</div>
								</td>
								<td><?php echo $value->OrderNumber; ?></td>
								<td><?php echo $value->LoanNumber; ?></td>
								<td><?php echo $value->CustomerName; ?></td>
								<td><?php echo $value->ProjectName; ?></td>
								<td><?php echo $value->FileName; ?></td>
							</tr>

							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<script type="text/javascript">
				
				$(function () {
					/* Datatable initialization for excel, pdf export */
					if ( $.fn.DataTable.isDataTable( '.table-fileupload' ) ) {
						$('.table-fileupload').dataTable().fnClearTable();
						$('.table-fileupload').dataTable().fnDestroy();
					}

					$('.table-fileupload').DataTable( {
						dom: 'Bfrtip',
						buttons: [
						'excel', 'pdf'
						]
					} );
				});
			</script>