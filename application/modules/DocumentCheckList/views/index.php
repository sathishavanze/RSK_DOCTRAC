
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<!-- <link rel="stylesheet" type="text/css" href="jquery.datetextentry.css"> -->
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


 .white-box {
  background: #ffffff;
  padding: 25px;
  margin-bottom: 15px;
}
.btn-outline {
  color: #fb9678;
  background-color: transparent;
}
.btn-danger.disabled {
  background: #fb9678;
  border: 1px solid #fb9678;
}
.exception{
	border: 1px dotted black;
  border-radius: 5px;
  padding-top: 10px;
  padding-bottom: 10px;
}
#iconc{
  float:right;
  margin-top:-19px;
}
.panel-heading-divider>p{
  border-bottom: 1px solid #d9d9d9;
  margin-left: 11px;
  padding-left: 0px;
  padding-right: 0;

  font-weight: 500px;

}
.panel-heading-divider{
 width: 100%;

}
.productadd_button{
  margin-top: -40px;
  font-size: 23px;
}
#Deleterow{
  margin-top: -40px; 
  font-size: 23px;
}
#fromdate{
  margin-top: -28px;
}
#ToDate{
 margin-top: -28px;
}
.Borrower
{
  width: 100%;
}
.bmd-form-group .bmd-label-floating, .bmd-form-group .bmd-label-placeholder {
  top: -18px;
}
input {
  margin-bottom: 5px;
}
</style>

<div class="col-md-12 pd-0" >
 <div class="card mt-0">
  <div class="card-header tabheader" id="">
   <div class="col-md-12 pd-0">
    <div id="headers" style="color: #ffffff;">
     <!-- Order Info Header View -->	
     <?php $this->load->view('orderinfoheader/orderinfo'); ?>

   </div>
 </div>
</div>
<div class="card-body pd-0">
 <!-- Workflow Header View -->	
 <?php $this->load->view('orderinfoheader/workflowheader'); ?>
 <div class="tab-content tab-space">
  <div class="tab-pane active" id="summary">
   <form action="#"  name="orderform" id="order_frm">
    <input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderSummary->OrderUID; ?>">
    <div class="col-md-12 pd-0">
    </div>
    <div class="row">

      <!-- <?php echo'<pre>';print_r($OrderSummary);?> -->
       <div class="col-md-3">
        <div class="form-group bmd-form-group">
         <label for="Customer" class="bmd-label-floating">Client<span class="mandatory"></span></label>
         <select class="select2picker form-control"  id="Customer" name="Customer"  required>

          <?php 

          foreach ($Customers as $key => $value) { 
            if($value->CustomerUID == $OrderSummary->CustomerUID)
              { ?>
                <option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
              <?php  }


              ?>

            <?php } ?>                
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group bmd-form-group">
         <label for="CustomerRefNum" class="bmd-label-floating">Client Ref Number</label>
         <input type="text" class="form-control" id="CustomerRefNum" name="CustomerRefNum" value="<?php echo $OrderSummary->CustomerReferenceNumber; ?>" />
       </div>
     </div>
     <div class="col-md-3 productrow">
      <div class="form-group bmd-form-group">
       <label for="ProductUID" class="bmd-label-floating">Product<span class="mandatory"></span></label>
       <select class="select2picker form-control ProductUID"  id="ProductUID" name="ProductUID" required>
        <option value="<?php echo $OrderDetails->ProductUID; ?>" selected><?php echo $OrderDetails->ProductName; ?></option>
      </select>
    </div>
  </div>
  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="ProjectUID" class="bmd-label-floating">Project<span class="mandatory"></span></label>
     <select class="select2picker form-control ProjectUID"  id="ProjectUID" name="ProjectUID" required>
      <option value="<?php echo $OrderDetails->ProjectUID; ?>" selected><?php echo $OrderDetails->ProjectName; ?></option>
    </select>
  </div>
</div>

</div>

<div class="row productfield_add mt-10">

 <div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
    <label for="TpoName" class="bmd-label-floating">TPO Name</label>
    <select class="select2picker form-control TpoName"  id="TpoName" name="TpoName" >
      <option value="<?php echo $TPOName->CorrespondentLenderName; ?>" selected><?php echo $TPOName->CorrespondentLenderName; ?></option>
    </select>
  </div> 
</div>

<div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
   <label for="PriorityUID" class="bmd-label-floating">Priority<span class="mandatory"></span></label>
   <select class="select2picker form-control PriorityUID"  id="PriorityUID" name="PriorityUID" required>
    <!--   <option value=""></option> -->
    <option value="<?php echo $OrderDetails->PriorityUID;?>" selected><?php echo $OrderDetails->PriorityName;?>  </option>

  </select>
</div>
</div>

<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="AltORderNumber" class="bmd-label-floating">Alternate Order Number</label>
   <input type="text" class="form-control" id="AltORderNumber" name="AltORderNumber" value="<?php echo $OrderSummary->AltOrderNumber; ?>">
 </div>
