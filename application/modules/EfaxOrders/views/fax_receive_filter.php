
<div class="SearchBlock" id="FaxReceiveSearch" style="display: none;">
  <fieldset class="">
    <legend>Advanced Search</legend>
    <form id="faxadvancedsearchdata" autocomplete="off">
      <div class="col-md-12 pd-0">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="FaxID" class="bmd-label-floating">Fax ID </label>
              <input type="text" class="form-control" id="ReceiveFaxID" name="FaxID">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="FromFaxNumber" class="bmd-label-floating">From Fax Number</label>
              <input type="text" class="form-control" id="ReceiveFromFaxNumber" name="FromFaxNumber">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="ToFaxNumber" class="bmd-label-floating">To Fax Number</label>
              <input type="text" class="form-control" id="ReceiveToFaxNumber" name="ToFaxNumber">
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="TransmissionStatus" class="bmd-label-floating">Transmission Status</label>
              <select class="select2picker form-control" id="ReceiveTransmissionStatus"  name="TransmissionStatus">   
                <option value="All">All</option> 
                <option value="NEW">NEW</option>
                <option value="INPROGESS">INPROGESS</option>
                <option value="COMPLETE">COMPLETE</option>
                <option value="ERROR">ERROR</option>
                <option value="CANCELED">CANCELED</option>
              </select>
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="FaxStatus" class="bmd-label-floating">Fax Status</label>
              <select class="select2picker form-control" id="ReceiveFaxStatus"  name="FaxStatus">   
                <option value="All">All</option>
                <option value="STORED">STORED</option>
                <option value="NOT_STORED">NOT_STORED</option>
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
                  <input type="text" id="ReceiveFromDate" name="FromDate" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-30 days")); ?>">
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
                  <input type="text" id="ReceiveToDate" name="ToDate" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12  text-right pd-0 mt-10">
          <button type="button" class="btn btn-fill btn-facebook faxReceivesearch" >Submit</button>
          <button type="button" class="btn btn-fill btn-tumblr faxReceiveReset">Reset</button>
        </div>
      </div>
    </form>
  </fieldset>
</div>
