<div class="modal fade" id="ChangeOrderAssignment" tabindex="-1" role="dialog" aria-labelledby="ChangeOrderAssignmentLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 450px!important;">
		<div class="modal-content">
			<form class="form-horizontal" id="frmChangeOrderAssignment" action="#" method="post" style="margin-bottom: 10px;">
				<input type="hidden" name="OrderAssignmentUID" value="" id="OrderAssignmentUID">
				<div class="modal-header" style="padding-top: 5px!important;">
					<h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;">Confirmation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="padding: 0 24px;">
					<div class="row">
						<div class="col-md-12">

					<h5 class="modal-title" id="ChangeOrderAssignmentLabel">This order was picked by <p class='AssignmentName' style='display: inline-block;color: red;font-weight: 400;'></p></h5>Do you want to assign this order?
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding: 0 12px;">
					<button class="btn btn-success Reopen_workflow_submit btnChangeOrderAssignment" name="submit" type="submit" id="btnChangeOrderAssignment" data-OrderAssignmentUID='' value="btnChangeOrderAssignment">Yes</button>

					<button type="button" class="btn btn-danger btn-dismiss-ChangeOrderAssignment" data-dismiss="modal">No</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<!-- Esclation Modal -->
<div class="modal fade" id="EsclationOrderModal" tabindex="-1" role="dialog" aria-labelledby="EsclationLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 450px!important;">
    <div class="modal-content">
      <form class="form-horizontal" id="formRaiseOrderEsclation" action="#" method="post" style="margin-bottom: 10px;">
        <input type="hidden" name="OrderUID" value="" id="OrderUID">
        <input type="hidden" name="EsclationType" value="" id="EsclationType">
        <input type="hidden" name="HighlightUID" value="" id="HighlightUID">
        <div class="modal-header" style="padding-top: 5px!important;">
          <h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;">Initiate Escalation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="padding: 0 24px;">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group">
                <label class="label-control">Remarks</label>
                <textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="RaisedRemarks"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 0 12px;">
          <button class="btn btn-success Reopen_workflow_submit btnEsclationOrder" name="submit" type="submit" id="btnEsclationOrder" value="">Initiate Escalation</button>

          <button type="button" class="btn btn-danger btn-dismiss" data-dismiss="modal">Cancel</button>
        </div>
      </form> 
    </div>
  </div>
</div>
<!-- Esclation Modal End -->