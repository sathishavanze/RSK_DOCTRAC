var tblNewOrders;
var DocsCheckedorderstable;
var QueueClearedorderstable;
var PendingDocsorderstable;
var PendiingUWorderstable;
var workingprogresstable;
var myorderstable;
var Expiredorderstable;
var ExpiredCompleteOrdersTable;
var parkingorderstable;
var completedorderstable;
var HardStopSchedule;
var commonsearchclicked;
var tabsearchclicked;
var fetchnotesinterval;
var CDInflowOrdersTable;
var CDPendingOrdersTable;
var CDCompletedOrdersTable;
var SubmittedforDocCheckOrdersTable;
var NonWorkableOrdersTable;
var WorkupReworkOrdersTable;

function schedule_yes(OrderUID){
	var schedule_date = $('#schedule_date_'+OrderUID).val();
	var schedule_time = $('#schedule_time_'+OrderUID).val();
	if(OrderUID) {
		confirm_schedule(OrderUID, schedule_date, schedule_time);
	}
}
function KindoDateTimePicker(row){
	if(HardStopSchedule && row){
		var presentDate = new Date();
		presentDate.setHours( presentDate.getHours() - HardStopSchedule );
		$(".schedule_date", row).kendoDatePicker({
			// display month and year in the input
			format: "MM/dd/yyyy",

			//max: presentDate,
			// specifies that DateInput is used for masking the input element
			dateInput: false,
		}); 
		$(".schedule_time", row).kendoTimePicker({
			min: new Date(2020, 0, 1, 6, 0, 0),
			max: new Date(2020, 0, 1, 22, 0, 0),
			interval: 15,
		});	
		$(".schedule_date", row).bind("focus", function(){
			$(this).data("kendoDatePicker").open();                    
		});
		$(".schedule_time", row).bind("focus", function(){
			$(this).data("kendoTimePicker").open();     
		});
	}	
}
function ProcessorChosenClosingDate_KindoDatePicker(row){
		$(".ProcessorChosenClosingDate", row).kendoDatePicker({
			// display month and year in the input
			format: "MM/dd/yyyy",
			// specifies that DateInput is used for masking the input element
			dateInput: false,
		}); 
		$(".ProcessorChosenClosingDate", row).bind("focus", function(){
			$(this).data("kendoDatePicker").open();                    
		});
}
function updateSchedule(OrderUID, schedule_date, schedule_time){
	$('#confirm_schedule').val('0');
	$.ajax({
		url: 'Scheduling_Orders/checkSchedule',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, schedule_date:schedule_date, schedule_time: schedule_time},
		success: function(res){
			if(res.status == '1'){
				$('.ScheduleWarning').html(res.Warn);
				$("#schedulereconfirm").attr("onclick","schedule_yes("+OrderUID+")");
				$('#ConfirmSchedule').modal('show');
			}else{
				$('#confirm_schedule').val('1');
			}
			console.log($('#confirm_schedule').val());
			if($('#confirm_schedule').val() == '1'){
				confirm_schedule(OrderUID,schedule_date,schedule_time);
			}
		}
	});

}

function confirm_schedule(OrderUID,schedule_date,schedule_time) {
	$.ajax({
		url: 'Scheduling_Orders/updateSchedule',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, schedule_date:schedule_date, schedule_time: schedule_time},
		success: function(res){
			if(res.status == '1'){
				$.notify(
				{
					icon:"icon-bell-check",
					message: res.Warn
				},
				{
					type:'danger',
					delay:1000 
				});

				$('#ConfirmSchedule').modal('hide');
			}else{
				$.notify(
				{
					icon:"icon-bell-check",
					message: res.Warn
				},
				{
					type:'success',
					delay:1000 
				});
				$('#ConfirmSchedule').modal('hide');
			}
			return false;
		}
	});	
}

function initialize(formData)
{

	tblNewOrders = $('#tblNewOrders').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/neworders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'tblNewOrders'},
		},
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		},
		],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}
function docscheckinitialize(formData)
{

	DocsCheckedorderstable = $('#DocsCheckedorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/docschecked_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'DocsCheckedorderstable'},
		},
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			} else if($(row).find('.HighlightFollowupOrders').length > 0) {
				// Highlight followup order row
				$(row).addClass('HighlightFollowupOrders');
				$(row).attr('title','Followup Initiated');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		},
		],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

function queueclearedinitialize(formData)
{

	QueueClearedorderstable = $('#QueueClearedorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/queuechecked_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'QueueClearedorderstable'},
		},
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		},
		],

	});
}

function pendingdocsinitialize(formData)
{

	PendingDocsorderstable = $('#PendingDocsorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/pendingdocs_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'PendingDocsorderstable'},
		},
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		},
		],

	});
}

function pendingfromuwinitialize(formData)
{

	PendiingUWorderstable = $('#PendiingUWorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/pendinguw_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'PendiingUWorderstable'},
		},
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		},
		],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

function KickBackinitialize(formData)
{

	KickBackorderstable = $('#KickBackorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/KickBackorders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'KickBackorderstable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}	

			ProcessorChosenClosingDate_KindoDatePicker(row);		

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

// HOI Rework Orders Ajax
function HOIReworkOrdersInitialize(formData)
{

	HOIReworkOrdersTable = $('#HOIReworkOrdersTable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/HOIReworkOrdersAjaxList",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'HOIReworkOrdersTable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}
// HOI Rework Orders Ajax End

function Expiredinitialize(formData)
{

	Expiredorderstable = $('#Expiredorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/Expiredorders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'Expiredorderstable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}


function FHAinitialize(formData)
{
	FHAtblNewOrders = $('#FHAtblNewOrders').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/FHAneworders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'FHAtblNewOrders'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]

	});
}

function parkingordersinitialize(formData) {

	parkingorderstable = $('#parkingorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/parkingorders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'parkingorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});



}

function workinprogressinitialize(formData) {
	formData.DateFilter = 'Assigned';
	workingprogresstable = $('#workingprogresstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/workinginprogress_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'workingprogresstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});



}
function myordersinitialize(formData){

	formData.DateFilter = 'Assigned';
	myorderstable = $('#myorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/myorders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'myorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}
			
			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});

}

// Completed Orders
function completedordersinitialize(formData){

	formData.DateFilter = 'Completed';
	completedorderstable = $('#completedorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url":"CommonController/completedordersbasedonworkflow_ajax_list?controller="+ModuleController,
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'completedorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});



}

function initializeexceptionqueue(formData, QueueUID, tblId){

	IsFollowup = $(tblId).attr('data-IsFollowup');
	IsDocsReceived = $(tblId).attr('data-IsDocsReceived');
	IsStatus = $(tblId).attr('data-IsStatus');

	console.log("table init");
	exceptionqueuetable = $(tblId).DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedHeader: false,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		},
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',


		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": "ExceptionQueue_Orders/ExceptionQueue_Orders_ajax",
			"type": "POST",
			"data" : {'formData':formData, 'QueueUID':QueueUID} ,
		},

		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		createdRow:function(row,data,index){
			if($(row).find('.selectPrior').length > 0){
				$(row).addClass('selected');
			}

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}

			KindoDateTimePicker(row);
			ProcessorChosenClosingDate_KindoDatePicker(row);
			

		},

		initComplete: function () {
			this.api().on( 'draw', function () {
				clearInterval(fetchnotesinterval);
				fetch_notescounts();
				fetchnotesinterval = setInterval(fetch_notescounts, 10000); // Call AJAX every 10 seconds

			} );
		},
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}


	});
	//Followup counts;
	if(IsFollowup == 1) { fetch_followupcounts(QueueUID); }
	if(IsDocsReceived == 1) { fetch_docreceivedcounts(QueueUID); }
	if(IsStatus == 1) { fetch_statuscounts(QueueUID);  }


}

function ReWorkOrdersInitialize(formData)
{

	ReWorkOrdersTable = $('#ReWorkOrdersTable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/ReWorkOrdersAjaxList",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'ReWorkOrdersTable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]

	});
}

/**
*Function Initialize Re-Work Pending Orders 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Monday 31 August 2020.
*/
function ReWorkPendingOrdersInitialize(formData)
{

	ReWorkPendingOrdersTable = $('#ReWorkPendingOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging: true,
		fixedColumns: {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable: "No Orders Found",
			info: "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty: "Showing 0 to 0 of 0 Orders",
			infoFiltered: "(filtered from _MAX_ total Orders)",
			zeroRecords: "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/ReWorkPendingOrdersAjaxList",
			"type": "POST",
			"data": {'formData':formData, 'SubQueueSection':'ReWorkPendingOrdersTable'},

		},
		createdRow:function(row,data,index){

			// Highlight esclation order row
			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]

	});
}

/**
*Function 3A Confirmation Initialize 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Thursday 03 September 2020.
*/
function ThreeAConfirmationOrdersInitialize(formData)
{
	ThreeAConfirmationOrdersTable = $('#ThreeAConfirmationOrdersTable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 3
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/ThreeAConfirmationOrdersAjaxList",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'ThreeAConfirmationOrdersTable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}



		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ]

	});

	// Initialize milestone widget
	MilestoneWidgetCounts();
}

function exceptionresetadvancedfilter() {
	var date = new Date();
	// var startdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()-90));
	// var currentdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()));

	var startdate = '';
	var currentdate = '';
	$("#adv_ProductUID").val('All');
	$("#adv_ProjectUID").val('All');
	$("#adv_MilestoneUID").val('All');
	$("#adv_StateUID").val('All');
	$("#adv_LoanNo").val('');
	$("#adv_FromDate").val(startdate);
	$("#adv_ToDate").val(currentdate);
	ResetAdvancedSearchProcessors();



	$(".followupfilter").removeClass('active');
	$(".followupduetodayfilter").removeClass('active');
	$(".followupduepastfilter").removeClass('active');
	$(".SubQueues_DocsReceive_Enabled").removeClass('active');
	$(".SubQueues_Status_Enabled").removeClass('active');


	callselect2();

	var filterlist = $(".exceptionqueue-navlink.active").attr("href");
	var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
	var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");


	var formData = ({ 'ProductUID':'','MilestoneUID':'','StateUID':'','LoanNo':'','Processors':'','ProjectUID':'','CustomerUID':'','FromDate':'','ToDate':'', 'QueueUID':QueueUID}); 

	console.log(filterlist);
	if(filterlist && filterlist != "")
	{
		initializeexceptionqueue(formData, QueueUID, tableid);
	}

	// initialize gatekeeping widget counts
	if(ModuleController == "GateKeeping_Orders" || ModuleController == "Submissions_Orders") {
		$('.prescreenpendingfilter,.hoipendingfilter,.titleteampendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter').removeClass('active');
		fetchPendingAndCompletedCounts();
	}

	// Initialize DocsOut Widget Counts
	if(ModuleController == "DocsOut_Orders") {

		$('.SubQueueWidgets').removeClass('active');
		var filterlist = $("#filter-bar .active").attr("href");
		if (filterlist == "#orderslist" || filterlist == "#pendinguworderslist" || filterlist == "#docsorderslist") {
			
		} else {
			$('.SubQueueWidgets').hide();
		}
		
	}
}
function commonexceptionresetadvancedfilter() {
	var date = new Date();
	//var startdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()-90));
	//var currentdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()));
	var startdate = '';
	var currentdate = '';
	$("#Commonadv_ProductUID").val('All');
	$("#Commonadv_ProjectUID").val('All');
	$("#Commonadv_MilestoneUID").val('All');
	$("#Commonadv_StateUID").val('All');
	$("#Commonadv_LoanNo").val('');
	$("#Commonadv_FromDate").val(startdate);
	$("#Commonadv_ToDate").val(currentdate);
	CommonResetAdvancedSearchProcessors();

	$("#adv_ProductUID").val('All');
	$("#adv_ProjectUID").val('All');
	$("#adv_MilestoneUID").val('All');
	$("#adv_StateUID").val('All');
	$("#adv_LoanNo").val('');
	$("#adv_FromDate").val(startdate);
	$("#adv_ToDate").val(currentdate);
	ResetAdvancedSearchProcessors();


	$(".followupfilter").removeClass('active');
	$(".followupduetodayfilter").removeClass('active');
	$(".followupduepastfilter").removeClass('active');
	$(".SubQueues_DocsReceive_Enabled").removeClass('active');
	$(".SubQueues_Status_Enabled").removeClass('active');


	callselect2();

	var filterlist = $(".exceptionqueue-navlink.active").attr("href");
	// var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
	var QueueUID = $(".exceptionqueue-navlink").attr("data-QueueUID");
	var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");


	var formData = ({ 'CommonProductUID':'','CommonMilestoneUID':'','CommonStateUID':'','CommonLoanNo':'','CommonProcessors':'','CommonProjectUID': '' ,'CommonCustomerUID':'','CommonFromDate':'','QueueUID':QueueUID}); 

	console.log(filterlist);
	if(filterlist && filterlist != "")
	{
		initializeexceptionqueue(formData, QueueUID, tableid);
	}


}


