
<div id="advancedFilterForReport"  style="display: none;">
	<fieldset class="col-md-12">
		<legend>Advanced Search</legend>
		<form id="advancedsearchdata">
			<div class="col-md-12 pd-0">
				<div class="row " >

					<!-- <div class="col-md-4 ">
						<div class="form-group bmd-form-group">
							<label for="adv_LoanNo" class="bmd-label-floating">Loan No. </label>
							<input type="text" class="form-control" id="adv_LoanNo" name="LoanNo">
						</div>
					</div> -->

					<?php if ($this->uri->segment(1) == 'CycleTimeReport' && ($this->uri->segment(2) == 'substatus' || $this->uri->segment(2) == 'substatusagent')) { ?> 
						<!-- workflow filter -->
						<div class="col-md-4 ">
							<div class="form-group bmd-form-group">
								<label for="adv_WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
								<select class="select2picker form-control" id="adv_WorkflowModuleUID"  name="WorkflowModuleUID">   
									<?php foreach ($Modules as $key => $value) { ?>
										<option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->SystemName; ?></option>
									<?php } ?>                      
								</select>
							</div>
						</div>
					<?php } ?>


					<?php if ($this->uri->segment(1) == 'CycleTimeReport' && ($this->uri->segment(2) == 'agent' || $this->uri->segment(2) == 'substatusagent')) { ?>
						<!-- user name filter with multi selecet -->
						<div class="col-md-4 ">
							<div class="form-group bmd-form-group">
								<label for="adv_Process" class="bmd-label-floating">Processor </label>
								<select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select Agent(s)">   
									<?php foreach ($ProcessUsers as $key => $value) {
									if($value->UserUID == $this->loggedid){ ?>
										<option selected value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
									<?php } else {?>
										<option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
									<?php } } ?>                      
								</select>
							</div>
						</div> 
					<?php } ?>

					<!-- time peroid -->
					<div class="col-md-4 ">
						<div class="form-group bmd-form-group">
							<label for="adv_period" class="bmd-label-floating">Period</label>
							<select class="select2picker form-control" id="adv_period"  name="period">   
								<option></option>
								<option  value="today">Today</option>
								<option  value="week">This Week</option>                
								<option selected value="month">This Month</option>             
							</select>
						</div>
					</div>

					<!-- order completed status -->
					<!-- <div class="col-md-4 ">
						<div class="form-group bmd-form-group">
							<label for="adv_Status" class="bmd-label-floating">Status</label>
							<select class="select2picker form-control" id="adv_Status"  name="Status">   
								<option value="All">All</option>
								<option value="Pending">Pending</option>
								<option value="Completed">Completed</option>                           
							</select>
						</div>
					</div> -->

					<!-- From Date filter with from date  -->
					<div class="col-md-4">
						<div class="form-group bmd-form-group">
							<label for="adv_fromDate" class="bmd-label-floating">From Date  <span style="color: red">*</span></label>
							<input type="text" id="adv_fromDate" name="fromDate" class="form-control datepicker" value="<?php echo $date['firstday'] ?>">
						</div> 
					</div>

					<!-- To Date filter with from date  -->
					<div class="col-md-4">
						<div class="form-group bmd-form-group">
							<label for="adv_toDate" class="bmd-label-floating">To Date <span style="color: red">*</span></label>
							<input type="text" id="adv_toDate" name="toDate" class="form-control datepicker" value="<?php echo $date['lastday'] ?>">
						</div> 
					</div>


				</div>
			</div>

			<div class="col-md-12  text-right pd-0 mt-10">
				<button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
				<!-- <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button> -->
			</div>

		</form>
	</fieldset>
</div>