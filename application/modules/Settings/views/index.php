<style type="text/css">
	.pd-btm-0{
		padding-bottom: 0px;
	}

	.margin-minus8{
		margin: -8px;
	}

	.mt--15{
		margin-top: -15px;
	}

	.bulk-notes
	{
		list-style-type: none
	}
	.bulk-notes li:before
	{
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}
</style>
<div class="card mt-40 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Settings
		</div>
		<div class="row">
			<div class="col-md-6">
			</div>

		</div>

	</div>

	<div class="card-body">
		<div class="col-md-12">

			<div class="material-datatables">
				<table id="tablelist" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
					<thead>
						<tr>
							<th class="text-left" style="width: 30px;" >S.No</th>
							<th class="text-left">Name</th>
							<th class="text-left">Status</th>
							<th class="no-sort">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($lists as $key => $row): ?>
							<tr>
								<td style="text-align: left;"><?php echo $key+1; ?></td>
								<td style="text-align: left;"><?php echo $row->DisplayName; ?></td>
								<td style="text-align: left;">

									<div class="togglebutton">
										<label class="label-color"> 
											<input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1) { echo "checked"; } ?> disabled="">
											<span class="toggle"></span>
										</label>
									</div>

								</td>

								<td style="text-align: left"> 
									<a href="<?php echo base_url('Settings/edit/'.$row->SettingUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs" style="text-align: center;width:100%;"><i class="icon-pencil"></i>
									</a>
								</td>
							</tr>

						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">

	$(document).ready(function(){



		$("#tablelist").dataTable({
			processing: true,
			scrollX:  false,
			paging:true,
			"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]


		});

	</script>