</div>
<div class="col-md-3" id="divLoanNumber">
  <div class="form-group bmd-form-group">
   <label for="LoanNumber" class="bmd-label-floating">Loan Number</label>
   <input type="text" class="form-control" id="LoanNumber" name="LoanNumber" value="<?php echo $OrderSummary->LoanNumber; ?>">
 </div>
</div>


</div>

<div class="row ">
 <div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyAddress1" class="bmd-label-floating">Address Line 1<span class="mandatory"></span></label>
   <input type="text" class="form-control" id="PropertyAddress1" name="PropertyAddress1" required value="<?php echo $OrderSummary->PropertyAddress1; ?>">
 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyAddress2" class="bmd-label-floating">Address Line 2</label>
   <input type="text" class="form-control" id="PropertyAddress2" name="PropertyAddress2" value="<?php echo $OrderSummary->PropertyAddress2; ?>">
 </div>
</div>

<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyZipcode" class="bmd-label-floating">Zipcode<span class="mandatory"></span></label>
   <input type="text" class="form-control" id="PropertyZipcode" name="PropertyZipcode" required value="<?php echo $OrderSummary->PropertyZipCode; ?>">
   <span data-modal="zipcode-form" class="label label-success label-zip md-trigger" id="zipcodeadd" style="display: none;">Add Zipcode</span>
 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyCityName" class="bmd-label-floating">City<span class="mandatory"></span></label>
   <input type="text" class="form-control" id="PropertyCityName" name="PropertyCityName" value="<?php echo $OrderDetails->PropertyCityName; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
   <ul class="dropdown-menu dropdown-style PropertyCityName"></ul>
 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyCountyName" class="bmd-label-floating">County<span class="mandatory"></span></label>
   <input type="text" class="form-control" id="PropertyCountyName" name="PropertyCountyName"  value="<?php echo $OrderDetails->PropertyCountyName; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
   <ul class="dropdown-menu dropdown-style PropertyCountyName"></ul>
 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="PropertyStateCode" class="bmd-label-floating">State<span class="mandatory"></span></label>
   <input type="text" class="form-control" id="PropertyStateCode" name="PropertyStateCode"  value="<?php echo $OrderDetails->PropertyStateCode; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" required>
   <ul class="dropdown-menu dropdown-style PropertyStateCode"></ul>
 </div>
</div>

</div>
<div class="col-md-12 pd-0">
  <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Doc Check-in</h4>                   
</div>


<div class="row mt-10" >
  <div class="Borrower">
    <?php 
    $sno = 1;
    if(sizeof($BorrowerName) == 0)
    {
      ?>
      <div class="col-sm-12 form-group">
        <div class="form-group bmd-form-group" >
         <label for="BorrowerName" class="bmd-label-floating">Borrower Name</label>
         <input type="text" class="form-control" id="BorrowerName" name="BorrowerName[]" ><i class="fa fa-plus-circle fa-2x material-icons mdl-textfield__label__icon productadd_button" style="float:right;" ></i>
       </div>
     </div>

     <?php

   }
   foreach ($BorrowerName as $row) {
    if($sno == 1)
    {
      ?>
      <div class="col-sm-12 form-group">
        <div class="form-group bmd-form-group" >
         <label for="BorrowerName" class="bmd-label-floating">Borrower Name</label>
         <input type="text" class="form-control" id="BorrowerName" name="BorrowerName[]" value="<?php echo $row->BorrowerFirstName; ?>"><i class="fa fa-plus-circle fa-2x material-icons mdl-textfield__label__icon productadd_button" style="float:right;" ></i>
       </div>
     </div>
     <?php
   }
   else
   {
    ?>
    <div class="col-md-12 col-sm-12 form-group delete">
      <div class="form-group bmd-form-group" >
       <label for="BorrowerName" class="bmd-label-floating">Borrower Name</label>
       <input type="text" class="form-control" id="BorrowerName" name="BorrowerName[]" value="<?php echo $row->BorrowerFirstName; ?>">
       <i class="fa fa-minus-circle fa-2x material-icons mdl-textfield__label__icon productremove_button Deleterow" id="Deleterow" style="float:right;" ></i>
     </div>
   </div>
   <?php
 }
 $sno++;
}

?>
</div>

</div>

<?php
$ClosingDateTime = '';
$DisbursementDate = '';
$SettlementAgentName = '';
$FileNumber = '';
                     //$LoanAmount = '';
$RecordingFeesDeed = '';
$RecordingFeesMortgage = '';
                     //$LoanPurpose = '';
                     //$LoanType = '';
$IncomingTrackingNumber = '';
foreach ($BorrowerDocumentDetails as $DocumentDetails) {
  $ClosingDateTime = $DocumentDetails->ClosingDateTime;
  $DisbursementDate = $DocumentDetails->DisbursementDate;
  $SettlementAgentName = $DocumentDetails->SettlementAgentName;
  $FileNumber = $DocumentDetails->FileNumber;
                        //$LoanAmount = $DocumentDetails->LoanAmount;
  $RecordingFeesDeed = $DocumentDetails->RecordingFeesDeed;
  $RecordingFeesMortgage = $DocumentDetails->RecordingFeesMortgage;
                        //$LoanPurpose = $DocumentDetails->LoanPurpose;
                        //$LoanType = $DocumentDetails->LoanType;
  $IncomingTrackingNumber = $DocumentDetails->IncomingTrackingNumber;
}

