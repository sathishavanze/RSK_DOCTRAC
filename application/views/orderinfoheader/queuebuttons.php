<?php 

if(empty($OrderAssignDetails)) {

$OrderUID = $OrderDetails->OrderUID;
$WorkflowModuleUID = $WorkflowModuleUID;
$mReasons = $this->Common_Model->get_mreasons();

$Queues = $this->Common_Model->getAvailableQueueButtons($OrderUID, $WorkflowModuleUID);

$PendingQueues = $this->Common_Model->getPendingQueueButtons($OrderUID, $WorkflowModuleUID);

if(empty($tOrder_ParkingQueue)) { 
foreach ($Queues as $key => $queue) { ?>
	<button type="button" style="background-color: #6f6f6f;" class="btn pull-right  btnRaiseExceptionQueue" data-WorkflowModuleName = "<?php echo $queue->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $queue->WorkflowModuleUID; ?>" data-QueueUID = "<?php echo $queue->QueueUID; ?>"  data-QueueName = "<?php echo $queue->QueueName; ?>"><?php echo $queue->QueueName; ?></button>

<?php }

foreach ($PendingQueues as $key => $queue) {  ?>
	<button type="button" class="btn btn-info pull-right btnCompleteExceptionQueue" data-completedisable="<?php echo $workflowcompleted; ?>" data-WorkflowModuleName = "<?php echo $queue->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $queue->WorkflowModuleUID; ?>" data-QueueUID = "<?php echo $queue->QueueUID; ?>"  data-QueueName = "<?php echo $queue->QueueName; ?>"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $queue->QueueName; ?></button>

<?php }
}

}

?>


