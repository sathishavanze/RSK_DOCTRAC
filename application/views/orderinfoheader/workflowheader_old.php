<?php 
$OrderUID = $this->uri->segment(3);
$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID); 

/* Workflow Status Calculation Starts*/
global $is_stacking_enabled;
global $is_review_enabled;
global $is_audit_enabled;
global $is_raise_exception_enabled;
global $is_clear_exception_enabled;
global $is_shipping_enabled;
global $is_orderreverse_enabled;
global $is_orderreverse_enabled;
global $is_cancel_enabled;
global $is_discard_enabled;


$is_stacking_enabled = 0;
$is_review_enabled = 0;
$is_audit_enabled = 0;
$is_raise_exception_enabled = 0;
$is_clear_exception_enabled = 0;
$is_shipping_enabled = 0;
$is_orderreverse_enabled = 0;
$is_cancel_enabled = 0;
$is_discard_enabled = 0;


$tOrderAssignment = $this->Common_Model->get_row('tOrderAssignment', ['OrderUID'=>$OrderUID]);
// Stacking Enable
if (in_array($OrderDetails->StatusUID, $this->config->item('StackingEnabled')) ) {
    // if (!empty($tOrderAssignment) && $tOrderAssignment->AssignedToUserUID == $this->loggedid) {
        $is_stacking_enabled = 1;
    // }
}
// Audit Enable
if (in_array($OrderDetails->StatusUID, $this->config->item('AuditEnabled')) ) {
    // if (!empty($tOrderAssignment) &&  $tOrderAssignment->AuditAssignedToUserUID == $this->loggedid) {
        $is_audit_enabled = 1;
    // }
}
// Review Enable
if (in_array($OrderDetails->StatusUID, $this->config->item('ReviewEnabled')) ) {
    // if (!empty($tOrderAssignment) && $tOrderAssignment->QcAssignedToUserUID == $this->loggedid) {
        $is_review_enabled = 1;
    // }
}
// Exception Clear Enable
if (in_array($OrderDetails->StatusUID, $this->config->item('ExceptionEnabled')) ) {
    $tOrderException = $this->Common_Model->get('tOrderException', ['OrderUID'=>$OrderUID, 'IsExceptionCleared'=>0]);

    // if (!empty($tOrderException) && !empty($tOrderAssignment) && $tOrderAssignment->ExceptionAssignedToUserUID == $this->loggedid ) {
        $is_clear_exception_enabled = 1;        
    // }
}
// Exception Enable
if (!in_array($OrderDetails->StatusUID, $this->config->item('ExceptionEnabled')) ) {
    $tOrderException = $this->Common_Model->get('tOrderException', ['OrderUID'=>$OrderUID, 'IsExceptionCleared'=>0]);

    if ( empty($tOrderException) ) {
        $is_raise_exception_enabled = 1;        
    }
}
// Shipping Enable
$CheckShippingEnabled = $this->Common_Model->CheckShippingEnabled($OrderUID);
if($CheckShippingEnabled ==  1)
{
    $is_shipping_enabled = 1;
}

// Order Reverse Enable
if ( $OrderDetails->StatusUID >= $this->config->item('keywords')['Stacking Completed'] && !in_array($OrderDetails->StatusUID, $this->config->item('ExceptionEnabled')) ) {

    $tOrderException = $this->Common_Model->get('tOrderException', ['OrderUID'=>$OrderUID, 'IsExceptionCleared'=>0]);
    
    if ( !empty($tOrderAssignment) ) {
        $is_orderreverse_enabled = 1;
    }
}

// Cancel Enable Starts
$orderstatus_array = [];
$orderstatus_array[] = $this->config->item('keywords')['Completed'];
$orderstatus_array[] = $this->config->item('keywords')['Cancelled'];


if (!in_array($OrderDetails->StatusUID, $orderstatus_array)) {
    $tOrderException = $this->Common_Model->get('tOrderException', ['OrderUID' => $OrderUID, 'IsExceptionCleared' => 0]);
    $is_cancel_enabled = 1;        
}
// Cancel Enable Ends

// Disable Enable Starts
if (!in_array($OrderDetails->StatusUID, $orderstatus_array)) {
    if (in_array($OrderDetails->StatusUID, $this->config->item('StackingEnabled'))) {
        if (!empty($tOrderAssignment) && $tOrderAssignment->AssignedToUserUID == $this->loggedid) {
            $is_discard_enabled = 1;
        }        
    }
    elseif (in_array($OrderDetails->StatusUID, $this->config->item('AuditEnabled'))) {
        if (!empty($tOrderAssignment) && $tOrderAssignment->AuditAssignedToUserUID == $this->loggedid) {
            $is_discard_enabled = 1;
        }        
    }
    elseif (in_array($OrderDetails->StatusUID, $this->config->item('ReviewEnabled'))) {
        if (!empty($tOrderAssignment) && $tOrderAssignment->QcAssignedByUserUID == $this->loggedid) {
            $is_discard_enabled = 1;
        }        
    }
    elseif (in_array($OrderDetails->StatusUID, $this->config->item('ExceptionEnabled'))) {
        if (!empty($tOrderAssignment) && $tOrderAssignment->ExceptionAssignedToUserUID == $this->loggedid) {
            $is_discard_enabled = 1;
        }        
    }
}
// Disable Enable Ends

/* Workflow Status Calculation Ends*/



?>
<div class="col-md-12 navmenu">
   <div class="row">
      <ul class="nav nav-pills nav-pills-link txt-white" role="tablist">
         <li class=" nav-item">
            <a class="nav-link ajaxload <?php if ($this->uri->segment(1) == "Ordersummary") {echo "active";}?>" role="tablist" href="<?php echo base_url() . 'Ordersummary/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">Order Info</a>
         </li>

        <li class=" nav-item">
            <a class="nav-link <?php if ($this->uri->segment(1) == "Stacking") {echo "active";}?> stacking_audit" role="tablist" href="<?php echo base_url() . 'Indexing/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">Stacking</a>
        </li>
        

?>

<?php          
if (!in_array($OrderDetails->StatusUID, $this->config->item('StackingEnabled')) && $OrderDetails->IsAuditing == 1 ) {
    // if (!empty($tOrderAssignment) &&  $tOrderAssignment->AuditAssignedToUserUID == $this->loggedid) {
        ?>
        <li class=" nav-item">
            <a class="nav-link <?php if ($this->uri->segment(1) == "Audit") {echo "active";}?> stacking_audit" role="tablist" href="<?php echo base_url() . 'Audit/index/' . $OrderDetails->OrderUID . '/' ?>"  data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">Auditing</a>
        </li>
        <?php
    // }
}
?>

      </ul>
   </div>
</div>


    