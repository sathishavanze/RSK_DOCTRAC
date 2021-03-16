<style type="text/css">.pd-btm-0 {
	padding-bottom: 0px;
}

.margin-minus8 {
	margin: -8px;
}

.mt--15 {
	margin-top: -15px;
}

.bulk-notes {
	list-style-type: none
}

.bulk-notes li:before {
	content: "*  ";
	color: red;
	font-size: 15px;
}

.nowrap {
	white-space: nowrap
}

.table-format>thead>tr>th {
	font-size: 12px;
}

.card .form-check {
    margin-top: 20px !important;
}

</style>

<div class="card mt-40" id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Sub Queue
      </div>
   </div>
   <div class="card-body">
      <form action="#"  name="SubQueueForm" id="SubQueueForm">
         <input type="hidden" name="QueueUID" value="<?php echo isset($SubQueuesDetails['QueueUID']) ? $SubQueuesDetails['QueueUID'] : ''; ?>">
         <div class="row">
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <label for="QueueName" class="bmd-label-floating">Sub Queue Name <span class="mandatory"></span></label>
                  <input type="text" class="form-control" id="QueueName" name="QueueName" value="<?php echo isset($SubQueuesDetails['QueueName']) ? $SubQueuesDetails['QueueName'] : ''; ?>"/>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <label for="WorkflowModuleUID " class="bmd-label-floating">Workflow<span class="mandatory"></span></label>
                  <select class="select2picker form-control"  id="WorkflowModuleUID" name="WorkflowModuleUID">
                     <option value=""></option>
                     <?php foreach ($WorkflowDetails as $key => $value) { ?>
                        <option value="<?php echo $value->WorkflowModuleUID ; ?>" <?php if(isset($SubQueuesDetails['WorkflowModuleUID']) && $value->WorkflowModuleUID == $SubQueuesDetails['WorkflowModuleUID']) { echo "selected"; } ?>><?php echo $value->WorkflowModuleName; ?></option>
                     <?php } ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <select class="select2picker form-control"  id="FollowupType" name="FollowupType">
                     <option value="">Select FollowUp Type</option>
                     <option value="Auto" <?php if(isset($SubQueuesDetails['FollowupType']) && 'Auto' == $SubQueuesDetails['FollowupType']) { echo "selected"; } ?>>Auto</option>
                     <option value="Manual" <?php if(isset($SubQueuesDetails['FollowupType']) && 'Manual' == $SubQueuesDetails['FollowupType']) { echo "selected"; } ?>>Manual</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-2">
               <div class="form-group bmd-form-group">
                  <label for="FollowupDuration" class="bmd-label-floating">FollowUp Duration</label>
                  <input type="text" class="form-control" id="FollowupDuration" name="FollowupDuration" value="<?php echo isset($SubQueuesDetails['FollowupDuration']) ? $SubQueuesDetails['FollowupDuration'] : ''; ?>" />
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="IsBorrowerDocs" type="checkbox" name="IsBorrowerDocs" <?php echo isset($SubQueuesDetails['IsBorrowerDocs']) && $SubQueuesDetails['IsBorrowerDocs'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>Borrower Docs</h6>
                  </label>
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="IsFollowup" type="checkbox" name="IsFollowup" <?php echo isset($SubQueuesDetails['IsFollowup']) && $SubQueuesDetails['IsFollowup'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>FollowUp</h6>
                  </label>
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="SkipWeekend" type="checkbox" name="SkipWeekend" <?php echo isset($SubQueuesDetails['SkipWeekend']) && $SubQueuesDetails['SkipWeekend'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>Exclude WeekEnd</h6>
                  </label>
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="IsBusinessHours" type="checkbox" name="IsBusinessHours" <?php echo isset($SubQueuesDetails['IsBusinessHours']) && $SubQueuesDetails['IsBusinessHours'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>Business Hours</h6>
                  </label>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-2">
               <div class="form-group bmd-form-group">
                  <label for="BusinessHourStartTime" class="bmd-label-floating">Business Hour Start Time</label>
                  <input type="text" id="BusinessHourStartTime" name="BusinessHourStartTime" class="form-control timepicker" <?php if (isset($SubQueuesDetails['BusinessHourStartTime']) && !empty($SubQueuesDetails['BusinessHourStartTime'])) {
                     if ($SubQueuesDetails['BusinessHourStartTime'] != "00:00:00") {
                        echo 'value="'.$SubQueuesDetails['BusinessHourStartTime'].'"';
                     }
                  } ?>>
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-group bmd-form-group">
                  <label for="BusinessHourEndTime" class="bmd-label-floating">Business Hour End Time</label>
                  <input type="text" id="BusinessHourEndTime" name="BusinessHourEndTime" class="form-control timepicker" <?php if (isset($SubQueuesDetails['BusinessHourEndTime']) && !empty($SubQueuesDetails['BusinessHourEndTime'])) {
                     if ($SubQueuesDetails['BusinessHourEndTime'] != "00:00:00") {
                        echo 'value="'.$SubQueuesDetails['BusinessHourEndTime'].'"';
                     }
                  } ?>>
               </div>
            </div>
             <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="IsDocsReceived" type="checkbox" name="IsDocsReceived" <?php echo isset($SubQueuesDetails['IsDocsReceived']) && $SubQueuesDetails['IsDocsReceived'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>Docs Received</h6>
                  </label>
               </div>
            </div>

            <div class="col-md-2">
               <div class="form-check">
                  <label class="form-check-label " style="color: teal">
                     <input class="form-check-input" id="IsStatus" type="checkbox" name="IsStatus" <?php echo isset($SubQueuesDetails['IsStatus']) && $SubQueuesDetails['IsStatus'] == 1 ? 'checked' : ''; ?>>
                     <span class="form-check-sign">
                     <span class="check"></span>
                     </span>
                     <h6>Status (Approved)</h6>
                  </label>
               </div>
            </div>
            <?php if (isset($SubQueuesDetails['Active'])) { ?>
               <div class="col-md-3">
                  <div class="form-group togglebutton">
                    <label class="label-color">Status
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($SubQueuesDetails['Active'] == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                     </label>
                  </div>
               </div>
            <?php } ?>
         </div>
         <div class="ml-auto text-right">
            <a href="<?php echo base_url('SubQueues'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
            <button type="submit" class="btn btn-fill btn-save btn-wd SubQueueSubmit" name="SubQueueSubmit"><i class="icon-floppy-disk pr-10"></i><?php echo isset($SubQueuesDetails['QueueUID']) ? 'Update Sub Queue' : 'Save Sub Queue'; ?></button>
         </div>
      </form>
   </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type = "text/javascript">

	$(document).ready(function () {

      $('.timepicker').datetimepicker({
         format: 'hh:mm A', 
         icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down",
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-screenshot',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
         }
      }).on('dp.change', function (e) {  
         $('.meetingfield:first').trigger('change');
      });

      $(document).off('blur','#BusinessHourStartTime').on('blur','#BusinessHourStartTime', function(e) {
         if ( this.value ) {
            $(this).parent('.form-group').addClass('is-filled');
         }
      });

      $(document).off('blur','#BusinessHourEndTime').on('blur','#BusinessHourEndTime', function(e) {
         if ( this.value ) {
            $(this).parent('.form-group').addClass('is-filled');
         }
      });

		$(document).off('click', '.SubQueueSubmit').on('click', '.SubQueueSubmit', function (e) {
			var formdata = $('#SubQueueForm').serialize();
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('SubQueues/SaveSubQueue'); ?>",
				data: formdata,
				dataType: 'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
				},
				success: function (response) {
					if (response.Status == 0) {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "success",
							delay: 1000
						});
						setTimeout(function () {

							triggerpage('<?php echo base_url();?>SubQueues');

						}, 3000);
                  button.html('Redirecting to Sub Queues List ...');
					} else {
						$.notify({
							icon: "icon-bell-check",
							message: response.message
						}, {
							type: "danger",
							delay: 1000
						});
						$.each(response, function (k, v) {
							console.log(k);
							$('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');

						});
                  button.html(button_text);
                  button.val(button_val);
                  button.prop('disabled', false);

					}

				}
			});

		});

	}); 
</script>