?>
<div class="row ">
  <div class="col-md-3">
   <div class="form-group bmd-form-group">
     <label for="DisbursementDate" class="bmd-label-floating" id="DisbursementDate" >Disbursement Date</label>
     <?php
     if(empty($DisbursementDate)): ?>
       <input type="text" class="form-control input-xs date-entry1 DisbursementDate"  id="DisbursementDate" />
       <input type="hidden" class="DtHidden" name="DisbursementDate" id="hiddenDisbursementDate"  />
       <?php else : ?>
        <input type="text" class="form-control input-xs date-entry1 DisbursementDate"  id="DisbursementDate" value="<?php echo $DisbursementDate; ?>"/>
        <input type="hidden" class="DtHidden" name="DisbursementDate" id="hiddenDisbursementDate"  value="<?php echo date('mm/dd/YYYY',strtotime($DisbursementDate));?>"/>
      <?php endif ; ?>
      <span class="input-group-addon calendar-icon">
        <i class="icon-calendar"></i>
      </span>
    </div>        
  </div>
  <div class="col-md-3">
   <div class="form-group bmd-form-group">
    <label for="ClosingDateTime" class="bmd-label-floating" id="ClosingDateTime" >Closing Date</label>
    <?php
    if(empty($ClosingDateTime)): ?>
      <input type="text" class="form-control input-xs date-entry1" id="ClosingDateTime"/>
      <input type="hidden" class="DtHidden" name="ClosingDateTime" id="hiddenClosingDateTime"  />
      <?php else : ?>
       <input type="text" class="form-control input-xs date-entry1" id="ClosingDateTime"  value="<?php echo $ClosingDateTime; ?>"/>
       <input type="hidden" class="DtHidden" name="ClosingDateTime" id="hiddenClosingDateTime"  value="<?php echo date('mm/dd/YYYY',strtotime($ClosingDateTime));?>"/>
     <?php endif ; ?>

     <span class="input-group-addon calendar-icon">
      <i class="icon-calendar"></i>
    </span>
  </div>
</div>

 <div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="RecordingFeesDeed" class="bmd-label-floating">Recording Fees Deed</label>
   <input type="text" class="form-control" id="RecordingFeesDeed" data-type="currency" name="RecordingFeesDeed" value="<?php echo $RecordingFeesDeed; ?>">
 </div>
</div>  
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="RecordingFeesMortgage" class="bmd-label-floating">Recording Fees Mortgage</label>
   <input type="text" class="form-control" id="RecordingFeesMortgage" data-type="currency" name="RecordingFeesMortgage" value="<?php echo $RecordingFeesMortgage; ?>">
 </div>
</div> 





</div>
<div class="row mt-10">
 
<div class="col-md-3">
  <div class="form-group bmd-form-group">
    <div class="input-group">
     <label for="LoanAmount" class="bmd-label-floating">Loan Amount</label>
     <input type="text"  data-type="currency" class="form-control" id="LoanAmount" name="LoanAmount" value="<?php echo $OrderSummary->LoanAmount; ?>">
   </div>
 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="loanpurpose" class="bmd-label-floating">Loan Purpose</label>
   <input type='text' class=" form-control loanpurpose"  id="loanpurpose" name="loanpurpose" value='<?php echo $OrderSummary->LoanPurpose; ?>'>

 </div>
</div>



 <div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="loantype" class="bmd-label-floating">Loan Type</label>
   <input type="text" class=" form-control loantype"  id="loantype" name="loantype" value="<?php echo $OrderSummary->LoanType; ?>">

 </div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="IncomingTrackingNumber" class="bmd-label-floating">Incoming Tracking Number</label>
   <input type="text" class="form-control" id="IncomingTrackingNumber" name="IncomingTrackingNumber" value="<?php echo $IncomingTrackingNumber; ?>">
 </div>
</div>


<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="FileNumber" class="bmd-label-floating">File Number</label>
   <input type="text" class="form-control" id="FileNumber" name="FileNumber" value="<?php echo $FileNumber; ?>">
 </div>
</div>

<div class="col-sm-12 form-group pull-right">
  <p class="text-right">
    <input type="hidden" class="save">
  </p>
</div>

</div>


<?php 

