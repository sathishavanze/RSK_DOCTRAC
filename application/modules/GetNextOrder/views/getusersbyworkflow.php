		

<table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
	<thead>
		<tr>
			<th>UserName</th>
			<!--  <th>Attendence</th> -->
			<?php foreach($LoanTypes as $type){ if($type->LoanTypeName != "FHA/VA"){?>
				<th><?php echo $type->LoanTypeName; ?></th>
				<?PHP }}?>
				<th>Assign</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=1;foreach($userslist as $row): 
			$LoanTypeUIDs = explode(',', $row->LoanTypeUIDs);
			?>
			<tr>
				<td style="text-align: left;"><?php echo $row->UserName; ?></td>

				<?php foreach($LoanTypes as $type) { 
					if($type->LoanTypeName != "FHA/VA") { 
						$checked = in_array($type->LoanTypeUID,$LoanTypeUIDs) ? 'checked' : '';
					 ?>
						<td style="text-align: left;"><div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input loan_type" type="checkbox" name="loan_type[]" value="<?php echo $type->LoanTypeUID; ?>" data-id="<?php echo $row->UserUID; ?>" <?php echo $checked; ?> > 
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</td>
					<?php }
				}?>
				<td style="text-align: left;"><div class="form-check">
					<button type="button" class="btn btn-fill btn-success  assign  <?php echo $row->UserUID?>-assign" value="<?php echo $row->UserUID;?>">Assign</button>

				</td>
			</tr>

			<?php 
			$i++;
		endforeach; ?>
	</tbody>
</table>
