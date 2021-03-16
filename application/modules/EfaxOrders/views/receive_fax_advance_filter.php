
<div class="SearchBlock" id="receive_faxadvancedsearch"  style="display: none;">
  <fieldset class="receive_faxadvancedsearchdiv">
    <legend>Advanced Search</legend>
    <form id="live_faxadvancedsearchdata" autocomplete="off">
      <div class="col-md-12 pd-0">
        <div class="row">
          
          <div class="col-md-3 ">
            <div class="form-group bmd-form-group">
              <label for="image_downloaded" class="bmd-label-floating">Image Download</label>
              <select class="select2picker form-control" id="receive_image_downloaded"  name="image_downloaded">   
                <option value="true" selected="">TRUE</option>
                <option value="false">FALSE</option>
              </select>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="originating_fax_number" class="bmd-label-floating">Originating Fax Number </label>
              <input type="text" class="form-control" id="receive_originating_fax_number" name="originating_fax_number">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="destination_fax_number" class="bmd-label-floating">Destination Fax Number </label>
              <input type="text" class="form-control" id="receive_destination_fax_number" name="destination_fax_number">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="min_pages" class="bmd-label-floating">MIN Pages</label>
              <input type="text" class="form-control" id="receive_min_pages" name="min_pages">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="max_pages" class="bmd-label-floating">MAX Pages</label>
              <input type="text" class="form-control" id="receive_max_pages" name="max_pages">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="search_text" class="bmd-label-floating">Search Text </label>
              <input type="text" class="form-control" id="receive_search_text" name="search_text">
            </div>
          </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row mt-5">
              <div class="col-md-3 pd-0 inputprepand" >
                <p class="mt-5"> From </p>
              </div>
              <div class=" col-md-9 " style="padding-left: 0px;">
                <div class="datediv">
                  <input type="text" id="receive_min_completed_timestamp" name="min_completed_timestamp" class="form-control datepicker" value="<?php echo date('m/d/Y',strtotime("-30 days")); ?>">
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
                  <input type="text" id="receive_max_completed_timestamp" name="max_completed_timestamp" class="form-control datepicker" value="<?php echo (date("m/d/Y")); ?>"/>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12  text-right pd-0 mt-10">
          <button type="button" class="btn btn-fill btn-facebook fax_receive_search" >Submit</button>
          <button type="button" class="btn btn-fill btn-tumblr fax_receive_reset">Reset</button>
        </div>
      </div>
    </form>
  </fieldset>
</div>
