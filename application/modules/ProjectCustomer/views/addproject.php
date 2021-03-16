    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script> 

    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet"> 
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
 .border{
  border-style: ridge;
}

</style>
  <style type="text/css">
  td {border: 1px #DDD solid; padding: 5px; cursor: pointer;}

.selected {
    background-color: brown;
    color: #FFF;
}

.proButtons{
      top: 35px;

}

.ui-select-match-text{
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-right: 40px;
}
.ui-select-toggle > .btn.btn-link {
  margin-right: 10px;
  top: 6px;
  position: absolute;
  right: 10px;
}
</style>
<div class="card  mt-40" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">PROJECT
    </div>
  </div>
  <div class="tab-content tab-space customtabpane">
    <div class="col-md-12">
      <ul class="nav nav-pills nav-pills-rose customtab">
        <li class="nav-item">
          <a class="nav-link active" href="#singleentry" data-toggle="tab" role="tablist">
            Summary
          </a>
        </li>
        <li class="nav-item disabled">
          <a class="nav-link" href="#ProjectUsers" data-toggle="tab" role="tablist">
           Users
         </a>
       </li>
       <li class="nav-item disabled">
        <a class="nav-link" href="#category" data-toggle="tab" role="tablist">
         Category & Doc Type
       </a>
     </li>
     <li class="nav-item disabled">
      <a class="nav-link" href="#TPO" data-toggle="tab" role="tablist">
       TPO Company
     </a>
   </li>
   <li class="nav-item disabled">
    <a class="nav-link" href="#Investor" data-toggle="tab" role="tablist">
     Investor / Custodian
   </a>
 </li>
<!--  <li class="nav-item">
  <a class="nav-link" href="#Custodian" data-toggle="tab" role="tablist">
   Custodian
 </a>
</li> -->
<li class="nav-item disabled">
  <a class="nav-link" href="#question" data-toggle="tab" role="tablist">
   Check List              
 </a>
</li>



</ul>
</div>


<div class="card-body">
  <form action="#"  name="user_form" id="user_form">  
    <div class="tab-content ">      

      <div class="tab-pane active" id="singleentry">

        <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Project Details</h4> 

        <div class="row mt-10">
          <div class="col-md-3">
           <div class="form-group bmd-form-group">
            <label for="username" class="bmd-label-floating">Project Name <span class="mandatory"></span></label>
            <input type="text" class="form-control" id="ProjectName" name="ProjectName" />
          </div>
        </div>
        <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="ProjectCode" class="bmd-label-floating">Project Code <span class="mandatory"></span></label>
          <input type="text" class="form-control" id="ProjectCode" name="ProjectCode" />
        </div>
      </div>

      <div class="col-md-3">
       <div class="form-group bmd-form-group">
        <label for="roleuid" class="bmd-label-floating">Client<span class="mandatory"></span></label>
        <select class="select2picker form-control"  id="CustomerUID" name="CustomerUID">
         <option value=""></option>
         <?php foreach ($Roles as $key => $value) { 
            if ($this->parameters['DefaultClientUID'] == $value->CustomerUID) { ?>
                <option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName; ?></option>    
            <?php }
          } ?>           
       </select>
     </div>

   </div>


   <div class="col-md-3 priorityrow">
    <div class="form-group bmd-form-group">
     <label for="PriorityUID" class="bmd-label-floating">Priority<span class="mandatory"></span></label>
     <select class="select2picker form-control PriorityUID"  id="Priority" name="Priority" required>
      <option value=""></option>
      <option value="Rush">Rush</option>
      <option value="ASAP">ASAP</option>
      <option value="Normal">Normal</option>
    </select>
  </div>
</div>
</div>

<div class="row mt-10">
  <div class="col-md-3" >
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">Priority Time <span class="mandatory"></span></label>
      <input type="number" class="form-control" id="PriorityTime" name="PriorityTime" />
    </div>
  </div>

<!--     <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="StacxDocuments" class="bmd-label-floating">Stacking Documents<span class="mandatory"></span></label>
     <select class="select2picker form-control StacxDocuments"  id="StacxDocuments" name="StacxDocuments" required>
      <option></option>
      <option value="1">Stacx Documents</option>
      <option value="2">Client Documents</option>
     
    </select>
  </div>
</div> -->

<div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="DataEntryDisplay" class="bmd-label-floating">Data Entry Display<span class="mandatory"></span></label>
     <select class="select2picker form-control DataEntryDisplay"  id="DataEntryDisplay" name="DataEntryDisplay" required>
      <option></option>
      <option value="1">1 Column</option>
      <option value="2">2 Column(s)</option>

    </select>
  </div>
</div>

  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="ExportType" class="bmd-label-floating">Export Type<span class="mandatory"></span></label>
     <select class="select2picker form-control ExportType"  id="ExportType" name="ExportType" required>
      
        <option></option>
      <option value="Loan Level" >Loan Level</option>
      <option value="Consolidated">Consolidated</option>
     
    </select>
  </div>
</div>

  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="DocInstance" class="bmd-label-floating">Document Instance<span class="mandatory"></span></label>
     <select class="select2picker form-control DocInstance"  id="DocInstance" name="DocInstance" required>
      
        <option></option>
      <option value="1" >Yes</option>
      <option value="0">No</option>
     
    </select>
  </div>
</div>

</div>
<div class="row mt-10">

 <div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
   <label for="PriorityUID" class="bmd-label-floating">BulkImportFormat<span class="mandatory"></span></label>
   <select class="select2picker form-control PriorityUID"  id="BulkImportFormat" name="BulkImportFormat" required>
    <option value=""></option>
    <option value="Stacx-Standard">Stacx-Standard</option>
    <option value="Stacx-Assignment">Stacx-Assignment</option>
  </select>
</div>
</div>
<div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
   <label for="PriorityUID" class="bmd-label-floating">SFTP</label>
   <select class="select2picker form-control PriorityUID"  id="SFTPUID" name="SFTPUID" required>
    <option value=""></option>
    <?php foreach ($SFTP as $key => $value) { ?>
      <option value="<?php echo $value->SFTPUID; ?>"><?php echo $value->SFTPName;?></option>
    <?php }?>
  </select>
</div>
</div>
<div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
   <label for="PriorityUID" class="bmd-label-floating">SFTP Export</label>
   <select class="select2picker form-control PriorityUID"  id="SFTPEXPORTUID" name="SFTPEXPORTUID">
    <option value=""></option>
    <?php foreach ($SFTP as $key => $value) { ?>
      <option value="<?php echo $value->SFTPUID; ?>"><?php echo $value->SFTPName;?></option>
    <?php }?>
  </select>
</div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="ExportLevel" class="bmd-label-floating">ExportLevel<span class="mandatory"></span></label>
   <select class="select2picker form-control ExportLevel"  id="ExportLevel" name="ExportLevel" required>
    <option value=""></option>
    <option value="Category">Category</option>
    <option value="DocumentType">DocumentType</option>
  </select>
</div>
</div>

<div class="col-md-3">
  <div class="form-group bmd-form-group">
   <label for="ProductUID" class="bmd-label-floating">Product<span class="mandatory"></span></label>
   <select class="select2picker form-control ProductUID"  id="ProductUID" name="ProductUID" required>

   </select>
 </div>
</div>

</div>
<div class="row">
  <div class="col-md-3 priorityrow">
  <div class="form-group bmd-form-group">
   <label for="PriorityUID" class="bmd-label-floating">Auto Import By</label>
   <select class="select2picker form-control PriorityUID"  id="importColumn" name="importColumn">
    <option value=""></option>
    <?php foreach ($importColumn as $key => $value) { ?>
      <option value="<?php echo $value->ColumnID; ?>"><?php echo $value->ColumnName;?></option>
    <?php }?>
  </select>
</div> 
</div>
  <div class="col-md-3">   
   <div class="form-check" style="margin-top: 19pt;">
    <label class="form-check-label">
      <input class="form-check-input" type="checkbox"  name="IsAutoExport"  id="IsAutoExport"  value="AutoExport"> AutoExport
      <span class="form-check-sign">
        <span class="check"></span>
      </span>
    </label>
  </div> 
</div>
 <div class="col-md-3">   
   <div class="form-check" style="margin-top: 19pt;">
    <label class="form-check-label">
      <input class="form-check-input" type="checkbox"  name="IsFolderExport"  id="IsFolderExport"  value=""> Export As Folder
      <span class="form-check-sign">
        <span class="check"></span>
      </span>
    </label>
  </div> 
</div>
</div>
<hr>
<div class="row mt-10">
 <div class="col-sm-12">
  <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>OCR Settings</h4> 

</div>
</div>

<div class="row mt-10">
  <div class="col-md-3" >
      <div class="form-group bmd-form-group">
        <label for="WorkflowModules" class="bmd-label-floating">WorkflowModule </label>
        <select class="select2picker form-control" name="OCRWorkflowModuleUID" id="OCRWorkflowModuleUID">
          <option></option>
          <?php foreach ($WorkflowModules as $key => $module) { 
            if ($module->WorkflowModuleUID != 5 && $module->WorkflowModuleUID != 6 && $module->WorkflowModuleUID != 7 && $module->WorkflowModuleUID != 8) { ?>
              <option value="<?php echo $module->WorkflowModuleUID; ?>" data-workflowname = "<?php echo $module->WorkflowModuleName; ?>"><?php echo $module->WorkflowModuleName; ?></option>
            <?php } } ?>                
        </select>
      </div>
  </div>   
</div>
</div>


<div class="tab-pane" id="fieldforproject">                
  <div class="col-sm-12">
    <h6 class="panelheading" style="font-weight: 700;  border-bottom: 1px solid #d9d9d9;">Project Fields</h6><br>
  </div>
  <div class="col-md-12">
    <div class="form-group bmd-form-group">
      <label for="Fields" class="bmd-label-floating">Fields<span class="mandatory"></span></label>
      <select class="select2picker form-control"  id="FieldsName" name="FieldsName[]" multiple>
        <option value=""></option>
        <?php foreach ($Fields as $key => $value) { ?>
          <option value="<?php echo $value->FieldUID; ?>"><?php echo $value->FieldName; ?></option>
        <?php } ?>                
      </select>
    </div>
  </div> 

</div>

<div class="tab-pane" id="ProjectUsers">
  <div class="row">
   <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="Fields" class="bmd-label-floating">Users</label>
      <select class="select2picker form-control"  id="UsersID" name="UsersID">
        <!-- <option value=""></option> -->
        <?php foreach ($GetUsersList as $key => $value) { ?>
          <option value="<?php echo $value->UserUID; ?>"><?php echo $value->UserName; ?></option>
        <?php } ?>                
      </select>
    </div>
  </div> 
  <div class="col-md-3" style="margin-top: 11px;">
    <div class="form-check">
      <label class="form-check-label">
        <input class="form-check-input" type="checkbox" id="CheckEmailUser"> Receive Emails
        <span class="form-check-sign">
          <span class="check"></span>
        </span>
      </label>
    </div>
  </div>
  <div class="col-md-3" style="margin-top: 11px;">
    <button  type="button" class="btn btn-success btn-round AddProjectUsersUsers"><i class="icon-plus22"></i> Add</button>
  </div>
</div>
<div class="col-sm-12 pd-0">

  <div class="material-datatables">
    <table id="ProjectUsersTable" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
     <thead>
      <tr>
       <th  class="text-center" style="display: none;">UserUID</th>
       <th  class="text-center">User Name</th>
       <th  class="text-center">Receive Emails</th>
       <th  class="text-center" style="width: 10%;">Action</th>

     </tr>
   </thead>
   <tbody>

   </tbody>
 </table>
</div>

</div>
</div>

<div class="tab-pane" id="category">
  <div class="row mt-10">
    <div class="col-md-4">
      
       <div class="col-md-12">
        <div class="">
          <label for="projectcategory" class="bmd-label-floating">Category Type<span class="mandatory"></span></label>
          <select class=" form-control" name="proCategory" id="projectcategory" >
            <?php   
            
            foreach ($catagoryname as $value) { ?>
              
                <option value="<?php echo $value->CategoryUID; ?>"   data-categoryName = "<?php echo $value->CategoryName; ?>"><?php echo $value->CategoryName ?></option> 
              
            <?php } ?>
          </select>
        </div>
      </div> 
    </div>
    
      <div class="col-md-4">
        
        <div class="">
          <label for="projectdocument" class="bmd-label-floating"> Document Type <span class="mandatory"></span> </label>
          <select class=" form-control"  id="projectdocument" >
            <option value=""></option>
            <?php
            
            foreach ($fetchDocuType as $key => $row) {
              ?>
              <?php if (in_array($row->DocumentTypeUID,$SelectedDoc)) {?>            
              <?php } else { ?>
                <option value="<?php echo $row->DocumentTypeUID; ?>"   data-documentname = "<?php echo $row->DocumentTypeName; ?>"><?php echo $row->DocumentTypeName; ?></option> 
                <?php
              }
            }
            ?>
          </select>
        </div>
      </div>

      <div class="col-md-1">
       <button  type="button" class="btn btn-success btn-round addDocumentType proButtons"><i class="icon-plus22"></i> Add</button>
     </div>

      <div class="col-md-1">
       <button  type="button" class="btn btn-warning btn-round proButtons" id="sortTable" title="Click here to Sort/Filter"><i class="icon-sort"></i> Sort</button>
     </div>

     <div class="col-md-1">
       <button  type="button" class="btn btn-info btn-round proButtons" id="back"><i class="icon-arrow-left15 pr-10"></i> Back</button>
     </div>
   </div>


  <div class="row  mt-10" id="sort_table" >
    <div class="col-md-6" style="margin-top: 20px;">
      <div class="material-datatables">
        <table class="table table-bordered" id="tblCategory">
          <thead>
            <th style="min-width:20px  !important;width:20px;">S.No</th>
            <th>Category Type</th>
            <th style="min-width:40px  !important;width:40px;">Action</th>
          </thead>
          <tbody>  
          
            </tbody>  
          </table>
        </div>
      </div>
   
  <div class="col-md-6" style="margin-top: 20px;">
   
      <div class="material-datatables">
        <table class="table table-bordered" id="tblDocumentType">
          <thead>
            <th style="min-width:20px !important;width:20px;">S.No</th>
            <th>Document Type</th>
            <th style="min-width:40px  !important;width:40px;">Action</th>
          </thead>
          <tbody>
    
         </tbody>  
       </table>
   
 </div> 
</div>
</div>

  <div class="row  mt-10" id="main_table">
     <div class="col-md-12" style="margin-top: 20px;">
      <div class="material-datatables">
        <table class="table table-bordered" id="tblCategoryDocumentType">
          <thead>
            <th  class="text-center" style="min-width:20px !important;width:20px;">S.No</th>
            <th  class="text-center" style="min-width:20px !important;width:20px;">Category Type</th>
            <th class="text-center" >Document Type</th>
            <th  class="text-center" style="min-width:40px  !important;width:40px;">Action</th>
          </thead>
          <tbody>
           
         </tbody>  
       </table>
     </div>
   </div>
</div>
</div>





<div class="tab-pane" id="Investor">

  <div class="row">
    <div class="col-md-12">
      <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Investors </h4>
    </div>
    <div class="col-md-5">
      <div class="form-group bmd-form-group">
        <label for="ddlInvestors" class="bmd-label-floating">Investors </label>
        <select class="select2picker form-control"  id="ddlInvestors">
          <option></option>
          <?php foreach ($Investors as $key => $investor) { ?>
            <option value="<?php echo $investor->InvestorUID; ?>" data-investorname = "<?php echo $investor->InvestorName; ?>" data-investorno = "<?php echo $investor->InvestorNo; ?>"><?php echo $investor->InvestorName; ?></option>
          <?php } ?>                
        </select>
      </div>
    </div> 
    <div class="col-md-5">
      <div class="form-group bmd-form-group">
        <label for="ddlCus" class="bmd-label-floating">Custodian</label>        
        <select class="select2picker form-control"  id="ddlCus">
          <option></option>
          <?php foreach ($Custodians as $key => $custodian) { ?>
            <option value="<?php echo $custodian->CustodianUID; ?>" data-custodianname = "<?php echo $custodian->CustodianName; ?>" data-custodianno = "<?php echo $custodian->CustodianNo; ?>"><?php echo $custodian->CustodianName; ?></option>
          <?php } ?>                
        </select>
      </div>
    </div>
    <div class="col-md-2 mt-10">
      <button  type="button" class="btn btn-success btn-round addinvestor"><i class="icon-plus22"></i> Add</button>
    </div>
    <div class="col-md-12 mt-10">
      <table class="table table-bordered" id="tblProductInvestors">
        <thead>
          <tr>
            <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
            <th style="padding: 3px 3px !important;" class="text-center">Investor No</th>       
            <th style="padding: 3px 3px !important;" class="text-center">Investor Name</th>       
            <th style="padding: 3px 3px !important;" class="text-center">Custodian</th>
            <th style="padding: 3px 3px !important;" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>             
</div>

<!-- <div class="tab-pane" id="Custodian">

  <div class="row">
    <div class="col-md-12">
      <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Custodians </h4>
    </div>
    <div class="col-md-6">
      <div class="form-group bmd-form-group">
        <label for="ddlInvestors" class="bmd-label-floating">Custodians </label>
        <select class="select2picker form-control"  id="ddlCustodians">
          <option></option>
          <?php foreach ($Custodians as $key => $custodian) { ?>
            <option value="<?php echo $custodian->CustodianUID; ?>" data-custodianname = "<?php echo $custodian->CustodianName; ?>" data-custodianno = "<?php echo $custodian->CustodianNo; ?>"><?php echo $custodian->CustodianName; ?></option>
          <?php } ?>                
        </select>
      </div>
    </div> 
    <div class="col-md-6" style="margin-top: 11px;">
      <button type="button" class="btn btn-success btn-round addcustodian"><i class="icon-plus22"></i> Add</button>
    </div>
    <div class="col-md-12 mt-10">
      <table class="table table-bordered" id="tblProductCustodians">
        <thead>
          <tr>
            <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
            <th style="padding: 3px 3px !important;" class="text-center">Custodian No</th>
            <th style="padding: 3px 3px !important;" class="text-center">Custodian Name</th>
            <th style="padding: 3px 3px !important;" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>             
</div> -->

<div class="tab-pane" id="TPO">
  <div class="row">
    <div class="col-md-12">
      <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>TPO Company </h4>
    </div>
    <div class="col-md-10">
      <div class="form-group bmd-form-group">
        <label for="ddlInvestors" class="bmd-label-floating">TPO </label>
        <select class="select2picker form-control"  id="ddlLenders">
          <option></option>
          <?php foreach ($Lenders as $key => $lender) { ?>
            <option value="<?php echo $lender->LenderUID; ?>" data-lendername = "<?php echo $lender->LenderName; ?>"><?php echo $lender->LenderName; ?></option>
          <?php } ?>                
        </select>
      </div>
    </div> 
    <div class="col-md-2" style="margin-top: 11px;">
      <button type="button" class="btn btn-success btn-round addlender"><i class="icon-plus22"></i> Add</button>
    </div>
    <div class="col-md-12 mt-10">
      <table class="table table-bordered" id="tblProductLenders">
        <thead>
          <tr>
            <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
            <th style="padding: 3px 3px !important;" class="text-center">TPO Name</th>
            <th style="padding: 3px 3px !important;" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>             
</div>

<div class="tab-pane" id="question">
  <div class="col-md-12 pd-0">
    <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Audit Check List </h4>
  </div>
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-10 pd-0 mt-10">
        <div class="form-group bmd-form-group">
          <label for="ddlInvestors" class="bmd-label-floating">Audit Check List Type </label>
          <select class="select2picker form-control"  id="auditCheckListType">
            <option></option>
            <?php 
            foreach ($QuestionType as $key => $value) {  ?>
              <option value="<?php echo $value->QuestionTypeUID; ?>" data-questionname = "<?php echo $value->QuestionTypeName; ?>"><?php echo $value->QuestionTypeName; ?></option>
            <?php    }    ?>
          </select>
        </div>
      </div> 
      <div class="col-md-2 pd-0 mt-10">
        <button type="button" class="btn btn-success btn-round addquestion"><i class="icon-plus22"></i> Add</button>
      </div>
    </div>
  </div>
  <div class="col-md-12 col-sm-12 pd-0">
   <table class="table table-bordered" id="tblquestionType">
    <thead>
      <tr>
        <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
        <th style="padding: 3px 3px !important;" class="text-center">QuestionTypeName</th>
        <th style="padding: 3px 3px !important;width: 5% !important;" class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>
</div>
<div class="ml-auto text-right">
  <a href="<?php echo base_url('ProjectCustomer'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
  <button type="submit" class="btn btn-fill btn-save btn-wd adduser" name="adduser"><i class="icon-floppy-disk pr-10"></i>Save Project </button>
</div>
</div>
</form>
</div>
</div>
</div>





<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript">

  $(document).ready(function(){

    var datatable = $('#ProjectUsersTable').DataTable();
    datatable.columns.adjust().draw();
    $("#ProjectUsersTable").dataTable({
      processing: true,
      scrollX:  true,
      paging:true,
      retrieve: true,

      fixedColumns:   {
        rightColumns: 1
      }

    });
    $("#ProjectUsersTable").on('click', '.btnDelete', function () {
      $(this).closest('tr').remove();
    });




    $(".AddProjectUsersUsers").click(function(){

      var name = $('#UsersID option:selected').text();
      var UserUID = $('#UsersID').val();
      var checkbox = $('#CheckEmailUser').is(':checked') ? "checked" : "";

      var rowcount = $('#ProjectUsersTable tbody tr').length;
      var markup = "<tr><input type='hidden' class='hid-ProjectUser' name='ProjectUserUID["+rowcount+"]' value='" + UserUID + "'><td style='text-align: center;''>" + name + "</td><td style='text-align: center;''><div class='form-check'><label class='form-check-label'><input class='form-check-input chk-reviewemail' type='checkbox' "+checkbox+" name='RecevieEmailChecked["+rowcount+"]' ><span class='form-check-sign'><span class='check'></span> </span></label> </div></td><td style='text-align: center'> <span style='text-align: center;width:100%;'><button class='btn btn-link btn-danger btn-just-icon btn-xs btnDelete'><i class='icon-bin'></i></button></span></td></tr>";

      $("#ProjectUsersTable tbody").append(markup);
      $('#CheckEmailUser').prop('checked', false);
      $('#UsersID').val('');
      $("#UsersID option[value='"+UserUID+"']").remove();           
      callselect2();
    });


    $(document).off('click','.adduser').on('click','.adduser', function(e) {
      var formdata = new FormData($('#user_form')[0]);

      $(".hid-ProjectUser").each(function(key, value){
        console.log(value);
        formdata.append('ProjectUserUID[]', $(value).val());
        formdata.append('RecevieEmailChecked[]', $(value).closest('tr').find('.chk-reviewemail').prop('checked'));
      });


   //   alert(formdata);
   button = $(this);
   button_val = $(this).val();
   button_text = $(this).html();
   $.ajax({
    type: "POST",
    url: "<?php echo base_url('ProjectCustomer/SaveProject'); ?>",
    data: formdata,
    processData: false,
    contentType: false,
    dataType:'json',
    beforeSend: function () {
      button.prop("disabled", true);
      button.html('Loading ...');
      button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
    },
    success: function (response) {
      console.log(response);
      if(response.Status == 0)
      {
       $.notify(
       {
        icon:"icon-bell-check",
        message:response.message
      },
      {
        type:"success",
        delay:1000 
      }); 
       setTimeout(function(){ 

        triggerpage('<?php echo base_url();?>ProjectCustomer/EditProject/'+response.url);

      }, 1000);
     }
     else
     {
      $.notify(
      {
        icon:"icon-bell-check",
        message:response.message
      },
      {
        type:"danger",
        delay:1000 
      });
      $.each(response, function(k, v) {
        console.log(k);
        $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
        $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

      });

    }
    button.html(button_text);
    button.val(button_val);
    button.prop('disabled',false);

  }
});

 });

    // $('#projectcategory').change(function(){
    //   var CategoryUID = $('#projectcategory').val();
    //   $.ajax({
    //     url: "<?php echo base_url('ProjectCustomer/GetDocumentTypeByCategory')?>",
    //     type: 'POST',
    //     data: {"CategoryUID":CategoryUID},
    //     dataType: "JSON",
    //     success: function (data) 
    //     { 
    //      if(data!='')
    //      {
    //       $('#projectdocument').empty();              
    //       $.each(data, function(index, value) {             
    //        if(Array.isArray(value)){
    //         $.each(value , function(m,n){
    //           $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" data-documentName="'+ n.DocumentTypeName +'">'+ n.DocumentTypeName +'</option>'); 
    //         });
    //       }
    //     });
    //     }      
    //   }
    // });



  });


 /*$(document).on('click','.Back',function()
   {
      setTimeout(function(){ 

          triggerpage('<?php echo base_url();?>ProjectCustomer');

        },50);
      });*/


      function log()
      {

        var loginid = $('#loginid').val();

        $.ajax({
          type: "POST",
          url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
          data: {'loginid' : loginid},
          dataType:'json',
          success: function (response) {

            if(response.Status == 1)
            {

              $('#loginexists').show();
            }else{
             $('#loginexists').hide();
           }


         },
         error:function(xhr){

           console.log(xhr);
         }
       });

      }

      $(document).off('change', '#CustomerUID').on('change', '#CustomerUID', function (e) {  
        e.preventDefault();
        var $dataobject = {'CustomerUID': $(this).val()};
        
        SendAsyncAjaxRequest('POST', 'CommonController/GetCustomerDetails', $dataobject, 'json', true, true, function () {
        // addcardspinner($('#AuditCard'));
      }).then(function (data) {
        var Product_Select = data.Products.reduce((accumulator, value) => {
          console.log(value);
          return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
        }, '<option value=""></option>');         
        $('#ProductUID').html(Product_Select);
        $('#ProductUID').trigger('change');
        callselect2();

      }).catch(function (error) {

        console.log(error);
      });
      
    });



      $(document).off('click', '.addinvestor').on('click', '.addinvestor', function (e) {
        e.preventDefault();
        var currentinvestor = $('#ddlInvestors').find('option:selected');    
           var currentcus = $('#ddlCus').find('option:selected');     
        if ($(currentinvestor).val() && $(currentcus).val()) {
          var investoruid = $(currentinvestor).val();
          var investorname = $(currentinvestor).attr('data-investorname');
          var investorno = $(currentinvestor).attr('data-investorno');
          var CustodianName =  $(currentcus).attr("data-custodianname");
          var CustodianUID =  $(currentcus).val();
          var sno = $('#tblProductInvestors tbody tr').length;
          valtxt  = investoruid + ',' + CustodianUID;
          var appendrow = `<tr>
          <input type="hidden" name="Investors[]" value="` + valtxt + `">         
          <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>
          <td class="text-center">` + investorno + `</td>
          <td class="text-center">` + investorname + `</td>
          <td class="text-center">` + CustodianName + `</td>
          <td class="text-center"><button  type="button" data-investoruid = "`+ investoruid +`" data-investorname = "`+ investorname +`" data-investorno = "`+ investorno +`" class="btn btn-pinterest removeinvestors"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;

          $(currentinvestor).remove();

          $('#tblProductInvestors tbody').append(appendrow);

          $('#tblProductInvestors tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });

          callselect2();


        }
      });

      $(document).off('click', '.removeinvestors').on('click', '.removeinvestors', function (e) {

        e.preventDefault();

        var investors = {};

        investors.InvestorUID = $(this).attr('data-investoruid');
        investors.InvestorName = $(this).attr('data-investorname');
        investors.InvestorNo = $(this).attr('data-investorno');

        if (investors.InvestorUID) {

          var appendoption = `<option value="`+investors.InvestorUID+`" data-investorname = "`+investors.InvestorName+`" data-investorno = "`+investors.InvestorNo+`">`+investors.InvestorName+`</option>`;
          $('#ddlInvestors').append(appendoption);
          callselect2();

          $(this).closest('tr').remove();

        }
      });



      // $(document).off('click', '.addcustodian').on('click', '.addcustodian', function (e) {
      //   e.preventDefault();
      //   var currentcustodian = $('#ddlCustodians').find('option:selected');
      //   console.log($('#ddlCustodians').find('option:selected'));
      //   if ($(currentcustodian).val()) {
      //     var custodianuid = $(currentcustodian).val();
      //     var custodianname = $(currentcustodian).attr('data-custodianname');
      //     var custodianno = $(currentcustodian).attr('data-custodianno');

      //     var sno = $('#tblProductCustodians tbody tr').length;

      //     var appendrow = `<tr>
      //     <input type="hidden" name="Custodians[]" value="` + custodianuid + `">
      //     <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>
      //     <td class="text-center">` + custodianno + `</td>
      //     <td class="text-center">` + custodianname + `</td>
      //     <td class="text-center"><button  type="button" data-custodianuid = "`+ custodianuid +`" data-custodianname = "`+ custodianname +`" data-custodianno = "`+ custodianno +`" class="btn btn-pinterest removecustodians"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
      //     </tr>`;

      //     $(currentcustodian).remove();

      //     $('#tblProductCustodians tbody').append(appendrow);

      //     $('#tblProductCustodians tbody tr').each(function (key, row) {
      //       $(row).find('td:first').html(key + 1);
      //     });
      //     callselect2();
      //   }
      // });



      // $(document).off('click', '.removecustodians').on('click', '.removecustodians', function (e) {
      //   e.preventDefault();
      //   var custodians = {};
      //   custodians.CustodianUID = $(this).attr('data-custodianuid');
      //   custodians.CustodianName = $(this).attr('data-custodianname');
      //   custodians.CustodianNo = $(this).attr('data-custodianno');

      //   if (custodians.CustodianUID) {

      //     var appendoption = `<option value="`+custodians.CustodianUID+`" data-custodianname = "`+custodians.CustodianName+`" data-custodianno = "`+custodians.CustodianNo+`">`+custodians.CustodianName+`</option>`;
      //     $('#ddlCustodians').append(appendoption);
      //     callselect2();
      //     $(this).closest('tr').remove();
      //   }
      // });


      $(document).off('click', '.addlender').on('click', '.addlender', function (e) {
        e.preventDefault();
        var currentlender = $('#ddlLenders').find('option:selected');
        console.log($('#ddlLenders').find('option:selected'));
        if ($(currentlender).val()) {
          var lenderuid = $(currentlender).val();
          var lendername = $(currentlender).attr('data-lendername');

          var sno = $('#tblProductCustodians tbody tr').length;

          var appendrow = `<tr>
          <input type="hidden" name="TPO[]" value="` + lenderuid + `">
          <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>
          <td class="text-center">` + lendername + `</td>
          <td class="text-center"><button  type="button" data-lenderuid = "`+ lenderuid +`" data-lendername = "`+ lendername +`"  class="btn btn-pinterest removelenders"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;

          $(currentlender).remove();

          $('#tblProductLenders tbody').append(appendrow);

          $('#tblProductLenders tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });

          callselect2();


        }
      });

      $(document).off('click', '.removelenders').on('click', '.removelenders', function (e) {

        e.preventDefault();

        var lenders = {};

        lenders.LenderUID = $(this).attr('data-lenderuid');
        lenders.LenderName = $(this).attr('data-lendername');

        if (lenders.LenderUID) {

          var appendoption = `<option value="`+lenders.LenderUID+`" data-lendername = "`+lenders.LenderName+`">`+lenders.LenderName+`</option>`;
          $('#ddlLenders').append(appendoption);
          callselect2();

          $(this).closest('tr').remove();

        }
      });


      $(document).off('click', '.addquestion').on('click', '.addquestion', function (e) {
        e.preventDefault();
        var currentquestion = $('#auditCheckListType').find('option:selected');
        if ($(currentquestion).val()) {
          var questionuid = $(currentquestion).val();
          var questionname = $(currentquestion).attr('data-questionname');          
          var sno = $('#tblquestionType tbody tr').length;
          var appendrow = `<tr>
          <input type="hidden" name="Questions[]" value="` + questionuid + `">
          <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>     
          <td class="text-center">` + questionname + `</td>
          <td class="text-center" ><button  type="button" data-questionuid = "`+ questionuid +`" data-questionname = "`+ questionname +`"  class="btn btn-pinterest removequestion"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;
          $(currentquestion).remove();
          $('#tblquestionType tbody').append(appendrow);

          $('#tblquestionType tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });
          callselect2();
        }
      });




      $(document).off('click', '.removequestion').on('click', '.removequestion', function (e) {
        e.preventDefault();
        var question = {};
        question.questionname = $(this).attr('data-questionname');
        question.questionUID = $(this).attr('data-questionuid');        
        if (question.questionUID) {
          var appendoption = `<option value="`+question.questionUID+`" data-questionname = "`+question.questionname+`">`+question.questionname+`</option>`;
          $('#auditCheckListType').append(appendoption);
          callselect2();
          $(this).closest('tr').remove();
        }
      });



  var categories = [];
  function GetDocumentTypeByCategoryUID(categoryuid){        
  $.ajax({
    url: "<?php echo base_url('ProjectCustomer/GetDocumentTypeByCategoryUID')?>",
    type: 'POST',
    data: {"CategoryUID":categoryuid},
    dataType: "JSON",
    success: function (data) 
    { 
     
     docuid  = [];
            $("#tblDocumentType tbody tr").each(function(){              
              docUID = $(this).closest("tr").find("input:hidden").val();
              docuid.push(docUID);
            });
            console.log(docuid) 
            if(data!='')
            {
              $('#projectdocument').empty();   
              for (var i = 0; i < data.length; i++) {
                var match=0;
                if ($.inArray(data[i].DocumentTypeUID, docuid) != -1)
                {
                  match=1;
                }else{
                  match=0;
                }

                if(match == 0){
                   $('#projectdocument').append('<option value="'+data[i].DocumentTypeUID+'" data-documentName="'+ data[i].DocumentTypeName +'">'+ data[i].DocumentTypeName +'</option>'); 
                 }
              }
            
            }      
    }
  });
  }

  $('#projectcategory').on('change',function(e){
    //e.preventdefault();
    var currentcategory = $('#projectcategory').find('option:selected');
    if ($(currentcategory).val()) {
      var categoryuid = $(currentcategory).val();
      GetDocumentTypeByCategoryUID(categoryuid);
      //callselect2();
    }
  }) 


        $(document).off('click', '.addDocumentType').on('click', '.addDocumentType', function (e) {
        // Add category

        e.preventDefault();
        var currentcategory = $('#projectcategory').find('option:selected');
        if ($(currentcategory).val()) {
          var matched=0;
          var categoryuid = $(currentcategory).val();
          var categoryname = $(currentcategory).attr('data-categoryName');
          
          if (categories.length!=0) {
            console.log("not empty")
          // for (var i = 0; i < categories.length; i++) {
          //   if (categories[i] != categoryuid) {
          //     categories.push(categoryuid);
          //     matched=0;
          //   }else{
          //     matched==1;
          //   }

          if ($.inArray(categoryuid, categories) != -1)
          {
            matched=1;
          }else{
            matched=0;
            categories.push(categoryuid);
          }
          
        }else{
          categories.push(categoryuid);
          matched=0;
        }
        
          var sno = $('#tblCategory tbody tr').length;
          var appendrow = `<tr id="` + categoryuid + `">
          <input type="hidden" name="projectcategory[]" value="` + categoryuid + `">
          <td style="min-width:20px !important;width:20px;"  class="text-center">` + (sno + 1) + `</td>     
          <td class="text-center catName">` + categoryname + `</td>
          <td class="text-center" style="min-width:40px !important;width:40px;" ><button  type="button" data-categoryuid = "`+ categoryuid +`" data-categoryName = "`+ categoryname +`"  class="btn btn-pinterest removecategory"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;
          //$(currentcategory).remove();
          if (matched==0) {
            $('#tblCategory tbody').append(appendrow);
          }

          $('#tblCategory tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });
          getcategory = [];
          $("#tblCategory tbody tr").each(function(){
            var getcate =  $(this).closest("tr").find("input:hidden").val();
            getcategory.push(getcate);
          });
          // getDocumentCategory(getcategory);      
          // callselect2();
        }else{
          $.notify(
          {
            icon:"icon-bell-check",
            message:"Category Is Mandatory"
          },
          {
            type:"info",
            delay:1000 
          });          
        }

        // Add document
        e.preventDefault();
        var currentDocument = $('#projectdocument').find('option:selected');
        if ($(currentDocument).val()) {
          var documentuid = $(currentDocument).val();
          var documentname = $(currentDocument).attr('data-documentName');          
          var sno = $('#tblDocumentType tbody tr').length;
          var appendrow = `<tr class="`+categoryuid+`">
          <input type="hidden" name="projectdocument[]" value="` + documentuid + `">
          <td style="min-width:20px !important;width:20px;" class="text-center">` + (sno + 1) + `</td>     
          <td class="text-center">` + documentname + ` - `+ categoryname +`</td>
          <td class="text-center  ` + categoryname.split(" ").join("") +`-`+ documentuid +`"  style="min-width:40px !important;width:40px;" ><button  type="button" data-documentuid = "`+ documentuid +`" data-documentName = "`+ documentname +`"  class="btn btn-pinterest removedocument ` + categoryname.split(" ").join("") +`-`+ documentuid +`" data-categoryName = "`+ categoryname.split(" ").join("") +`"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;
          $(currentDocument).remove();
          $('#tblDocumentType tbody').append(appendrow);
          $('#tblDocumentType tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });

          var documentuid = $(currentDocument).val();
          var documentname = $(currentDocument).attr('data-documentName');          
          var sno = $('#tblCategoryDocumentType tbody tr').length;
          var appendrow = `<tr class="`+categoryuid+`">
          
          <td style="min-width:20px !important;width:20px;" class="text-center">` + (sno + 1) + `</td>     
          <td class="text-center">` + categoryname +`</td>
          <td class="text-center">` + documentname + `</td>
          <td class="text-center ` + categoryname.split(" ").join("") +`-`+ documentuid +`"  style="min-width:40px !important;width:40px;" ><button  type="button" data-documentuid = "`+ documentuid +`" data-documentName = "`+ documentname +`"  class="btn btn-pinterest removedocument ` + categoryname.split(" ").join("") +`-`+ documentuid +`" data-categoryName = "`+ categoryname.split(" ").join("") +`"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>`;
          $(currentDocument).remove();
          $('#tblCategoryDocumentType tbody').append(appendrow);
          $('#tblCategoryDocumentType tbody tr').each(function (key, row) {
            $(row).find('td:first').html(key + 1);
          });

          callselect2();
        }else{
          $.notify(
          {
            icon:"icon-bell-check",
            message:"Document Type Is Mandatory"
          },
          {
            type:"info",
            delay:1000 
          });
        }


        var selected_category='';
        $(".catName").click(function(){
              
           selected_category=$(this).closest('tr').attr('id');
           //alert(selected_category)
           //selectByText( $.trim( $('input[name=proCategory]').val() ) );
           $('#'+selected_category).addClass('selected').siblings().removeClass('selected');
           // $('select[name^="proCategory"] option:selected').attr("selected",null);
           // $('select[name^="proCategory"] option[value="'+selected_category+'"]').attr("selected","true");
           if($('#projectcategory').val()!=selected_category)
              $('#projectcategory').val(selected_category);

           GetDocumentTypeByCategoryUID(selected_category);
          $('#tblDocumentType tbody tr').each(function (key, row) {
            var row_class=$(row).prop('className');
            row_class=row_class.split(" ");
            row_class=row_class[0];
            if (row_class != selected_category) {
              $('.'+row_class).hide();
            }else{
              $('.'+row_class).show();
              
            }
          }); 
            $('#tblDocumentType tbody').sortable();
          
        });

        //  if($( ".catName" ).hasClass('selected')) {
        //   selected_category=$(this).closest('tr').attr('id');
        //   $('#'+selected_category).removeClass('selected');
        //   if($('#projectcategory').val()!=selected_category)
        //       $('#projectcategory').val(selected_category);
        //    GetDocumentTypeByCategoryUID(selected_category);
        //   $("#projectcategory").val(selected_category);
        //   $('#tblDocumentType tbody tr').each(function (key, row) {
        //     var row_class=$(row).prop('className');
        //     row_class=row_class.split(" ");
        //     row_class=row_class[0];
        //       $('.'+row_class).show();
            
        //   }); 
        // };

        $( "#tblCategory tr" ).dblclick(function() {
          selected_category=$(this).closest('tr').attr('id');
          $('#'+selected_category).removeClass('selected');
          if($('#projectcategory').val()!=selected_category)
              $('#projectcategory').val(selected_category);
           GetDocumentTypeByCategoryUID(selected_category);
          $("#projectcategory").val(selected_category);
          $('#tblDocumentType tbody tr').each(function (key, row) {
            var row_class=$(row).prop('className');
            row_class=row_class.split(" ");
            row_class=row_class[0];
              $('.'+row_class).show();
            
          }); 
        });

      });

// setInterval(function(){
//   var currentcategory = $('#projectcategory').find('option:selected');
//     var currentDocument = $('#projectdocument').find('option:selected');
//     console.log($(currentcategory).val()+'ii'+$(currentDocument).val())
//   if ($(currentcategory).val()!='' &&  $(currentDocument).val()==null) {

//           $(currentcategory).remove();
//         }
//         console.log("dd")
//       },1000);
      $(document).off('click', '.removecategory').on('click', '.removecategory', function (e) {
        e.preventDefault();
        var carr  = [];
        var category = {};
        category.categoryName = $(this).attr('data-categoryName');
        category.categoryuid = $(this).attr('data-categoryuid'); 
        var categoryuid = $(this).attr('data-categoryuid');  
        carr.push(categoryuid); 
        $.ajax({
          type : "POST",
          url : "<?php echo base_url();?>/ProjectCustomer/GetDocumentTypeByCategory",
          data: {"CategoryUID":carr},
          dataType: "JSON",
          success :  function(data){    
            $.each(data, function(index, value) {             
             if(Array.isArray(value)){
              $.each(value , function(m,n){
                $("#tblDocumentType tbody tr").each(function(){
                  var getdocumentType  =  $(this).closest("tr").find("input:hidden").val();                 
                  if(n.DocumentTypeUID == getdocumentType){
                    $(this).closest("tr").remove();
                  } 
                });    
              });
            }
          });
          }
        });
        categories=[];
        if (category.categoryuid) {
          var appendoption = `<option value="`+category.categoryuid+`" data-categoryName = "`+category.categoryName+`">`+category.categoryName+`</option>`;
          $('#projectcategory').append(appendoption);
          $(this).closest('tr').remove();    
          $("."+category.categoryuid).remove();   
          getcategory = [];
          $("#tblCategory tbody tr").each(function(){
            var getcate =  $(this).closest("tr").find("input:hidden").val();
            getcategory.push(getcate);
            categories.push(getcate);
          });
          getDocumentCategory(getcategory); 
          $("#projectcategory").val(category.categoryuid);
          GetDocumentTypeByCategoryUID(category.categoryuid);     
          callselect2();       
        }
      });        

       $(document).off('click', '.removedocument').on('click', '.removedocument', function (e) {
        e.preventDefault();
        //alert("fff")
        var documents = {};
        documents.documentname = $(this).attr('data-documentname');
        documents.documentuid = $(this).attr('data-documentuid');   
        documents.categoryName = $(this).attr('data-categoryName');       
        if (documents.documentuid) {
          // var appendoption = `<option value="`+documents.documentuid+`" data-documentname = "`+documents.documentname+`">`+documents.categoryName+`</option>`;
          // $('#projectcategory').append(appendoption);
          selected_category=$(this).closest('tr').prop('className');
          //alert(selected_category)
          if($('#projectcategory').val()!=selected_category)
              $('#projectcategory').val(selected_category);
           GetDocumentTypeByCategoryUID(selected_category);

           if($('#projectdocument').val()!=documents.documentuid)
              $('#projectdocument').val(documents.documentuid);

          $(this).closest('tr').remove();      
          // $("table#tblCategoryDocumentType ."+documents.documentuid).closest('tr').remove();
          // $("table#tblDocumentType ."+documents.documentuid).closest('tr').remove(); 
          getcategory = [];
          categories = [];
          var rowCount = $('#tblCategoryDocumentType tr').length;
          if (rowCount <=1) {
            $("#tblCategory tbody").empty();
          }
          $("#tblCategory tbody tr").each(function(){
            var getcate =  $(this).closest("tr").find("input:hidden").val();
            getcategory.push(getcate);
            categories.push(getcate);
          });
          console.log()
          $(".btn.btn-pinterest.removedocument."+documents.categoryName+'-'+documents.documentuid).parents("tr").remove(); 
            $(".text-center."+documents.categoryName+'-'+documents.documentuid).parents("tr").remove(); 
          getDocumentCategory(getcategory);      
          callselect2();          
        }
      });


      function getDocumentCategory(getcategory){   
      var currentcategory = $('#projectcategory').find('option:selected');
        var categoryuid = $(currentcategory).val();     
        $.ajax({
          url: "<?php echo base_url('ProjectCustomer/GetDocumentTypeByCategory')?>",
          type: 'POST',
          data: {"CategoryUID":getcategory},
          dataType: "JSON",
          success: function (data) 
          { 
            docuid  = [];
            $("#tblDocumentType tbody tr").each(function(){              
              docUID = $(this).closest("tr").find("input:hidden").val();
              docuid.push(docUID);
            });
            if(data!='')
            {
              $('#projectdocument').empty();              
              $.each(data, function(index, value) {             
               if(Array.isArray(value)){
                $.each(value , function(m,n){
                  var match = 0;
                  $.each(docuid , function(a,b){
                    if(b == n.DocumentTypeUID){   
                      match = 1;          
                    }
                  });
                  // if (n.CategoryUID == categoryuid) {
                  //     $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" data-documentName="'+ n.DocumentTypeName +'" selected="selected">'+ n.DocumentTypeName +'</option>'); 
                  // }else{
                      $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" data-documentName="'+ n.DocumentTypeName +'">'+ n.DocumentTypeName +'</option>');
                  //}
               });
              }
            });
            }      
          }
        });
      }


  function selectByText( txt ) {
    $('#projectcategory option')
    .filter(function() { return $.trim( $(this).val() ) == txt; })
    .attr('selected',true);
}
</script>

<script type="text/javascript">

          var selected_category='';
        $(".catName").click(function(){
              
           selected_category=$(this).closest('tr').attr('id');
           //selectByText( $.trim( $('input[name=proCategory]').val() ) );
           $('#'+selected_category).addClass('selected').siblings().removeClass('selected');
           if($('#projectcategory').val()!=selected_category)
              $('#projectcategory').val(selected_category);
           GetDocumentTypeByCategoryUID(selected_category);
          $('#tblDocumentType tbody tr').each(function (key, row) {
            var row_class=$(row).prop('className');
            row_class=row_class.split(" ");
            row_class=row_class[0];
            if (row_class != selected_category) {
              $('.'+row_class).hide();
            }else{
              $('.'+row_class).show();
              $('#tblDocumentType tbody').sortable();
            }
          }); 
          
        });

        $( ".catName" ).dblclick(function() {
          selected_category=$(this).closest('tr').attr('id');
          if($('#projectcategory').val()!=selected_category)
              $('#projectcategory').val(selected_category);
           GetDocumentTypeByCategoryUID(selected_category);
          $('#'+selected_category).removeClass('selected');
          $("#projectcategory").val(selected_category);
          $('#tblDocumentType tbody tr').each(function (key, row) {
            var row_class=$(row).prop('className');
            row_class=row_class.split(" ");
            row_class=row_class[0];
              $('.'+row_class).show();
            
          }); 
        });

  $('#tblCategory tbody').sortable();
  $('#tblDocumentType tbody').sortable();  
</script>

<script type="text/javascript">
  $(document).ready(function(){
    var currentcategory = $('#projectcategory').find('option:selected');
    if ($(currentcategory).val()) {
      var categoryuid = $(currentcategory).val();
      GetDocumentTypeByCategoryUID(categoryuid);
      //callselect2();
    }
  })
</script>


<script type="text/javascript">
  $("#sort_table").css('display','none');
    var click=0;
    $("#back").css('display','none');
  $("#sortTable").on('click',function(){
     $("#back").css('display','none');
    if (click==0) {
      $("#sort_table").css('display','flex');
      $("#main_table").css('display','none');
      $("#back").css('display','block');
      click=1;
    }else{
      $("#sort_table").css('display','none');
      $("#main_table").css('display','flex');
      $("#back").css('display','none');
      click=0;
    }
  });

  $("#back").on('click',function(){
    $("#sort_table").css('display','none');
      $("#main_table").css('display','flex');
      $("#back").css('display','none');
  });
  
</script>



