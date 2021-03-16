<?php
$WorkflowDocuments = $this->Common_Model->GetWorkflowDocuments($WorkflowModuleUID);
?>

<?php foreach ($WorkflowDocuments as $Document) { ?>
    <a target="_blank" href="<?php echo base_url().$Document->DocumentURL; ?>" type="button" class="viewFile" title="<?php echo $Document->DocumentName; ?>" style="margin-left: 5px;"><img src="assets/img/icon.png" style="max-width: 21px;"></a>
<?php } ?>