if(!empty($BorrowerDocumentDetails)){
  foreach ($BorrowerDocumentDetails as $k => $v) { ?>
    <div class="row mt-10">
      <div class="col-md-3">
        <div class="form-group bmd-form-group">
         <label for="InvestorName" class="bmd-label-floating">Investor</label>
         <select  class="form-control InvestorName select2picker"  id="InvestorName" name="InvestorName">
          <option></option>
          <?php 
          foreach ($Investors as $key => $value) {
            if(stripos($v->InvestorName, $value->InvestorName) !== false){  ?>
              <option value="<?php echo  $value->InvestorUID; ?>" selected><?php echo  $value->InvestorName; ?></option>

            <?php   } else{  ?>
              <option value="<?php echo  $value->InvestorUID; ?>" ><?php echo  $value->InvestorName; ?></option>
            <?php  }   } ?>
          </select>    
        </div>
      </div>

      <div class="col-md-3">
       <div class="form-group bmd-form-group">
         <label for="CustodianName" class="bmd-label-floating">Custodian</label>
         <select  class="form-control CustodianName select2picker"  id="CustodianName" name="CustodianName">
          <option></option>
          <?php 
          foreach ($Custodians as $key => $value) { 
            if(stripos($v->CustodianName, $value->CustodianName) !== false){  ?>
             <option value="<?php echo  $value->CustodianUID; ?>" selected><?php echo  $value->CustodianName; ?></option>


           <?php } else {  ?>
             <option value="<?php echo  $value->CustodianUID; ?>"><?php echo  $value->CustodianName; ?></option>
           <?php  }  } ?> 
         </select>    
       </div>
     </div>

     <div class="col-md-3">
       <div class="form-group bmd-form-group"> 
         <label for="SettlementAgentName" class="bmd-label-floating">Settlement Agent </label>
         <select  class="form-control InvestorName select2picker"  id="SettlementAgentName" name="SettlementAgentName">
          <option></option>
          <?php 
          foreach ($SettlementAgent as $key => $value) { 
            if(stripos($v->SettlementAgentName, $value->SettlementAgentName) !== false){  ?>
             <option value="<?php echo  $value->SettlementAgentUID; ?>" selected><?php echo  $value->SettlementAgentName; ?></option>
           <?php   } else {  ?>
             <option value="<?php echo  $value->SettlementAgentUID; ?>"><?php echo  $value->SettlementAgentName; ?></option>
             ?>
           <?php  }  } ?> 
         </select>           
       </div>
     </div>


     <div class="col-md-3">
       <div class="form-group bmd-form-group">
         <label for="BussinessChannel" class="bmd-label-floating">Business Channel</label>
         <select  class="form-control InvestorName select2picker"  id="BussinessChannel" name="BussinessChannel">
          <option></option>
          <?php 
          foreach ($BusinessChannel as $key => $value) {
            if(stripos($v->BussinessChannel, $value->BusinessChannelName) !== false) { ?>
              <option value="<?php echo  $value->BusinessChannelUID; ?>" selected><?php echo  $value->BusinessChannelName; ?></option>
            <?php   } else {  ?>
             <option value="<?php echo  $value->BusinessChannelUID; ?>"><?php echo  $value->BusinessChannelName; ?></option>
           <?php  }  } ?> 
         </select> 
       </div>
     </div>
   </div>

 <?php   } }else{ ?>

  <div class="row mt-10">
    <div class="col-md-3">
      <div class="form-group bmd-form-group">
       <label for="InvestorName" class="bmd-label-floating">Investor</label>
       <select  class="form-control InvestorName select2picker"  id="InvestorName" name="InvestorName">
        <option></option>
        <?php 
        foreach ($Investors as $key => $value) {    ?>
          <option value="<?php echo  $value->InvestorUID; ?>" ><?php echo  $value->InvestorName; ?></option>
        <?php  } ?>
      </select>    
    </div>
  </div>


  <div class="col-md-3">
   <div class="form-group bmd-form-group">
     <label for="CustodianName" class="bmd-label-floating">Custodian</label>
     <select  class="form-control CustodianName select2picker"  id="CustodianName" name="CustodianName">
      <option></option>
      <?php 
      foreach ($Custodians as $key => $value) {  ?>   
       <option value="<?php echo  $value->CustodianUID; ?>"><?php echo  $value->CustodianName; ?></option>
     <?php  }  ?> 
   </select>    
 </div>
</div>



<div class="col-md-3">
 <div class="form-group bmd-form-group"> 
   <label for="SettlementAgentName" class="bmd-label-floating">Settlement Agent </label>
   <select  class="form-control InvestorName select2picker"  id="SettlementAgentName" name="SettlementAgentName">
    <option></option>
    <?php 
    foreach ($SettlementAgent as $key => $value) {   ?>
     <option value="<?php echo  $value->SettlementAgentUID; ?>"><?php echo  $value->SettlementAgentName; ?></option>
     ?>
   <?php  }  ?> 
 </select>           
</div>
</div>

<div class="col-md-3">
 <div class="form-group bmd-form-group">
   <label for="BussinessChannel" class="bmd-label-floating">Business Channel</label>
   <select  class="form-control InvestorName select2picker"  id="BussinessChannel" name="BussinessChannel">
    <option></option>
    <?php 
    foreach ($BusinessChannel as $key => $value) { ?>          
     <option value="<?php echo  $value->BusinessChannelUID; ?>"><?php echo  $value->BusinessChannelName; ?></option>
   <?php  }  ?> 
 </select> 
</div>
</div>
</div>
 <?php } ?>





