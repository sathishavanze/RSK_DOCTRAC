<style>
	.highlight{
		border-bottom: 2px solid #f44336;
	}
	.check_highlight{
		border: 2px solid #f44336!important;
	}
	.ProblemIdentifiedbtn{
    font-weight: 500;
    text-decoration: underline;
    font-size: 11px;
    text-align: right;
    margin: 0;
    display: block;
    cursor: pointer;
    color: #5a5a5a;
}
.select2-drop .select2-results li:nth-child(1) .select2-result-label{
	height: 26px;
}

input.form-control.checklists{
	width: 135px;
}
.ml-30 {
		margin-left: 30px;
}
</style>





<div class="col-md-12 text-right" style="margin-top: 5px;"> 
	<a class="btn-checklist-export-xlsx" data-checklisttableid="chicklisttableshow<?php if($this->uri->segment(1) == 'PreScreen'){ echo '_'.preg_replace('/[\s\/()]+/', '_', $workflowtitle); } ?>">
		<i class="fa fa-file-excel-o" title="Excel Export" aria-hidden="true" style="font-size:18px;color:#0B781C;cursor: pointer;"></i>
	</a> 

	<a class="btn-checklist-export-pdf" data-checklisttableid="chicklisttableshow<?php if($this->uri->segment(1) == 'PreScreen'){ echo '_'.preg_replace('/[\s\/()]+/', '_', $workflowtitle); } ?>" style="margin-left: 6px;">
		<i class="fa fa-file-pdf-o" title="PDF Export" aria-hidden="true" style="font-size:18px;color:red;cursor: pointer;"></i>
	</a>

	<!-- <button class="btn btn-secondary buttons-excel buttons-html5 btn-checklist-export-xlsx" tabindex="0" aria-controls="customerlisttable" type="button" data-checklisttableid="chicklisttableshow<?php if($this->uri->segment(1) == 'PreScreen'){ echo '_'.preg_replace('/[\s\/()]+/', '_', $workflowtitle); } ?>">
		<span>Excel</span>
	</button>  -->
	<!-- <button class="btn btn-secondary buttons-pdf buttons-html5 btn-checklist-export-pdf" tabindex="0" aria-controls="customerlisttable" type="button" data-checklisttableid="chicklisttableshow<?php if($this->uri->segment(1) == 'PreScreen'){ echo '_'.preg_replace('/[\s\/()]+/', '_', $workflowtitle); } ?>">
		<span>PDF</span>
	</button>  -->
</div>

<?php if($this->uri->segment(1) != 'PreScreen' && $this->uri->segment(1) != 'DocChase'){ ?>
	<p  class="ProblemIdentifiedbtn pull-right" <?php echo ($OrderDetails->DefaultChecklistView == 'Show Issue(s)') ?  "data-status='show' " : "data-status='hide'"; ?>> <?php echo ($OrderDetails->DefaultChecklistView == 'Show Problem Identified') ?  "Show All Checklist" : "Show Issue(s)" ; ?></p>
	<!-- <h3 class="welcome_head click_expand ProblemIdentifiedbtn" title="Show All Checklist">Show All Checklist</h3> -->
<?php }?>

<?php if($this->uri->segment(1) == 'PreScreen'){ ?>
	<table class="table table-striped check_list_table checklisttable" id="chicklisttableshow_<?php echo preg_replace('/[\s\/()]+/', '_', $workflowtitle); ?>" data-workflowtitle="<?php echo preg_replace('/[\s\/()]+/', ' ', $workflowtitle); ?>" data-workflowpdftitle="<?php echo $workflowtitle; ?>" cellspacing="0" width="100%"  style="width:100%;margin:0;">
<?php } else {?>
	<table class="table check_list_table checklisttable" id="chicklisttableshow" data-workflowtitle="<?php echo $this->uri->segment(1); ?>" data-workflowpdftitle="<?php echo $this->uri->segment(1); ?>" cellspacing="0" width="100%"  style="width:100%;margin:0;">
<?php } ?>
<thead>
	<tr>
		<th style="width:30%;">Checklist</th>
		<th style="text-align:center;">Findings</th>
		<!-- CHECKLIST DYNAMIC FIELDS -->
		<?php foreach ($ChecklistFields as $key => $ChecklistField) { ?>

			<?php if($ChecklistField->FieldType == 'checkbox') { ?>
				<th class="text-center"><?php echo $ChecklistField->FieldLabel; ?></th>			
			<?php } else if($ChecklistField->FieldType == 'combobox') { ?>
				<th><?php echo $ChecklistField->FieldLabel; ?></th>			
			<?php } else { 
				$ExpirationDuration = '';
				if($ChecklistField->FieldType == 'label') {
					$ExpirationDuration = $ChecklistField->ExpirationDuration;
					if(!empty($ChecklistField->StateCode) && in_array($OrderDetails->PropertyStateCode, explode(',', $ChecklistField->StateCode))) {
						$ExpirationDuration = $ChecklistField->StateExpirationDuration;
					}	
				}
				
				?>
				<th><?php echo $ChecklistField->FieldLabel.(($ExpirationDuration != 0) ? '<br><span class="ml-30"> ('.$ExpirationDuration.' Days) </span>' : ''); ?></th>			
			<?php } ?>

		<?php } ?>
		<!-- CHECKLIST DYNAMIC FIELDS -->
		<th>Comments</th>
	</tr>
</thead>