function fetch_followupcounts(QueueUID)
{
	$.ajax({
		url: 'CommonController/fetch_followupcounts',
		type: 'POST',
		dataType: 'json',
		data: {'QueueUID': QueueUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		$('.followupcount').text(response.followupcount);	
		$('.followupduetodaycount').text(response.followupduetodaycount);	
		$('.followupduepastcount').text(response.followupduepastcount);	

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

function fetch_docreceivedcounts(QueueUID)
{
	$.ajax({
		url: 'ExceptionQueue_Orders/fetch_docreceivedcounts',
		type: 'POST',
		dataType: 'json',
		data: {'QueueUID': QueueUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		$('.SubQueues_IsDocsReceivedcount').text(response.SubQueues_IsDocsReceivedcount);	

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

function fetch_statuscounts(QueueUID)
{
	$.ajax({
		url: 'ExceptionQueue_Orders/fetch_statuscounts',
		type: 'POST',
		dataType: 'json',
		data: {'QueueUID': QueueUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		$('.SubQueues_IsStatuscount').text(response.SubQueues_IsStatuscount);	

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

$(function() {
	$("select.select2picker").select2({
		//tags: false,
		theme: "bootstrap",
	});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
		localStorage.setItem('ActiveTab', $(e.target).attr('href'));
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
	});

	$(window).resize(function() {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
	});
});


// HOI loan process queues 
function hoiwaitingqueues(formData){


	hoiwaiting = $('#hoiwaitingorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		paging:  true,
		scrollY: '100vh',
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/HOIloan_list",
			"type": "POST",
			"data" : {'formData':formData,'queue_status':'Waiting', 'SubQueueSection':'hoiwaitingorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});

}
function hoiresponsed(formData){

	hoiwaiting = $('#hoiresponsedorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		paging:  true,
		scrollY: '100vh',
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/HOIloan_list",
			"type": "POST",
			"data" : {'formData':formData,'queue_status':'Responsed', 'SubQueueSection':'hoiresponsedorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});

}

function hoireceivedqueues(formData){


	hoiwaiting = $('#hoireceivedorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		paging:  true,
		scrollY: '100vh',
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/HOIloan_list",
			"type": "POST",
			"data" : {'formData':formData,'queue_status':'Received', 'SubQueueSection':'hoireceivedorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});

}

function hoiexceptionqueues(formData){
	hoiwaiting = $('#hoiexceptionorderstable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		searchDelay:1500,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/HOIloan_list",
			"type": "POST",
			"data" : {'formData':formData,'queue_status':'Exceptional', 'SubQueueSection':'hoiexceptionorderstable'} 
		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});

}
// end of loan process queues 

function fetchPendingAndCompletedCounts()
{
	var filterlist = $("#filter-bar .active").attr("href");
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');

	$.ajax({
		url: ModuleController+'/widgetGetPendingOrdersCount',
		type: 'POST',
		dataType: 'json',
		data: {'WorkflowModuleUID':WorkflowModuleUID,'filterlist':filterlist,'QueueUID': QueueUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		console.log(response);
		if (response) {
			$('.PrescreenPendingCount').text(response.PreScreen);	
			$('.HOIPendingCount').text(response.HOI);	
			$('.TitleTeamPendingCount').text(response.TitleTeam);	
			$('.FHAVACaseTeamPendingCount').text(response.FHAVACaseTeam);
			$('.WorkupPendingCount').text(response.Workup);	
			$('.CompletedOrdersCount').text(response.CompletedOrdersCount);	
		}		

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

//advanced search

function advancedsearch_ajax_elements() {
	// Product
	var $dataobject = {'CustomerUID': $('#adv_CustomerUID').val()};

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var Product_Select = data.Products.reduce((accumulator, value) => {
				return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
			}, '<option value="All">All</option>');					
			$('#adv_ProductUID').html(Product_Select);
			$('#adv_ProductUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/******************************MileStone start************************* */

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchMilestone', [], 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var Milestone_Select = data.Milestone.reduce((accumulator, value) => {

				return accumulator + '<option value="' + value.MilestoneUID + '">' + value.MilestoneName + '</option>';
			}, '<option value="All">All</option>');         
			$('#adv_MilestoneUID').html(Milestone_Select);
			$('#adv_MilestoneUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/***************************Milestone End********************* */
	/***************************State start************ */

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchState', [], 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var State_Select = data.States.reduce((accumulator, value) => {

				return accumulator + '<option value="' + value.StateCode + '">' + value.StateCode + '</option>';
			}, '<option value="All">All</option>');         
			$('#adv_StateUID').html(State_Select);
			$('#adv_StateUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/***************************State End****************** */
}	

function commonadvancedsearch_ajax_elements() {
	// Product
	var $dataobject = {'CustomerUID': $('#adv_CustomerUID').val()};

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var Product_Select = data.Products.reduce((accumulator, value) => {
				return accumulator + '<Option value="' + value.ProductUID + '">' + value.ProductName + '</Option>';
			}, '<option value="All">All</option>');					
			$('#Commonadv_ProductUID').html(Product_Select);
			$('#Commonadv_ProductUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/******************************MileStone start************************* */

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchMilestone', [], 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var Milestone_Select = data.Milestone.reduce((accumulator, value) => {

				return accumulator + '<option value="' + value.MilestoneUID + '">' + value.MilestoneName + '</option>';
			}, '<option value="All">All</option>');         
			$('#Commonadv_MilestoneUID').html(Milestone_Select);
			$('#Commonadv_MilestoneUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/***************************Milestone End********************* */
	/***************************State start************ */

	SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchState', [], 'json', true, true, function () {

	}).then(function (data) {
		if (data.validation_error == 0) {
			var State_Select = data.States.reduce((accumulator, value) => {

				return accumulator + '<option value="' + value.StateCode + '">' + value.StateCode + '</option>';
			}, '<option value="All">All</option>');         
			$('#Commonadv_StateUID').html(State_Select);
			$('#Commonadv_StateUID').trigger('change');
		}
		callselect2();

	}).catch(function (error) {

		console.log(error);
	});

	/***************************State End****************** */
}	

//Common Search 
function resetadvancedfilter() {

	var date = new Date();
	// var startdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()-90));
	// var currentdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()));
	var startdate = '';
	var currentdate = '';

	$("#adv_ProductUID").val('All');
	$("#adv_ProjectUID").val('All');
	$("#adv_MilestoneUID").val('All');
	$("#adv_StateUID").val('All');
	$("#adv_LoanNo").val('');
	$("#adv_FromDate").val(startdate);
	$("#adv_ToDate").val(currentdate);
	ResetAdvancedSearchProcessors();

	callselect2();
	//advancedsearch_ajax_elements();

	var filterlist = $("#filter-bar .active").attr("href");

	if(filterlist == '#orderslist')
	{
		initialize('false');
	}
	else if(filterlist == '#Expiredorderslist'){

		Expiredinitialize('false');
	}
	else if(filterlist == '#ExpiredCompleteorderslist'){

		ExpiredCompleteinitialize('false');
	}
	else if(filterlist == '#docsorderslist'){

		docscheckinitialize('false');
	}

	else if(filterlist == '#queueorderslist'){

		queueclearedinitialize('false');
	}
	else if(filterlist == '#pendingdocsoderslist'){

		pendingdocsinitialize('false');
	}
	else if(filterlist == '#pendinguworderslist'){

		pendingfromuwinitialize('false');
	}
	else if(filterlist == '#KickBacklist')
	{
		KickBackinitialize('false');
	}
	else if(filterlist == '#HOIReworkOrderList')
	{
		HOIReworkOrdersInitialize('false');
	}
	else if(filterlist == '#FHAorderslist')
	{
		FHAinitialize('false');
	}
	else if(filterlist == '#workinprogresslist')
	{
		workinprogressinitialize('false');

	}
	else if(filterlist == '#myorderslist')
	{
		myordersinitialize('false');

	}
	else if(filterlist == '#parkingorderslist')
	{
		parkingordersinitialize('false');

	}
	else if(filterlist == '#completedorderslist')
	{
		completedordersinitialize('false');

	}else if(filterlist == '#hoiwaitingorderstablelist')
	{

		hoiwaitingqueues('false');

	}else if(filterlist == '#hoiresponsedorderstablelist')
	{

		hoiresponsed('false');

	}else if(filterlist == '#hoireceivedorderstablelist')
	{

		hoireceivedqueues('false');

	}else if(filterlist == '#hoiexceptionorderstablelist')
	{

		hoiexceptionqueues('false');

	}else if(filterlist == '#ReWorkOrdersList')
	{
		ReWorkOrdersInitialize('false');
	}else if(filterlist == '#ReWorkPendingOrdersList')
	{
		ReWorkPendingOrdersInitialize('false');
	}else if(filterlist == '#ThreeAConfirmationOrdersList')
	{
		ThreeAConfirmationOrdersInitialize('false');
	} else if(filterlist == '#CDInflowOrdersList'){

		CDInflowInitialize('false');
	} else if(filterlist == '#CDPendingOrdersList'){

		CDPendingInitialize('false');
	} else if(filterlist == '#CDCompletedOrdersList'){

		CDCompletedInitialize('false');
	} else if(filterlist == '#SubmittedforDocCheck_OrdersList'){

		SubmittedforDocCheckInitialize('false');
	} else if(filterlist == '#NonWorkable_OrdersList'){

		NonWorkableInitialize('false');
	} else if(filterlist == '#WorkupRework_OrdersList'){

		WorkupReworkInitialize('false');
	}

	// initialize gatekeeping widget counts
	if(ModuleController == "GateKeeping_Orders" || ModuleController == "Submissions_Orders") {
		$('.prescreenpendingfilter,.hoipendingfilter,.titleteampendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter').removeClass('active');
		fetchPendingAndCompletedCounts();
	}

	// Initialize DocsOut Widget Counts
	if(ModuleController == "DocsOut_Orders") {

		$('.SubQueueWidgets').removeClass('active');

		$('.SubQueueWidgets').hide();

		if (filterlist == "#orderslist") {
			$('.DocsOutTATMissedNewOrdersFilter').show();
			GetSubQueueWidgetCounts();
		} else if (filterlist == "#pendinguworderslist") {
			$('.DocsOutTATMissedPendingFromUWFilter').show();
			GetSubQueueWidgetCounts();
		} else if (filterlist == "#docsorderslist") {
			$('.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter').show();
			$('.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter').show();
			GetSubQueueWidgetCounts();
		}
	}

	// Remove milestone widget active class
	$('.MilestoneWidget').removeClass('active');

}


function commonsearchtotal(formData)
{	
	$.ajax({
		type: "POST",
		url: 'CommonController/GetCommonSearchCount',
		"data" : {'formData':formData},
		dataType:"json",
		success: function(data)
		{
			console.log(data);
			$(".newordercount").html(data.counts.NewOrders);
			$(".assignordercount").html(data.counts.AssignedOrders);
			$(".myordercount").html(data.counts.MyOrders);
			$(".parkordercount").html(data.counts.parkingorders);
			$(".hoiwaitingorder").html(data.counts.HOIWaiting);
			$(".hoiresponceorder").html(data.counts.HOIResponseReceived);
			$(".hoireceived").html(data.counts.HOIDocReceived);
			$(".hoiexception").html(data.counts.HOIException);
			$(".kickbackorder").html(data.counts.KickBackOrders);
			$(".Expiredorder").html(data.counts.ExpiredOrders);
			$(".ExpiredCompleteorder").html(data.counts.ExpiredCompleteOrders);
			$(".completeordercount").html(data.counts.CompletedOrders);
			// CD Section
			$(".CDInflowOrdersCount").html(data.counts.CDInflowOrders);
			$(".CDPendingOrdersCount").html(data.counts.CDPendingOrders);
			$(".CDCompletedOrdersCount").html(data.counts.CDCompletedOrders);
			// CD Section End
			$(".DocsCheckedConditionPendingCount").html(data.counts.DocsCheckOrders);
			$(".PendingfromUWCount").html(data.counts.PendinguwOrders);
			$(".SubmittedforDocCheckCount").html(data.counts.SubmittedforDocCheckOrders);
			$(".NonWorkableCount").html(data.counts.NonWorkableOrders);
			$(".WorkupReworkCount").html(data.counts.WorkupReworkOrders);

			$.each(data.subqueues_counts, function(key, val) {
				/* iterate through array or object */
				$(".exceptionqueue-navlink[data-queueuid='"+key+"']").find('span').text(val);

			});

		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});

}

function commonresetadvancedfilter() {
	var date = new Date();
	//var startdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()-90));
	//var currentdate = getFormattedDate(new Date(date.getFullYear(), date.getMonth(), date.getDate()));
	var startdate = '';
	var currentdate = '';

	$("#Commonadv_ProductUID").val('All');
	$("#Commonadv_ProjectUID").val('All');
	$("#Commonadv_MilestoneUID").val('All');
	$("#Commonadv_StateUID").val('All');
	$("#Commonadv_LoanNo").val('');
	$("#Commonadv_FromDate").val(startdate);
	$("#Commonadv_ToDate").val(currentdate);
	CommonResetAdvancedSearchProcessors();


	var formData = ({ 'ModuleController':ModuleController}); 

	commonsearchtotal(formData)

	callselect2();

	var filterlist = $("#filter-bar .active").attr("href");

	if(filterlist == '#orderslist')
	{
		initialize('false');
	}
	else if(filterlist == '#KickBacklist')
	{
		KickBackinitialize('false');
	}
	else if(filterlist == '#HOIReworkOrderList')
	{
		HOIReworkOrdersInitialize('false');
	}
	else if(filterlist == '#workinprogresslist')
	{
		workinprogressinitialize('false');

	}
	else if(filterlist == '#myorderslist')
	{
		myordersinitialize('false');

	}
	else if(filterlist == '#parkingorderslist')
	{
		parkingordersinitialize('false');

	}
	else if(filterlist == '#completedorderslist')
	{
		completedordersinitialize('false');

	}else if(filterlist == '#hoiwaitingorderstablelist')
	{

		hoiwaitingqueues('false');

	}else if(filterlist == '#hoiresponsedorderstablelist')
	{

		hoiresponsed('false');

	}else if(filterlist == '#hoireceivedorderstablelist')
	{

		hoireceivedqueues('false');

	}else if(filterlist == '#hoiexceptionorderstablelist')
	{

		hoiexceptionqueues('false');

	}else if(filterlist == '#ReWorkOrdersList')
	{
		ReWorkOrdersInitialize('false');
	}else if(filterlist == '#ReWorkPendingOrdersList')
	{
		ReWorkPendingOrdersInitialize('false');
	}else if(filterlist == '#ThreeAConfirmationOrdersList')
	{
		ThreeAConfirmationOrdersInitialize('false');
	}
}

function MilestoneWidgetCounts()
{
	var filterlist = $("#filter-bar .active").attr("href");

	// Remove first character from string
	var filterlist = filterlist.substring(1, filterlist.length);

	$.ajax({
		url: 'CommonController/GetMilestoneWidgetCounts',
		type: 'POST',
		dataType: 'json',
		data: {'ModuleController':ModuleController,'WorkflowModuleUID':WorkflowModuleUID,'filterlist':filterlist},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		console.log(response);
		if (response) {
			$.each(response, function(key, val) {
				/* iterate through array or object */
				$("."+key).text(val);

			});	
		}		

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

$(document).ready(function() {




	$(document).on('change','.schedule_date, .schedule_time',function(){
		var OrderUID = $(this).attr('data-orderuid');
		var schedule_date = $('#schedule_date_'+OrderUID).val();
		var schedule_time = $('#schedule_time_'+OrderUID).val();
		if(OrderUID) {
			updateSchedule(OrderUID, schedule_date, schedule_time);	
		}

	});

	$(document).off('click','.search').on('click','.search',function(e)
	{
		e.preventDefault();
		tabsearchclicked = 1;

		var filterlist = $("#filter-bar .active").attr("href");

		var ProductUID = $('#adv_ProductUID option:selected').val();
		var ProjectUID = $('#adv_ProjectUID option:selected').val();
		//added Milestone,State, loan no.
		var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
		var StateUID = $('#adv_StateUID option:selected').val();
		var LoanNo = $('#adv_LoanNo').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		var Processors = $('#adv_Processors').val();

		//common search
		var CommonProductUID = $('#Commonadv_ProductUID option:selected').val();
		var CommonProjectUID = $('#Commonadv_ProjectUID option:selected').val();
		//added Milestone,State, loan no.
		var CommonMilestoneUID = $('#Commonadv_MilestoneUID option:selected').val();
		var CommonStateUID = $('#Commonadv_StateUID option:selected').val();
		//var LoanNo = $('#adv_LoanNo').val();
		var CommonLoanNo = $('#Commonadv_LoanNo').val();
		var CommonCustomerUID = $('#Commonadv_CustomerUID option:selected').val();
		var CommonFromDate = $('#Commonadv_FromDate').val();
		var CommonToDate = $('#Commonadv_ToDate').val();
		var CommonProcessors = $('#Commonadv_Processors').val();


		if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '')  && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='')&& (CommonProjectUID == '') && (CommonMilestoneUID == '') && (CommonStateUID == '') && (CommonLoanNo == '')  && (CommonCustomerUID == '') && (CommonFromDate == '') && (CommonToDate == '') && (CommonProductUID ==''))
		{

			$.notify({icon:"icon-bell-check", message:'Please Choose Search Keywords'}, {type:'danger', delay:1000 });
		} 

		else {

			var filterlist = $("#filter-bar .active").attr("href");

			var formData = ({  'ProductUID':ProductUID,'MilestoneUID':MilestoneUID,'StateUID':StateUID,'LoanNo':LoanNo,'Processors':Processors,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'CommonProductUID':CommonProductUID,'CommonMilestoneUID':CommonMilestoneUID,'CommonStateUID':CommonStateUID,'CommonLoanNo':CommonLoanNo,'CommonProcessors':CommonProcessors,'CommonProjectUID': CommonProjectUID ,'CommonCustomerUID':CommonCustomerUID,'CommonFromDate':CommonFromDate,'ModuleController':ModuleController,'CommonToDate':CommonToDate}); 

			/**
			*Function Getekeeping Widget 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Tuesday 04 August 2020.
			*/
			if ($('.prescreenpendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.prescreenpendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.prescreenpendingfilter').attr('data-workflowmoduleuid');
			}
			if ($('.hoipendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.hoipendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.hoipendingfilter').attr('data-workflowmoduleuid');
			}
			if ($('.titleteampendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.titleteampendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.titleteampendingfilter').attr('data-workflowmoduleuid');
			}
			if ($('.fhavacaseteampendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.fhavacaseteampendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.fhavacaseteampendingfilter').attr('data-workflowmoduleuid');
			}
			if ($('.workuppendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.workuppendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.workuppendingfilter').attr('data-workflowmoduleuid');
			}
			if ($('.completedorderfilter').hasClass('active')) {
				formData['IsWidgetCompletedOrders'] = $('.completedorderfilter').hasClass('active');
				formData['WorkflowModuleUID'] = WorkflowModuleUID;
			}

			/**
			*Scheduling Queue 2G, 3A (Pending Email & Checkbox Checkbox not checked) and total (2G,3A 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Saturday 05 September 2020.
			*/
			if ($('.MilestoneWidgetPendingFilter').hasClass('active')) {
				formData['MilestoneWidgetPendingFilter'] = $('.MilestoneWidgetPendingFilter').hasClass('active');
				formData['WidgetMilestoneUID'] = $('.MilestoneWidgetPendingFilter.active').attr('data-milestoneuid');
				formData['WorkflowModuleUID'] = WorkflowModuleUID;
			}
			if ($('.MilestoneWidgetTotalFilter').hasClass('active')) {
				formData['MilestoneWidgetTotalFilter'] = $('.MilestoneWidgetTotalFilter').hasClass('active');
				formData['WorkflowModuleUID'] = WorkflowModuleUID;
			}

			/**
			*Function TAT missed widgets more than 4 hours of new orders 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Saturday 31 October 2020.
			*/
			if ($('.DocsOutTATMissedNewOrdersFilter').hasClass('active')) {
				formData['IsTATMissedNewOrdersFilter'] = $('.DocsOutTATMissedNewOrdersFilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.DocsOutTATMissedNewOrdersFilter').attr('data-workflowmoduleuid');
			}

			// Pending From UW 4 hours - queue date & time
			if ($('.DocsOutTATMissedPendingFromUWFilter').hasClass('active')) {
				formData['IsTATMissedPendingFromUWFilter'] = $('.DocsOutTATMissedPendingFromUWFilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.DocsOutTATMissedPendingFromUWFilter').attr('data-workflowmoduleuid');
			}

			// Followup
			if ($('.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter').hasClass('active')) {
				formData['IsDocsCheckedConditionPendingFollowupPastDueFilter'] = $('.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter').attr('data-workflowmoduleuid');
			}
			if ($('.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter').hasClass('active')) {
				formData['IsDocsCheckedConditionPendingFollowupYetToBeReviewedFilter'] = $('.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter').attr('data-workflowmoduleuid');
			}

			if(filterlist == '#orderslist')
			{				
				initialize(formData);
			}
			else if(filterlist == '#Expiredorderslist'){

				Expiredinitialize(formData);
			}
			else if(filterlist == '#ExpiredCompleteorderslist'){

				ExpiredCompleteinitialize(formData);
			}
			/*@Author Sathis Kannan <sathish.kannan@avanzegroup.com> @Updated July 17 2020*/

			else if(filterlist == '#docsorderslist'){

				docscheckinitialize(formData);
			}

			else if(filterlist == '#queueorderslist'){

				queueclearedinitialize(formData);
			}
			else if(filterlist == '#pendingdocsoderslist'){

				pendingdocsinitialize(formData);
			}
			else if(filterlist == '#pendinguworderslist'){

				pendingfromuwinitialize(formData);
			}

			else if(filterlist == '#KickBacklist')
			{
				KickBackinitialize(formData);
			}
			else if(filterlist == '#HOIReworkOrderList')
			{
				HOIReworkOrdersInitialize(formData);
			}
			else if(filterlist == '#workinprogresslist')
			{ 

				workinprogressinitialize(formData);
			}


			else if(filterlist == '#myorderslist')
			{
				myordersinitialize(formData);

			}
			else if(filterlist == '#parkingorderslist')
			{
				parkingordersinitialize(formData);

			}
			else if(filterlist == '#completedorderslist')
			{
				completedordersinitialize(formData);

			}else if(filterlist == '#hoiwaitingorderstablelist')
			{
				hoiwaitingqueues(formData);

			}else if(filterlist == '#hoiresponsedorderstablelist')
			{
				hoiresponsed(formData);

			}else if(filterlist == '#hoireceivedorderstablelist')
			{
				hoireceivedqueues(formData);

			}else if(filterlist == '#hoiexceptionorderstablelist')
			{
				hoiexceptionqueues(formData);

			}else if(filterlist == '#FHAorderslist')
			{
				FHAinitialize(formData);

			}else if(filterlist == '#ReWorkOrdersList')
			{
				ReWorkOrdersInitialize(formData);
			}else if(filterlist == '#ReWorkPendingOrdersList')
			{
				ReWorkPendingOrdersInitialize(formData);
			}else if(filterlist == '#ThreeAConfirmationOrdersList')
			{
				ThreeAConfirmationOrdersInitialize(formData);
			} else if(filterlist == '#CDInflowOrdersList'){

				CDInflowInitialize(formData);
			} else if(filterlist == '#CDPendingOrdersList'){

				CDPendingInitialize(formData);
			} else if(filterlist == '#CDCompletedOrdersList'){

				CDCompletedInitialize(formData);
			} else if(filterlist == '#SubmittedforDocCheck_OrdersList'){

				SubmittedforDocCheckInitialize(formData);
			} else if(filterlist == '#NonWorkable_OrdersList'){

				NonWorkableInitialize(formData);
			} else if(filterlist == '#WorkupRework_OrdersList'){

				WorkupReworkInitialize(formData);
			}

		}

		return false;
	});



$(document).off('click','.exceldownload').on('click','.exceldownload',function(){
	/*@Author Jainulabdeen <jainulabdeeen.b@avanzegroup.com> @Updated Mar 4 2020*/
	var filterlist = $("#filter-bar .active").attr("href");


	if(filterlist == '#orderslist')
	{
		var filter = 'orderslist';

	}
	else if(filterlist == '#Expiredorderslist'){

		var filter = 'Expiredorderslist';
	}
	else if(filterlist == '#ExpiredCompleteorderslist'){

		var filter = 'ExpiredCompleteorderslist';
	}
	/*@Author Sathis Kannan <sathish.kannan@avanzegroup.com> @Updated July 17 2020*/
	else if(filterlist == '#docsorderslist'){

		var filter = 'docsorderslist';
	}

	else if(filterlist == '#queueorderslist'){

		var filter = 'queueorderslist';
	}
	else if(filterlist == '#pendingdocsoderslist'){

		var filter = 'pendingdocsoderslist';
	}
	else if(filterlist == '#pendinguworderslist'){

		var filter = 'pendinguworderslist';
	}
	else if(filterlist == '#KickBacklist')
	{
		var filter = 'KickBacklist';
	}
	else if(filterlist == '#HOIReworkOrderList')
	{
		var filter = 'HOIReworkOrderList';
	}
	else if(filterlist == '#FHAorderslist')
	{
		var filter = 'FHAorderslist';
	}
	else if(filterlist == '#workinprogresslist')
	{
		var filter= 'workinprogresslist';
	}
	else if(filterlist == '#myorderslist')
	{
		var filter= 'myorderslist';
	}
	else if(filterlist == '#parkingorderslist')
	{
		var filter= 'parkingorderslist';
	}
	else if(filterlist == '#completedorderslist')
	{
		var filter= 'completedorderslist';
	}
	else if(filterlist == '#hoiwaitingorderstablelist')
	{
		var filter= 'hoiwaitingorderstablelist';
	}
	else if(filterlist == '#hoiresponsedorderstablelist')
	{
		var filter= 'hoiresponsedorderstablelist';
	}
	else if(filterlist == '#hoireceivedorderstablelist')
	{
		var filter= 'hoireceivedorderstablelist';
	}
	else if(filterlist == '#hoiexceptionorderstablelist')
	{
		var filter= 'hoiexceptionorderstablelist';
	}
	else if(filterlist == '#ReWorkOrdersList')
	{
		var filter= 'ReWorkOrdersList';
	}
	else if(filterlist == '#ReWorkPendingOrdersList')
	{
		var filter= 'ReWorkPendingOrdersList';
	}
	else if(filterlist == '#ThreeAConfirmationOrdersList')
	{
		var filter= 'ThreeAConfirmationOrdersList';
	}
	else if(filterlist == '#CDInflowOrdersList'){

		var filter = 'CDInflowOrdersList';
	}
	else if(filterlist == '#CDPendingOrdersList'){

		var filter = 'CDPendingOrdersList';
	}
	else if(filterlist == '#CDCompletedOrdersList'){

		var filter = 'CDCompletedOrdersList';
	}
	else if(filterlist == '#SubmittedforDocCheck_OrdersList'){

		var filter = 'SubmittedforDocCheck_OrdersList';
	}
	else if(filterlist == '#NonWorkable_OrdersList'){

		var filter = 'NonWorkable_OrdersList';
	}
	else if(filterlist == '#WorkupRework_OrdersList'){

		var filter = 'WorkupRework_OrdersList';
	}
	/*End*/

	var ProductUID = $('#adv_ProductUID option:selected').val();
	var ProjectUID = $('#adv_ProjectUID option:selected').val();
	var CustomerUID = $('#adv_CustomerUID option:selected').val();
	var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
	var StateUID = $('#adv_StateUID option:selected').val();
	var LoanNo = $('#adv_LoanNo').val();
	var FromDate = $('#adv_FromDate').val();
	var ToDate = $('#adv_ToDate').val();
	var Processors = $('#adv_Processors').val();
	if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
	{
		var formData = 'All';
	} 
	else 
	{
		var formData = ({ 'ProductUID':ProductUID,'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'Processors':Processors, 'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'Status':filter}); 
	}

	// Check Completed Orders Tab
	var action_url;

	action_url = 'CommonController/WriteGlobalExcelSheet?controller='+ModuleController+'&activesubqueue='+filter;

	$.ajax({
		type: "POST",
		url: action_url,
		xhrFields: {
			responseType: 'blob',
		},
		data: {'formData':formData},
		beforeSend: function(){


		},
		success: function(data)
		{
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			var filename = $.trim($("ul.customtab .nav-link.active").clone().children().remove().end().text())+'.xlsx';
			if (typeof window.chrome !== 'undefined') {
				//Chrome version
				var link = document.createElement('a');
				link.href = window.URL.createObjectURL(data);
				link.download = ModuleController + '_' + filename;
				link.click();
			} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
				//IE version
				var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
				window.navigator.msSaveBlob(blob, filename);
			} else {
				//Firefox version
				var file = new File([data], filename, { type: 'application/octet-stream' });
				window.open(URL.createObjectURL(file));
			}


		},
		error: function (jqXHR, textStatus, errorThrown) {

			console.log(jqXHR);


		},
		failure: function (jqXHR, textStatus, errorThrown) {

			console.log(errorThrown);

		},
	}); 
	$('#followupfilter').removeClass('active');
	$('#followupduetodayfilter').removeClass('active');
});	



//Order Assignment
$(document).off('click','.PreScreenPickNewOrder').on('click','.PreScreenPickNewOrder',function(){

	var OrderUID = $(this).attr('data-orderuid');

	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {

		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

			setTimeout(function(){ 

				// triggerpage('PreScreen/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'PreScreen/index/'+OrderUID, '_blank');

			}, 2000);

		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');

		}


	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});

});

$(document).off('click','.FHAPickNewOrder').on('click','.FHAPickNewOrder',function(){

	var OrderUID = $(this).attr('data-orderuid');

	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {

		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

			setTimeout(function(){ 

				// triggerpage('FHA_VA_CaseTeam/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'FHA_VA_CaseTeam/index/'+OrderUID, '_blank');

			}, 2000);

		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');

		}
		else{

			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

		}

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});

});

$(document).off('click','.HOIPickNewOrder').on('click','.HOIPickNewOrder',function(){

	var OrderUID = $(this).attr('data-orderuid');

	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {

		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

			setTimeout(function(){ 

				// triggerpage('HOI/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'HOI/index/'+OrderUID, '_blank');

			}, 2000);

		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');

		}


	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});

});

$(document).off('click','.TitleTeamPickNewOrder').on('click','.TitleTeamPickNewOrder',function(){

	var OrderUID = $(this).attr('data-orderuid');

	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {

		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

			setTimeout(function(){ 

				// triggerpage('TitleTeam/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'TitleTeam/index/'+OrderUID, '_blank');

			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');

		}

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});

});

$(document).off('click','.WorkupPickNewOrder').on('click','.WorkupPickNewOrder',function(){

	var OrderUID = $(this).attr('data-orderuid');

	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');


	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {

		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});

			setTimeout(function(){ 

				window.location.reload();
				window.open(base_url+'Workup/index/'+OrderUID, '_blank');

			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');

		}

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});

});

$(document).off('blur', '.comments_editable').on('blur', '.comments_editable', function (e) {
	var Comments = $(this).text();
	var OrderUID = $(this).data("orderuid");
	$LastUpdateComments = $(this).closest("td").find("#LastUpdateComments");
	if (Comments != $LastUpdateComments.val()) {
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			global: false,
			url:'Priority_Report/updateprioritycomments',
			data: {'Comments':Comments,'OrderUID':OrderUID},
			success: function(data)
			{
				if (data.error == 0) {
					$LastUpdateComments.val(Comments);
				}
				$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });						
			}
		});
	}
});

//comments update section
$(document).off('blur', '.workflowcomments_editable').on('blur', '.workflowcomments_editable', function (e) {
	var Comments = $(this).text();
	var OrderUID = $(this).data("orderuid");
	var workflowmoduleuid = $(this).data("workflowmoduleuid");
	$LastUpdateComments = $(this).closest("td").find(".LastUpdateWorkflowComments");
	if (Comments != $LastUpdateComments.val()) {
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			global: false,
			url:'Priority_Report/update_workflowcomments',
			data: {'Comments':Comments,'OrderUID':OrderUID,'WorkflowModuleUID':workflowmoduleuid},
			success: function(data)
			{
				if (data && data.error == 0) {
					$LastUpdateComments.val(Comments);
					$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });						
				}
			}
		});
	}
});

// ICD Order Assignment Begin
$(document).off('click','.ICDPickNewOrder').on('click','.ICDPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('ICD/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'ICD/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// ICD Order Assignment End

// Disclosures Order Assignment Begin
$(document).off('click','.DisclosuresPickNewOrder').on('click','.DisclosuresPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Disclosures/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'Disclosures/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Disclosures Order Assignment End

// NTB Order Assignment Begin
$(document).off('click','.NTBPickNewOrder').on('click','.NTBPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('NTB/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'NTB/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// NTB Order Assignment End

// FloodCert Order Assignment Begin
$(document).off('click','.FloodCertPickNewOrder').on('click','.FloodCertPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('FloodCert/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'FloodCert/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// FloodCert Order Assignment End

/*assign reassign orders*/

$(document).on('click','.AssignUsers, .ReAssignUsers', function(){
	var OrderUID = $(this).data('orderuid');
	var WorkflowModuleUID = $(this).data('workflowmoduleuid');
	var WorkflowModuleName = $('#AssignUser').find('#WorkflowModuleName').val();
	$('#AssignUser').find('#OrderUID').val(OrderUID);$('#AssignUser').find('td.tdOrderUID').html(OrderUID);
	$('#AssignUser').find('#WorkflowModuleUID').val(WorkflowModuleUID);
	action_url = 'CommonController/AssignedUsers';
	if($(this).hasClass('ReAssignUsers')){
		$('#AssignUser').find('.modal-title').html('Reassign User');
		// $('#AssignUser').find('.btnAssignUser').html('<i class="icon-rotate-ccw2 pr-10"></i>Reassign');
	}else{
		$('#AssignUser').find('.modal-title').html('Assign User');
		// $('#AssignUser').find('.btnAssignUser').html('<i class="icon-rotate-cw2 pr-10"></i>Assign');
	}

	$.ajax({
		url: action_url,
		type: 'POST',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID, WorkflowModuleName:WorkflowModuleName},
		success: function(data){
			$(document).find('#AssignmentHistoryBody').html(data);
			$('#AssignUser').modal('show');
		}
	});
});

$(document).on('click', '#btnAssignUser', function(e){
	e.preventDefault();
	action_url = 'CommonController/AssignUsers';
	$.ajax({
		url: action_url,
		type: 'POST',
		dataType: 'json',
		data: $('#formAssignUser').serialize(),
		success: function(response){


			if(response.validation_error == '0'){
				$('#AssignUser').modal('hide');
				$.notify({icon:"icon-bell-check",message:response.message},{type:response.color,delay:2000,onClose:redirecturl(window.location.href) });
			} else {
				$.notify({icon:"icon-bell-check",message:response.message},{type:response.color,delay:2000});

			}

		}
	});
	return false;
});
/*assign reassign orders end */

// Appraisal Order Assignment Begin
$(document).off('click','.AppraisalPickNewOrder').on('click','.AppraisalPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Appraisal/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'Appraisal/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Appraisal Order Assignment End

// Escrows Order Assignment Begin
$(document).off('click','.EscrowsPickNewOrder').on('click','.EscrowsPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Escrows/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'Escrows/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Escrows Order Assignment End

// TwelveDayLetter Order Assignment Begin
$(document).off('click','.TwelveDayLetterPickNewOrder').on('click','.TwelveDayLetterPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('TwelveDayLetter/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'TwelveDayLetter/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// TwelveDayLetter Order Assignment End

// MaxLoan Order Assignment Begin
$(document).off('click','.MaxLoanPickNewOrder').on('click','.MaxLoanPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('MaxLoan/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'MaxLoan/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// MaxLoan Order Assignment End

// POO Order Assignment Begin
$(document).off('click','.POOPickNewOrder').on('click','.POOPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('POO/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'POO/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// POO Order Assignment End

// CondoQR Order Assignment Begin
$(document).off('click','.CondoQRPickNewOrder').on('click','.CondoQRPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('CondoQR/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'CondoQR/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// CondoQR Order Assignment End

// FHACaseAssignment Order Assignment Begin
$(document).off('click','.FHACaseAssignmentPickNewOrder').on('click','.FHACaseAssignmentPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('FHACaseAssignment/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'FHACaseAssignment/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// FHACaseAssignment Order Assignment End

// VACaseAssignment Order Assignment Begin
$(document).off('click','.VACaseAssignmentPickNewOrder').on('click','.VACaseAssignmentPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('VACaseAssignment/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'VACaseAssignment/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// VACaseAssignment Order Assignment End

// VVOE Order Assignment Begin
$(document).off('click','.VVOEPickNewOrder').on('click','.VVOEPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('VVOE/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'VVOE/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// VVOE Order Assignment End

// CEMA Order Assignment Begin
$(document).off('click','.CEMAPickNewOrder').on('click','.CEMAPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('CEMA/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'CEMA/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// CEMA Order Assignment End

// SCAP Order Assignment Begin
$(document).off('click','.SCAPPickNewOrder').on('click','.SCAPPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('SCAP/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'SCAP/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// SCAP Order Assignment End

// NLR Order Assignment Begin
$(document).off('click','.NLRPickNewOrder').on('click','.NLRPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('NLR/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'NLR/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// NLR Order Assignment End

// CTCFlipQC Order Assignment Begin
$(document).off('click','.CTCFlipQCPickNewOrder').on('click','.CTCFlipQCPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('CTCFlipQC/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'CTCFlipQC/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// CTCFlipQC Order Assignment End

// PrefundAuditCorrection Order Assignment Begin
$(document).off('click','.PrefundAuditCorrectionPickNewOrder').on('click','.PrefundAuditCorrectionPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('PrefundAuditCorrection/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'PrefundAuditCorrection/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// PrefundAuditCorrection Order Assignment End

// AdhocTasks Order Assignment Begin
$(document).off('click','.AdhocTasksPickNewOrder').on('click','.AdhocTasksPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('AdhocTasks/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'AdhocTasks/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// AdhocTasks Order Assignment End

// UWClear Order Assignment Begin
$(document).off('click','.UWClearPickNewOrder').on('click','.UWClearPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('UWClear/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'UWClear/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// UWClear Order Assignment End

// TitleReview Order Assignment Begin
$(document).off('click','.TitleReviewPickNewOrder').on('click','.TitleReviewPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('TitleReview/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'TitleReview/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// TitleReview Order Assignment End

// WelcomeCall Order Assignment Begin
$(document).off('click','.WelcomeCallPickNewOrder').on('click','.WelcomeCallPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('WelcomeCall/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'WelcomeCall/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// WelcomeCall Order Assignment End

// PayOff Order Assignment Begin
$(document).off('click','.PayOffPickNewOrder').on('click','.PayOffPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('PayOff/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'PayOff/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// PayOff Order Assignment End




// BorrowerDocs Order Assignment Begin
$(document).off('click','.BorrowerDocsPickNewOrder').on('click','.BorrowerDocsPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('BorrowerDocs/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'BorrowerDocs/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// BorrowerDocs Order Assignment End

// GateKeeping Order Assignment Begin
$(document).off('click','.GateKeepingPickNewOrder').on('click','.GateKeepingPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				window.location.reload();
				window.open(base_url+'GateKeeping/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// GateKeeping Order Assignment End


// Submissions Order Assignment Begin
$(document).off('click','.SubmissionsPickNewOrder').on('click','.SubmissionsPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				window.location.reload();
				window.open(base_url+'Submissions/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Submissions Order Assignment End


// CD Order Assignment Begin
$(document).off('click','.CDPickNewOrder').on('click','.CDPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('CD/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'CD/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// CD Order Assignment End

// Scheduling Order Assignment Begin
$(document).off('click','.SchedulingPickNewOrder').on('click','.SchedulingPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Scheduling/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'Scheduling/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Scheduling Order Assignment End

// Scheduling Order Assignment Begin
$(document).off('click','.DocsOutPickNewOrder').on('click','.DocsOutPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Scheduling/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'DocsOut/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// DocsOut Order Assignment End

// SignedDocs Order Assignment Begin
$(document).off('click','.SignedDocsPickNewOrder').on('click','.SignedDocsPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				// triggerpage('Scheduling/index/'+OrderUID);
				window.location.reload();
				window.open(base_url+'SignedDocs/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// SignedDocs Order Assignment End

// FundingConditions Order Assignment Begin
$(document).off('click','.FundingConditionsPickNewOrder').on('click','.FundingConditionsPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				window.location.reload();
				window.open(base_url+'FundingConditions/index/'+OrderUID, '_blank');
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Scheduling Order Assignment End

//Subqueue received checkbox event

$(document).off('change','.QueueIsDocsReceived').on('change','.QueueIsDocsReceived',function(event){
	//event.preventDefault();
	/* Act on the event */
	var ischecked = $(this).is( ':checked' ) ? 1: 0;
	var QueueUID = $(this).attr('data-queueuid');
	var OrderUID = $(this).attr('data-orderuid');

	$.ajax({
		url: 'ExceptionQueue_Orders/update_IsQueueReceived',
		type: 'POST',
		dataType: 'json',
		data: {'OrderUID': OrderUID, 'WorkflowModuleUID': WorkflowModuleUID,'QueueUID': QueueUID,'ischecked':ischecked},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.success == 1)
		{
			$.notify({icon:"icon-bell-check",message:response.message},{type:'success',delay:1000 });
		} else {
			$.notify({icon:"icon-bell-check",message:response.message},{type:'danger',delay:1000 });
		}
		fetch_docreceivedcounts(QueueUID);
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify({icon:"icon-bell-check",message:'Failed'},{type:'danger',delay:1000 });

	})
	.always(function() {
		console.log("complete");
	});	

});

//Subqueue Status dropdown event

$(document).off('change','.QueueIsStatus').on('change','.QueueIsStatus',function(event){
	//event.preventDefault();
	/* Act on the event */
	var QueueIsStatus = $(this).val();
	var QueueUID = $(this).attr('data-queueuid');
	var OrderUID = $(this).attr('data-orderuid');

	$.ajax({
		url: 'ExceptionQueue_Orders/update_IsQueueStatus',
		type: 'POST',
		dataType: 'json',
		data: {'OrderUID': OrderUID, 'WorkflowModuleUID': WorkflowModuleUID,'QueueUID': QueueUID,'QueueIsStatus':QueueIsStatus},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.success == 1)
		{
			$.notify({icon:"icon-bell-check",message:response.message},{type:'success',delay:1000 });
		} else {
			$.notify({icon:"icon-bell-check",message:response.message},{type:'danger',delay:1000 });
		}
		fetch_statuscounts(QueueUID);
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify({icon:"icon-bell-check",message:'Failed'},{type:'danger',delay:1000 });

	})
	.always(function() {
		console.log("complete");
	});	

});

/**
*Function Gatekeeping Filter in Widget 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Tuesday 04 August 2020.
*/
$(document).off('click','.prescreenpendingfilter').on('click','.prescreenpendingfilter',function(){
	$('.hoipendingfilter,.titleteampendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter,.completedorderfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});
$(document).off('click','.hoipendingfilter').on('click','.hoipendingfilter',function(){
	$('.prescreenpendingfilter,.titleteampendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter,.completedorderfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});
$(document).off('click','.titleteampendingfilter').on('click','.titleteampendingfilter',function(){
	$('.prescreenpendingfilter,.hoipendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter,.completedorderfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});
$(document).off('click','.fhavacaseteampendingfilter').on('click','.fhavacaseteampendingfilter',function(){
	$('.prescreenpendingfilter,.hoipendingfilter,.titleteampendingfilter,.workuppendingfilter,.completedorderfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});
$(document).off('click','.workuppendingfilter').on('click','.workuppendingfilter',function(){
	$('.prescreenpendingfilter,.hoipendingfilter,.fhavacaseteampendingfilter,.titleteampendingfilter,.completedorderfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});
$(document).off('click','.completedorderfilter').on('click','.completedorderfilter',function(){
	$('.prescreenpendingfilter,.hoipendingfilter,.titleteampendingfilter,.fhavacaseteampendingfilter,.workuppendingfilter').removeClass('active');
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');
	$(this).addClass('active');
	if (QueueUID) {
		$('.exceptionsearch').trigger('click');
	} else {		
		$('.search').trigger('click');
	}
});



if(ModuleController == "GateKeeping_Orders" || ModuleController == "Submissions_Orders") {
	fetchPendingAndCompletedCounts();
}


$(document).off('click','.morelinktoggle').on('click','.morelinktoggle',function(event) {
	if($(this).hasClass("less")) {
		$(this).removeClass("less");
		$(this).html("...");
	} else {
		$(this).addClass("less");
		$(this).html("less");
	}
	$(this).parent().prev().toggle();
	$(this).prev().toggle();
	$(window).trigger('resize');			
	$($.fn.dataTable.tables(true)).DataTable().columns.adjust().rows().recalcHeight().draw;
	event.preventDefault();
	return false;
});

$('.advancedfilter').click(function(){
	$("#advancedsearch").slideToggle("fast", function(){
		//call function only when opened
		if($("#advancedsearch").is(":visible")){
			advancedsearch_ajax_elements();
		}

	});
});

$('.commonfilter').click(function(){

	$("#commonsearch").slideToggle("fast", function(){
		//call function only when opened
		if($("#commonsearch").is(":visible")){
			commonadvancedsearch_ajax_elements();
		}

	});
});


$(document).off('change', '#adv_ProductUID').on('change', '#adv_ProductUID', function (e) {  
	e.preventDefault();
	var $dataobject = {'ProductUID': $(this).val()};
	if($(this).val() == 'All')
	{
		$('#adv_ProjectUID').html('<option value="All">All</option>');
		$('#adv_PackageUID').html('<option value="All">All</option>');
		//$('#adv_InputDocTypeUID').html('<option value="All">All</option>');
		callselect2();
	}
	else
	{
		SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProjects', $dataobject, 'json', true, true, function () {
			// addcardspinner($('#AuditCard'));
		}).then(function (data) {
			if (data.validation_error == 0) {
				var Project_Select = data.Projects.reduce((accumulator, value) => {
					return accumulator + '<Option value="' + value.ProjectUID + '">' + value.ProjectName + '</Option>';
				}, '<option value="All">All</option>');					
				$('#adv_ProjectUID').html(Project_Select);
				$('#adv_ProjectUID').trigger('change');

			}
			callselect2();

		}).catch(function (error) {

			console.log(error);
		});
	}
});



$(document).off('change', '#Commonadv_ProductUID').on('change', '#Commonadv_ProductUID', function (e) {  
	e.preventDefault();
	var $dataobject = {'ProductUID': $(this).val()};
	if($(this).val() == 'All')
	{
		$('#Commonadv_ProjectUID').html('<option value="All">All</option>');
		$('#Commonadv_PackageUID').html('<option value="All">All</option>');
		//$('#adv_InputDocTypeUID').html('<option value="All">All</option>');
		callselect2();
	}
	else
	{
		SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProjects', $dataobject, 'json', true, true, function () {
			// addcardspinner($('#AuditCard'));
		}).then(function (data) {
			if (data.validation_error == 0) {
				var Project_Select = data.Projects.reduce((accumulator, value) => {
					return accumulator + '<Option value="' + value.ProjectUID + '">' + value.ProjectName + '</Option>';
				}, '<option value="All">All</option>');					
				$('#Commonadv_ProjectUID').html(Project_Select);
				$('#Commonadv_ProjectUID').trigger('change');

			}
			callselect2();

		}).catch(function (error) {

			console.log(error);
		});
	}
});

$(document).off('click','.commonsearch').on('click','.commonsearch',function(e)
{
	e.preventDefault();
	commonsearchclicked =  1;

	var filterlist = $("#filter-bar .active").attr("href");

	var CommonProductUID = $('#Commonadv_ProductUID option:selected').val();
	var CommonProjectUID = $('#Commonadv_ProjectUID option:selected').val();
	//added Milestone,State, loan no.
	var CommonMilestoneUID = $('#Commonadv_MilestoneUID option:selected').val();
	var CommonStateUID = $('#Commonadv_StateUID option:selected').val();
	//var LoanNo = $('#adv_LoanNo').val();
	var CommonLoanNo = $('#Commonadv_LoanNo').val();


	var CommonCustomerUID = $('#Commonadv_CustomerUID option:selected').val();
	var CommonFromDate = $('#Commonadv_FromDate').val();
	var CommonToDate = $('#Commonadv_ToDate').val();
	var CommonProcessors = $('#Commonadv_Processors').val();

	var QueueUID = $(".exceptionqueue-navlink").attr("data-QueueUID");



	if((CommonProjectUID == '') && (CommonMilestoneUID == '') && (CommonStateUID == '') && (CommonLoanNo == '')  && (CommonCustomerUID == '') && (CommonFromDate == '') && (CommonToDate == '') && (CommonProductUID ==''))
	{


		$.notify(
		{
			icon:"icon-bell-check",
			message:'Please Choose Search Keywords'
		},
		{
			type:'danger',
			delay:1000 
		});
	} else {


		var filterlist = $("#filter-bar .active").attr("href");

		var formData = ({ 'CommonProductUID':CommonProductUID,'CommonMilestoneUID':CommonMilestoneUID,'CommonStateUID':CommonStateUID,'CommonLoanNo':CommonLoanNo,'CommonProcessors':CommonProcessors,'CommonProjectUID': CommonProjectUID ,'CommonCustomerUID':CommonCustomerUID,'CommonFromDate':CommonFromDate,'ModuleController':ModuleController,'QueueUID':QueueUID,'CommonToDate':CommonToDate}); 


		if(filterlist == '#orderslist')
		{
			initialize(formData);
		}
		else if(filterlist == '#KickBacklist')
		{
			KickBackinitialize(formData);
		}
		else if(filterlist == '#HOIReworkOrderList')
		{
			HOIReworkOrdersInitialize(formData);
		}
		else if(filterlist == '#workinprogresslist')
		{ 

			workinprogressinitialize(formData);
		}


		else if(filterlist == '#myorderslist')
		{
			myordersinitialize(formData);

		}
		else if(filterlist == '#parkingorderslist')
		{
			parkingordersinitialize(formData);

		}
		else if(filterlist == '#completedorderslist')
		{
			completedordersinitialize(formData);

		}else if(filterlist == '#hoiwaitingorderstablelist')
		{
			hoiwaitingqueues(formData);

		}else if(filterlist == '#hoiresponsedorderstablelist')
		{
			hoiresponsed(formData);

		}else if(filterlist == '#hoireceivedorderstablelist')
		{
			hoireceivedqueues(formData);

		}else if(filterlist == '#hoiexceptionorderstablelist')
		{
			hoiexceptionqueues(formData);

		}else if(filterlist == '#ReWorkOrdersList')
		{
			ReWorkOrdersInitialize(formData);
		}else if(filterlist == '#ReWorkPendingOrdersList')
		{
			ReWorkPendingOrdersInitialize(formData);
		}else if(filterlist == '#ThreeAConfirmationOrdersList')
		{
			ThreeAConfirmationOrdersInitialize(formData);
		}


		//Count of CommonSearch Table

		commonsearchtotal(formData);



	}
	return false;

});



//Tab click function
$(document).off('click','.customtab .nav-link').on('click','.customtab .nav-link',function(){

	$("#advancedsearch").slideUp();

	if ($(this).is('.exceptionqueue-navlink')) {
		//dynamic subqueue tabs

		if(commonsearchclicked == 1) {
			$(".exceptionsearch").trigger('click');
		} else {
			exceptionresetadvancedfilter();
		}

		$('.exceldownload').hide();
		$('.exceptionexceldownload').show();

	} else{
		//Normal tabs

		if(commonsearchclicked == 1) {
			$(".search").trigger('click');
		} else {
			resetadvancedfilter();
		}

		$('.exceldownload').show();
		$('.exceptionexceldownload').hide();	    		
	}
});




//SubQueues

$(document).off('click','.followupfilter').on('click','.followupfilter',function(){
	$('.followupduetodayfilter,.followupduepastfilter,.SubQueues_DocsReceive_Enabled,.SubQueues_Status_Enabled').removeClass('active');
	$(this).addClass('active');
	$('.exceptionsearch').trigger('click');
})

$(document).off('click','.followupduetodayfilter').on('click','.followupduetodayfilter',function(){
	$(this).addClass('active');
	$('.followupduepastfilter,.followupfilter,.SubQueues_DocsReceive_Enabled,.SubQueues_Status_Enabled').removeClass('active');
	$('.exceptionsearch').trigger('click');
})

$(document).off('click','.followupduepastfilter').on('click','.followupduepastfilter',function(){
	$('.followupduetodayfilter,.followupfilter,.SubQueues_DocsReceive_Enabled,.SubQueues_Status_Enabled').removeClass('active');
	$(this).addClass('active');
	$('.exceptionsearch').trigger('click');
})

$(document).off('click','.SubQueues_DocsReceive_Enabled').on('click','.SubQueues_DocsReceive_Enabled',function(){
	$('.followupduetodayfilter,.followupfilter,.followupduepastfilter,.SubQueues_Status_Enabled').removeClass('active');
	$(this).addClass('active');
	$('.exceptionsearch').trigger('click');
})

$(document).off('click','.SubQueues_Status_Enabled').on('click','.SubQueues_Status_Enabled',function(){
	$('.followupduetodayfilter,.followupfilter,.followupduepastfilter,.SubQueues_DocsReceive_Enabled').removeClass('active');
	$(this).addClass('active');
	$('.exceptionsearch').trigger('click');
})



$(document).off('click','.exceptionsearch').on('click','.exceptionsearch',function()
{
	var filterlist = $(".exceptionqueue-navlink.active").attr("href");
	var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
	var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");

	if (filterlist && filterlist != "") {

		var ProductUID = $('#adv_ProductUID option:selected').val();
		var ProjectUID = $('#adv_ProjectUID option:selected').val();
		var CustomerUID = $('#adv_CustomerUID option:selected').val();
		//added Milestone,State, loan no.
		var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
		var StateUID = $('#adv_StateUID option:selected').val();
		var LoanNo = $('#adv_LoanNo').val();
		var FromDate = $('#adv_FromDate').val();
		var ToDate = $('#adv_ToDate').val();
		var Processors = $('#adv_Processors').val();
		var Followup = $('.followupfilter:visible').hasClass('active');
		var Followupduetoday = $('.followupduetodayfilter:visible').hasClass('active');
		var Followupduepast = $('.followupduepastfilter:visible').hasClass('active');
		var SubQueues_DocsReceive_Enabled = $('.SubQueues_DocsReceive_Enabled:visible').hasClass('active');
		var SubQueues_Status_Enabled = $('.SubQueues_Status_Enabled:visible').hasClass('active');

		//common search
		var CommonProductUID = $('#Commonadv_ProductUID option:selected').val();
		var CommonProjectUID = $('#Commonadv_ProjectUID option:selected').val();
		//added Milestone,State, loan no.
		var CommonMilestoneUID = $('#Commonadv_MilestoneUID option:selected').val();
		var CommonStateUID = $('#Commonadv_StateUID option:selected').val();
		var CommonLoanNo = $('#Commonadv_LoanNo').val();
		var CommonCustomerUID = $('#Commonadv_CustomerUID option:selected').val();
		var CommonFromDate = $('#Commonadv_FromDate').val();
		var CommonToDate = $('#Commonadv_ToDate').val();
		var CommonProcessors = $('#Commonadv_Processors').val();




		if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '')  && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID =='')&& (CommonProjectUID == '') && (CommonMilestoneUID == '') && (CommonStateUID == '') && (CommonLoanNo == '')  && (CommonCustomerUID == '') && (CommonFromDate == '') && (CommonToDate == '') && (CommonProductUID ==''))
		{

			$.notify({icon:"icon-bell-check", message:'Please Choose Search Keywords'}, {type:'danger', delay:1000 });

		} else {

			var formData = ({  'ProductUID':ProductUID,'MilestoneUID':MilestoneUID,'StateUID':StateUID,'LoanNo':LoanNo,'Processors':Processors,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'CommonProductUID':CommonProductUID,'CommonMilestoneUID':CommonMilestoneUID,'CommonStateUID':CommonStateUID,'CommonLoanNo':CommonLoanNo,'CommonProcessors':CommonProcessors,'CommonProjectUID': CommonProjectUID ,'CommonCustomerUID':CommonCustomerUID,'CommonFromDate':CommonFromDate,'ModuleController':ModuleController,'CommonToDate':CommonToDate,'QueueUID':QueueUID,'Followup':Followup,'Followupduetoday':Followupduetoday,'Followupduepast':Followupduepast,'SubQueues_DocsReceive_Enabled':SubQueues_DocsReceive_Enabled,'SubQueues_Status_Enabled':SubQueues_Status_Enabled}); 

			/**
			*Function Getekeeping Widget 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Tuesday 04 August 2020.
			*/
			if ($('.prescreenpendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.prescreenpendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.prescreenpendingfilter').attr('data-workflowmoduleuid');
				console.log(formData);
			}
			if ($('.hoipendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.hoipendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.hoipendingfilter').attr('data-workflowmoduleuid');
				console.log(formData);
			}
			if ($('.titleteampendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.titleteampendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.titleteampendingfilter').attr('data-workflowmoduleuid');
				console.log(formData);
			}
			if ($('.fhavacaseteampendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.fhavacaseteampendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.fhavacaseteampendingfilter').attr('data-workflowmoduleuid');
				console.log(formData);
			}
			if ($('.workuppendingfilter').hasClass('active')) {
				formData['IsPendingOrders'] = $('.workuppendingfilter').hasClass('active');
				formData['WorkflowModuleUID'] = $('.workuppendingfilter').attr('data-workflowmoduleuid');
				console.log(formData);
			}
			if ($('.completedorderfilter').hasClass('active')) {
				formData['IsWidgetCompletedOrders'] = $('.completedorderfilter').hasClass('active');
				formData['WorkflowModuleUID'] = WorkflowModuleUID;
				console.log(formData);
			}

			if(filterlist && filterlist != "")
			{
				initializeexceptionqueue(formData, QueueUID, tableid);
			}


		}
	}

	// $('.followupfilter').removeClass('active');
	// $('.followupduetodayfilter').removeClass('active');
	// $('.followupduepastfilter').removeClass('active');
	$('.SubQueues_DocsReceive_Enabled').removeClass('active');
	$('.SubQueues_Status_Enabled').removeClass('active');

	return false;
});

$(document).off('click','.commonexceptionsearch').on('click','.commonexceptionsearch',function()
{
	var filterlist = $(".exceptionqueue-navlink.active").attr("href");
	var QueueUID = $(".exceptionqueue-navlink").attr("data-QueueUID");

	var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");

	if (filterlist && filterlist != "") {

		var CommonProductUID = $('#Commonadv_ProductUID option:selected').val();
		var CommonProjectUID = $('#Commonadv_ProjectUID option:selected').val();
		//added Milestone,State, loan no.
		var CommonMilestoneUID = $('#Commonadv_MilestoneUID option:selected').val();
		var CommonStateUID = $('#Commonadv_StateUID option:selected').val();
		//var LoanNo = $('#adv_LoanNo').val();
		var CommonLoanNo = $('#Commonadv_LoanNo').val();
		var CommonCustomerUID = $('#Commonadv_CustomerUID option:selected').val();
		var CommonFromDate = $('#Commonadv_FromDate').val();
		var CommonToDate = $('#Commonadv_ToDate').val();
		var CommonProcessors = $('#Commonadv_Processors').val();
		//var QueueUID = $(".exceptionqueue-navlink").attr("data-QueueUID");
		var Followup = $('.followupfilter:visible').hasClass('active');
		var Followupduetoday = $('.followupduetodayfilter:visible').hasClass('active');
		var Followupduepast = $('.followupduepastfilter:visible').hasClass('active');
		var SubQueues_DocsReceive_Enabled = $('.SubQueues_DocsReceive_Enabled:visible').hasClass('active');
		var SubQueues_Status_Enabled = $('.SubQueues_Status_Enabled:visible').hasClass('active');
		if((CommonProjectUID == '') && (CommonMilestoneUID == '') && (CommonStateUID == '') && (CommonLoanNo == '')  && (CommonCustomerUID == '') && (CommonFromDate == '') && (CommonToDate == '') && (CommonProductUID ==''))
		{


			$.notify(
			{
				icon:"icon-bell-check",
				message:'Please Choose Search Keywords'
			},
			{
				type:'danger',
				delay:1000 
			});
		} else {


			var formData = ({ 'CommonProductUID':CommonProductUID,'CommonMilestoneUID':CommonMilestoneUID,'CommonStateUID':CommonStateUID,'CommonLoanNo':CommonLoanNo,'CommonProcessors':CommonProcessors,'CommonProjectUID': CommonProjectUID ,'CommonCustomerUID':CommonCustomerUID,'CommonFromDate':CommonFromDate,'ModuleController':ModuleController,'QueueUID':QueueUID,'CommonToDate':CommonToDate}); 

			if(filterlist && filterlist != "")
			{
				initializeexceptionqueue(formData, QueueUID, tableid);
			}


		}
	}

	$('.followupfilter').removeClass('active');
	$('.followupduetodayfilter').removeClass('active');
	$('.followupduepastfilter').removeClass('active');
	$('.SubQueues_DocsReceive_Enabled').removeClass('active');
	$('.SubQueues_Status_Enabled').removeClass('active');

	return false;
});







$(document).off('click','.exceptionexceldownload').on('click','.exceptionexceldownload',function(){

	/*@Author Parthasarathy <parthasarathy.m@avanzegroup.com> @Updated APR 25 2020*/
	var filterlist = $(".exceptionqueue-navlink.active").attr("href");
	var QueueUID = $(".exceptionqueue-navlink.active").attr("data-QueueUID");
	var tableid = $(".exceptionqueue-navlink.active").attr("data-tableid");
	var workflowmodulename = $(".exceptionqueue-navlink.active").attr("data-workflowmodulename");

	/*End*/

	var ProductUID = $('#adv_ProductUID option:selected').val();
	var ProjectUID = $('#adv_ProjectUID option:selected').val();
	var CustomerUID = $('#adv_CustomerUID option:selected').val();
	/*Milestone, state, loanNo*/
	var MilestoneUID = $('#adv_MilestoneUID option:selected').val();
	var StateUID = $('#adv_StateUID option:selected').val();
	var LoanNo = $('#adv_LoanNo').val();
	var FromDate = $('#adv_FromDate').val();
	var ToDate = $('#adv_ToDate').val();
	var Processors = $('#adv_Processors').val();
	var Followup = $('.followupfilter').hasClass('active');
	var Followupduetoday = $('.followupduetodayfilter').hasClass('active');
	var Followupduepast = $('.followupduepastfilter').hasClass('active');
	var SubQueues_DocsReceive_Enabled = $('.SubQueues_DocsReceive_Enabled').hasClass('active');
	var SubQueues_Status_Enabled = $('.SubQueues_Status_Enabled').hasClass('active');
	if((ProjectUID == '') && (MilestoneUID == '') && (StateUID == '') && (LoanNo == '') && (CustomerUID == '') && (FromDate == '') && (ToDate == '') && (ProductUID ==''))
	{
		var formData = 'All';
	} 
	else 
	{
		var formData = ({ 'ProductUID':ProductUID, 'MilestoneUID':MilestoneUID, 'StateUID':StateUID, 'LoanNo':LoanNo,'Processors':Processors,'ProjectUID': ProjectUID,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'QueueUID':QueueUID,'Followup':Followup,'Followupduetoday':Followupduetoday,'Followupduepast':Followupduepast,'SubQueues_DocsReceive_Enabled':SubQueues_DocsReceive_Enabled,'SubQueues_Status_Enabled':SubQueues_Status_Enabled}); 
	}


	$.ajax({
		type: "POST",
		url: 'CommonController/WriteGlobalExcelSheet?controller='+ModuleController+'&QueueUID='+QueueUID,
		xhrFields: {
			responseType: 'blob',
		},
		data: {'formData':formData, 'QueueUID': QueueUID},
		beforeSend: function(){


		},
		success: function(data)
		{
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			var filename = workflowmodulename + " " + $.trim($("ul.customtab .nav-link.active").clone().children().remove().end().text())+'.xlsx';

			if (typeof window.chrome !== 'undefined') {
				//Chrome version
				var link = document.createElement('a');
				link.href = window.URL.createObjectURL(data);
				link.download = filename;
				link.click();
			} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
				//IE version
				var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
				window.navigator.msSaveBlob(blob, filename);
			} else {
				//Firefox version
				var file = new File([data], filename, { type: 'application/octet-stream' });
				window.open(URL.createObjectURL(file));
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {

			console.log(jqXHR);


		},
		failure: function (jqXHR, textStatus, errorThrown) {

			console.log(errorThrown);

		},
	});

});



$(document).off('click','.commonreset').on('click','.commonreset',function(){
	commonresetadvancedfilter();	  
	commonexceptionresetadvancedfilter();   	

});


$(document).off('click','.reset').on('click','.reset',function(){
	resetadvancedfilter();	 
	exceptionresetadvancedfilter();
});

//Update phone
$(document).off('click', '#IsPhoneEnabled').on('click', '#IsPhoneEnabled', function (e) {
	var OrderUID = $(this).data('orderuid');
	var WorkflowModuleUID = $(this).data('workflowmoduleuid');
	var IsPhoneEnabled;
	if($(this).prop("checked") == true){
		IsPhoneEnabled = 1;
	}
	else if($(this).prop("checked") == false){
		IsPhoneEnabled = 0;
	}
	$.ajax({
		type:'POST',
		dataType: 'JSON',
		global: false,
		url: 'Scheduling_Orders/UpdatePhone',
		data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'IsPhoneEnabled':IsPhoneEnabled},
		success: function(data)
		{
			console.log(data);
			$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
		}
	});
});

//Update Email
$(document).off('click', '#IsEmailEnabled').on('click', '#IsEmailEnabled', function (e) {
	var OrderUID = $(this).data('orderuid');
	var WorkflowModuleUID = $(this).data('workflowmoduleuid');
	var IsEmailEnabled;
	if($(this).prop("checked") == true){
		IsEmailEnabled = 1;
	}
	else if($(this).prop("checked") == false){
		IsEmailEnabled = 0;
	}
	$.ajax({
		type:'POST',
		dataType: 'JSON',
		global: false,
		url: 'Scheduling_Orders/UpdateEmail',
		data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'IsEmailEnabled':IsEmailEnabled},
		success: function(data)
		{
			console.log(data);
			$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
		}
	});
	// Update milestone widget
	MilestoneWidgetCounts();
});




//queue report fetch
$(document).off('click', '.viewqueuereport').on('click', '.viewqueuereport', function (e) {
	$('#QueuesModal').modal('hide');
	var element = $(this);
	var element_text = element.html();
	$.ajax({
		url: 'Reports/fetch_queuereports',
		type: 'POST',
		dataType: 'json',
		data: {'WorkflowModuleUID':WorkflowModuleUID},
		beforeSend: function () {
			element.addClass("disabled");
			element.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(res){
			if(res.success == '1'){
				$('#QueuesModal').modal('show');
				$('#QueuesModal .modal-body').html(res.content);
			}
			element.html(element_text);
			element.removeClass("disabled");
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: '300px',
				buttonsStyling: false
			}).catch(swal.noop);
			element.html(element_text);
			element.removeClass("disabled");
		}
	});

	

});

/**
*Scheduling Queue 2G, 3A (Pending Email & Checkbox Checkbox not checked) and total (2G,3A 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Saturday 05 September 2020.
*/
$(document).off('click','.MilestoneWidget').on('click','.MilestoneWidget',function(){

	// Remove active class
	$('.MilestoneWidget').removeClass('active');

	// add active class
	$(this).addClass('active');

	$('.search').trigger('click');
});



/* checklist open as an modal 
* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
* @since Date : 28-08-2020
*/
$(document)
.off("click", ".OrderWorkflowChecklist")
.on("click", ".OrderWorkflowChecklist", function (e) {
	e.preventDefault();
	$("#WorkflowModule").modal("show");
	var OrderUID = $(this).attr("data-orderuid");
	var uri = $(this).attr("data-uri");
	$.ajax({
		type: "post",
		url: base_url + "PreScreen/getOtherModal",
		data: {
			OrderUID: OrderUID,
			uri: uri,
		},
		success: function (data) {
			$(".appendModalData").empty();
			$(".appendModalData").append(data);
			$(".appendModalData").find("select.select2picker").select2();
			checklistdatepicker_init();
			//$('#addchecklistrow').attr('data-moduleUID',$('#workflowUID').val());
		},
	});
});

//Subqueue category dropdown event
$(document).off('change','.SubQueueCategory').on('change','.SubQueueCategory',function(event){
		
	// Resize the window size
	$($.fn.dataTable.tables( true ) ).css('width', '100%');
	$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();

	//event.preventDefault();
	/* Act on the event */
	var CategoryUID = $(this).val();
	var OrderUID = $(this).attr('data-orderuid');
	var SubQueueCategoryUID = $(this).attr('data-subqueuecategoryuid');
	var StaticQueueUID = $("#filter-bar .active").attr("data-staticqueueuid");
	var $this = $(this);

	$.ajax({
		url: 'CommonController/UpdateSubQueueCategory',
		type: 'POST',
		dataType: 'json',
		data: {'OrderUID': OrderUID, 'SubQueueCategoryUID': SubQueueCategoryUID,'CategoryUID':CategoryUID, 'StaticQueueUID':StaticQueueUID, 'WorkflowModuleUID':WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.success == 1)
		{
			$this.closest('.HighlightFollowupOrders').attr('style', 'background-color: white !important');
			$this.closest('.HighlightFollowupOrders').attr('title', '');
			$this.closest('.HighlightFollowupOrders').find('.clearfollowupstaticqueuepopup').remove();
			$($.fn.dataTable.tables( true ) ).css('width', '100%');
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();

			$.notify({icon:"icon-bell-check",message:response.message},{type:'success',delay:1000 });

			GetSubQueueWidgetCounts();
		} else {
			$.notify({icon:"icon-bell-check",message:response.message},{type:'danger',delay:1000 });
		}
	})
	.fail(function(jqXHR) {	
		console.error("error", jqXHR);
		$.notify({icon:"icon-bell-check",message:'Failed'},{type:'danger',delay:1000 });

	})
	.always(function() {
		console.log("complete");
	});	

});


// InitialUnderWriting Order Assignment Begin
$(document).off('click','.InitialUnderWritingPickNewOrder').on('click','.InitialUnderWritingPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				triggerpage('InitialUnderWriting/index/'+OrderUID);
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// InitialUnderWriting Order Assignment End


// ConditionwithApproval Order Assignment Begin
$(document).off('click','.ConditionwithApprovalPickNewOrder').on('click','.ConditionwithApprovalPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				triggerpage('ConditionwithApproval/index/'+OrderUID);
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// ConditionwithApproval Order Assignment End

// Underwriting Order Assignment Begin
$(document).off('click','.UnderwritingPickNewOrder').on('click','.UnderwritingPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				triggerpage('Underwriting/index/'+OrderUID);
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Underwriting Order Assignment End

// Closing Order Assignment Begin
$(document).off('click','.ClosingPickNewOrder').on('click','.ClosingPickNewOrder',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-WorkflowModuleUID');

	$.ajax({
		url: 'CommonController/PickExistingOrderCheck',
		type: 'POST',
		dataType: 'json',
		data: {OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		if(response.validation_error == 2)
		{
			$.notify(
			{
				icon:"icon-bell-check",
				message:response.message
			},
			{
				type:response.color,
				delay:1000 
			});
			setTimeout(function(){ 
				triggerpage('Closing/index/'+OrderUID);
			}, 2000);
		}else if(response.validation_error == 1){
			$('.AssignmentName').html(response.message.UserName);
			$('.btnChangeOrderAssignment').attr('data-OrderAssignmentUID',response.message.OrderAssignmentUID);
			$('#ChangeOrderAssignment').modal('show');
		}
	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
		$.notify(
		{
			icon:"icon-bell-check",
			message:"unable to assign"
		},
		{
			type:"danger",
			delay:1000 
		});
	})
	.always(function() {
		console.log("complete");
	});
});
// Closing Order Assignment End



//New orders initialize function - set Active tab

var ActiveTab = localStorage.getItem('ActiveTab');
if(ActiveTab && $('.customtab .nav-item a[href="' + ActiveTab + '"]').length){

	$('.customtab .nav-item a[href="' + ActiveTab + '"]').trigger('click');
	$('.tab-content.customtabpane .tab-pane' + ActiveTab).addClass("active");

} else {

	$('.customtab .nav-item > a').eq(0).trigger('click');
	$('.tab-content.customtabpane .tab-pane:first').addClass("active");

}


$('.datepicker').datetimepicker({
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
	},
	format: 'MM/DD/YYYY'
});

$('.dateraideparking').datetimepicker({
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
});   
});

// CD Section
function CDInflowInitialize(formData)
{

	$('#CDInflowOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/CDInflowOrdersAjax",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'CDInflowOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

function CDPendingInitialize(formData)
{

	$('#CDPendingOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/CDPendingOrdersAjax",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'CDPendingOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

function CDCompletedInitialize(formData)
{

	$('#CDCompletedOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			// "url": ModuleController+"/CDCompletedOrdersAjax",
			"url":"CommonController/completedordersbasedonworkflow_ajax_list?controller=CD_Orders",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'CDCompletedOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}
// CD Section End

// DocsOut Submitted for Doc Check funtion
function SubmittedforDocCheckInitialize(formData)
{

	$('#SubmittedforDocCheckOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/SubmittedforDocCheck_AjaxList",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'SubmittedforDocCheckOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}
// DocsOut NonWorkable funtion
function NonWorkableInitialize(formData)
{

	$('#NonWorkableOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/NonWorkable_AjaxList",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'NonWorkableOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

// DocsOut WorkupRework funtion
function WorkupReworkInitialize(formData)
{

	$('#WorkupReworkOrdersTable').DataTable( {
		scrollX: true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": ModuleController+"/WorkupReworkorders_ajax_list",
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'WorkupReworkOrdersTable'},
		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}	

			ProcessorChosenClosingDate_KindoDatePicker(row);		

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

function ExpiredCompleteinitialize(formData)
{

	ExpiredCompleteOrdersTable = $('#ExpiredCompleteOrdersTable').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '100vh',
		paging:  true,
		fixedColumns:   {
			leftColumns: 2,
			rightColumns: 1
		}, 
		"bDestroy": true,
		searchDelay:1500,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		language: {
			sLengthMenu: "Show _MENU_ Orders",
			emptyTable:     "No Orders Found",
			info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
			infoEmpty:      "Showing 0 to 0 of 0 Orders",
			infoFiltered:   "(filtered from _MAX_ total Orders)",
			zeroRecords:    "No matching Orders found",
			processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
		},
		// Load data for the table's content from an Ajax source
		"ajax": {
			// "url": ModuleController+"/ExpiredCompleteorders_ajax_list",
			"url":"CommonController/ExpiredCompleteorders_ajax_list?controller="+ModuleController,
			"type": "POST",
			"data" : {'formData':formData, 'SubQueueSection':'ExpiredCompleteOrdersTable'},

		},
		createdRow:function(row,data,index){

			if($(row).find('.HighlightEsclationOrder').length > 0){
				// Highlight esclation order row
				$(row).addClass('HighlightEsclationOrderRow');
				$(row).attr('title','Escalation Initiated');
			} else if($(row).find('.HighlightNBSOrder').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightNBSOrderRow');
				$(row).attr('title','NBS Required');
			} else if($(row).find('.HighlightState').length > 0) {
				// Highlight NBS Required
				$(row).addClass('HighlightStateOrderRow');
				$(row).attr('title',$(row).find('.HighlightState').val()+ ' Loan');
			}			

		},
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		drawCallback: function() {
			$('select.select2table').select2({dropdownAutoWidth : true});;
		}

	});
}

$(document).ready(function() {

	// Multi select
	create_fselect();
	function create_fselect(){
		$(".mdb-select").each(function(){
			var placeholder = $(this).attr('placeholder');
			$(this).fSelect({
				placeholder: placeholder,
				numDisplayed: 2,
				overflowText: '{n} selected', 
				showSearch: true
			}); 
		});   
	}

	function destroy_fselect(){
		$(".mdb-select").fSelect('destroy');
	}
});

function ResetAdvancedSearchProcessors() {

	$('#adv_Processors option:selected').removeAttr('selected');

	$('#adv_Processors').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
		$(this).removeClass('selected', false);
	});

	$('#adv_Processors').closest(".ProcessorsCont").find('.fs-label').html('Select Processor(s)');
}

function CommonResetAdvancedSearchProcessors() {

	$('#Commonadv_Processors option:selected').removeAttr('selected');

	$('#Commonadv_Processors').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
		$(this).removeClass('selected', false);
	});

	$('#Commonadv_Processors').closest(".CommonProcessorsCont").find('.fs-label').html('Select Processor(s)');
}

/**
*Function TAT missed widgets more than 4 hours of new orders. 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Saturday 31 October 2020.
*/
$(document).off('click','.DocsOutTATMissedNewOrdersFilter').on('click','.DocsOutTATMissedNewOrdersFilter',function(){
	$('.SubQueueWidgets').removeClass('active');
	$(this).addClass('active');
	$('.search').trigger('click');
});

// Pending From UW 4 hours - queue date & time
$(document).off('click','.DocsOutTATMissedPendingFromUWFilter').on('click','.DocsOutTATMissedPendingFromUWFilter',function(){
	$('.SubQueueWidgets').removeClass('active');
	$(this).addClass('active');
	$('.search').trigger('click');
});

// Followup
$(document).off('click','.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter').on('click','.DocsOutDocsCheckedConditionPendingFollowupPastDueFilter',function(){
	$('.SubQueueWidgets').removeClass('active');
	$(this).addClass('active');
	$('.search').trigger('click');
});

$(document).off('click','.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter').on('click','.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter',function(){
	$('.SubQueueWidgets').removeClass('active');
	$(this).addClass('active');
	$('.search').trigger('click');
});

function GetSubQueueWidgetCounts()
{
	var filterlist = $("#filter-bar .active").attr("href");
	var QueueUID = $("#filter-bar .active").attr('data-queueuid');

	$.ajax({
		url: ModuleController+'/GetSubQueueWidgetCounts',
		type: 'POST',
		dataType: 'json',
		data: {'WorkflowModuleUID':WorkflowModuleUID,'filterlist':filterlist,'QueueUID': QueueUID},
		beforeSend: function(){

		},
	})
	.done(function(response) {
		console.log(response);
		if (response) {
			$('.DocsOutTATMissedNewOrdersCount').text(response.DocsOutTATMissedNewOrdersCount);
			$('.DocsOutTATMissedPendingFromUWCount').text(response.DocsOutTATMissedPendingFromUWCount);
			$('.DocsOutDocsCheckedConditionPendingFollowupPastDueCount').text(response.DocsOutDocsCheckedConditionPendingFollowupPastDueCount);
			$('.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedCount').text(response.DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedCount);
		}		

	})
	.fail(function(jqXHR) {
		console.error("error", jqXHR);
	});
}

// Update Processor Chosen Closing Date
$(document).on('change','.ProcessorChosenClosingDate',function(){
	var OrderUID = $(this).attr('data-orderuid');
	var ProcessorChosenClosingDate = $('#ProcessorChosenClosingDate_'+OrderUID).val();
	if(OrderUID) {
		updateProcessorChosenClosingDate(OrderUID, ProcessorChosenClosingDate);	
	}

});

function updateProcessorChosenClosingDate(OrderUID, ProcessorChosenClosingDate){
	$.ajax({
		url: 'CommonController/Update_ProcessorChosenClosingDate',
		type: 'POST',
		dataType: 'json',
		data: {'OrderUID': OrderUID, 'ProcessorChosenClosingDate':ProcessorChosenClosingDate, 'WorkflowModuleUID': WorkflowModuleUID},
		success: function(response){
			if(response.status == 1)
			{
				// Refresh Current Data Table
				$('.search').trigger('click');
				
				$.notify({icon:"icon-bell-check",message:response.message},{type:'success',delay:1000 });				
			} else {

				$.notify({icon:"icon-bell-check",message:response.message},{type:'danger',delay:1000 });
			}
		}
	});

}