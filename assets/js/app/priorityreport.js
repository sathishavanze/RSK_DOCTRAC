	var orderslist = false;
	var prioritydatatable = false;
	var fetchnotesinterval = 0;

	$(function() {
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
		
	});


	function fetch_counts(data) {

		return new Promise(function (resolve, reject) {  
			SendAsyncAjaxRequest('POST', data.url, data, 'json', true, true, function () {
				addcardspinner($('#prioritycount-view'));
				$("#prioritycount-view").fadeIn('fast');
				$("#orderstablediv").fadeOut('fast');
			}).then(function (response) {

				if (response.success == 1) {
					//$('#priority-data tbody tr').hide();
					$.each(response.data, function(key, value) {
						/* iterate through array or object */
						//$("[data-count='"+key+"']:hidden").closest('tr').show();
						$("[data-count='"+key+"']").attr('data-orderid', value);

						var charCount = (value == "" || value === null || value === "undefined") ? null : value.split(/[\.,\?]+/).length;

						$("[data-count='"+key+"']").text(charCount);
					});



				}

				prioritydatatable = fn_datatable(data.ReportName);
				resolve('success');
				removecardspinner($('#prioritycount-view'));

			}).catch(function (error) {

				console.log(error);
				removecardspinner($('#prioritycount-view'));
			});
		})
	}

	function fn_datatable(ReportName)
	{
		prioritydatatable = $('#priority-data').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY: '100vh',
			paging:  false,
			searchDelay:1500,
			//"bInfo" : false,
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": false, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			fixedColumns: {
				leftColumns: 1,
				rightColumns: 1
			},
			dom: 'Bfrtip',
			"buttons": [
			{
				extend: 'csv',
				title: ReportName,
			},
			{
				extend: 'excelHtml5',
				title: ReportName,
				footer: true,
			},
			],

			language: {
				processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
		});

		return prioritydatatable;
	}


	function fetchorders(OrderUID,WorkflowModuleUID,Orderlistname,Priority)
	{
		$("#prioritycount-view").fadeOut('fast');
		$("#orderstablediv").fadeIn('fast');
		$('#orderlisttitle').text(Orderlistname);
		$('#orderlist_orderuids').val(OrderUID);
		$('#orderlist_workflowmoduleuid').val(WorkflowModuleUID);
		orderslist = $('#orderslist').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			scrollY: '100vh',
			paging:  true,
			"bDestroy": true,
			searchDelay:1500,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			fixedColumns: {
				leftColumns: 2,
				rightColumns: 1
			},

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
				"url": base_url+"Priority_Report/fetchorders",
				"type": "POST",
				"data" : {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'Priority':Priority}  
			},
			createdRow:function(row,data,index){

				// Highlight esclation order row
				if($(row).find('.HighlightEsclationOrder').length > 0){
					$(row).addClass('HighlightEsclationOrderRow');
				}

			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
			initComplete: function () {
				this.api().on( 'draw', function () {
					clearInterval(fetchnotesinterval);
					fetch_notescounts();
					fetchnotesinterval = setInterval(fetch_notescounts, 10000); // Call AJAX every 10 seconds

				} );
			},
			"fnDrawCallback": function (oSettings) {
				tabledatepicker();
			},
		});
	}

	function tabledatepicker()
	{


		$('.tabledatepicker').datetimepicker({
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
		}).on('dp.hide', function (e) { console.log(e.date);

			var $elem = $(this);
			$closesttr = $elem.closest('tr');
			var OrderUID = $closesttr.find(".OrderUID").data("orderuid");
			var value = $elem.val();
			var ColumnName = $elem.attr('data-ColumnName');
			$LastUpdated = $elem.attr("data-LastUpdated");
			if (value != $LastUpdated) {
				if($closesttr.find('.DocumentExpiryDate').length)
				{
					var ExpirationDuration = parseInt($closesttr.find('.DocumentExpiryDate').attr('data-ExpirationDuration'));
					var newdate = new Date(value);
					newdate.setDate(newdate.getDate() + ExpirationDuration);
					var dd = newdate.getDate(); 
					var mm = newdate.getMonth() + 1; 

					var yyyy = newdate.getFullYear(); 
					if (dd < 10) { 
						dd = '0' + dd; 
					} 
					if (mm < 10) { 
						mm = '0' + mm; 
					} 
					var someFormattedDate = mm + '/' + dd + '/' + yyyy; 
					if(someFormattedDate != 'NaN/NaN/NaN')
					{
						$closesttr.find('.DocumentExpiryDate').text(someFormattedDate);
					}

				}

				$.ajax({
					type: "POST",
					dataType: 'JSON',
					url: base_url + 'Priority_Report/Update_Dynamic_Column',
					data: {'OrderUID':OrderUID,'value':value,'ColumnName':ColumnName}, 
					success: function(data)
					{
						if(value == '') {
							$closesttr.find('.DocumentExpiryDate').text('');
						}
						$elem.attr('data-lastupdated', value);
						$closesttr.find('.DocumentExpiryDate').trigger('change')
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });	
					},
					error: function (jqXHR, textStatus, errorThrown) {

						console.log(errorThrown);

					},
					failure: function (jqXHR, textStatus, errorThrown) {

						console.log(errorThrown);

					},
				});
			}

		});
	}

	//update date readonly label date

	$(document).off('change', '.DocumentExpiryDate').on('change', '.DocumentExpiryDate', function (e) {
		var OrderUID = $(this).closest("tr").find(".OrderUID").data("orderuid");
		var value = $(this).text();
		var ColumnName = $(this).attr('data-ColumnName');
		$(this).attr('data-lastupdated',value);
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			global: false,
			url:'Priority_Report/Update_Dynamic_Column',
			data: {'OrderUID':OrderUID,'value':value,'ColumnName':ColumnName},
			success: function(data)
			{
				console.log(data);					
			}
		});
	});

	//update priority document date 
	$(document).on('change', '.priorityinputfield', function(event) {
		var currentvalue = $(this).text();
		var OrderUID = $(this).closest("tr").find(".OrderUID").data("orderuid");
		$LastUpdated = $(this).closest("td").find("#LastUpdated");
		if (currentvalue != $LastUpdated.val()) {
			$.ajax({
				type:'POST',
				dataType: 'JSON',
				global: false,
				url:'Priority_Report/updateprioritycomments',
				data: {'Comments':currentvalue,'OrderUID':OrderUID},
				success: function(data)
				{
					if (data.error == 0) {
						$LastUpdateComments.val(currentvalue);
					}
					$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });						
				}
			});
		}
	});

	$(document).ready(function(){

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




		$(document).off('click','.listorders').on('click','.listorders',function() {


			var $cell = $(this).closest('td');
			var cellIndex = $cell.index();
			// get the value of the matching header
			var headerVal = $cell.closest( "table" ).find( "thead > tr > th" ).eq( cellIndex ).text();

			var OrderUID = $(this).attr('data-orderid');
			var WorkflowModuleUID = $(this).attr('data-workflowmoduleuid');
			var title = $(this).attr('title');
			var reporttabtitle = $('.prioritytab .active').text();
			var Orderlistname = 'Priority Report - '+reporttabtitle+' - '+title;

			if(!OrderUID || OrderUID.length === 0) {
				$.notify(
				{
					icon:"icon-bell-check",
					message:'No Orders Available'
				},
				{
					type:'danger',
					delay:1000 
				});
				return false;
			}

			fetchorders(OrderUID,WorkflowModuleUID,Orderlistname,headerVal)
		});

		$('.fa-filter').click(function(){
			$("#advancedFilterForReport").slideToggle();
		});

		$(document).off('click','.filterreport').on('click','.filterreport',function()
		{
			var ProjectUID = $('#adv_ProjectUID option:selected').val();
			var CustomerUID = $('#adv_CustomerUID option:selected').val();
			var MilestoneUID = $('#adv_MilestoneUID').val();
			var adv_Status = $('#adv_Status option:selected').val();
			var FromDate = $('#adv_FromDate').val();
			var ToDate = $('#adv_ToDate').val();
			if((ProjectUID == '')  && (MilestoneUID == '') && (CustomerUID == '') && (FromDate == '')&& (ToDate == ''))
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
			}
			else 
			{


				var reporttype = $('.prioritytab .active').attr('data-reporttype');

				var url = 'Priority_Report/fetch_counts';
				var ReportName = 'Priority Report';
				if(reporttype == "agingbucket") {
					url = 'Priority_Report/fetch_agingcounts';
					ReportName = 'Priority Report - Aging Bucket';
				}else if(reporttype == "processor") {
					url = 'Priority_Report/fetch_processorcounts';					
					ReportName = 'Priority Report - Processor';
				}else if(reporttype == "teamleader") {
					url = 'Priority_Report/fetch_teamleadcounts';					
					ReportName = 'Priority Report - Team Leader';
				}else if(reporttype == "onshoreprocessor") {
					url = 'Priority_Report/fetch_onshoreprocessorcounts';					
					ReportName = 'Priority Report - Processor';
				}else if(reporttype == "onshoreteamleader") {
					url = 'Priority_Report/fetch_onshoreteamleadcounts';					
					ReportName = 'Priority Report - Team Leader';
				}else if(reporttype == "loantype") {
					url = 'Priority_Report/fetch_loantypecounts';
					ReportName = 'Priority Report - Loan Type';
				}

				var formData = ({'MilestoneUID':MilestoneUID,'ProjectUID': ProjectUID ,'CustomerUID':CustomerUID,'FromDate':FromDate,'ToDate':ToDate,'url':url,'ReportName':ReportName,'FilterStatus':adv_Status}); 

				fetch_counts(formData).then(function (resolve) {  
					removecardspinner($('#prioritycount-view'));
					$('.counter').counterUp({
						delay: 10,
						time: 2000,
					});
				});

			}

			return false;
		});

		$(document).off('click','.reset').on('click','.reset',function(){
			$("#adv_ProjectUID").val('All');
			$("#adv_CustomerUID").val('All');
			$("#adv_Status").val('Pending');
			$("#adv_FromDate").val('');
			$("#adv_ToDate").val('');
			$('.filterreport').trigger('click');
			callselect2();

		});

		$(document).on("click" , ".orderclose" , function(){
			$("#orderstablediv").fadeOut('fast');
			$("#prioritycount-view").fadeIn('fast');
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
		})

		$('.filterreport').trigger('click');


	});


	$(document).off('click','.excelorderlist').on('click','.excelorderlist',function(){

		var OrderUID = $('#orderlist_orderuids').val();
		var WorkflowModuleUID = $('#orderlist_workflowmoduleuid').val();
		var filename = 'Priority Report - '+$('#orderlisttitle').text()+'.xlsx';
		$.ajax({
			type: "POST",
			url: base_url+'Priority_Report/WriteOrdersExcel',
			xhrFields: {
				responseType: 'blob',
			},
			data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'filename':filename},
			beforeSend: function(){


			},
			success: function(data)
			{
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


	$(document).off('change', '#adv_CustomerUID').on('change', '#adv_CustomerUID', function (e) {  
		e.preventDefault();
		var $dataobject = {'CustomerUID': $(this).val()};
		if($(this).val() == 'All')
		{
			$('#adv_ProjectUID').html('<option value="All">All</option>');
			callselect2();
		}
		else
		{
			SendAsyncAjaxRequest('POST', 'CommonController/GetAdvancedSearchProducts', $dataobject, 'json', true, true, function () {
				//addcardspinner($('#AuditCard'));
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
		}
	});

	$(document).off('blur', '.comments_editable').on('blur', '.comments_editable', function (e) {
		var Comments = $(this).text();
		var OrderUID = $(this).closest("tr").find(".OrderUID").data("orderuid");
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


	//Order Reverse
	$(document).off('click', '.btnOrderReverse').on('click', '.btnOrderReverse', function (e) {

		var OrderUID = $(this).attr('data-orderuid');
		$('#OrderUID').val( OrderUID)
		$.ajax({
			type:"POST",
			url : base_url + 'Priority_Report/fetch_reversepopup',
			data:{'OrderUID':OrderUID},
			dataType :"json",
			cache: false,
			beforeSend: function () {
			},
			success :function(response){
				if (response.success == 1) {

					$('#appendmodal').html(response.data);
					$('#modal-OrderReverse').modal('show');
					callselect2byid('ReverseStatusUID');
				} else {
					$.notify({icon:"icon-bell-check",message:response.message},{type:"success",delay:2000 });

				}

			},
			error: function (jqXHR) {
			}
		})
	});

	//Order Reverse
	$(document).off('click', '.btnLoanInfo').on('click', '.btnLoanInfo', function (e) {

		var OrderUID = $(this).attr('data-orderuid');
		$('#OrderUID').val( OrderUID)
		$.ajax({
			type:"POST",
			url : base_url + 'Priority_Report/fetch_LoanInfoPopup',
			data:{'OrderUID':OrderUID},
			dataType :"json",
			cache: false,
			beforeSend: function () {
			},
			success :function(response){
				if (response.success == 1) {

					$('#appendmodal').html(response.data);
					$('#modal-LoanInfo').modal('show');
					callselect2byid('ReverseStatusUID');
				} else {
					$.notify({icon:"icon-bell-check",message:response.message},{type:"success",delay:2000 });

				}

			},
			error: function (jqXHR) {
			}
		})
	});

	$(document).off('submit', '#frm_priorityorderreverse').on('submit', '#frm_priorityorderreverse', function (e) {
	e.preventDefault();
	e.stopPropagation();
	
	var button = $('.btnreverse');
	var button_text = button.html();
	var OrderUID = $('#OrderUID').val();
	var WorkflowUID = $('#ReverseStatusUID').val();
	var DependentWorkflowModuleUIDs = $('#ReverseDependentWorkflows').val();
	if(WorkflowUID == '') {

		$.notify({icon:"icon-bell-check",message:'Please select workflow'},{type:"danger",delay:2000});
		return false;
	}

	$.ajax({
		type:"POST",
		url : base_url + 'OrderComplete/WorkflowOrderReverse',
		data:{OrderUID:OrderUID,WorkflowUID:WorkflowUID,'DependentWorkflowModuleUIDs':DependentWorkflowModuleUIDs},
		dataType :"json",
		cache: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success :function(response){
			if (response.success == 1) {

				$('#modal-OrderReverse').modal('hide');

				orderslist.ajax.reload(null, false); 
				$.notify({icon:"icon-bell-check",message:response.message},{type:"success",delay:2000 });

				//(response['RedirectURL'] != '') ? open_backgroundtab(response['RedirectURL']) : false;

				button.html(button_text);
				button.attr("disabled", false);

			} else {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Reverse Failed</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: '300px',
					buttonsStyling: false
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Reverse Failed</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: '300px',
				buttonsStyling: false
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		}
	})

});

$(document).off('click', 'input[type=radio][name=STC]').on('click', 'input[type=radio][name=STC]', function (e) {
	if (this.value == 'Amount') {
		$(".stcamountdiv").show();
	} else {
		$(".stcamountdiv").hide();
	}
});

$(window).resize(function() {
	$($.fn.dataTable.tables( true ) ).css('width', '100%');
	$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
});

//Calculator Info
$(document).off('click', '.btnCalculatorInfo').on('click', '.btnCalculatorInfo', function (e) {

	var element = $(this);
	var element_text = element.html();
	var OrderUID = $(this).attr('data-orderuid');
	var WorkUpCalculatorShow = $(this).attr('data-workupcalculatorshow');
	var DocsOutCalculatorShow = $(this).attr('data-docsoutcalculatorshow');
	$('#OrderUID').val( OrderUID)
	$.ajax({
		type:"POST",
		url : base_url + 'Priority_Report/fetch_CalculatorInfoPopup',
		data:{'OrderUID':OrderUID, 'WorkUpCalculatorShow':WorkUpCalculatorShow, 'DocsOutCalculatorShow':DocsOutCalculatorShow},
		dataType :"json",
		cache: false,
		beforeSend: function () {
			element.addClass("disabled");
			element.html('<i class="fa fa-spin fa-spinner" style="font-size: 15px !important;"></i>');
		},
		success :function(response){
			if (response.success == 1) {

				$('#appendmodal').html(response.data);
				$('#modal-CalculatorInfo').modal('show');
				callselect2byid('ReverseStatusUID');
				element.html(element_text);
				element.removeClass("disabled");
			} else {
				$.notify({icon:"icon-bell-check",message:response.message},{type:"success",delay:2000 });
				element.html(element_text);
				element.removeClass("disabled");

			}

		},
		error: function (jqXHR) {
		}
	})
});




