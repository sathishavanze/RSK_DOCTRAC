			<div class="">
				<div class="tablescroll defaultfontsize">
					<input type="hidden" name="FilePath" id="FilePath" value="<?php echo $FilePath; ?>" placeholder="">
					<table class="table table-striped table-hover table-format nowrap datatable table-fileupload" >
						<thead>
							<tr>
								<th>#</th>
								<th>Order No</th>
								<th>Loan No</th>
								<th>Client</th>
								<th>Project</th>
								<th>Page Start</th>
								<th>Page End</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($Orders as $key => $value) { ?>
							<tr>
								<td>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input choosen-orders" type="checkbox" value="" checked="checked" data-orderuid="<?php echo $value->OrderUID; ?>"  data-start="<?php echo ($value->StartPage+1); ?>" data-end="<?php echo ($value->EndPage); ?>" >
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
								<td><a href="javascript:void(0);" class="gotopage" data-pageno="<?php echo $value->StartPage; ?>"><?php echo $value->StartPage; ?></a></td>
								<td><a href="javascript:void(0);" class="gotopage" data-pageno="<?php echo $value->EndPage; ?>"><?php echo $value->EndPage; ?></a></td>
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
					
					$('.gotopage').off('click').on('click', function (e) {
						e.preventDefault();

						var pageno = $(this).attr('data-pageno');


						var local_files = $('#file-bulkimage')[0];
						if (local_files.files && local_files.files[0]) {

							var reader = new FileReader();
							reader.onload = function (e) {
								$('#pdf-preview').html('<embed id="iframe-pdfviewer" src="'+e.target.result+'#page=5" style="height:300px; width: 100%;"/>');
								$('#PreviewLabel').modal('show');
							}
							reader.readAsDataURL(local_files.files[0]);
						}

					})
				});
			</script>
