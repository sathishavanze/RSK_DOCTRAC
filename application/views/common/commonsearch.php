<?php
// Processors
$Processors = $this->Common_Model->get_allonshoreprocessors();
?>
<style>
	.input-group-prepend{
		background: #00bcd4;
		height: 35px;
		font-size: 12px;
		color: #ddd;
		text-align: center;
	}
	.input-group-text{
		font-size: 12px;
		color: #fff;
		text-align: center;
	}
	.ma-0{
		margin: 0px !important;
	}

	/*Multi select*/
	.fs-wrap.multiple .fs-option.selected .fs-checkbox i {
		background-color:#e91e63 !important;
	}
	.fs-option:hover {
		background-color: #e91e63 !important;
		color: #fff !important;
	}

</style>


<div class="pull-right"> 
	<i class="fa fa-filter commonfilter" title="Search" aria-hidden="true" style="font-size:15px;color: #900C3F;position:absolute;right: 55px;top: 13px;cursor: pointer;"></i>&nbsp;&nbsp;
	<!-- <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer;"></i>
		<i class="fa fa-file-excel-o exceptionexceldownload" title="Export Excel" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer; display: none;"></i> -->
	</div>
	<br>
	<div id="commonsearch"  style="display: none;">
		<fieldset class="commonsearchdiv">
			<legend>Search</legend>
			<form id="commonadvancedsearchdata" autocomplete="off">
				<div class="col-md-12 pd-0">
					<div class="row " >

						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_ProductUID" class="bmd-label-floating">Product <span class="mandatory"></span></label>
								<select class="select2picker form-control" id="Commonadv_ProductUID"  name="ProductUID">   
									<option value="All">All</option>  

								</select>
							</div>
						</div>
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_ProjectUID" class="bmd-label-floating">Project <span class="mandatory"></span></label>
								<select class="select2picker form-control" id="Commonadv_ProjectUID"  name="ProjectUID">   
									<option value="All">All</option> 

								</select>
							</div>
						</div>
						<!---ADDing Milstone, State , Loan number-start---------------->
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_MilestoneUID" class="bmd-label-floating">Milestones <span class="mandatory"></span></label>
								<select class="select2picker form-control" id="Commonadv_MilestoneUID"  name="MilestoneUID">   
									<option value="All">All    </option>

								</select>

							</div>
						</div>
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_StateUID" class="bmd-label-floating">State <span class="mandatory"></span></label>
								<select class="select2picker form-control" id="Commonadv_StateUID"  name="StateUID">   
									<option value="All">All</option>                 
								</select>
							</div>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-3 ">
							<div class="form-group bmd-form-group">
								<label for="adv_LoanNo" class="bmd-label-floating">Loan No. </label>
								<input type="text" class="form-control" id="Commonadv_LoanNo" name="CommonLoanNo">
							</div>
						</div>

						<!-- Processors name filter with multi selecet -->
						<div class="col-md-3">
							<div class="form-group bmd-form-group CommonProcessorsCont">
								<!-- <label for="Commonadv_Processors" class="bmd-label-floating"> Processors </label> -->
								<select class="processUser form-control mdb-select" id="Commonadv_Processors"  name="CommonProcessors" multiple="true" placeholder="Select Processor(s)">   
									<?php foreach ($Processors as $key => $value) { ?>
										<option value="<?php echo $value->UserName; ?>" ><?php echo $value->UserName; ?></option>
									<?php } ?>                      
								</select>
							</div>
						</div>

						<!-- ADDing Milstone, State , Loan number- end----------------->
						<div class="col-md-3 datadiv">
							<div class="bmd-form-group row mt-5">
								<div class="col-md-3 pd-0 inputprepand" >
									<p class="mt-5"> From </p>
								</div>
								<div class=" col-md-9 " style="padding-left: 0px;">
									<div class="datediv">
										<input type="text" id="Commonadv_FromDate" name="FromDate" class="form-control datepicker" value="">
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
										<input type="text" id="Commonadv_ToDate" name="ToDate" class="form-control datepicker" value=""/>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div class="col-md-12 pd-0">
						<div class="row " >



						</div>
					</div>
					<div class="col-md-12  text-right pd-0 mt-10">

						<button type="button" class="btn btn-fill btn-facebook commonsearch commonexceptionsearch" value=1>Submit</button>
						<button type="button" class="btn btn-fill btn-tumblr commonreset commonexceptionreset">Reset</button>
					</div>
				</div>
			</form>
		</fieldset>
	</div>

	<!-- <div class="GlobalSelectiveExport">
		<i class="fa fa-download globalexceldownloadNew" title="Overall Queue Excel Export" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer;margin: -23px 15px -28px 2px;float: right !important;"></i>
	</div> -->


