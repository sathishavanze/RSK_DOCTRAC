<!--ENABLE  WORKUP POPUP CONTENT -->                
<div id="WorkupQueue" tabindex="-1" role="dialog" aria-hidden="true"  class="modal fade">
	<div class="modal-dialog" > 
		<div class="modal-content" style="width: 450px;left: 30%;">
			<div class="modal-header" style="padding: 10px !important;">
				<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"> <i class="icon-x"></i></button>
			</div>
			<div class="modal-body">
				<div class="text-center">
					<div class="text-success mt-5" id="iconchg"><i class="fa fa-info-circle fa-2x" aria-hidden="true"></i></div>
					<div class="mt-5" >Do you want to enable the Work-up?</div>
					<div class="col-md-12">
						<div class="form-group bmd-form-group is-filled">
							<label class="label-control">Closing date</label>
							<input type="text" class="form-control datepicker" value="" id="ProcessorChosenClosingDate" name="ProcessorChosenClosingDate" required="">
							<span class="material-input"></span>
							<span class="material-input"></span>
						</div>
					</div>
					<div class="form-check-inline">
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input STCRadio" type="radio" name="STC" value="OneMonthPayment" checked=""> One Month Payment
								<span class="circle">
									<span class="check"></span>
								</span>
							</label>
						</div>
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input STCRadio" type="radio" name="STC" value="ZeroSTC"> Zero STC
								<span class="circle">
									<span class="check"></span>
								</span>
							</label>
						</div>
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input STCRadio" type="radio" name="STC" value="Amount"> Amount 
								<span class="circle">
									<span class="check"></span>
								</span>
							</label>
						</div>
					</div>
					<div class="col-md-12 stcamountdiv" style="display: none;">
						<div class="form-group bmd-form-group is-filled">
							<label class="label-control"></label>
							<input type="text" class="form-control" value="" id="STCAmount" name="Amount" data-type="currency" required="">
							<span class="material-input"></span>
						</div>
					</div>
					<div class="mt-20">
						<button type="button" class="btn btn-success btn-space forceenable_workflow" data-workflowmoduleuid="">Proceed</button>
						<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cancel</button>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>