<script type="text/javascript">
	var OrderUID = "<?php echo $OrderUID; ?>";
	var WorkflowModuleUID = "<?php echo $WorkflowModuleUID; ?>";

	/*@author Parthasarathy @purpose raiseExceptionQueue*/
	/*click event for .btnRaiseExceptionQueue*/
	$(document).off('click', '.btnRaiseExceptionQueue').on('click', '.btnRaiseExceptionQueue', function(e){
		e.preventDefault();
    var modalqueueuid = $(this).attr('data-queueuid');
		$('#raise-queueuid').val(modalqueueuid);
		$('.raiseexceptionqueue').html($(this).attr('data-QueueName'));
		$('#RaiseExceptionQueue').modal('show');

    $('#ExceptionRaiseReason').val('').trigger("change");

    $("#ExceptionRaiseReason option[data-queueuid]").hide();
    $('#ExceptionRaiseReason option[data-queueuid=""]').show();
    $('#ExceptionRaiseReason option[data-queueuid="' + modalqueueuid + '"]').show();
	});

	/*@author Parthasarathy @purpose completeExceptionQueue*/
	/*click event for .btnRaiseExceptionQueue*/
	$(document).off('click', '.btnCompleteExceptionQueue').on('click', '.btnCompleteExceptionQueue', function(e){
		e.preventDefault();
    var modalqueueuid = $(this).attr('data-queueuid');
		$('#clear-queueuid').val(modalqueueuid);
		$('.clearexceptionqueue').html($(this).attr('data-QueueName'));
    $('.clearexceptionworkflow').html($(this).attr('data-WorkflowModuleName'));
    $('#ClearExceptionQueue').modal('show');
    $("#ExcecptionQueueClearReason option[data-queueuid]").hide();
    $('#ExcecptionQueueClearReason option[data-queueuid=""]').show();
    $('#ExcecptionQueueClearReason option[data-queueuid="' + modalqueueuid + '"]').show();
    tocompleteworkflow = $('.tocompleteworkflow').length;
    if(tocompleteworkflow == 0) {
      $('.btnFormClearException[value=1]').hide();
    }

		
	});


	/*@author Parthasarathy @purpose frmRaiseExcepitonQueue*/
	/*submit event for #frmRaiseExcepitonQueue*/
  $(document).off('submit', '#frmRaiseExcepitonQueue').on('submit', '#frmRaiseExcepitonQueue', function (e) {

  	e.preventDefault();
  	e.stopPropagation();

  	var button = $('.btnFrmRaiseExceptionQueue');
  	var button_text = button.html();

  	$(button).prop('disabled', true);
  	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Initiating');

  	var formdata = new FormData($(this)[0]);

    // Update Ordersummary
    var formData_ordersummary = new FormData($("#frmordersummary")[0]);
    AutoUpdate(formData_ordersummary);

  	$.ajax({
  		type: "POST",
  		url: base_url + 'OrderComplete/RaiseExceptionQueue',
  		data: formdata,
  		dataType: 'json',
  		cache: false,
  		processData: false,
  		contentType: false,
  		beforeSend: function () {
  			button.attr("disabled", true);
  			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
  		},

  	})
  	.done(function(response) {
  		console.log("success", response);

  			if (data.validation_error == 0) {
  				/*Sweet Alert MSG*/

  				$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:2000,onClose:redirecturl(window.location.href) });
  				disposepopover();
  				$('#ParkingQueue').modal('hide');
  			} else if(response.validation_error == 2) {
          swal({
            text: "<h5>" + response.message + "</h5>",
            type: "warning",
            confirmButtonText: "Ok",
            confirmButtonClass: "btn btn-success",
              //timer: 5000,
              buttonsStyling: false,
            }).catch(swal.noop);
        } else {
  				$.notify({
  					icon: "icon-bell-check",
  					message: data['message']
  				}, {
  					type: "danger",
  					delay: 1000
  				});
  				button.html(button_text);
  				button.attr("disabled", false);
  			}

  	})
  	.fail(function(jqXHR) {
  		console.error("error", jqXHR);
  		swal({
  			title: "<i class='icon-close2 icondanger'></i>",
  			html: "<p>Failed to Initiate</p>",
  			confirmButtonClass: "btn btn-success",
  			allowOutsideClick: false,
  			width: '300px',
  			buttonsStyling: false
  		}).catch(swal.noop);
  	})
  	.always(function() {
  		console.log("complete");
  		button.html(button_text);
  		button.attr("disabled", false);
  	});

  });


	/*@author Parthasarathy @purpose frmRaiseExcepitonQueue*/
	/*submit event for #frmRaiseExcepitonQueue*/
  $(document).off('submit', '#frmRaiseExcepitonQueue').on('submit', '#frmRaiseExcepitonQueue', function (e) {

  	e.preventDefault();
  	e.stopPropagation();

  	var button = $('.btnFrmRaiseExceptionQueue');
  	var button_text = button.html();

  	$(button).prop('disabled', true);
  	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

  	var formdata = new FormData($(this)[0]);

    // Update Ordersummary
    var formData_ordersummary = new FormData($("#frmordersummary")[0]);
    AutoUpdate(formData_ordersummary);

  	$.ajax({
  		type: "POST",
  		url: base_url + 'OrderComplete/RaiseExceptionQueue',
  		data: formdata,
  		dataType: 'json',
  		cache: false,
  		processData: false,
  		contentType: false,
  		beforeSend: function () {
  			button.attr("disabled", true);
  			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
  		},

  	})
  	.done(function(response) {
  		console.log("success", response);

  			if (response.validation_error == 0) {
  				/*Sweet Alert MSG*/

  				$.notify({icon:"icon-bell-check",message:response['message']},{type:"success",delay:2000,onClose:redirecturl(window.location.href) });
  				disposepopover();
  				$('#RaiseExceptionQueue').modal('hide');
  			} else if(response.validation_error == 2) {
          swal({
            text: "<h5>" + response.message + "</h5>",
            type: "warning",
            confirmButtonText: "Ok",
            confirmButtonClass: "btn btn-success",
              //timer: 5000,
              buttonsStyling: false,
            }).catch(swal.noop);
        } else {
  				$.notify({
  					icon: "icon-bell-check",
  					message: response['message']
  				}, {
  					type: "danger",
  					delay: 1000
  				});
  				button.html(button_text);
  				button.attr("disabled", false);

          $.each(response, function(k, v) {
            console.log(k);
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
            $('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">'+v+'</span>');

          });
  			}

  	})
  	.fail(function(jqXHR) {
  		console.error("error", jqXHR);
  		swal({
  			title: "<i class='icon-close2 icondanger'></i>",
  			html: "<p>Failed to Complete</p>",
  			confirmButtonClass: "btn btn-success",
  			allowOutsideClick: false,
  			width: '300px',
  			buttonsStyling: false
  		}).catch(swal.noop);
  	})
  	.always(function() {
  		console.log("complete");
  		button.html(button_text);
  		button.attr("disabled", false);
  	});

  });



  var clearexceptionvalue = 0;
  var clearexceptionbutton = "";

  /*@author Parthasarathy @purpose btnFormClearException*/
  /*click event for .btnFormClearException*/
  $(document).off('click', '.btnFormClearException').on('click', '.btnFormClearException', function(e){
  	clearexceptionvalue = $(this).val();
  	clearexceptionbutton = $(this);
  });

	/*@author Parthasarathy @purpose frmClearExceptionQueue*/
	/*submit event for #frmClearExceptionQueue*/
  $(document).off('submit', '#frmClearExceptionQueue').on('submit', '#frmClearExceptionQueue', function (e) {

  	e.preventDefault();
  	e.stopPropagation();

  	var button = clearexceptionbutton;
  	var button_text = button.html();

  	$(button).prop('disabled', true);
  	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Completing');

    //NBS alert
    var nbsmodalconfirmation = $('.btnCompleteExceptionAndWorkflow').attr("data-nbsmodalconfirmation");

  	var formdata = new FormData($(this)[0]);
  	formdata.append('CompleteWorkflow', clearexceptionvalue);

    if (nbsmodalconfirmation) {
      formdata.append('nbsmodalconfirmation', nbsmodalconfirmation);
    }    

    // Update Ordersummary
    var formData_ordersummary = new FormData($("#frmordersummary")[0]);
    AutoUpdate(formData_ordersummary);

  	$.ajax({
  		type: "POST",
  		url: base_url + 'OrderComplete/ClearExceptionQueue',
  		data: formdata,
  		dataType: 'json',
  		cache: false,
  		processData: false,
  		contentType: false,
  		beforeSend: function () {
  			button.attr("disabled", true);
  			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
  		},

  	})
  	.done(function(response) {
  		console.log("success", response);

  			if (response.validation_error == 0) {
  				/*Sweet Alert MSG*/

  				$.notify({icon:"icon-bell-check",message:response['message']},{type:"success",delay:2000,onClose:redirecturl(window.location.href) });
  				disposepopover();
  				$('#ClearExceptionQueue').modal('hide');
  			} else if(response.validation_error == 2) {
          swal({
            text: "<h5>" + response.message + "</h5>",
            type: "warning",
            confirmButtonText: "Ok",
            confirmButtonClass: "btn btn-success",
              //timer: 5000,
              buttonsStyling: false,
            }).catch(swal.noop);
        } else if(response.NBSRequiredConfirmation == 1) {
          swal({
            text: response.message,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-info",
            buttonsStyling: false,
          }).then(
          function () {
            $('.btnCompleteExceptionAndWorkflow').attr("data-nbsmodalconfirmation", "1");
            $(".btnCompleteExceptionAndWorkflow").trigger("click");
          },
          function (dismiss) {
            $('#ClearExceptionQueue').modal('hide');
          }
          );
        } else {
          $.notify({
           icon: "icon-bell-check",
           message: response['message']
         }, {
           type: "danger",
           delay: 1000
         });
          button.html(button_text);
          button.attr("disabled", false);

          $.each(response, function(k, v) {
            console.log(k);
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
            $('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">'+v+'</span>');

          });
          
        }

  	})
  	.fail(function(jqXHR) {
  		console.error("error", jqXHR);
  		swal({
  			title: "<i class='icon-close2 icondanger'></i>",
  			html: "<p>Failed to Complete</p>",
  			confirmButtonClass: "btn btn-success",
  			allowOutsideClick: false,
  			width: '300px',
  			buttonsStyling: false
  		}).catch(swal.noop);
  	})
  	.always(function() {
  		console.log("complete");
  		button.html(button_text);
  		button.attr("disabled", false);
  	});

  });

  /*Update Exception Reason*/
  $(document).off('click', '.btnFormExceptionReasonUpdate').on('click', '.btnFormExceptionReasonUpdate', function (e) {

    e.preventDefault();
    e.stopPropagation();

    var button = $('.btnFormExceptionReasonUpdate');
    var button_text = button.html();

    $(button).prop('disabled', true);
    $(button).html('<i class="fa fa-spin fa-spinner"></i> ...Updating');

    var formdata = new FormData($('#frmClearExceptionQueue')[0]);

    $.ajax({
      type: "POST",
      url: base_url + 'OrderComplete/UpdateExceptionReason',
      data: formdata,
      dataType: 'json',
      cache: false,
      processData: false,
      contentType: false,
      beforeSend: function () {
        button.attr("disabled", true);
        button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
      },

    })
    .done(function(response) {
      console.log("success", response);

        if (response.validation_error == 0) {
          /*Sweet Alert MSG*/

          $.notify({icon:"icon-bell-check",message:response['message']},{type:"success",delay:2000});
          disposepopover();
          $('#ClearExceptionQueue').modal('hide');
        } else if(response.validation_error == 2) {
          swal({
            text: "<h5>" + response.message + "</h5>",
            type: "warning",
            confirmButtonText: "Ok",
            confirmButtonClass: "btn btn-success",
              //timer: 5000,
              buttonsStyling: false,
            }).catch(swal.noop);
        } else {
          $.notify({
            icon: "icon-bell-check",
            message: response['message']
          }, {
            type: "danger",
            delay: 1000
          });
          button.html(button_text);
          button.attr("disabled", false);

          $.each(response, function(k, v) {
            console.log(k);
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
            $('#'+k).parent().append('<span class="loginidunique" style="color:#e53935; display: none; font-size: 11px;">'+v+'</span>');

          });
        }

    })
    .fail(function(jqXHR) {
      console.error("error", jqXHR);
      swal({
        title: "<i class='icon-close2 icondanger'></i>",
        html: "<p>Failed to Complete</p>",
        confirmButtonClass: "btn btn-success",
        allowOutsideClick: false,
        width: '300px',
        buttonsStyling: false
      }).catch(swal.noop);
    })
    .always(function() {
      console.log("complete");
      button.html(button_text);
      button.attr("disabled", false);
    });

  });
</script>