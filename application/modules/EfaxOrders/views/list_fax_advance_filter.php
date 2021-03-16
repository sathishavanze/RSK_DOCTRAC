
<div class="SearchBlock" id="sent_faxadvancedsearch"  style="display: none;">
  <fieldset class="live_faxadvancedsearchdiv">
    <legend>Advanced Search</legend>
    <form id="live_faxadvancedsearchdata" autocomplete="off">
      <div class="col-md-12 pd-0">
        <div class="row">

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="transaction-id" class="bmd-label-floating">Transaction ID</label>
              <input type="text" class="form-control" id="transaction_id" name="transaction-id">
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="transmission_status" class="bmd-label-floating">Transmission Status</label>
              <select class="select2picker form-control" id="transmission_status"  name="transmission_status">   
                <option value="NEW">NEW</option>
                <option value="INPROGESS">INPROGESS</option>
                <option value="COMPLETE" selected="">COMPLETE</option>
                <option value="ERROR">ERROR</option>
                <option value="CANCELED">CANCELED</option>
              </select>
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="fax_status" class="bmd-label-floating">Fax Status</label>
              <select class="select2picker form-control" id="fax_status"  name="fax_status">   
                <option value=""></option>
                <option value="STORED">STORED</option>
                <option value="NOT_STORED">NOT_STORED</option>
              </select>
            </div>
          </div>

          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="image_downloaded" class="bmd-label-floating">Image Download</label>
              <select class="select2picker form-control" id="image_downloaded"  name="image_downloaded">   
                <option value=""></option>
                <option value="true">TRUE</option>
                <option value="false">FALSE</option>
              </select>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="originating_fax_number" class="bmd-label-floating">Originating Fax Number </label>
              <input type="text" class="form-control" id="originating_fax_number" name="originating_fax_number">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="destination_fax_number" class="bmd-label-floating">Destination Fax Number </label>
              <input type="text" class="form-control" id="destination_fax_number" name="destination_fax_number">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="error_code" class="bmd-label-floating">Error Code </label>
              <input type="text" class="form-control" id="error_code" name="error_code">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="min_pages" class="bmd-label-floating">MIN Pages</label>
              <input type="text" class="form-control" id="min_pages" name="min_pages">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="max_pages" class="bmd-label-floating">MAX Pages</label>
              <input type="text" class="form-control" id="max_pages" name="max_pages">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="search_text" class="bmd-label-floating">Search Text </label>
              <input type="text" class="form-control" id="search_text" name="search_text">
            </div>
          </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row mt-5">
              <div class="col-md-3 pd-0 inputprepand" >
                <p class="mt-5"> From </p>
              </div>
              <div class=" col-md-9 " style="padding-left: 0px;">
                <div class="datediv">
                  <input type="text" id="min_completed_timestamp" name="min_completed_timestamp" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-30 days")); ?>">
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
                  <input type="text" id="max_completed_timestamp" name="max_completed_timestamp" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12  text-right pd-0 mt-10">
          <button type="button" class="btn btn-fill btn-facebook fax_search" >Submit</button>
          <button type="button" class="btn btn-fill btn-tumblr fax_reset">Reset</button>
        </div>
      </div>
    </form>
  </fieldset>
</div>
