<!-- Followup Queue -->
<?php
$data['mReasons'] = $this->Common_Model->get_mqueuesmreasons();
$this->load->view('orderinfoheader/followupmodal',$data);
$this->load->view('orderinfoheader/staticqueuefollowupmodal',$data); 
$this->load->view('orderinfoheader/confirmschedulemodal',$data);
$this->load->view('orderinfoheader/Queuereportmodal',$data); 
 
?>

<!--WORKFLOW COMMENTS POPUP CONTENT -->                
<?php $this->load->view('orderinfoheader/workflownotesmodal'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/kendo.default-v2.min.css" />
<script src="<?php echo base_url(); ?>assets/js/kendo.all.min.js"></script>

<!-- QUEUE JS -->
<script src="<?php echo base_url(); ?>assets/js/Queue.js?reload=2.5.9"></script>

<!-- Multi select -->
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<script type="text/javascript" src="assets/lib/fselect/fSelect.js"></script>