<div class="field_add">
 <div class="row mt-10">
  <div class="col-md-12">
   <div class="row">
   </div>
 </div>
</div>
</div>
<div class="col-sm-12 form-group pull-right mt-20">
  <p class="text-right">
    <?php if ($OrderSummary->StatusUID != $this->config->item('keywords')['Cancelled']) {?>
      <button type="submit" class="btn btn-space btn-social btn-color btn-twitter single_submit pull-right" value="1">Update</button>
    <?php }?>
    <?php $this->load->view('orderinfoheader/workflowbuttons'); ?>

  </p>
</div>
</form>
</div>
</div>
</div>
</div>

<?php if (!empty($ExceptionList)) { ?>


 <div class="card mt-10">
   <div class="card-body">
    <div class="row">
     <div class="col-md-12">
      <h4 class="text-danger">Exception List</h4>
    </div>
    <div class="col-md-12 white-box perfectscrollbar" id="exception_list" style="max-height: 330px;overflow-y:scroll;">
      <?php foreach ($ExceptionList as $key => $value) { ?>

        <div class="btn-outline row col-md-12 m-b-10 m-t-30 mb-20 exception"> 
          <div class="col-md-6"> 

            <p align="left" style="margin-bottom: 0px;" class="col-md-12 Exception_label">Exception 
              <span class="Exception_span">
                <span class="fa fa-exclamation-circle"> </span>
              </span> 
            </p>

            <p align="left" style="margin-bottom: 0px;" class="col-md-12">Exception Name: 
              <span class="Exception_Name_span"><?php echo $value->ExceptionName; ?> </span>
            </p>

            <p align="left" style="margin-bottom: 0px;" class="col-md-12">Remarks: 
              <span class="Exception_Remark_span"><?php echo $value->ExceptionRemarks; ?></span>
            </p>
          </div>

          <div class="col-md-6"> 
            <p align="right" style="margin-bottom: 0px;" class="col-md-12">Date: 
              <span class="Exception_Date_span">: <?php echo $value->ExceptionRaisedDateTime; ?></span>                  
            </p>

            <p align="right" style="margin-bottom: 0px;" class="col-md-12">Exception Status: 
             <span class="Exception_Name_span"><?php echo $value->IsExceptionCleared == 1 ? 'Cleared' : 'Pending'; ?> </span>
           </p>
         </div>

       </div>

       <?php 
     } ?>
   </div>
 </div>
</div>
</div>
<?php } ?>
</div>

<?php $this->load->view('orderinfoheader/workflowpopover'); ?>

<!-- Delete Document Modal -->
<div class="modal fade modal-mini modal-primary custommodal" id="deletedocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-cancel-circle2"></i></button>
      </div>
      <div class="modal-body">
        <p>Are you sure want to delete this ?</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-link No" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-success btn-link Yes">Yes
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Delete Document Modal -->

<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>
<!-- <script src="<?php echo base_url(); ?>assets/datepicker/jquery/1.10.2/jquery.min.js"></script>

  <script src="<?php echo base_url(); ?>assets/datepicker/jquery.datetextentry.js"></script> -->
  <script src="<?php echo base_url(); ?>assets/plugins/EditableDatePicker.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/formatcurrency.js?reload=1.0.1"></script>

  <script type="text/javascript">
    $('select[name="loantype"] option[value="<?php  echo $OrderSummary->LoanType;  ?>"]').attr("selected","selected");
    $('select[name="loanpurpose"] option[value="<?php  echo $OrderSummary->LoanPurpose;  ?>"]').attr("selected","selected");
    $('#documentpreview_pane').hide();
/*$('#menu').datetextentry({
    on_change : function(date_str) {
        $('#valid-date10').text(date_str);
}
});
$('#ToDate').datetextentry({
    on_change : function(date_str) {
        $('#valid-date10').text(date_str);
}
});*/


var select_option='';
select_option +='<?php
$documenttypes = array('Final Report' => 'Final Report', 'Stacking' => 'Stacking', 'Others' => 'Others');
foreach ($documenttypes as $key => $type) {
  if ($type=="Others") {
    echo '<option value="'.$type.'" selected>'. $type .'</option>';;

  }
  else{
    echo '<option value="'.$type.'">'. $type .'</option>';

  }
} ?>'

var sync_show_notification_flag = true;

var stacking_document_id = $('.Status:checked').attr('id');


$('.exception').hover(
  function(){ $(this).addClass('btn-danger') },
  function(){ $(this).removeClass('btn-danger') },
  );


