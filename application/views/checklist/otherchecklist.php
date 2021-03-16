<?php
$OtherChecklist = $this->Common_Model->getOtherCheckList($OrderUID, $WorkflowUID);
foreach ($OtherChecklist as $key => $DocTypeName) {
	$question_sno += 1;
	?>
	<tr class="removeRow questionlist <?php if ($this->uri->segment(1) != 'PreScreen' && $DocTypeName->Answer != 'Problem Identified') {
		echo 'ProblemIdentified';
	} ?>" <?php if ($this->uri->segment(1) != 'PreScreen' && $DocTypeName->Answer != 'Problem Identified' && $OrderDetails->DefaultChecklistView == 'Show Problem Identified') {
		echo 'style = "display :none"';
	} ?> data-delete="<?php echo $DocTypeName->DocumentTypeUID . '~' . $DocTypeName->DocumentTypeName . '~' . $DocTypeName->OrderUID; ?>">
	<td><input type='hidden' class="checklists" name="checklist[<?php echo $DocTypeName->WorkflowUID; ?>][OtherChecklist][<?php echo  $question_sno; ?>][question]" value='<?php echo $DocTypeName->DocumentTypeName; ?>'><?php echo $question_sno; ?><?php echo  "." ?> <?php echo $DocTypeName->DocumentTypeName; ?>
	<?php if ($DocTypeName->ScreenCode) {
		echo '<span style="font-size:9px; background-color: #26A69A; color:#ffffff; padding: 0px 4px; text-align: right; float: right;">' . $DocTypeName->ScreenCode . '</span></td>';
	} ?>

	<td>

		<select name="checklist[<?php echo $DocTypeName->WorkflowUID; ?>][OtherChecklist][<?php echo  $question_sno; ?>][Answer]" title="Findings" class="form-control form-check-input1 checklists pre_select">
			<option value="empty"></option>
			<?php $answerList = array('Completed' => 'Completed', 'Issue' => 'Problem Identified', 'NA' => 'NA');
			foreach ($answerList as $answerkey => $value) {

				if ($value == $DocTypeName->Answer) {
					echo '<option value="' . $value . '" selected>' . $answerkey . '</option>';
				} else {
					echo '<option value="' . $value . '" >' . $answerkey . '</option>';
				}
			} ?>
		</select>
	</td>

	<!-- CHECKLIST DYNAMIC FIELDS -->
	<?php if (!empty($ChecklistFields)) { ?>
		<?php foreach ($ChecklistFields as $key => $ChecklistField) { ?>

			<?php if ($ChecklistField->FieldType == 'checkbox') { ?>
				<td class="form-check dynamicCheckedList" style="text-align: center;border: 0!important;padding-top: 7px!important;">
					<label class="form-check-label Dashboardlable" title="<?php echo $ChecklistField->FieldLabel; ?>" for="<?php echo $ChecklistField->FieldName; ?><?php echo $ChecklistField->WorkflowModuleUID . $question_sno; ?>" style="color: teal">
						<input class="form-check-input checklists allworkflow " id="<?php echo $ChecklistField->FieldName; ?><?php echo $ChecklistField->WorkflowModuleUID . $question_sno; ?>" type="checkbox" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][OtherChecklist][<?php echo  $question_sno; ?>][<?php echo $ChecklistField->FieldName; ?>]" <?php echo isset($DocTypeName->{$ChecklistField->FieldName}) && $DocTypeName->{$ChecklistField->FieldName} == 'Yes' ? 'checked' : ''; ?>>
						<span class="form-check-sign">
							<span class="check"></span>
						</span>
					</label>
				</td>
			<?php } else if ($ChecklistField->FieldType == 'combobox') { ?>
				<td>
					<select name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][OtherChecklist][<?php echo $question_sno; ?>][<?php echo $ChecklistField->FieldName; ?>]" title="<?php echo $ChecklistField->FieldLabel; ?>" data-uid="<?php echo $DocTypeName->DocumentTypeUID; ?>" class="form-control form-check-input1 checklists pre_select">
						<option value="empty"></option>
						<?php $checklistdropdown = $this->Common_Model->get_dynamicchecklistdropdownfields($ChecklistField->FieldUID);
						foreach ($checklistdropdown as $checklistdropdownkey => $checklistdropdownvalue) { ?>

							<option value="<?php echo $checklistdropdownvalue->DropDownName; ?>" <?php echo ($checklistdropdownvalue->DropDownName ==  $DocTypeName->{$ChecklistField->FieldName}) ? 'selected' : ''; ?>><?php echo $checklistdropdownvalue->DropDownName; ?></option>

						<?php } ?>
					</select>
				</td>
			<?php } else if ($ChecklistField->FieldType == 'date') { ?>
				<td>
					<span class="bmd-form-group">
						<input type="text" title="<?php echo !empty($ExpirationDuration) ? $ExpirationDuration . ' Days' : $ChecklistField->FieldLabel; ?>" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][OtherChecklist][<?php echo $question_sno; ?>][<?php echo $ChecklistField->FieldName; ?>]" class="form-control checklistdatepicker checklists <?php echo $ChecklistField->FieldName; ?>" value="<?php echo $DocTypeName->{$ChecklistField->FieldName}; ?>">
					</span>
				</td>
			<?php } else if ($ChecklistField->FieldType == 'label') {

				$ExpirationDuration = $ChecklistField->ExpirationDuration;
				if (!empty($ChecklistField->StateCode) && in_array($OrderDetails->PropertyStateCode, explode(',', $ChecklistField->StateCode))) {
					$ExpirationDuration = $ChecklistField->StateExpirationDuration;
				}

				if ($ChecklistField->WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {
					if (!empty($OrderSummary->PropertyStateCode) && $OrderSummary->PropertyStateCode == $DocTypeName->StateCode) {
						$ExpirationDuration = $DocTypeName->StateCalculateDays;
					} else {
						$ExpirationDuration = $DocTypeName->CalculateDays;
					}
				}
				?>
				<td>
					<span class="bmd-form-group">
						<input type="text" title="<?php echo $ChecklistField->FieldLabel; ?>" class="form-control checklists" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][OtherChecklist][<?php echo $question_sno; ?>][<?php echo $ChecklistField->FieldName; ?>]" value="<?php echo $DocTypeName->{$ChecklistField->FieldName}; ?>" data-expiration="<?php echo $ExpirationDuration; ?>" readonly>
					</span>
				</td>
			<?php } else { ?>
				<td>
					<span class="bmd-form-group">
						<input type="text" title="<?php echo $ChecklistField->FieldLabel; ?>" class="form-control checklists" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][OtherChecklist][<?php echo $question_sno; ?>][<?php echo $ChecklistField->FieldName; ?>]" value="<?php echo $DocTypeName->{$ChecklistField->FieldName}; ?>">
					</span>
				</td>
			<?php } ?>

		<?php } ?>
	<?php } ?>
	<!-- CHECKLIST DYNAMIC FIELDS -->


	<td class="icon_minus_td">
		<textarea title="Comments" class="form-control checklists" name="checklist[<?php echo $DocTypeName->WorkflowUID; ?>][OtherChecklist][<?php echo  $question_sno; ?>][Comments]" rows="1"><?php if (!empty($DocTypeName->Comments)) { echo  $DocTypeName->Comments; } ?></textarea>
	</td>

</tr>
<?php } ?>