<div class="row">
	<div class="col-md-6">
		<form id="frmprivateabstractor">
			<div class="abstractordiv" style="border:1px solid #ddd;">
				<h5 class="text-center"><i class="icon-file-locked" style="padding-right:10px;"></i>Private Abstractor</h5>
				<div class="col-md-12 perfectscrollbar pd-0" style="height:300px;overflow:scroll;position:relative;">
					<div class="table-responsive">
						<table class="table  table-striped table-bordered" id="privateabstractor" style="border-top:1px solid #ddd;">
							<thead style="display: none;">
								<th></th>
								<th></th>
							</thead>
							<tbody class="Tbody_PrivateAbstractorTable">
								<?php foreach ($PrivateAbstractorDtails as $row) {  
									$IsPrivateChecked = $this->Customer_Model->CheckCustomerPrivateAbstractorDtails($row->AbstractorUID,$CustomerUID);
									?>
									<tr style="width:100%;" data-id="<?php echo $row->AbstractorUID?>" data-name="<?php echo $row->AbstractorFirstName ?>">
										<td style="width:75%;">
											<?php echo $row->AbstractorFirstName ?>
										</td>
										<td style="width:25%;">
											<div class="col-md-12">
												<div class="row">
													<div class="col-6">
														<button type="button" class="btn btn-xs btn-outline-info abstractordetails"><i class="icon-eye"></i></button>
													</div>
													<div class="col-6">
														<div class="form-check">
															<label class="form-check-label">
																<?php if(!empty($IsPrivateChecked)){ ?>
																	<input id="private_input_checkbox<?php echo $row->AbstractorUID?>" class="form-check-input privatecheck" type="checkbox" checked="true"> 
																<?php } else { ?>
																	<input class="form-check-input privatecheck" type="checkbox" > 
																<?php } ?>
																<span class="form-check-sign">
																	<span class="check"></span>
																</span>
															</label>  
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr> 
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6">
		<form id="frmexcludeabstractor">
			<div class="excludeabstractordiv" style="border:1px solid #ddd;">
				<h5 class="text-center"><i class="icon-folder-remove" style="padding-right:10px"></i>Exclude Abstractor</h5>
				<div class="col-md-12 perfectscrollbar pd-0" style="height:300px;overflow:scroll;position:relative;">
					<div class="table-responsive">
						<table class="table  table-striped table-bordered" id="excludeabstractor" style="border-top:1px solid #ddd;">
							<thead style="display: none;">
								<th></th>
								<th></th>
							</thead>
							<tbody class="Tbody_ExcludeAbstractorTable">
								<?php foreach ($ExcludeAbstractorDtails as $row) {  
									$IsExcludeChecked = $this->Customer_Model->CheckCustomerExcludeAbstractorDtails($row->AbstractorUID,$CustomerUID);
									?>
									<tr style="width:100%;" data-id="<?php echo $row->AbstractorUID?>" data-name="<?php echo $row->AbstractorFirstName ?>" data-customeruid="<?php echo $CustomerUID; ?>">
										<td style="width:75%;">
											<?php echo $row->AbstractorFirstName ?>
										</td>
										<td style="width:25%;">
											<div class="col-md-12">
												<div class="row">
													<div class="col-6">
														<button type="button" class="btn btn-xs btn-outline-info abstractordetails"><i class="icon-eye"></i></button>
													</div>
													<div class="col-6">
														<div class="form-check">
															<label class="form-check-label">
																<?php if(!empty($IsExcludeChecked)){ ?>
																	<input class="form-check-input excludecheck" type="checkbox" 
																	id="exclude_input_checkbox<?php echo $row->AbstractorUID?>" name="exclude_input_checkbox" checked="true"> 
																<?php } else { ?>
																	<input id="exclude_input_checkbox<?php echo $row->AbstractorUID?>" name="exclude_input_checkbox" class="form-check-input excludecheck" type="checkbox" value=""> 
																<?php } ?>
																<span class="form-check-sign">
																	<span class="check"></span>
																</span>
															</label>  
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr> 
								<?php } ?>           
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(function () {
		customerabstractorinit();
		$('.perfectscrollbar').perfectScrollbar();

		/*-----Private Abstractor and Exclude Abstractor JS Starts*/


		$('.Tbody_ExcludeAbstractorTable').find('tr').each(function () {
			if (!$(this).find(':checkbox').is(':checked'))
				$('.Tbody_ExcludeAbstractorTable').append(this);
		});

		$('.Tbody_PrivateAbstractorTable').find('tr').each(function () {
			if (!$(this).find(':checkbox').is(':checked'))
				$('.Tbody_PrivateAbstractorTable').append(this);
		});

		$('#privateabstractor').off('change', '[type=checkbox]').on('change', '[type=checkbox]', function () {
			var $this = $(this);
			var row = $this.closest('tr');

			if ( $this.prop('checked') ){ 
				row.insertBefore( row.parent().find('tr:first-child') )
			}
			else { 
				row.insertAfter( row.parent().find('tr:last-child') )
			}
		});

		$('#excludeabstractor').off('change', '[type=checkbox]').on('change', '[type=checkbox]', function () {
			var $this = $(this);
			var row = $this.closest('tr');

			if ( $this.prop('checked') ){ 
				row.insertBefore( row.parent().find('tr:first-child') )
			}
			else { 
				row.insertAfter( row.parent().find('tr:last-child') )
			}
		});


		$(document).off('change', '.excludecheck').on('change', '.excludecheck', function (e) {
			var formdata={};
			formdata.CustomerUID=$('#CustomerUID').val();
			formdata.AbstractorUID=$(this).closest('tr').attr('data-id');
			if ($(this).prop('checked')==false) {
				formdata.Action='delete';

			}
			else{
				formdata.Action='insert';
			}

			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>Customer/ExcludeAbstractor',
				data: formdata, 
				dataType:'json',
				beforeSend: function(){
					// $(progressbar).show();
					// $(e.target).find('.upload').addClass('hide');
				},
				success: function(data)
				{
					if(data.validation_error == 0){
						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"success",
							delay:1000 
						})
					}else if(data.validation_error == 1){

						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"danger",
							delay:1000 
						})
					}


				},
				error: function(jqXHR){
					console.log(jqXHR);
				}
			});

		})

		$(document).off('change', '.privatecheck').on('change', '.privatecheck', function (e) {
			var formdata={};
			formdata.CustomerUID=$('#CustomerUID').val();
			formdata.AbstractorUID=$(this).closest('tr').attr('data-id');
			if ($(this).prop('checked')==false) {
				formdata.Action='delete';

			}
			else{
				formdata.Action='insert';
			}

			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>Customer/PrivateAbstractor',
				data: formdata, 
				dataType:'json',
				beforeSend: function(){
					// $(progressbar).show();
					// $(e.target).find('.upload').addClass('hide');
				},
				success: function(data)
				{
					if(data.validation_error == 0){
						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"success",
							delay:1000 
						})
					}else if(data.validation_error == 1){

						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"danger",
							delay:1000 
						})
					}


				},
				error: function(jqXHR){
					console.log(jqXHR);
				}
			});

		})
		/*-----Private Abstractor and Exclude Abstractor JS Ends*/


	});

	function customerabstractorinit() {
		$('#privateabstractor').DataTable({
			"paging": false,
			"ordering": false
		});
		$('#excludeabstractor').DataTable({
			"paging": false,
			"ordering": false
		});

	}
</script>