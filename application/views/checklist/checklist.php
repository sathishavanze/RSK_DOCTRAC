<?php $CheckListAnswers = $this->Common_Model->getCheckListAnswers($DocTypeName->DocumentTypeUID, $OrderUID, $DocTypeName->WorkflowModuleUID); 
$AlertUID=$this->Common_Model->getAlertData();?>

<!-- GROUP HEADING -->
<?php if(isset($DocTypeName->GroupHeadingName) && !empty($DocTypeName->GroupHeadingName)) { ?>

<tr><td class="checklist-groupheading" colspan="5"><?php echo $DocTypeName->GroupHeadingName; ?></td> </tr>

<?php } ?>
<!-- HEADING -->
<?php if(isset($DocTypeName->HeadingName) && !empty($DocTypeName->HeadingName)) { ?>

<tr class="no-border"><td class="checklist-heading <?php echo $Tmp_HeadingName ?>" colspan="5"><?php echo $DocTypeName->HeadingName; ?></td> </tr>

<?php } ?>


<tr class="questionlist <?php if ($this->uri->segment(1) != 'PreScreen' && $CheckListAnswers->Answer != 'Problem Identified') {
    echo 'ProblemIdentified';
} ?> <?php echo $Tmp_HeadingName ?>" <?php if ($this->uri->segment(1) != 'PreScreen' && $CheckListAnswers->Answer != 'Problem Identified' && $OrderDetails->DefaultChecklistView == 'Show Problem Identified') {
    echo 'style = "display :none"';
} ?> data-delete="<?php echo $DocTypeName->DocumentTypeUID . '~' . $DocTypeName->DocumentTypeName . '~' . $OrderUID; ?>">

	<td class="mb-0"><?php echo $question_sno; ?><?php echo "." ?> <?php echo nl2br($DocTypeName->DocumentTypeName); ?> <?php if ($DocTypeName->ScreenCode) {
    echo '<span style="font-size:9px; background-color: #26A69A; color:#ffffff; padding: 0px 4px; text-align: right; float: right;">' . $DocTypeName->ScreenCode . '</span></td>';
} ?>
	</td>

	<td>
		<select name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][Answer]" title="Findings" data-uid="<?php echo $DocTypeName->DocumentTypeUID; ?>" class="form-control form-check-input1 checklistAlert checklistfindings checklists pre_select">
			<option value="empty"></option>
			<?php 
			if ($DocTypeName->WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {
				$answerList = array('Yes' => 'Yes', 'Issue' => 'Problem Identified', 'NA' => 'NA');
			} else {
				$answerList = array('Completed' => 'Completed', 'Issue' => 'Problem Identified', 'NA' => 'NA');	
			}
			
foreach ($answerList as $answerkey => $value) {
    if ($value == $CheckListAnswers->Answer) {
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
					<label class="form-check-label Dashboardlable" title="<?php echo $ChecklistField->FieldLabel; ?>" for="<?php echo $ChecklistField->FieldName; ?>[<?php echo $DocTypeName->DocumentTypeUID; ?>]" style="color: teal">
						<input class="form-check-input checklists allworkflow" id="<?php echo $ChecklistField->FieldName; ?>[<?php echo $DocTypeName->DocumentTypeUID; ?>]" type="checkbox" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" <?php echo !empty($CheckListAnswers) && $CheckListAnswers->{$ChecklistField->FieldName} == 'Yes' ? 'checked' : ''; ?>>
						<span class="form-check-sign">
							<span class="check"></span>
						</span>
					</label>
				</td>
			<?php
        } else if ($ChecklistField->FieldType == 'combobox') { ?>
				<td>
					<select name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" title="<?php echo $ChecklistField->FieldLabel; ?>" data-uid="<?php echo $DocTypeName->DocumentTypeUID; ?>" class="form-control form-check-input1 checklists pre_select">
						<option value="empty"></option>
						<?php $checklistdropdown = $this->Common_Model->get_dynamicchecklistdropdownfields($ChecklistField->FieldUID);
            foreach ($checklistdropdown as $checklistdropdownkey => $checklistdropdownvalue) { ?>

							<option value="<?php echo $checklistdropdownvalue->DropDownName; ?>" <?php echo ($checklistdropdownvalue->DropDownName == $CheckListAnswers->{$ChecklistField->FieldName}) ? 'selected' : ''; ?>><?php echo $checklistdropdownvalue->DropDownName; ?></option>

						<?php
            } ?>
					</select>
				</td>
			<?php
        } else if ($ChecklistField->FieldType == 'date') {
            $AutoPopulateChecklistClass;
            if ($ChecklistField->WorkflowModuleUID == $this->config->item('Workflows') ['GateKeeping']) {
                if (in_array($DocTypeName->DocumentTypeUID, $this->config->item('AutoPopulateChecklist'))) {
                    $AutoPopulateChecklistClass = 'AutoPopulateChecklist';
                }
            } ?>
				<td>
					<span class="bmd-form-group">

						<?php 
						$Expired_MonthOnlyChecklist = $this->config->item('Expired_MonthOnlyChecklist')[$ChecklistField->WorkflowModuleUID];
						if ($ChecklistField->WorkflowModuleUID == $this->config->item('Workflows')['FHAVACaseTeam'] && (is_array($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($DocTypeName->DocumentTypeUID, $Expired_MonthOnlyChecklist))) { ?>

							<div id="ChecklistDocumentDateInMonth">
								<select class="select2picker form-control checklists <?php echo $ChecklistField->FieldName; ?>" autocomplete="off" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" style="width: 135px !important; background-color: #8aff8a73 !important;" title="<?php echo $ChecklistField->FieldLabel; ?>">
									<option value=""></option>
			        				<?php
			        				$shortMonths = $this->config->item('shortMonths');
			        				foreach ($shortMonths as $shortMonthskey => $shortMonthsvalue) { 
		              					if ($shortMonthsvalue == $CheckListAnswers->{$ChecklistField->FieldName}) {   ?>
			              					<option value="<?php echo $shortMonthsvalue; ?>" selected><?php echo $shortMonthsvalue;?></option>
			              				<?php } else {?>
			              					<option value="<?php echo $shortMonthsvalue; ?>"><?php echo $shortMonthsvalue;?></option>
			              				<?php }
		              				 } ?>
		                      	</select>
	                      	</div>
	                      	
						<?php } else { ?>

							<?php $Expired_Checklist = $this->config->item('Expired_Checklist')[$ChecklistField->WorkflowModuleUID];
							foreach ($Expired_Checklist as $Expired_Checklistkey => $Expired_Checklistvalue) { 
								if ($DocTypeName->DocumentTypeUID == $Expired_Checklistvalue) { ?>
									<input type="text" title="<?php echo $ChecklistField->FieldLabel; ?>" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" class="form-control checklists checklistdatepicker <?php echo $ChecklistField->FieldName;
									echo ' ' . $AutoPopulateChecklistClass; ?> <?php if (in_array($DocTypeName->DocumentTypeUID, $this->config->item('AutoPopulateChecklistAnswerCalc'))) {
										echo "AutoPopulateDocumentDateHide";
									} ?> highlight-row" value="<?php echo $CheckListAnswers->{$ChecklistField->FieldName}; ?>">
								<?php } 					
							}
							
						} ?>
					</span>
				</td>
			<?php
        } else if ($ChecklistField->FieldType == 'label') {
            $ExpirationDuration = $ChecklistField->ExpirationDuration;
            if (!empty($ChecklistField->StateCode) && in_array($OrderDetails->PropertyStateCode, explode(',', $ChecklistField->StateCode))) {
                $ExpirationDuration = $ChecklistField->StateExpirationDuration;
            }
            if ($ChecklistField->WorkflowModuleUID == $this->config->item('Workflows') ['GateKeeping']) {

            	// Checklist Document expiry date if "Vylla Title, LLC (OH)" have calculate 90 days
            	if (stripos($OrderSummary->TitleInsuranceCompanyName, 'Vylla Title') !== false || stripos($OrderSummary->TitleInsuranceCompanyName, 'Vyla Title') !== false) {

            		$ExpirationDuration = $this->config->item('VyllaTitleLLC_ExpiryDays');
            		
            	} elseif (!empty($OrderSummary->PropertyStateCode) && $OrderSummary->PropertyStateCode == $DocTypeName->StateCode) {
                    $ExpirationDuration = $DocTypeName->StateCalculateDays;
                } else {
                    $ExpirationDuration = $DocTypeName->CalculateDays;
                }
                $AutoPopulateDocumentExpiryDate;
                if (in_array($DocTypeName->DocumentTypeUID, $this->config->item('AutoPopulateChecklist'))) {
                    $AutoPopulateDocumentExpiryDateClass = 'AutoPopulateDocumentExpiryDate';
                }
            }
?>
				<td>
					<span class="bmd-form-group">
						<input type="text" title="<?php echo !empty($ExpirationDuration) ? $ExpirationDuration . ' Days' : $ChecklistField->FieldLabel; ?>" class="form-control checklists <?php echo $AutoPopulateDocumentExpiryDateClass; ?> <?php if (in_array($DocTypeName->DocumentTypeUID, $this->config->item('AutoPopulateChecklistAnswerCalc'))) {
                echo "MinimumExpiryDate";
            } ?>" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" value="<?php echo $CheckListAnswers->{$ChecklistField->FieldName}; ?>" data-expiration="<?php echo $ExpirationDuration; ?>" readonly>
					</span>
				</td>
			<?php
        } else { ?>
				<td>
					<span class="bmd-form-group">
						<input type="text" title="<?php echo $ChecklistField->FieldLabel; ?>" class="form-control checklists" name="checklist[<?php echo $ChecklistField->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][<?php echo $ChecklistField->FieldName; ?>]" value="<?php echo $CheckListAnswers->{$ChecklistField->FieldName}; ?>">
					</span>
				</td>
			<?php
        } ?>

		<?php
    } ?>
	<?php
} ?>
	<!-- CHECKLIST DYNAMIC FIELDS -->


	<td class="icon_minus_td">

		<?php