$(document).ready(function(){
  //append document for view 
  $('#Doc_show').html(' <li class="nav-item  dropdown show">                <a class="nav-link doc_preview" id="navbarDropdownMenuLink" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">                 <i class="icon-files-empty" style="font-size:20px"></i>                <div class="ripple-container "></div></a>                <div class="dropdown-menu dropdown-menu-right DocumentList" aria-labelledby="navbarDropdownMenuLink">                </div>              </li>');
  $('.doc_preview').click(function()
  {
   var OrderUID=$('#OrderUID').val();
   $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>DocumentCheckList/Document_List',
    data: {'OrderUID' :OrderUID}, 
    success : function(data)
    {
      $('.DocumentList').html(data);
      $('#ShowDocument').modal('show');
    }
  });
 });
  $('.main-panel').scrollTop = 0;
      // Default Hide 
      $('.perfectScrollbar').perfectScrollbar('update');


    // $('#Customer').trigger('change');
    // $('#PropertyZipcode').trigger('blur');

    filetoupload=[];
    $('#filebulk_entry').dropify();	

    $("select.select2picker").select2({
     theme: "bootstrap",
   });


    $('.changeentry').click(function()
    {
     $('.textentry').toggle();
     $('.fileentry').toggle();
     $('#preview-table').html('');
     $('#imported-table').html('');

   });


    $('#btndocumentpreview_pane').off('click').on('click', function (e) {

      $('#documentpreview_pane').slideToggle('slow');
    });


    $('#btncloseupload').off('click').on('click', function (e) {
      $('#documentpreview_pane').slideUp('slow');
    });

    /* --- Dropify initialization starts */

    $('.dropify').dropify();

                // Used events
                var drEvent = $('.dropify').dropify();

                drEvent.on('dropify.beforeClear', function(event, element){
                	// return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
                });

                drEvent.on('dropify.afterClear', function(event, element){
                	// alert('File deleted');
                });

                drEvent.on('dropify.errors', function(event, element){
                	console.log('Has Errors');
                });

                var drDestroy = $('#input-file-to-destroy').dropify();
                drDestroy = drDestroy.data('dropify')
                $('#toggleDropify').on('click', function(e){
                	e.preventDefault();
                	if (drDestroy.isDropified()) {
                		drDestroy.destroy();
                	} else {
                		drDestroy.init();
                	}
                });


                /* --- Dropify initialization ends */

                $(document).off('click',".single_submit").on('click',".single_submit", function(e) {
                  $('.save').trigger('click');
                  $(".single_submit", $(this).parents("form")).removeAttr("clicked");
                  $(this).attr("clicked", "true");
                  console.log($(this));
                });

                /*For single entry*/
                $(document).off('submit', '#order_frm').on('submit', '#order_frm', function(event) {
                  /* Act on the event */
                  event.preventDefault();
                  event.stopPropagation();
                  button = $(".single_submit[clicked=true]");
                  button_val = $(".single_submit[clicked=true]").val();
                  button_text = $(".single_submit[clicked=true]").html();
                  var OrderUID = $('#OrderUID').val();

                  console.log(button);
				// var LoanAmount = $('#LoanAmount').val();
				// LoanAmount = LoanAmount.replace(/[,$]/g , ''); 
				// var LoanAmount = Number(LoanAmount);
				// var formData = $('#order_frm').serialize()+'&'+$.param({ 'LoanAmount': LoanAmount });

				var progress=$('.progress-bar');


				$('#DocumentUpload').val('');
				var formData = new FormData($(this)[0]);
				

				$.each(filetoupload, function (key, value) {
					formData.append('DocumentFiles[]', value.file);
				});

console.log(formData);


				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>Ordersummary/insert',
					data: formData, 
					dataType:'json',
					cache: false,
					processData:false,
					contentType:false,
					beforeSend: function(){
						addcardspinner('#Orderentrycard');
						button.attr("disabled", true);
						button.html('Loading ...'); 
						if (filetoupload.length) {
              $("#orderentry-progressupload").show();
            }
          },
          xhr: function () {
            var xhr = new window.XMLHttpRequest();
            if (filetoupload.length) {
              xhr.upload.addEventListener("progress", function (evt) {
               if (evt.lengthComputable) {
                 var percentComplete = evt.loaded / evt.total;
                 percentComplete = parseInt(percentComplete * 100);
                 $(progress).width(percentComplete + '%');
                 $(progress).text('Uploading ' + percentComplete + '%');
               }
             }, false);
            }
            return xhr;
          },
          success: function(data)
          {
            if(data['validation_error'] == 0){

             $.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:3000 });

             if(button_val == 1)
             {
              triggerpage(base_url+'Ordersummary/index/'+OrderUID);
            }

          }else if(data['validation_error'] == 1){

           removecardspinner('#Orderentrycard');

           $.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });

           button.html(button_text);
           button.removeAttr("disabled");


           $.each(data, function(k, v) {
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

          });
         }else if(data['validation_error'] == 2){
           removecardspinner('#Orderentrycard');
           $('#duplicate-modal').modal('show');
           $('#Skip_duplicate').val(1);
           $('#button_value').val(button_val);
           $('#insert_html').html(data['html']);	
           $('#insert_order').removeAttr('disabled');									
         }


       },
       error: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
      failure: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
    });
			});
                $(document).off('click','.save').on('click','.save', function(e) {           

                  var BorrowerName;
                  BorrowerName = $("input[name='BorrowerName[]']").map(function(){return $(this).val();}).get();
                  var data = $('#order_frm').serializeArray();
                  data.push({name: 'BorrowerName', value: BorrowerName});

 console.log(data);
 var OrderUID = $('#OrderUID').val();
 $.ajax({
  type: "POST",
  url: "<?php echo base_url('DocumentCheckList/SaveDocument'); ?>",
  data: data,
  dataType:'json',
  success: function () {


  },

});
});

                $('.productadd_button').click(function()
                {
                  var appendrow='<div class="col-sm-12 delete"><div class="form-group bmd-form-group" > <label for="BorrowerName" class="bmd-label-floating">Borrower Name</label> <input type="text" class="form-control" id="" name="BorrowerName[]"><i class="fa fa-minus-circle fa-2x material-icons mdl-textfield__label__icon productremove_button Deleterow" id="Deleterow" style="float:right;" ></i> </div></div>';
                  $('.Borrower').append(appendrow);
                });

                $('body').on('click','.Deleterow',function(){
                  var whichtr = $(this).closest('.delete');
                  whichtr.remove();  
                });



                /* ABSTRACTOR DOCUMENT SCRIPT SECTION STARTS */
                $(document).off('change', '#DocumentUpload').on('change', '#DocumentUpload', function(event){


                  var output = [];


                  for(var i = 0; i < event.target.files.length; i++)
                  {
                    var fileid=filetoupload.length;
                    var file = event.target.files[i];
                    filetoupload.push({file: file, filename: file.name , is_stacking: 1});
                    console.log(filetoupload);


                    var select = '<select id="client_select_'+i+'" name="status_select" class="select2picker status_select" disabled>';
                    select += select_option;
                    select += '</select>';


                    var datetime=calcTime('Caribbean', '-4');
                    var uploaded={};
                    uploaded.username=USERNAME;
                    uploaded.userid=USERUID;
                    uploaded.datetime=datetime;
                    var sizeof = bytesToSize(file.size);
                    var documentrow='<tr class="AbstractorFileRow">';
                    documentrow+='<td>'+file.name+'</td>';
                    documentrow+='<td>'+sizeof+'</td>';
                    documentrow+='<td>'+select+'</td>';
                    documentrow+='<td>'+datetime+'</td>';
                    documentrow+='<td>'+USERNAME+'</td>';
                    documentrow+='<td><div class="togglebutton"><label><input disabled type="checkbox" name="Stacking['+fileid+']" class="chkbox_stacking" value="1"><span class="toggle"></span> </label></div></td>';
                    documentrow+='<td style="text-align: left;"><button type="button" data-fileuploadid="'+fileid+'" class="DeleteUploadDocument btn btn-link btn-danger btn-just-icon btn-xs"><i class="icon-x"></i></button></td>';
                    documentrow+='</tr>';

                    output.push(documentrow);

                  }

                  $('#DocumentPreviewTable').find('tbody').append(output.join(""));

                  callselect2();
                  /*Loader START To BE Added*/

                });



                $("body").on("click" , ".DeleteUploadDocument" , function(e){
                 e.preventDefault();

                 var currentrow = $(this);
                 var fuid = $(currentrow).attr('data-fileuploadid');

                 filetoupload.splice(fuid,1);
                 $(currentrow).closest('tr').remove();
                 console.log(filetoupload);

                 $('tr.AbstractorFileRow').find('.DeleteUploadDocument').each(function(key, element){
                  $(element).attr('data-fileuploadid', key);
                });
               });


	});//Document Ends


