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
/*.nav-pills .nav-item .nav-link.active {
    color: #fff !important;
    background-color: #ea4642;
    box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(235, 73, 69, 0.88);
    }*/
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

    /*#projectcategory { width: 350px; }*/
  </style>
  <div class="card mt-40 " id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon"> <?php echo $DocumentDetails->ProjectName;?>
  </div>
</div>
<div class="card-body mb-0">



  <!-- <div class="wizard-navigation"> -->
    <ul class="nav nav-pills nav-pills-rose customtab ">
      <li class="nav-item">
        <a class="nav-link active" href="#singleentry" data-toggle="tab" role="tablist">
          Summary
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#ProjectUsers" data-toggle="tab" role="tablist">
         Users/Email
       </a>
     </li>
     <li class="nav-item">
      <a class="nav-link" href="#category" data-toggle="tab" role="tablist">
        Category & Doc Type
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#TPO" data-toggle="tab" role="tablist">
       TPO Company
     </a>
   </li>
   <li class="nav-item">
    <a class="nav-link" href="#Investor" data-toggle="tab" role="tablist">
     Investor / Custodian
   </a>
 </li>
<!--  <li class="nav-item">
  <a class="nav-link" href="#Custodian" data-toggle="tab" role="tablist">
   Custodian
 </a>
</li> -->
<li class="nav-item">
  <a class="nav-link" href="#question" data-toggle="tab" role="tablist">
   Check List              
 </a>
</li>
</ul>