if (!empty($DocTypeName->ParentDocumentTypeUID)) { ?>
			<input type="text" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][Comments]" class="form-control checklists child-<?php echo $DocTypeName->ParentDocumentTypeUID; ?>" data-ChildLable="<?php echo $DocTypeName->ChildLabel; ?>" value="<?php if (!empty($CheckListAnswers->Comments)) {
        echo $CheckListAnswers->Comments;
    } ?>"><?php if(in_array($DocTypeName->DocumentTypeUID,$AlertUID)){ ?>
		<a href="javascript::0" class="alertMessage" data-delete="<?php echo $DocTypeName->DocumentTypeUID . '~' . $DocTypeName->DocumentTypeName . '~' . $OrderUID; ?>"><i class="icon-warning" style="color:green" title="Alert"></i></a></span>
		<?php } ?>
			<?php
} else {
    if ($field_type == 'select') {
        $getSource = $this->Common_Model->getHtmlSource($table_name, $table_key);
?>
				<select class="pre_select form-control checklists parentUID" data-uid="<?php echo $DocTypeName->DocumentTypeUID; ?>" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][SelectIn]">
					<option value=""> Select... </option>
					<?php
        if ($getSource) {
            foreach ($getSource as $key => $value) {
                if (isset($CheckListAnswers->SelectIn) && $CheckListAnswers->SelectIn == $value[$table_key]) {
                    echo '<option value="' . $value[$table_key] . '" selected>' . $value[$table_key] . '</option>';
                } else {
                    echo '<option value="' . $value[$table_key] . '">' . $value[$table_key] . '</option>';
                }
            }
        }
?>
				</select>

			<?php
    }
    if ($field_type == 'checkbox') { ?>
				<input type="checkbox" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][checkIn]" class="checklists" value="yes" id="check_option_2" <?php if (isset($CheckListAnswers->checkIn) && $CheckListAnswers->checkIn == 'yes') {
            echo "checked";
        } ?>><label for="check_option_2"> IsApplicable </label>
			<?php
    }
    if ($field_type == 'radio') { ?>
				<input type="radio" class="checklists" id="yes" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][radioIn]" value="yes" <?php if (isset($CheckListAnswers->radioIn) && $CheckListAnswers->radioIn == 'yes') {
            echo "checked";
        } ?>>
				<label for="yes">Yes</label>
				<input type="radio" class="checklists" id="no" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][radioIn]" value="no" <?php if (isset($CheckListAnswers->radioIn) && $CheckListAnswers->radioIn == 'no') {
            echo "checked";
        } ?>>
				<label for="no">No</label>
				<input type="radio" class="checklists" id="na" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][radioIn]" value="none" <?php if (isset($CheckListAnswers->radioIn) && $CheckListAnswers->radioIn == 'none') {
            echo "checked";
        } ?>>
				<label for="na">Not Applicable</label>
			<?php
    }
    if (empty($field_type) || $field_type == 'text') { ?>
				<textarea title="Comments" class="form-control checklists" name="checklist[<?php echo $DocTypeName->WorkflowModuleUID; ?>][<?php echo $DocTypeName->DocumentTypeUID; ?>][Comments]" rows="1"><?php if (!empty($CheckListAnswers->Comments)) {
            echo $CheckListAnswers->Comments;
        } ?></textarea><span>
		<?php if(in_array($DocTypeName->DocumentTypeUID,$AlertUID)){ ?>
		<a href="javascript::0" class="alertMessage" data-delete="<?php echo $DocTypeName->DocumentTypeUID . '~' . $DocTypeName->DocumentTypeName . '~' . $OrderUID; ?>"><i class="icon-info22" style="color:#009baf ! important;font-size: 15px;margin-top: 10px !important;" title="Alert"></i></a></span>
		<?php } ?>
		<?php
    }
} ?>

	</td>

</tr>