function bytesToSize(bytes) {
  if(bytes < 1024) return bytes + " Bytes";
  else if(bytes < 1048576) return(bytes / 1024).toFixed(3) + " KB";
  else if(bytes < 1073741824) return(bytes / 1048576).toFixed(3) + " MB";
  else return(bytes / 1073741824).toFixed(3) + " GB";
};



function checkbox_status_change (e) { 

  e.preventDefault();
  let documentuid = e.target.id;
  var switch_status = 'off';
  sync_show_notification_flag = true;
  var current_element = e.target;
  var statuscheck_count = $('.Status:checked').length;

  if (statuscheck_count == 0) {

    $.notify(
    {
      icon:"icon-bell-check",
      message:"One document should be in stacking"
    },
    {
      type:"danger",
      delay:1000 
    });

    $(e.target).prop('checked', true);
    return false;
  }
  else if(statuscheck_count >= 1){


    if (!documentuid) {

      $.notify(
      {
        icon:"icon-bell-check",
        message:"Invalid Document"
      },
      {
        type:"danger",
        delay:1000 
      });

      return false;
    }

    /*SWEET ALERT CONFIRMATION*/
    swal({
      title: "<i class='icon-warning iconwarning'></i>",     
      html: '<p>Do you want to switch stacking document. May lose previous stacking data ?</p>',   
      showCancelButton: true,
      confirmButtonClass: 'btn btn-success',
      cancelButtonClass: 'btn btn-danger',
      buttonsStyling: false,
      closeOnClickOutside: false,
      allowOutsideClick: false,
      showLoaderOnConfirm: true,
      position: 'top-end'
    }).then(function(confirm) {

      if ($(e.target).prop('checked') == true) {
        var switch_status = 'on';

      }
      else{
        var switch_status = 'off';
      }



      $.ajax({
        type: "POST",
        url: base_url + "Ordersummary/switchdocumentstatus/",
        data: {'DocumentUID': documentuid, 'Switch':switch_status, 'OrderUID':Const_ORDERUID},
        dataType: "json",
        success: function (response) {

          $.notify(
          {
            icon:"icon-bell-check",
            message:response.message
          },
          {
            type:response.color,
            delay:1000 
          });



          $('.Status').prop('checked', false);
          $(e.target).prop('checked', true);


          $.each($('.status_select'), function (indexInArray, valueOfElement) { 
            if($(this).val() == 'Stacking')
            {
              $(this).val('Others');
                  // trigger('change');
                }
                
              });

          $(e.target).closest('tr').find('.status_select').val('Stacking');
          console.log($(e.target).closest('tr').find('.status_select').val());
          callselect2();

        },
        error: function (jqXHR, textstatus, errorThrown) {

        }
      });

    },
    function(dismiss) {              
      $(e.target).prop('checked', false);
    });
  }




}