<form action="#"  name="user_form" id="user_form">             

  <div class="tab-content customtabpane mb-0">
    <div class="tab-pane active" id="singleentry">

      <div class="row">
       <div class="col-sm-12">
        <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Project Details</h4> 

      </div>
    </div>

    <div class="row mt-10">
     <div class="col-md-3">
      <div class="form-group bmd-form-group">
       <label for="username" class="bmd-label-floating">Project Name</label>
       <input type="text" class="form-control" id="ProjectName" name="ProjectName" value="<?php echo $DocumentDetails->ProjectName;?>" />
     </div>
   </div>
   <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="username" class="bmd-label-floating">Project Code</label>
      <input type="text" class="form-control" id="ProjectCode" name="ProjectCode" value="<?php echo $DocumentDetails->ProjectCode;?>" />
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="roleuid" class="bmd-label-floating">Client </label>
      <select class="select2picker form-control"  id="CustomerUID" name="CustomerUID" >
        <option value=""></option>
        <?php foreach ($Category as $key => $value) { 
          if($value->CustomerUID == $DocumentDetails->CustomerUID)
          { ?>
            <option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
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
 <div class="col-md-3">
   <div class="form-group bmd-form-group">
    <label for="username" class="bmd-label-floating">Priority Time<span class="mandatory"></span></label>
    <input type="number" class="form-control" id="PriorityTime" name="PriorityTime" value="<?php echo $DocumentDetails->PriorityTime;?>" />
  </div>
</div>
<div class="col-md-3">
 <div class="form-group bmd-form-group">
  <label for="roleuid" class="bmd-label-floating">SFTP </label>
  <select class="select2picker form-control"  id="SFTPUID" name="SFTPUID" >
   <option value=""></option>
   <?php foreach ($SFTP as $key => $value) { 
    if($value->SFTPUID == $DocumentDetails->SFTPUID)
     { ?>

      <option value="<?php echo $value->SFTPUID; ?>" selected><?php echo $value->SFTPName; ?></option>

    <?php }else{ ?>
      <option value="<?php echo $value->SFTPUID; ?>"><?php echo $value->SFTPName; ?></option>
    <?php }
    ?>
  <?php } ?>

</select>
</div>
</div>
<div class="col-md-3">
 <div class="form-group bmd-form-group">
  <label for="roleuid" class="bmd-label-floating">SFTP Export</label>
  <select class="select2picker form-control"  id="SFTPExportUID" name="SFTPExportUID" >
   <option value=""></option>
   <?php foreach ($SFTP as $key => $value) { 
    if($value->SFTPUID == $DocumentDetails->SFTPExportUID)
     { ?>

      <option value="<?php echo $value->SFTPUID; ?>" selected><?php echo $value->SFTPName; ?></option>

    <?php }else{ ?>
      <option value="<?php echo $value->SFTPUID; ?>"><?php echo $value->SFTPName; ?></option>
    <?php }
    ?>
  <?php } ?>

</select>
</div>
</div>
<div class="col-md-3">
  <div class="form-group bmd-form-group">

   <label for="BulkImportFormat" class="bmd-label-floating">BulkImportFormat<span class="mandatory"></span></label>
   <select class="select2picker form-control PriorityUID"  id="BulkImportFormat" name="BulkImportFormat" required>
    <option value=""></option>
    <option value="Stacx-Standard">Doctrac-Standard</option>
    <option value="Stacx-Assignment">Doctrac-Assignment</option>
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
</div>

<div class="row mt-10">
  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="ProductUID" class="bmd-label-floating">Product<span class="mandatory"></span></label>
     <select class="select2picker form-control ProductUID"  id="ProductUID" name="ProductUID" required>
        <?php foreach ($CustomerProducts as $key => $value) { ?>
          <option value="<?php echo $value->ProductUID; ?>" <?php if ($DocumentDetails->ProductUID == $value->ProductUID) { echo "selected"; } ?>><?php echo $value->ProductName; ?></option>
        <?php } ?>
    </select>
  </div>
</div>

<!--   <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="StacxDocuments" class="bmd-label-floating">Stacking Documents<span class="mandatory"></span></label>
     <select class="select2picker form-control StacxDocuments"  id="StacxDocuments" name="StacxDocuments" required>
      <?php if ($DocumentDetails->StacxDocuments == 1){ ?> 
        <option value="1" selected>Stacx Documents</option>
        <option value="2" >Client Documents</option>
      <?php }else if ($DocumentDetails->StacxDocuments == 2){ ?>
        <option value="1" >Stacx Documents</option>
        <option value="2" selected>Client Documents</option> 
      <?php }else{ ?>
        <option></option>
        <option value="1" >Stacx Documents</option>
        <option value="2" >Client Documents</option>
      <?php } ?>
      
    </select>
  </div>
</div> -->


  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="DataEntryDisplay" class="bmd-label-floating">Data Entry Display<span class="mandatory"></span></label>
     <select class="select2picker form-control DataEntryDisplay"  id="DataEntryDisplay" name="DataEntryDisplay" required>
      <?php if ($DocumentDetails->DataEntryDisplay == 1){ ?> 
        <option value="1" selected>1 Column</option>
        <option value="2" >2 Column(s)</option>
      <?php }else if ($DocumentDetails->DataEntryDisplay == 2){ ?>
        <option value="1" >1 Column</option>
        <option value="2" selected>2 Column(s)</option>
      <?php }else{ ?>
        <option></option>
        <option value="1" >1 Column</option>
        <option value="2">2 Column(s)</option>
      <?php } ?>
      
    </select>
  </div>
</div>

  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="ExportType" class="bmd-label-floating">Export Type<span class="mandatory"></span></label>
     <select class="select2picker form-control ExportType"  id="ExportType" name="ExportType" required>
      <?php if ($DocumentDetails->ExportType == 'Loan Level'){ ?> 
        <option value="Loan Level" selected>Loan Level</option>
      <option value="Consolidated" >Consolidated</option>
    <?php }else if ($DocumentDetails->ExportType == 'Consolidated'){ ?>
      <option value="Loan Level" >Loan Level</option>
      <option value="Consolidated" selected>Consolidated</option>
      <?php }else{ ?>
        <option></option>
      <option value="Loan Level" >Loan Level</option>
      <option value="Consolidated">Consolidated</option>
      <?php } ?>
      
    </select>
  </div>
</div>


  <div class="col-md-3 productrow">
    <div class="form-group bmd-form-group">
     <label for="DocInstance" class="bmd-label-floating">Document Instance<span class="mandatory"></span></label>
     <select class="select2picker form-control DocInstance"  id="DocInstance" name="DocInstance" required>
       <?php if ($DocumentDetails->DocInstance == 1){ ?>
        <option value="1" selected>Yes</option>
      <option value="0">No</option>
       <?php } else if ($DocumentDetails->DocInstance == 0){ ?> 
        <option value="1" >Yes</option>
      <option value="0" selected>No</option>
       <?php } else { ?>
        <option></option>
        <option value="1" >Yes</option>
        <option value="0">No</option>
      <?php } ?>
     
    </select>
  </div>
</div>

</div>

<div class="row">   
 <div class="col-md-3">
  <div class="form-group bmd-form-group">
   <input type="hidden" class="form-control" id="username" name="ProjectUID" value="<?php echo $DocumentDetails->ProjectUID;?>" />
 </div>
</div>
</div>
<div class="row">
  <div class="col-md-3">
 <div class="form-group bmd-form-group">
  <label for="roleuid" class="bmd-label-floating">Auto Import By</label>
  <select class="select2picker form-control"  id="importColumn" name="importColumn" >
   <option value=""></option>
   <?php foreach ($importColumn as $key => $value) { 
    if($value->ColumnID == $DocumentDetails->AutoImportColumn)
     { ?>

      <option value="<?php echo $value->ColumnID; ?>" selected><?php echo $value->ColumnName; ?></option>

    <?php }else{ ?>
      <option value="<?php echo $value->ColumnID; ?>"><?php echo $value->ColumnName; ?></option>
    <?php }
    ?>
  <?php } ?>

</select>
</div>
</div>
<div class="col-md-3" >
    <div class="form-check">
      <label class="form-check-label">
        <input class="form-check-input" type="checkbox" <?php if($DocumentDetails->ExportAsFolder == 1){ echo "checked"; } ?>  name="IsFolderExport" id="IsFolderExport" > Export As Folder
        <span class="form-check-sign">
          <span class="check"></span>
        </span>
      </label>
    </div>
  </div>  
  <div class="col-md-2" >
    <div class="form-check">
      <label class="form-check-label">
        <input class="form-check-input" type="checkbox" <?php if($DocumentDetails->IsAutoExport == 1){ echo "checked"; } ?>  name="IsAutoExport" id="IsAutoExport" > AutoExport
        <span class="form-check-sign">
          <span class="check"></span>
        </span>
      </label>
    </div>
  </div>   
  <div class="col-md-3" >
    <div class="form-check">
      <label class="form-check-label">
        <input class="form-check-input" type="checkbox" <?php if($DocumentDetails->ZipImport == 1){ echo "checked"; } ?>  name="ZipImport" id="ZipImport" > Zip Import
        <span class="form-check-sign">
          <span class="check"></span>
        </span>
      </label>
    </div>
  </div>  
  <div class="col-md-3">
    <div class="togglebutton" >
      <label class="label-color"> Active
        <input type="checkbox" id="Active" name="Active" class="Active" <?php if($DocumentDetails->Active == 1){ echo "checked"; } ?>>
        <span class="toggle"></span>
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
            if ($module->WorkflowModuleUID != 5 && $module->WorkflowModuleUID != 6 && $module->WorkflowModuleUID != 7 && $module->WorkflowModuleUID != 8) { 
            if ($module->WorkflowModuleUID == $DocumentDetails->OCRWorkflowModuleUID) { ?>
              <option value="<?php echo $module->WorkflowModuleUID; ?>" data-workflowname = "<?php echo $module->WorkflowModuleName; ?>" selected ><?php echo $module->WorkflowModuleName; ?></option>
            <?php }else{ ?> 
             <option value="<?php echo $module->WorkflowModuleUID; ?>" data-workflowname = "<?php echo $module->WorkflowModuleName; ?>" ><?php echo $module->WorkflowModuleName; ?></option>
            <?php }
              }
             } ?>                
        </select>
      </div>
  </div>   
<!--   <div class="col-md-3">
    <div class="togglebutton" >
      <label class="label-color"> Active
        <input type="checkbox" id="Active" name="Active" class="Active" <?php if($DocumentDetails->Active == 1){ echo "checked"; } ?>>
        <span class="toggle"></span>
      </label>
    </div>
  </div> -->
</div>

</div>
<div class="tab-pane" id="fieldforproject">
 <div class="col-sm-12">
  <h6 class="panelheading" style="font-weight: 700; border-bottom: 1px solid #d9d9d9;">Project Fields  </h6><br>
</div>
<?php
$fieldsarray=$this->ProjectCustomer_model->SelectFields($DocumentDetails->ProjectUID);
?>
<div class="col-md-12 mt-10">
 <div class="form-group bmd-form-group">
   <label for="Fields" class="bmd-label-floating">Fields<span class="mandatory"></span></label>
   <select class="select2picker form-control"  id="FieldsName" name="FieldsName[]" multiple>
    <?php
    foreach ($Fields as $value) 
    {
     if(in_array($value->FieldUID,$fieldsarray))
     {
      ?>
      <option value="<?php echo $value->FieldUID; ?>" selected><?php echo $value->FieldName ?></option> 
      <?php
    } else {
      ?>
      <option value="<?php echo $value->FieldUID; ?>" ><?php echo $value->FieldName ?></option> 
    <?php }  
  } ?>
</select>
</div>
</div> 
</div>    

<div class="tab-pane  mt-30" id="ProjectUsers">
  <div class="row mt-10 " >
   <div class="col-md-3">
    <div class="form-group bmd-form-group">
      <label for="Fields" class="bmd-label-floating">Users</label>
      <select class="select2picker form-control"  id="UsersID" name="UsersID">
        <!-- <option value=""></option> -->
        <?php foreach ($GetUsersList as $key => $value) { ?>
          <option value="<?php echo $value->UserUID; ?>" data-id="<?php echo $value->RoleUID; ?>"><?php echo $value->UserName.' - '.$value->RoleName; ?></option>
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
    <button type="button" class="btn btn-success btn-round UpdateProjectUser"><i class="icon-plus22"></i> Add</button>
  </div>
</div>
<div class="col-sm-12 pd-0">

  <div class="material-datatables">
    <table id="ProjectUsersTable" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
     <thead>
      <tr>
        <!-- <th  class="text-center" >User UID</th> -->
        <th class="text-center">User Name</th>
        <th class="text-center">Receive Emails</th>
        <th class="text-center">Loan Level Export</th>
        <th class="text-center" style="width: 10%;">Action</th>

      </tr>
    </thead>
    <tbody>
      <?php
      foreach($GetProjectUsers as $ac=> $row): ?>
        <tr id=<?php echo $row->UserUID; ?>>

         <input type='hidden' class='hid-ProjectUser'  value=<?php echo $row->UserUID; ?>>
         <td style="text-align: center;"><?php echo $row->UserName.' - '.$row->RoleName?></td>
         <td style='text-align: center;'><div class='form-check'><label class='form-check-label'><input class='form-check-input chk-reviewemail'  <?php if($row->CanReceiveExceptionMails == 1){ echo "checked"; } ?>  type='checkbox'><span class='form-check-sign'><span class='check'></span> </span></label> </td>
          <td style='text-align: center;'><div class='form-check'><label class='form-check-label'><input class='form-check-input chk-accessible' <?php if ($row->RoleUID != 13) {echo "disabled";}else{ echo ""; } ?> <?php if($row->IsAccessible == 1){ echo "checked"; } ?>  type='checkbox'><span class='form-check-sign'><span class='check'></span> </span></label> </td>
          <td style="text-align: center"> 
            <span style="text-align: center;width:100%;">
              <button type="button" class="btn btn-link btn-danger btn-just-icon btn-xs btnDelete"><i class="icon-bin"></i></button>
            </span>
          </td>



        </tr>

        <?php 

      endforeach; ?>
    </tbody>
  </table>
</div>

</div>
</div>


<div class="tab-pane" id="category">
  <div class="row mt-10">
    <div class="col-md-4">
      <?php  $testarray=$this->Common_Model->projectcategory($DocumentDetails->ProjectUID);  ?>
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
      <?php 
      $countCategory = count($CheckcategoryID); 
      if (!empty($CheckcategoryID)) {

        $fetchDocuType = $this->ProjectCustomer_model->GetDocumentTypeByCategory($CheckcategoryID);

      }
      else{
        $fetchDocuType = [];
      }
      $ProjectDocarray=$this->ProjectCustomer_model->ProjectDocumentType($DocumentDetails->ProjectUID);
      ?>

      <div class="">
        <label for="projectdocument" class="bmd-label-floating"> Document Type <span class="mandatory"></span> </label>
        <select class=" form-control"  id="projectdocument" >
          <option value=""></option>
          <?php
          $SelectedDoc =[];
          foreach ($ProjectDocarray as $key => $Doc) {
            $SelectedDoc[] =$Doc->DocumentTypeUID;
          }
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
   <?php  $testarray=$this->Common_Model->projectcategory($DocumentDetails->ProjectUID);  ?>

   <div class="material-datatables">
    <table class="table table-bordered" id="tblCategory">
      <thead>
        <th style="min-width:20px  !important;width:20px;">S.No</th>
        <th>Category Type</th>
        <th style="min-width:40px  !important;width:40px;">Action</th>
      </thead>
      <tbody>  
       <?php  $testarray1=$this->ProjectCustomer_model->projectcategory($DocumentDetails->ProjectUID);  ?>               
       <?php   
       $CheckcategoryID = [];
       $sno =  0;
       foreach ($testarray1 as $key => $value) {
        if(in_array($value->CategoryUID,$testarray)){
          $CheckcategoryID[] = $value->CategoryUID;  ?>  
          <tr id="<?php echo $value->CategoryUID;?>">
            <?php $sno = $sno+1; ?>
            <input type="hidden" value="<?php echo $value->CategoryUID;?>"  name="projectcategory[]">
            <td  style="min-width:20px  !important;width:20px;" class="text-center"><?php echo $sno; ?></td>
            <td  class="text-center catName"><?php echo $value->CateName; ?></td>
            <td  style="min-width:40px  !important;width:40px;" class="text-center" ><button  type="button" data-categoryuid = "<?php echo  $value->CategoryUID;?>" data-categoryName = "<?php echo  $value->CategoryName;?>"  class="btn btn-pinterest removecategory"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>      
        <?php }   } ?>

      </tbody>  
    </table>
  </div>
</div>

<div class="col-md-6" style="margin-top: 20px;">

  <?php 
  $countCategory = count($CheckcategoryID); 
  if (!empty($CheckcategoryID)) {

    $fetchDocuType = $this->ProjectCustomer_model->GetDocumentTypeByCategory($CheckcategoryID);

  }
  else{
    $fetchDocuType = [];
  }
  $ProjectDocarray=$this->ProjectCustomer_model->ProjectDocumentType($DocumentDetails->ProjectUID);
  ?>


  <div class="material-datatables">
    <table class="table table-bordered" id="tblDocumentType">
      <thead>
        <th style="min-width:20px !important;width:20px;">S.No</th>
        <th>Document Type</th>
        <th style="min-width:40px  !important;width:40px;">Action</th>
      </thead>
      <tbody>
        <?php

        $sno =  0;
        $ProjectDocarray=$this->ProjectCustomer_model->getProjectDocumentType($DocumentDetails->ProjectUID);

        foreach ($ProjectDocarray as $key => $row) {
          ?>

          <tr class="<?php echo $row->CategoryUID;?>">
           <?php $sno = $sno+1; ?>
           <input type="hidden" value="<?php echo $row->DocumentTypeUID; ?>" name="projectdocument[]">
           <td  style="min-width:20px  !important;width:20px;"  class="text-center"><?php echo $sno; ?></td>
           <td  class="text-center"><?php echo $row->DocumentTypeName.' - '.$row->CategoryName; ?></td>
           <td style="min-width:40px  !important;width:40px;" class="text-center <?php echo  str_replace(' ', '', $row->CategoryName);?>-<?php echo  $row->DocumentTypeUID;?>" ><button  type="button" data-documentuid = "<?php echo  $row->DocumentTypeUID;?>" data-documentname = "<?php echo  $row->DocumentTypeName;?>"  class="btn btn-pinterest removedocument <?php echo  str_replace(' ', '', $row->CategoryName);?>-<?php echo  $row->DocumentTypeUID;?>" data-categoryName = "<?php echo  str_replace(' ', '', $row->CategoryName);?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
         </tr>
         <?php          
       }
       ?>     
     </tbody>  
   </table>
   
 </div> 
</div>
</div>

<div class="row  mt-10" id="main_table">

  <?php $sno =  0; ?>

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
          <?php

          $sno =  0;
          $ProjectDocarray=$this->ProjectCustomer_model->getProjectDocumentTypeWithCategory($DocumentDetails->ProjectUID);

          foreach ($ProjectDocarray as $key => $row) {
            ?>

            <tr class="<?php echo $row->CategoryUID;?>">
             <?php $sno = $sno+1; ?>

             <td  style="min-width:20px  !important;width:20px;"  class="text-center"><?php echo $sno; ?></td>
             <td  style="min-width:20px  !important;width:20px;"  class="text-center"><?php echo $row->CategoryName; ?></td>
             <td  class="text-center"><?php echo $row->DocumentTypeName; ?></td>
             <td style="min-width:40px  !important;width:40px;" class="text-center  <?php echo  str_replace(' ', '', $row->CategoryName);?>-<?php echo  $row->DocumentTypeUID;?>" ><button  type="button" data-documentuid = "<?php echo  $row->DocumentTypeUID;?>" data-documentname = "<?php echo  $row->DocumentTypeName;?>"  class="btn btn-pinterest removedocument <?php echo  str_replace(' ', '', $row->CategoryName);?>-<?php echo  $row->DocumentTypeUID;?>" data-categoryName = "<?php echo  str_replace(' ', '', $row->CategoryName);?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
           </tr>
           <?php          
         }
         ?>     
       </tbody>  
     </table>
   </div>
 </div>
</div>
</div>






<div class="tab-pane" id="TPO">

  <div class="row">
    <div class="col-md-12">
      <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>TPO Company </h4>
    </div>
    <div class="col-md-6">
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
    <div class="col-md-6" style="margin-top: 11px;">
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
          <?php foreach ($TPOLenders as $key => $prolender) { ?>
            <tr>
              <input type="hidden" name="TPO[]" value="<?php echo $prolender->LenderUID; ?>">
              <td style="width: 5%;" class="text-center"><?php echo $key + 1; ?></td>
              <td class="text-center"><?php echo $prolender->LenderName; ?></td>
              <td class="text-center"><button type="button" data-lenderuid = "<?php echo $prolender->LenderUID; ?>" data-lendername = "<?php echo $prolender->LenderName; ?>" class="btn btn-pinterest removelenders"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
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
        <label for="ddlInvestors" class="bmd-label-floating">Custodians </label>
        <select class="select2picker form-control"  id="ddlCus">
          <option></option>
          <?php foreach ($AllCustodians as $key => $custodian) { ?>
            <option value="<?php echo $custodian->CustodianUID; ?>" data-custodianname = "<?php echo $custodian->CustodianName; ?>" data-custodianno = "<?php echo $custodian->CustodianNo; ?>"><?php echo $custodian->CustodianName; ?></option>
          <?php } ?>                
        </select>
      </div>
    </div>
    <div class="col-md-2 mt-10">
      <button type="button" class="btn btn-success btn-round addinvestor"><i class="icon-plus22"></i> Add</button>
    </div>
    <div class="col-md-12 mt-10">
      <table class="table table-bordered" id="tblProductInvestors">
        <thead>
          <tr>
            <th style="width: 5%; padding: 3px 3px !important;" class="text-center">SNo</th>
            <th style="padding: 3px 3px !important;" class="text-center">Investor No</th>
            <th style="padding: 3px 3px !important;" class="text-center">Investor Name</th>
            <th style="padding: 3px 3px !important;" class="text-center">Custodian Name</th>
            <th style="padding: 3px 3px !important;" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>          
          <?php  foreach ($ProjectInvestors as $key => $proinvestor) { ?>
            <tr>
              <?php  $txtval  =  $proinvestor->InvestorUID.','.$proinvestor->CustodianUID; ?>
              <input type="hidden" name="Investors[]" value="<?php echo $txtval; ?>">
              <td style="width: 5%;" class="text-center"><?php echo $key + 1; ?></td>
              <td class="text-center"><?php echo $proinvestor->InvestorNo; ?></td>
              <td class="text-center"><?php echo $proinvestor->InvestorName; ?></td>
              <td class="text-center"><?php echo $proinvestor->CustodianName; ?></td>
              <td class="text-center"><button type="button" data-Investoruid = "<?php echo $proinvestor->InvestorUID; ?>" data-investorno = "<?php echo $proinvestor->InvestorNo; ?>"   data-InvestorName = "<?php echo $proinvestor->InvestorName; ?>" class="btn btn-pinterest removeinvestors removerules"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
            </tr>
            <?php
          }
          ?>
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
          <?php foreach ($ProjectCustodians as $key => $procustodian) { ?>
            <tr>
              <input type="hidden" name="Custodians[]" value="<?php echo $procustodian->CustodianUID; ?>">
              <td style="width: 5%;" class="text-center"><?php echo $key + 1; ?></td>
              <td class="text-center"><?php echo $procustodian->CustodianNo; ?></td>
              <td class="text-center"><?php echo $procustodian->CustodianName; ?></td>
              <td class="text-center"><button type="button" data-ruleuid = "<?php echo $procustodian->CustodianUID; ?>" data-rulename = "<?php echo $procustodian->CustodianName; ?>" class="btn btn-pinterest removerules"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>             
</div>
-->


<div class="tab-pane" id="question">
  <div class="col-md-12 pd-0">
    <h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Check List </h4>
  </div>
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-10 pd-0 mt-10">
        <div class="form-group bmd-form-group">
          <label for="ddlInvestors" class="bmd-label-floating">Audit Check List Type </label>
          <?php 
          foreach ($QuestionType as $key => $value) {  ?>



          <?php } ?>
          <select class="select2picker form-control"  id="auditCheckListType">
            <option></option>
            <?php 
            foreach ($QuestionType as $key => $value) {  ?>
              <option value="<?php echo $value->QuestionTypeUID; ?>" data-questionname = "<?php echo $value->QuestionTypeName; ?>"><?php echo $value->QuestionTypeName; ?></option>
            <?php    }        ?>
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
        <th style="padding: 3px 3px !important;width:50px !important;" class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>

     <?php foreach ($ProjectQuestionType as $key => $ProjectQuestion) {  ?>
      <tr>
        <input type="hidden" name="Questions[]" value="<?php echo $ProjectQuestion->QuestionTypeUID; ?>">
        <td style="width: 5%;" class="text-center"><?php echo $key + 1; ?></td>   
        <td class="text-center"><?php echo $ProjectQuestion->QuestionTypeName; ?></td>
        <td class="text-center" style="width:50px !important;"><button type="button" data-questionuid = "<?php echo $ProjectQuestion->QuestionTypeUID; ?>" data-questionname = "<?php echo $ProjectQuestion->QuestionTypeName; ?>" class="btn btn-pinterest removerules removequestion"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
      </tr>

      <?php
    }
    ?>


  </tbody>
</table>
</div>
</div>



<div class="ml-auto text-right mb-10">
 <a href="<?php echo base_url('ProjectCustomer'); ?>" class="btn btn-fill btn-back btn-wd ajaxload mt-0" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
 <button type="submit" class="btn btn-fill btn-update btn-wd updateuser mt-0" name="updateuser"><i class="icon-floppy-disk pr-10"></i>Update Project </button>
</div>
</div>


</form>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script> 
<script type="text/javascript">
   var ProjectUserID = [];
    var EmailNotification = [];
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#tblCategory tbody').sortable();
      if($("#IsAutoExport").prop('checked')){
        $("#LoanLevelUsersDiv").css('display','block');
      }else{
        $("#LoanLevelUsersDiv").css('display','none');
        
      }
    var usersList=('<?php echo json_encode($GetProjectUsers);?>');
    
   
    if (usersList.length > 2) {
    var datatable = $('#ProjectUsersTable').DataTable({pageLength: 100, // Set Page Length
      lengthMenu:[[10, 25, 50, 100], [10, 25, 50, 100]]});
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
    
    datatable.rows().every(function () {

      var value = this.id();
      if (value!='') {
        ProjectUserID.push(value);
      }
      
    });
    }

    $('select[name="Priority"] option[value="<?php  echo $DocumentDetails->Priority;  ?>"]').attr("selected","selected");
    $('select[name="BulkImportFormat"] option[value="<?php  echo $DocumentDetails->BulkImportFormat;  ?>"]').attr("selected","selected");
    $('select[name="ExportLevel"] option[value="<?php  echo $DocumentDetails->ExportLevel;  ?>"]').attr("selected","selected");

    function selectCategory()
    {
      var CategoryUID = $('#projectcategory').val();
      var projectdocument = $('#projectdocument').val();
      $.ajax({
       url: "<?php echo base_url('ProjectCustomer/GetDocumentTypeByCategory')?>",
       type: 'POST',
       data: {"CategoryUID":CategoryUID},
       dataType: "JSON",
       success: function (data) 
       { 
        if(data!='')
        {
          $('#projectdocument').empty();              
          $.each(data, function(index, value) {             
            if(Array.isArray(value)){
              $.each(value , function(m,n){

                if($.inArray(n.DocumentTypeUID,projectdocument) == -1)
                {
                  $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'">'+ n.DocumentTypeName +'</option>');
                }
                else {

                  $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" selected="true">'+ n.DocumentTypeName +'</option>');
                }

              });
            }
          });
          $('#projectdocument').trigger('change.select2');
        }      
      }
    });
    }
    $('#projectcategory').change(function(){
     selectCategory();
   });

    $("#ProjectUsersTable").on('click', '.btnDelete', function () {
      $(this).closest('tr').remove();
      
      var removeItem = $(this).closest('tr').attr('id');

      ProjectUserID = jQuery.grep(ProjectUserID, function(value) {
        return value != removeItem;
      });
      
    });

    $(".UpdateProjectUser").click(function(){

      var name = $('#UsersID option:selected').text();
      var UserUID = $('#UsersID').val();
      var RoleUID = $('#UsersID option:selected').attr('data-id');
      var Class = '';
      if(RoleUID == 13){
        Class = '';
      }else{
        Class = 'disabled';
      }
      var checkbox = $('#CheckEmailUser').is(':checked') ? "checked" : "";
      if (name!='') {
       var rowcount = $('#ProjectUsersTable tbody tr').length;
      var markup = "<tr><input type='hidden' class='hid-ProjectUserNewRow' name='ProjectUserUID["+rowcount+"]' value='" + UserUID + "'><td style='text-align: center;''>" + name + "</td><td style='text-align: center;''><div class='form-check'><label class='form-check-label'><input class='form-check-input chk-reviewemail' type='checkbox' "+checkbox+" name='RecevieEmailChecked["+rowcount+"]' ><span class='form-check-sign'><span class='check'></span> </span></label> </div></td><td style='text-align: center;''><div class='form-check'><label class='form-check-label'><input class='form-check-input chk-accessible' type='checkbox' "+checkbox+" name='IsAccessible["+rowcount+"]' "+Class+" ><span class='form-check-sign'><span class='check'></span> </span></label> </div></td><td style='text-align: center'> <span style='text-align: center;width:100%;'><button class='btn btn-link btn-danger btn-just-icon btn-xs btnDelete'><i class='icon-bin'></i></button></span></td></tr>";

        $("#ProjectUsersTable tbody").append(markup);
        $('#CheckEmailUser').prop('checked', false);
        $('#UsersID').val('');
        $("#UsersID option[value='"+UserUID+"']").remove();           
        callselect2();
      }else{
        $.notify(
        {
         icon:"icon-bell-check",
         message:"User cannot be empty"
       },
       {
         type:"danger",
         delay:1000 
       }); 
      }
    });

    $(document).off('click','.updateuser').on('click','.updateuser', function(e) {

     var formdata = new FormData($('#user_form')[0]);

     $(".hid-ProjectUser").each(function(key, value){
      console.log(value);
      formdata.append('ProjectUserUID[]', $(value).val());
      formdata.append('RecevieEmailChecked[]', $(value).closest('tr').find('.chk-reviewemail').prop('checked'));
      formdata.append('IsAccessible[]', $(value).closest('tr').find('.chk-accessible').prop('checked'));
    });
    
 button=$(this);
 button_val=$(this).val();
 button_text=$(this).html();
 var ProjectUID = $("#username").val();
 $.ajax({
  type: "POST",
  url: "<?php echo base_url('ProjectCustomer/SaveUpdate'); ?>",
  data: formdata,
  processData: false,
  contentType: false,
  dataType:'json',
  beforeSend: function () {
   button.prop("disabled", true);
   button.html('Loading ...');
   button.val('<i class="fa fa-spin fa-spinner"></i> update..');

 },
 success: function (response) {
   if(response.Status == 2)
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

     triggerpage('<?php echo base_url();?>ProjectCustomer/EditProject/'+ProjectUID);

   }, 1000);}
  
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
    var currentcustodian = $('#ddlCus').find('option:selected');    
    if ($(currentinvestor).val() && $(currentcustodian).val()) {
      var investoruid = $(currentinvestor).val();
      var investorname = $(currentinvestor).attr('data-investorname');
      var investorno = $(currentinvestor).attr('data-investorno');
      var custodianval = $(currentcustodian).val();
      var custodianname = $(currentcustodian).attr('data-custodianname');
      var sno = $('#tblProductInvestors tbody tr').length;
      var txtval  = investoruid + ',' + custodianval;
      var appendrow = `<tr>
      <input type="hidden" name="Investors[]" value="` + txtval + `">
      <td style="width: 5%;" class="text-center">` + (sno + 1) + `</td>
      <td class="text-center">` + investorno + `</td>
      <td class="text-center">` + investorname + `</td>
      <td class="text-center">` + custodianname + `</td>
      <td class="text-center"><button type="button" data-Investoruid = "`+ investoruid +`" data-InvestorName = "`+ investorname +`" data-investorno = "`+ investorno +`" data-investorno = "`+investorno+`"  class="btn btn-pinterest removeinvestors"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
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
    investors.Investoruid = $(this).attr('data-Investoruid');
    investors.InvestorName = $(this).attr('data-InvestorName');  
    investors.investorno = $(this).attr('data-investorno');
    if (investors.Investoruid) {
      var appendoption = `<option value="`+investors.Investoruid+`" data-InvestorName = "`+investors.InvestorName+`"  data-investorno="`+investors.investorno+`">`+investors.InvestorName+`</option>`;
      $('#ddlInvestors').append(appendoption);
      callselect2();
      $(this).closest('tr').remove();
    }
  });



  //  $(document).off('click', '.addcustodian').on('click', '.addcustodian', function (e) {
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
  //     <td class="text-center"><button type="button" data-custodianuid = "`+ custodianuid +`" data-custodianname = "`+ custodianname +`" data-custodianno = "`+ custodianno +`" class="btn btn-pinterest removecustodians"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
  //     </tr>`;
  //     $(currentcustodian).remove();
  //     $('#tblProductCustodians tbody').append(appendrow);
  //     $('#tblProductCustodians tbody tr').each(function (key, row) {
  //       $(row).find('td:first').html(key + 1);
  //     });
  //     callselect2();
  //   }
  // });

  //  $(document).off('click', '.removecustodians').on('click', '.removecustodians', function (e) {

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
      <td class="text-center"><button type="button" data-lenderuid = "`+ lenderuid +`" data-lendername = "`+ lendername +`"  class="btn btn-pinterest removelenders"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
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
      <td class="text-center"   style="width:50px !important;"><button  type="button" data-questionuid = "`+ questionuid +`" data-questionname = "`+ questionname +`"  class="btn btn-pinterest removequestion"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
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


});

var categories = <?php echo json_encode($CheckcategoryID);?>;
//console.log(categories)
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
        }else {
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
          <td class="text-center">` + documentname + ` - `+ categoryname.split(" ").join("") +`</td>
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
          //$('#projectcategory option[value='+category.categoryuid+']').attr('selected','selected');
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
            if(match == 0){
              if (n.CategoryUID == categoryuid) {
                $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" data-documentName="'+ n.DocumentTypeName +'" selected="selected">'+ n.DocumentTypeName +'</option>'); 
              }else{
                $('#projectdocument').append('<option value="'+n.DocumentTypeUID+'" data-documentName="'+ n.DocumentTypeName +'">'+ n.DocumentTypeName +'</option>');
              }
            }
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
           //alert(selected_category)
           //selectByText( $.trim( $('input[name=proCategory]').val() ) );
           $(this).closest('tr').addClass('selected').siblings().removeClass('selected');
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

  
  //$('#tblDocumentType tbody').sortable();  
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

<script type='text/javascript'>

(function()
{
  if( window.localStorage )
  {
    if( !localStorage.getItem('firstLoad') )
    {
      localStorage['firstLoad'] = true;
      window.location.reload();
    }  
    else
      localStorage.removeItem('firstLoad');
  }
})();

</script>

