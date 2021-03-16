<div class="pull-right"> 
  <i class="fa fa-filter automation-advance" title="Advanced Search" aria-hidden="true" style="font-size:13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
  <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer;"></i>
</div>
<br>
<div id="automationadvancedsearch"  style="display: none;">
  <fieldset class="automationadvancedsearchdiv">
    <legend>Advanced Search</legend>
    <form id="automationadvancedsearchdata" autocomplete="off">
      <div class="col-md-12 pd-0">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="OrderNumber" class="bmd-label-floating">OrderNumber </label>
              <input type="text" class="form-control" id="OrderNumber" name="OrderNumber">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="LoanNumber" class="bmd-label-floating">Loan Number</label>
              <input type="text" class="form-control" id="LoanNumber" name="LoanNumber">
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="AutomationType" class="bmd-label-floating">Automation Type</label>
              <select class="select2picker form-control" id="AutomationType"  name="AutomationType">   
                <option value="All">All</option> 
                <option value="OCR Sent">OCR Sent</option>
                <option value="OCR Receive">OCR Receive</option>
                <option value="eFax Sent">eFax Sent</option>
                <option value="eFax Receive">eFax Receive</option>
                <option value="Email Sent">Email Sent</option>
                <option value="Email Receive">Email Receive</option>
              </select>
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="AutomationStatus" class="bmd-label-floating">Automation Status</label>
              <select class="select2picker form-control" id="AutomationStatus"  name="AutomationStatus">   
                <option value="All">All</option>
                <option value="Success">Success</option>
                <option value="Failure">Failure</option>
              </select>
            </div>
          </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row mt-5">
              <div class="col-md-3 pd-0 inputprepand" >
                <p class="mt-5"> From </p>
              </div>
              <div class=" col-md-9 " style="padding-left: 0px;">
                <div class="datediv">
                  <input type="text" id="FromDate" name="FromDate" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-30 days")); ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row mt-5">
              <div class="col-md-3 pd-0 inputprepand" >
                <p class="mt-5"> To </p>
              </div>
              <div class=" col-md-9 " style="padding-left: 0px;">
                <div class="datediv">
                  <input type="text" id="ToDate" name="ToDate" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12  text-right pd-0 mt-10">
          <button type="button" class="btn btn-fill btn-facebook search" >Submit</button>
          <button type="button" class="btn btn-fill btn-tumblr reset">Reset</button>
        </div>
      </div>
    </form>
  </fieldset>
</div>