$(document).off('focus', '.status_select').on('focus', '.status_select', function(e){
  let select2_open = $(this).prev('select');
  $(select2_open).attr('data-value', $(select2_open).val());
});

$(document).off('change', '.status_select').on('change', '.status_select', function(e){

  e.preventDefault();

  var $currentelem = $(this);
  var flag = false;

  var obj = {};
  obj.DocumentUID = e.target.id;
  obj.value = e.target.value;

  var passingelement = '';
  if ($(this).val() == 'Stacking') {


    /*SWEET ALERT CONFIRMATION*/
    swal({
      title: "<i class='icon-warning iconwarning'></i>",     
      html: '<p>Do you want to switch stacking document. May lose previous stacking data ?</p>',   
      showCancelButton: true,
      confirmButtonClass: 'btn btn-success',
      cancelButtonClass: 'btn btn-danger',
      buttonsStyling: false,
      closeOnClickOutside: false,
      allowOutsideClick: false,
      showLoaderOnConfirm: true,
      position: 'top-end'
    }).then(function(confirm) {

      fn_status_select(obj, $currentelem);

      $.each($('.status_select').not($($currentelem)), function (indexInArray, valueOfElement) { 
        if($(valueOfElement).val() == 'Stacking')
        {
          if(valueOfElement != '')
          {
            $(valueOfElement).val('Others');
            $(valueOfElement).closest('tr').find('.Status').prop('checked', false);
          }
        }
        
      });

    },
    function(dismiss) {              
      $($currentelem).val('Others');
      callselect2();
      // $(e.target).prop('checked', false);
    });

    
  }
  else{
    fn_status_select(obj, '');
  }
  if (flag == true) {
    return false;
  }
  // var status_select_value = $(this).val();
  // var status_checkbox = $(this).closest('tr').find('.Status');

  // if (status_select_value == 'Stacking') {
  //   $(status_checkbox).prop('checked', true).trigger('change');
  // }



});

$(document).off('click', '.DeleteUploadDocument_Server').on('click', '.DeleteUploadDocument_Server', function(event){

  $('#deletedocument').modal('show');
  var documentuid = $(this).attr('data-documentuid');
  var DeletedFileRow = $(this).closest('tr');
  $(document).off('click', '.Yes').on('click', '.Yes', function(event){

    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>Ordersummary/DeleteExistingDocument',
      data:{'documentuid':documentuid},
      dataType:"json",
      success: function(data)
      {
        if(data.validation_error == 1)
        {
          $.notify(
          {
            icon:"icon-bell-check",
            message:data.message
          },
          {
            type:data.color,
            delay:1000 
          });
          $(DeletedFileRow).remove();
        }else{
          $.notify(
          {
            icon:"icon-bell-check",
            message:data.message
          },
          {
            type:data.color,
            delay:1000 
          });
        }
        $('#deletedocument').modal('hide');
      },
      error: function(jqXHR, textStatus, errorThrown){

      }
    });


  });

});



var fn_status_select = function(obj, target) {

  $.ajax({
    type: "POST",
    url: base_url + "Ordersummary/changedocumenttype/",
    data: {'DocumentUID': obj.DocumentUID, 'value':obj.value, 'OrderUID':Const_ORDERUID},
    dataType: "json",
    success: function (response) {

      $(target).closest('tr').find('.Status').prop('checked', true);

            // if (sync_show_notification_flag) {


              $.notify(
              {
                icon:"icon-bell-check",
                message:response.message
              },
              {
                type:response.color,
                delay:1000 
              });
              
            // }
            // sync_show_notification_flag = true;
            callselect2();

          },
          error: function (jqXHR, textstatus, errorThrown) {

          }
        });

}


function callselect2 () {
  $("select.select2picker").select2({
    theme: "bootstrap",
  }).focus(function () { $(this).select2('open'); });;

}


</script>







