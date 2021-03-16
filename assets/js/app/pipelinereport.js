	var orderslist = false;
	var prioritydatatable = false;
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

				fn_datatable(data.ReportName);
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
			scrollY: 360,
			paging:  false,
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


	function fetchorders(OrderUID,WorkflowModuleUID,Orderlistname)
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
			scrollY: 360,
			paging:  true,
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			fixedColumns: {
				leftColumns: 1,
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
				"url": base_url+"Pipeline/fetchorders",
				"type": "POST",
				"data" : {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID}  
			},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
		});
	}

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
			var td = $(this).closest('td');
			var OrderUID = $(this).attr('data-orderid');
			var WorkflowModuleUID = $(this).attr('data-workflowmoduleuid');
			var title = $(this).attr('title');
			var Orderlistname = 'Pipeline Report - '+title;

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

			fetchorders(OrderUID,WorkflowModuleUID,Orderlistname)
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

				var url = 'Pipeline/fetch_counts';
				var ReportName = 'Pipeline Report';
				if(reporttype == "agingbucket") {
					url = 'Pipeline/fetch_agingcounts';
				 	ReportName = 'Pipeline Report - Aging Bucket';
				}else if(reporttype == "processor") {
					url = 'Pipeline/fetch_processorcounts';					
				 	ReportName = 'Pipeline Report - Processor';
				}else if(reporttype == "teamleader") {
					url = 'Pipeline/fetch_teamleadcounts';					
				 	ReportName = 'Pipeline Report - Team Leader';
				}else if(reporttype == "onshoreprocessor") {
					url = 'Pipeline/fetch_onshoreprocessorcounts';					
				 	ReportName = 'Pipeline Report - Processor';
				}else if(reporttype == "onshoreteamleader") {
					url = 'Pipeline/fetch_onshoreteamleadcounts';					
				 	ReportName = 'Pipeline Report - Team Leader';
				}else if(reporttype == "loantype") {
					url = 'Pipeline/fetch_loantypecounts';
				 	ReportName = 'Pipeline Report - Loan Type';
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
		// var filename = 'Pipeline Report - '+$('#orderlisttitle').text()+'.xlsx';
		var filename = $('#orderlisttitle').text().replace(/\//g, '_')+'.xlsx';
		$.ajax({
			type: "POST",
			url: base_url+'Pipeline/WriteOrdersExcel',
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


	$(window).resize(function() {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
	});
