<div id="advancedFilterForReport"  style="display: none;">
	<fieldset class="col-md-12">
		<legend></legend>
		<form id="advancedsearchdata">
			<div class="col-md-12 pd-0">
				<div class="row" >

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="adv_ProductUID" class="bmd-label-floating">Product <span class="mandatory"></span></label>
							<select class="select2picker form-control" id="adv_ProductUID"  name="ProductUID">   
								<option value="All">All</option>                  
							</select>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
							<select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
								<option value="All">All</option>                  
							</select>
						</div>
					</div>

					<!-- <div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="adv_Status" class="bmd-label-floating">Status <span class="mandatory"></span></label>
							<select class="select2picker form-control" id="adv_Status"  name="Status">   
								<option value="Pending" selected>Pending</option>                  
								<option value="Completed">Completed</option>                  
							</select>
						</div>
					</div> -->

					<div class="col-md-3 datadiv">
						<div class="bmd-form-group row">
							<div class="col-md-6 pd-0 inputprepand" >
								<p class="mt-5"> Order Entry From Date</p>
							</div>
							<div class=" col-md-6 " style="padding-left: 0px">
								<div class="datediv">
									<input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="<?php //echo date('Y-m-d',strtotime('-90 days'));?>">
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3 datadiv">
						<div class="bmd-form-group row">
							<div class="col-md-6 pd-0 inputprepand" >
								<p class="mt-5"> Order Entry To Date</p>
							</div>
							<div class=" col-md-6 " style="padding-left: 0px">
								<div class="datediv">
									<input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value="<?php //echo date('Y-m-d');?>"/>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="col-md-12  text-right pd-0 mt-10">
				<button type="button" class="btn btn-fill btn-facebook filterreport" >Submit</button>
				<button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
			</div>

		</form>
	</fieldset>
</div>
