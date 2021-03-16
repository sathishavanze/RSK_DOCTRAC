/*DASHBOARD*/
function fn_dashboardtabledatatable($selector,ReportName='Export')
{
	dashboardtable = $($selector).DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: '80vh',
		paging:  false,
		searchDelay:1500,
		"bInfo" : false,
		"bSort" : false,
		"bDestroy": true,
		"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": false, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"pageLength": 10, // Set Page Length
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
		dom: 'lfrtip',
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

	return dashboardtable;
}


//chart Queue Progess PeriodWise
function queueprogress_periodwise_chart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Queueprogresscard'));
	$.ajax({
		type: "POST",
		//url: 'Dashboard/queueProgress_periodcounts',
		url: 'Dashboard/queueProgress_periodcount_new',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$('.TableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.queueprogress-period'));
			removecardspinner($('#Queueprogresscard'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Queueprogresscard'));

		}
	});
}




function queueprogress_periodwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Queueprogresscard'));
	$.ajax({
		type: "POST",
		//url: 'Dashboard/queueProgress_periodcounts',
		url: 'Dashboard/queueProgress_periodcount_new',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{

			$('.TableDiv').show();
			$('#chart').hide();		
			$('.TableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.queueprogress-period'));
			removecardspinner($('#Queueprogresscard'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Queueprogresscard'));

		}
	});
}


function queueprogress_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Queueprogresscard'));
	$.ajax({
		type: "POST",
		url: 'Dashboard/queueProgress_individualcounts',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$('.TableDiv').show();
			$('#chart').hide();		
			$('.TableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.queueprogress-individual'));
			removecardspinner($('#Queueprogresscard'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Queueprogresscard'));

		}
	});
}

function agingReport_queuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Aging_Card'));
	$.ajax({
		type: "POST",
		url: 'Dashboard/agingReport_queuecounts',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$("#stack").hide();
			$('.AgingTableDiv').show();
			$('.AgingTableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.queue-aging'));
			removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Aging_Card'));

		}
	});
}

function agingReport_subqueuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Aging_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/agingReport_subqueuecounts',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$("#stack").hide();
			$('.AgingTableDiv').show();
			$('.AgingTableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.subqueue-aging'));
			removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Aging_Card'));

		}
	});
}

function agingreport_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Aging_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/agingReport_individualcounts',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			console.log(data);
			$("#stack").hide();
			$('.AgingTableDiv').show();
			$('.AgingTableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.aging-individual'));
			removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Aging_Card'));

		}
	});
}

function agingReport_periodcounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Aging_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/agingReport_periodicResult',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$("#stack").hide();
			$('.AgingTableDiv').show();
			$('.AgingTableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.aging-period'));
			removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Aging_Card'));

		}
	});
}


function agingReport_categorycounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#Aging_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/agingReport_categoryWiseResult',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$("#stack").hide();
			$('.AgingTableDiv').show();
			$('.AgingTableDiv').html(data.Result);
			fn_dashboardtabledatatable($('.aging-period'));
			removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#Aging_Card'));

		}
	});
}

//Pipleline_reviewReport
function Pipleline_reviewCountReport(adv_period,adv_fromDate,adv_toDate,WorkFlows)
{
	addcardspinner($('#PipeLine_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/Pipeline_reviewCountResult',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$('.PipeLineTableDiv').show();
			$('.PipeLineChart').hide();
			$('.PipeLineAgingChart').hide();
			$('.PipeLineTableDiv').html(data.Result);
			//fn_dashboardtabledatatable($('.aging-period'));
		},
		error: function (jqXHR) {
			removecardspinner($('#PipeLine_Card'));

		}
	});
}

//pipeLine_AgingFileWiseRepo
function pipeLine_AgingFileWiseRepo(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
{
	addcardspinner($('#PipeLine_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/Pipeline_AgingFileRepo',
		data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$('.PipeLineTableDiv').show();
			$('.PipeLineChart').hide();
			$('.PipeLineAgingChart').hide();
			$('.PipeLineTableDiv').html(data.Result);
			//fn_dashboardtabledatatable($('.aging-period'));
			//removecardspinner($('#Aging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#PipeLine_Card'));

		}
	});
}


//change Pending File chart and table
$(document).on('change','.TATAgingDrop',function(){

		var type=($(this).val());
		var val1 = $("#TATAgingDrop option:selected").val();
	
		$('#TATAgingDrop').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		//var Type = $('#TATAgingDrop').val() ? $('#TATAgingDropType').val() : 'TATAging';
		//alert(Type);
		if(val1 =='TATAgingTable'){
			//alert("TAT Table");

			TATAgingCounts(WorkflowModuleUID,FromDate,ToDate);
		}else if (val1 =='TATAgingChart') 
		{
			TAT_AgingChart(WorkflowModuleUID,FromDate,ToDate);
			//GetPendingChart(WorkflowModuleUID,FromDate,ToDate);
		}

});



///TAT_AgingChart
	function TAT_AgingChart(WorkFlows,adv_fromDate,adv_toDate)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/TAT_Aging_Chart',
			data: {WorkFlows:WorkFlows, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				console.log(data["label"]);
				//console.log(data["series"]);
				//$('.TableDiv').html(data.Result);
				$('.TATAgingTableDiv').hide();
				$("#multi-line-chart").empty();
				$('#multi-line-chart').show();
		if (data)
		{

			var chart = new Chartist.Bar(
			    "#multi-line-chart",
			    {
			        labels: data["label"],
			        series: [
			           { "name": "5-10", "data": data["fivetotendays"] },
			           { "name": "10-15", "data": data["tentofifteendays"] },
			           { "name": "15-20", "data": data["fifteentotwentydays"] },
			           { "name": "25-30", "data": data["twentyfivetothirty"] },
			            //data["fivetotendays"],
			            //data["tentofifteendays"],
			            //data["fifteentotwentydays"],
			            //data["twentyfivetothirty"],
			        ],
			    },
			    {
			        seriesBarDistance: 15,
			        axisX: { offset: 60 },
			        axisY: {
			            offset: 80,
			            labelInterpolationFnc: function (e) {
			                return e;
			            },
			            scaleMinSpace: 25,
			        },
			        plugins: [

			        Chartist.plugins.tooltip()
			        //Chartist.plugins.legend()

			        ],
			    }
			)


		}
		else
		{

			$('#multi-line-chart').removeAttr('style');
			$('#multi-line-chart').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');

		}

			},
			error: function (jqXHR) {
			}
		});



	}




//TATAgingCounts
function TATAgingCounts(WorkFlows,adv_fromDate,adv_toDate)
{
	addcardspinner($('#TATAging_Card'));

	$.ajax({
		type: "POST",
		url: 'Dashboard/TAT_AgingRepo',
		data: {WorkFlows:WorkFlows,adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
		dataType: 'JSON',
		success: function(data)
		{
			$("#multi-line-chart").hide();
			$('.TATAgingTableDiv').show();
			$('.TATAgingTableDiv').html(data.Result);
			//fn_dashboardtabledatatable($('.aging-period'));
			removecardspinner($('#TATAging_Card'));
		},
		error: function (jqXHR) {
			removecardspinner($('#TATAging_Card'));

		}
	});
}








//get individual workflow 

function individualWorkflow(formdate){

	$.ajax({
		type: "POST",
		url: 'Dashboard/GetUsersByWorkflow',
		data: formdate,
		dataType: 'JSON',
		success: function(data)
		{	
			$('.TableInflow').hide();
			$('.productivityChart').hide();
			$('#Workflow-Userslist').show();
			$('#Workflow-Userslist').html(data);
			fn_dashboardtabledatatable($('.productivity-individual'));

			removecardspinner($('GetNextOrder-div'));
		}
	});
}

//function for get inflow reports
function ProcessReport(formData)
{     
	$.ajax({
		type: "POST",
		url: 'Dashboard/getProductivityTable',
		data: formData,
		dataType: 'JSON',
		beforeSend: function()
		{
			addcardspinner($('#productivitycard'));
		},
		success: function(data)
		{
			$('#Workflow-Userslist').hide();
			$('.productivityChart').hide();
			$('.TableInflow').show();
			$('.TableInflow').html(data);
			fn_dashboardtabledatatable($('.productivity-period'));
			removecardspinner($('#productivitycard'));
		}
	});
}

//get report
function getReportInflow(Period,FromDate,ToDate,WorkflowModuleUID,Target=10,Type='Team'){
	var productivityType=$('#productivitydropdown').val();

	var adv_period = $('#adv_period').val();
	if(Period == '')
	{
		//$.toast({text:'Please Choose Period',position: "top-right",loaderBg:"#bf441d",icon:"error",hideAfter:3e3,stack:1});
		$.notify({
			message: "Please Choose Period",
		}, {
			type: "danger",
			delay: 1000,
		});
	}
	else if((FromDate == '') || (ToDate == ''))
	{
		//$.toast({text:'Please Choose Date',position: "top-right",loaderBg:"#bf441d",icon:"error",hideAfter:3e3,stack:1});
		$.notify({
			message: "Please Choose Date",
		}, {
			type: "danger",
			delay: 1000,
		});
	}
	else if(Target == '')
	{
		//$.toast({text:'Please Choose Target',position: "top-right",loaderBg:"#bf441d",icon:"error",hideAfter:3e3,stack:1});
		$.notify({
			message: "Please Choose Target",
		}, {
			type: "danger",
			delay: 1000,
		});
	}
	else 
	{
		var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Target':Target,'WorkflowModuleUID':WorkflowModuleUID,'Type':Type,'adv_period':adv_period});
			$('#adv_Target').val(Target);
		if(productivityType=='TablePeriodic' || productivityType=='TableIndividual'){
			var Type=productivityType.split('Table')[1] == 'Periodic' ? 'Team' : 'Individual';
			var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Target':Target,'WorkflowModuleUID':WorkflowModuleUID,'Type':Type,'adv_period':adv_period});
			$('#adv_Target').val(Target);
			if(Type=='Team'){
				ProcessReport(formData);
			}else{
				individualWorkflow(formData);
			}
		}else{
			ProcessReport(formData);
		}
		
	}
}


//get inflow report 
function ProcessinflowReport(FromDate,ToDate,WorkflowModuleUID,inflowType='Team')
{   
	var formData = ({'Type': inflowType,'FromDate' : FromDate,'ToDate':ToDate,'WorkflowModuleUID':WorkflowModuleUID,'Status':'Pending'});
	$.ajax({
		type: "POST",
		url: 'Dashboard/getInflowProcessTable',
		data: formData,
		dataType: 'JSON',
		beforeSend: function()
		{
			addcardspinner($('#ProcessInflow'));
		},
		success: function(data)
		{
			$('.ChartDiv').hide();
			$('.TableProcessInflow').show();
			$('.TableProcessInflow').html(data);
			fn_dashboardtabledatatable($('.inflowReport'));
			removecardspinner($('#ProcessInflow'));
		}
	});
}

//get FollowUp report 
function ProcessFollowUpReport(FromDate,ToDate,WorkflowModuleUID,FollowUpType='Team')
{   
	var formData = ({'Type': FollowUpType,'FromDate' : FromDate,'ToDate':ToDate,'WorkflowModuleUID':WorkflowModuleUID,'Status':'Pending'});
	$.ajax({
		type: "POST",
		url: 'Dashboard/getFollowUpProcessTable',
		data: formData,
		dataType: 'JSON',
		beforeSend: function()
		{
			addcardspinner($('#ProcessFollowUp'));
		},
		success: function(data)
		{
			$('.FollowUpChartDiv').hide();
			$('.TableProcessFollowUp').show();
			$('.TableProcessFollowUp').html(data);
			fn_dashboardtabledatatable($('.FollowUpReport'));
			removecardspinner($('#ProcessFollowUp'));
		}
	});
}

//get quality individual report 
function GetQualityReport(FromDate,ToDate,WorkflowModuleUID,QualityType='Team')
{    
	var formData = ({'Type': QualityType,'FromDate' : FromDate,'ToDate':ToDate,'WorkflowModuleUID':WorkflowModuleUID});
	console.log(formData);
	$.ajax({
		type: "POST",
		url: 'Dashboard/GetQualityReport',
		data: formData,
		dataType: 'JSON',
		beforeSend: function()
		{
			addcardspinner($('.QualityRefresh'));
		},
		success: function(data)
		{
			$('.TableQualityReport').html(data);
			fn_dashboardtabledatatable($('.QCReportTable'));
			removecardspinner($('.QualityRefresh'));
		}
	});
}

//get inflow report 
function GetProductivityTarget(WorkflowModuleUID)
{   
	var formData = ({'WorkflowModuleUID':WorkflowModuleUID});
	$.ajax({
		type: "POST",
		url: 'Dashboard/getTarget',
		data: formData,
		dataType: 'JSON',
		success: function(data)
		{
			console.log(data);
			$('#adv_Target').val('');
			if(data !=''){
				$('#adv_Target').val(data);
			}else{
				$('#adv_Target').val(10);
			}
			var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
			var Period = $('#adv_period').val();
			var FromDate = $('#adv_fromDate').val();
			var ToDate = $('#adv_toDate').val();
			var Target = $('#adv_Target').val();
			var Type = $('.inflowtype').val() ? $('.inflowtype').val() : 'Individual';
			//getReportInflow(Period,FromDate,ToDate,WorkflowModuleUID,Target,Type);
			// $('#adv_Target').trigger('change');
		}
	});
}


//get date
function getDate(period)
{      
	if(period)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/getFromToDate',
			data: {'period':period},
			dataType: 'JSON',
			success: function(data)
			{
				$('#adv_fromDate').val(data.fromDate);
				$('.DateRange').val(data.fromDate);
				$('.DateRangeTo').val(data.toDate);
				$('#adv_toDate').val(data.toDate);
				$(window).scroll();

			}
		});
	}
}

function render_table(OrderUID,WorkflowModuleUID)
{
	orderslist = $('#orderslist').DataTable( {
		scrollX:        true,
		scrollCollapse: true,
		fixedHeader: false,
		scrollY: "80vh",
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
			"url": base_url+"Dashboard/fetchorders",
			"type": "POST",
			"data" : {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID}  
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
		"drawCallback": function(settings) {

			$($.fn.dataTable.tables( true ) ).css('width', '100%');
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
		},

	});
}


function fetchorders(OrderUID,WorkflowModuleUID,Orderlistname)
{
	$("#prioritycount-view").fadeOut('fast');
	$("#orderstablediv").fadeIn('fast');
	$('#orderlisttitle').text(Orderlistname);
	$('#orderlist_orderuids').val(OrderUID);
	$('#orderlist_workflowmoduleuid').val(WorkflowModuleUID);
	$('#ordersloanmodal').modal('show');
}


$(document).ready(function() {

$("#SuccessQueueChart").select2().select2("val", 'SuccessChartPeriodic');
$("#AgingChartType").select2().select2("val", 'ChartAgingPeriodic');
$("#TATAgingDrop").select2().select2("val", 'TATAgingChart');


	$("#orderfilter").click(function() {
		var x = document.getElementById("advancedFilterForReport");
		if (x.style.display === "none") {
			x.style.display = "block";
		} else {
			x.style.display = "none";
		}
	});

	$(".refreshdiv").click(function(){	
		$(this).closest(".card").append(spinner);	
		$(this).closest(".card").append(overlaydiv);
		$(this).closest(".card .btn").addClass("reduceindex");
	});





	$(document).on('click', '.dropdown-view', function() {
		var dropdownview = $(this).attr('id');
		if(dropdownview) {
			var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
			var adv_period = $('#adv_period').val();
			var adv_fromDate = $('#adv_fromDate').val();
			var adv_toDate = $('#adv_toDate').val();
			var adv_Target = $('#adv_Target').val();
			if(dropdownview == 'queueprogress_periodwise') {
				queueprogress_periodwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} else if(dropdownview == 'queueprogress_individualwise') {
				queueprogress_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} else if(dropdownview == 'agingreport_individualwise') {
				agingreport_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} else if(dropdownview == 'agingreport_queue') {
				agingReport_queuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			}else if(dropdownview == 'agingreport_subqueue') {
				agingReport_subqueuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} else if(dropdownview == 'agingreport_periodwise') {
				agingReport_periodcounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} else if(dropdownview == 'agingreport_categorywise') {
				agingReport_categorycounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			} 
			else if(dropdownview == 'pipeline_agingfilewise') {
				pipeLine_AgingFileWiseRepo(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target);
			}

		}
	});


	//change Aging 
	$(document).on('change','#AgingChartType',function(){
		var type=($(this).val());
		$('#agingdropdown').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Target = $('#adv_Target').val();
		var Type = $('#agingdropdown').val() ? $('#agingdropdown').val() : '';

		if(Type=='ChartAgingIndividual'){

			Aging_Individual_Chart_Result(Period,FromDate,ToDate,WorkflowModuleUID);

		}else if(Type=='ChartAgingPeriodic'){

			Aging_Periodic_Chart_Result(Period,FromDate,ToDate,WorkflowModuleUID);

		}else if(Type=='ChartAgingCateory'){

			Aging_Category_Chart_Result(Period,FromDate,ToDate,WorkflowModuleUID);

		}else if(Type=='ChartAgingQueue'){

			Aging_Queue_Chart_Result(Period,FromDate,ToDate,WorkflowModuleUID);
			
		}else if(Type=='ChartAgingSubQueue'){

			Aging_SubQueue_Chart_Result(Period,FromDate,ToDate,WorkflowModuleUID);

		}else if(Type=='TableAgingIndividual'){

			agingreport_individualwise(Period,FromDate,ToDate,WorkflowModuleUID,Target);

		}else if(Type=='TableAgingPeriodic'){

			agingReport_periodcounts(Period,FromDate,ToDate,WorkflowModuleUID,Target);

		}else if(Type=='TableAgingCateory'){

			agingReport_categorycounts(Period,FromDate,ToDate,WorkflowModuleUID,Target);

		}else if(Type=='TableAgingQueue'){

			agingReport_queuecounts(Period,FromDate,ToDate,WorkflowModuleUID,Target);

		}else if(Type=='TableAgingSubQueue'){

			agingReport_subqueuecounts(Period,FromDate,ToDate,WorkflowModuleUID,Target);
		}

	});

///AGING INDIVIDUAL CHART
	function Aging_Individual_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/aging_individual_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.AgingTableDiv').hide();
				$("#stack").empty();
				$('#stack').show();

				// Use Morris.Bar

				var result = data;
				if (data)
				{

					Morris.Bar({
					  element: 'stack',
					  barSizeRatio:0.15,
					  data: result,
					  xkey: 'UserName',
					  ykeys: ['fivetotendayscount', 'tentofifteendayscount', 'fifteentotwentydayscount', 'twentyfivetothirtydayscount'],
					  labels: ['5-10', '10-15', '15-20', '25-30'],
					  hideHover: 'auto',
					  xLabelAngle: 90,
					  stacked: true
					});
				}
				else
				{

					$('#stack').removeAttr('style');
					$('#stack').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
					
				}

			},
			error: function (jqXHR) {
			}
		});
	}


//Aging_Periodic_Chart_Result
	function Aging_Periodic_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/aging_Periodic_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.AgingTableDiv').hide();
				$("#stack").empty();
				$('#stack').show();

				// Use Morris.Bar

				if (data)
				{
					var result = data;
					Morris.Bar({
					  element: 'stack',
					  barSizeRatio:0.15,
					  data: result,
					  xkey: 'Date',
					  ykeys: ['fivetotendayscount', 'tentofifteendayscount', 'fifteentotwentydayscount', 'twentyfivetothirtydayscount'],
					  labels: ['5-10', '10-15', '15-20', '25-30'],
					  hideHover: 'auto',
					  stacked: true,
					  barcolors:['red','blue','yellow','green']
					});

				}
				else
				{

					$('#stack').removeAttr('style');
					$('#stack').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			},
			error: function (jqXHR) {
			}
		});
	}

//Aging_SubQueue_Chart_Result
	function Aging_SubQueue_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/aging_SubQueue_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.AgingTableDiv').hide();
				$("#stack").empty();
				$('#stack').show();

				// Use Morris.Bar
				if (data)
				{
	
					var result = data;
					Morris.Bar({
					  element: 'stack',
					  barSizeRatio:0.10,
					  data: result,
					  xkey: 'QueueName',
					  ykeys: ['fivetotendayscount', 'tentofifteendayscount', 'fifteentotwentydayscount', 'twentyfivetothirtydayscount'],
					  labels: ['5-10', '10-15', '15-20', '25-30'],
					  hideHover: 'auto',
					  xLabelAngle: 90,
					  stacked: true
					});
				}
				else
				{

					$('#stack').removeAttr('style');
					$('#stack').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			},
			error: function (jqXHR) {
			}
		});
	}





//Aging_Queue_Chart_Result
	function Aging_Queue_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/aging_Queue_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.AgingTableDiv').hide();
				$("#stack").empty();
				$('#stack').show();

				// Use Morris.Bar
				if (data)
				{
					var result = data;
					Morris.Bar({
					  element: 'stack',
					  barSizeRatio:0.10,
					  data: result,
					  xkey: 'WorkflowModuleName',
					  ykeys: ['fivetotendayscount', 'tentofifteendayscount', 'fifteentotwentydayscount', 'twentyfivetothirtydayscount'],
					  labels: ['5-10', '10-15', '15-20', '25-30'],
					  hideHover: 'auto',
					  xLabelAngle: 90,
					  stacked: true
					});
				}
				else
				{

					$('#stack').removeAttr('style');
					$('#stack').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');

				}
		
			},
			error: function (jqXHR) {
			}
		});
	}



//Aging_Category_Chart_Result
	function Aging_Category_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/aging_Category_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.AgingTableDiv').hide();
				$("#stack").empty();
				$('#stack').show();

				// Use Morris.Bar

				if (data)
				{

					var result = data;
						Morris.Bar({
					  element: 'stack',
					  barSizeRatio:0.15,
					  data: result,
					  xkey: 'CateName',
					  ykeys: ['fivetotendayscount', 'tentofifteendayscount', 'fifteentotwentydayscount', 'twentyfivetothirtydayscount'],
					  labels: ['5-10', '10-15', '15-20', '25-30'],
					  hideHover: 'auto',
					  stacked: true,
					  xLabelAngle: 90
					});
		
				}
				else
				{

					$('#stack').removeAttr('style');
					$('#stack').append('<div class="agingChart" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			},
			error: function (jqXHR) {
			}
		});
	}

	//change productivity periodic and individual
	$(document).on('click','.QualityType',function(){
		var type=($(this).attr('data-type'));
		$('#QualityType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#QualityType').val() ? $('#QualityType').val() : 'Individual';
		GetQualityReport(FromDate,ToDate,WorkflowModuleUID,Type);
	});

	//change productivity periodic and individual
	$(document).on('change','#ProductivityChartType',function(){
		var type=($(this).val());
		$('#productivitydropdown').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Target = $('#adv_Target').val();
		var Type = $('#productivitydropdown').val() ? $('#productivitydropdown').val() : '';

		if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
			var Type=Type.split('Chart');console.log(Type[1]);
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			GetProductivityChart(Period,FromDate,ToDate,WorkflowModuleUID,Target,inflowType);
		}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
			var Type=Type.split('Table');console.log(Type[1]);
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			getReportInflow(Period,FromDate,ToDate,WorkflowModuleUID,Target,inflowType);
		}else{
			GetProductivityChart(Period,FromDate,ToDate,WorkflowModuleUID,Target);
		}

		//getReportInflow(Period,FromDate,ToDate,WorkflowModuleUID,Target,Type);

	});


	//change process inflow periodic and individual
	$(document).on('click','.processInflowType',function(){
		var type=($(this).attr('data-type'));
		if(type=='Team'){
			$('#Team').hide();
			$('#Individual').show();
		}else{
			$('#Team').show();
			$('#Individual').hide();
		}
		$('.processInflowType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('.processInflowType').val() ? $('.processInflowType').val() : 'Team';
		
		ProcessinflowReport(FromDate,ToDate,WorkflowModuleUID,Type);

	});

	//change process FollowUp periodic and individual
	$(document).on('click','.processFollowUpType',function(){
		var type=($(this).attr('data-type'));
		if(type=='Team'){
			$('#Team').hide();
			$('#Individual').show();
		}else{
			$('#Team').show();
			$('#Individual').hide();
		}
		$('.processFollowUpType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('.processFollowUpType').val() ? $('.processFollowUpType').val() : 'Team';
		
		ProcessFollowUpReport(FromDate,ToDate,WorkflowModuleUID,Type);

	});

	//change target
	$(document).on('change','#adv_Target',function(){
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Target = $('#adv_Target').val();
		var Type = $('#productivitydropdown').val() ? $('#productivitydropdown').val() : '';

		if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
			var Type=Type.split('Chart');
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			GetProductivityChart(Period,FromDate,ToDate,WorkflowModuleUID,Target,inflowType);
		}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
			var Type=Type.split('Table');
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			getReportInflow(Period,FromDate,ToDate,WorkflowModuleUID,Target,inflowType);
		}else{
			GetProductivityChart(Period,FromDate,ToDate,WorkflowModuleUID,Target);
		}
		$.ajax({
			type: "POST",
			url: 'Dashboard/UpdateTarget',
			data: {Target:Target,WorkflowModuleUID:WorkflowModuleUID},
			dataType: 'JSON',
			success: function(data)
			{

				//$("#resultFilter").show();
				console.log(data); 

			}
		})
	});

	$(document).off('click','.listorders').on('click','.listorders',function() {


		$('#append-data').empty();

		var OrderUID = $(this).attr('data-orderid');
		var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();
		var title = $(this).attr('title');
		var reporttabtitle =  $(this).attr('data-heading');
		$('.ordersloanmodal-title').text(reporttabtitle);
		if(!OrderUID || OrderUID.length === 0) {
			$.notify(
			{
				icon:"icon-bell-check",
				message:'No Orders Available!'
			},
			{
				type:'danger',
				delay:1000 
			});
			return false;
		}

		fetchorders(OrderUID,WorkflowModuleUID,reporttabtitle)
	});

	$(document).off('click','.excelorderlist').on('click','.excelorderlist',function(){

		var OrderUID = $('#orderlist_orderuids').val();
		var WorkflowModuleUID = $('#orderlist_workflowmoduleuid').val();
		var filename = $('.ordersloanmodal-title').text()+'.xlsx';
		$.ajax({
			type: "POST",
			url: base_url+'Dashboard/WriteOrdersExcel',
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

	//refresh productivity widget
	$(document).on('click','#productivityRefresh',function(e){
		e.preventDefault();

		dashboardaddcardspinner($('.productivityRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var processInflowType = $('.processInflowType').val() ? $('.processInflowType').val() : 'Team' ;
		//call Productivity report details
		var $fn_Array = [];
		var Type = $('#productivitydropdown').val() ? $('#productivitydropdown').val() : '';

		if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
			var Type=Type.split('Chart');console.log(Type[1]);
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			$fn_Array.push(GetProductivityChart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target,inflowType));
		}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
			var Type=Type.split('Table');console.log(Type[1]);
			var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			$fn_Array.push(getReportInflow(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target,inflowType));
		}else{
			$fn_Array.push(GetProductivityChart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
		}
		//$fn_Array.push(getReportInflow(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('.productivityRefresh'));
		});
		//dashboardremovecardspinner($('.productivityRefresh'));
	});

	//refresh Gatekeeping widget
	$(document).on('click','#PipeLineRefresh',function(e){
		e.preventDefault();

		dashboardaddcardspinner($('.PipeLineRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		//call Pipeline aging report details
		var $fn_Array = [];
		
		var Type = $('#GatekeepingType').val() ? $('#GatekeepingType').val() : 'TotalReviewedChart';

		if(Type=='AgingTable'){
			$fn_Array.push(pipeLine_AgingFileWiseRepo(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
		}else if(Type=='TotalReviewedTable'){
			$fn_Array.push(Pipleline_reviewCountReport(adv_period,adv_fromDate,adv_toDate,WorkFlows));
		}else if(Type=='AgingChart'){
			$fn_Array.push(GetGatekeepingAgingChart(adv_fromDate,adv_toDate,WorkFlows));
		}else{
			$fn_Array.push(GetReviewCountChart(adv_fromDate,adv_toDate,WorkFlows));
		}
		//$fn_Array.push(getReportInflow(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('.productivityRefresh'));
		});
		//dashboardremovecardspinner($('.productivityRefresh'));
	});

	//refresh productivity widget
	$(document).on('click','#QualityRefresh',function(e){
		e.preventDefault();

		dashboardaddcardspinner($('.QualityRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var QualityType = $('#QualityType').val() ? $('#QualityType').val() : 'Individual' ;
		//call Productivity report details
		var $fn_Array = [];
		$fn_Array.push(GetQualityReport(adv_fromDate,adv_toDate,WorkFlows,QualityType));
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('.QualityRefresh'));
		});
		//dashboardremovecardspinner($('.productivityRefresh'));
	});

	//refresh inflow widget
	$(document).on('click','#InflowRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.InflowRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var Inflow_Type = $('#InflowType').val();
		console.log(Inflow_Type);
		if(Inflow_Type=='ChartIndividual' || Inflow_Type=='ChartPeriodic'){
			var Type=Inflow_Type.split('Chart');
			var InflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(ProcessinflowChart(adv_fromDate,adv_toDate,WorkFlows,InflowType));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.InflowRefresh'));
			});
		}else if(Inflow_Type=='TablePeriodic' || Inflow_Type=='TableIndividual'){
			var Type=Inflow_Type.split('Table');
			var InflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(ProcessinflowReport(adv_fromDate,adv_toDate,WorkFlows,InflowType));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.InflowRefresh'));
			});
		}else{
			var $fn_Array = [];console.log('hi');
			$fn_Array.push(ProcessinflowChart(adv_fromDate,adv_toDate,WorkFlows));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.InflowRefresh'));
			});
		}
		
		//dashboardremovecardspinner($('.productivityRefresh'));
	});

	//refresh FollowUp widget
	$(document).on('click','#FollowUpRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.FollowUpRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var FollowUp_Type = $('#FollowUpType').val();console.log(FollowUp_Type);
		if(FollowUp_Type=='ChartIndividual' || FollowUp_Type=='ChartPeriodic'){
			var Type=FollowUp_Type.split('Chart');
			var FollowUpType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			
			//call process FollowUp report details
			var $fn_Array = [];
			$fn_Array.push(ProcessFollowUpChart(adv_fromDate,adv_toDate,WorkFlows,FollowUpType));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.FollowUpRefresh'));
			});
		}else if(FollowUp_Type=='TablePeriodic' || FollowUp_Type=='TableIndividual'){
			var Type=FollowUp_Type.split('Table');
			var FollowUpType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
			//call process FollowUp report details
			var $fn_Array = [];
			$fn_Array.push(ProcessFollowUpReport(adv_fromDate,adv_toDate,WorkFlows,FollowUpType));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.FollowUpRefresh'));
			});
		}else{
			var $fn_Array = [];
			// $fn_Array.push(ProcessFollowUpChart(adv_fromDate,adv_toDate,WorkFlows));
			$fn_Array.push(ProcessFollowUpReport(adv_fromDate,adv_toDate,WorkFlows));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.FollowUpRefresh'));
			});
		}
		
		//dashboardremovecardspinner($('.productivityRefresh'));
	});

	//refresh Queue widget
	$(document).on('click','#QueueRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.QueueRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var AgingType = $("#SuccessQueueChart option:selected").val();;
		//var Inflow_Type = $('#AgingChartType').val();
		console.log(AgingType);
		
		if(AgingType=='SuccessChartPeriodic')
		{
			//call process inflow report details
			//call Queue Progress report details
			var $fn_Array = [];
			//$fn_Array.push(queueprogress_periodwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			$fn_Array.push(Success_Queue_Chart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.QueueRefresh'));
			});
		}
		else if(AgingType=='SuccessChartIndividual')
		{
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Success_Queue_Individual_Chart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.QueueRefresh'));
			});
		}
		else if(AgingType=='SuccessTablePeriodic')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(queueprogress_periodwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.QueueRefresh'));
			});
		}
		else if(AgingType=='SuccessTableIndividual')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(queueprogress_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.QueueRefresh'));
			});
		}

	});

	//refresh Queue widget
	$(document).on('click','#AgingRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.AgingRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var AgingType = $("#AgingChartType option:selected").val();;
		//var Inflow_Type = $('#AgingChartType').val();
		console.log(AgingType);
		if(AgingType=='ChartAgingPeriodic'){
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Aging_Periodic_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='ChartAgingIndividual')
		{
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Aging_Individual_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='ChartAgingCateory')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Aging_Category_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='ChartAgingQueue')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Aging_Queue_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='ChartAgingSubQueue')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(Aging_SubQueue_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='TableAgingIndividual')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(agingreport_individualwise(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='TableAgingPeriodic')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(agingReport_periodcounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='TableAgingCateory')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(agingReport_categorycounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='TableAgingQueue')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(agingReport_queuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else if(AgingType=='TableAgingSubQueue')
		{	
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(agingReport_subqueuecounts(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
		}
		else{
			var $fn_Array = [];console.log('hi');
			$fn_Array.push(Aging_Periodic_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.AgingRefresh'));
			});
			}

	});

	//refresh Gatekeeping widget
	$(document).on('click','#GatekeepingRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.GatekeepingRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		
		//call Queue Progress report details
		var $fn_Array = [];
		$fn_Array.push(GetReviewCountChart(adv_fromDate,adv_toDate,WorkFlows));
		//$fn_Array.push(Pipleline_reviewCountReport(adv_period,adv_fromDate,adv_toDate,WorkFlows));
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('.GatekeepingRefresh'));
		});
	});	

	//refresh Gatekeeping widget
	$(document).on('click','#PendingRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.PendingRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		
		//call Queue Progress report details
		var $fn_Array = [];
		var Type = $('#PendingFileType').val() ? $('#PendingFileType').val() : 'PendingChart';

		if(Type=='PendingTable'){
			$fn_Array.push(fetchPendingCounts(WorkFlows,adv_fromDate,adv_toDate));
		}else{
			$fn_Array.push(GetPendingChart(WorkFlows,adv_fromDate,adv_toDate));
		}
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('.PendingRefresh'));
		});
	});	


	//TAT Aging widget
	$(document).on('click','#TATAgingRefresh',function(e) {
		e.preventDefault();

		dashboardaddcardspinner($('.TATAgingRefresh'));
		var WorkFlows = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var TATType = $("#TATAgingDrop option:selected").val();;
		console.log(TATType);
		if(TATType=='TATAgingChart'){
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(TAT_AgingChart(WorkFlows,adv_fromDate,adv_toDate));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.TATAgingRefreshDIV'));
			});
		}
		else if(TATType=='TATAgingTable')
		{
			//call process inflow report details
			var $fn_Array = [];
			$fn_Array.push(TATAgingCounts(WorkFlows,adv_fromDate,adv_toDate));
			Promise.all($fn_Array).then(function (resolve) {  
				dashboardremovecardspinner($('.TATAgingRefreshDIV'));
			});
		}
		
	});	



	$("#ordersloanmodal").on('show.bs.modal', function () {
		render_table($('#orderlist_orderuids').val(),$('#orderlist_workflowmoduleuid').val());
	});

	$("#ordersloanmodal").on('shown.bs.modal', function () {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
	});

	$(window).resize(function() {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
	});

	$( document ).ajaxComplete(function() {
		setTimeout(function(){
			$('.bodyoverlaydiv').css('display','none');
			$('.d2tspinner-circular').css('display','none');
		},500);
	});


	$(document).off('click','.orderclose').on('click','.orderclose',function() {
		$("#ordersloanmodal").modal('hide');
	});

	$(window).scroll(function() {
		//check if your div is visible 
		var fn_Array = [];
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var adv_period = $('#adv_period').val();
		var adv_fromDate = $('#adv_fromDate').val();
		var adv_toDate = $('#adv_toDate').val();
		var adv_Target = $('#adv_Target').val();
		var processInflowType = $('.processInflowType').val() ? $('.processInflowType').val() : 'Team' ;

		if ($(window).scrollTop() + $(window).height() >= $('#productivitycard').offset().top+350) {
			var pending_load = ($('#productivitycard').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#productivitycard').attr('loaded', true);  
				//Get Productivity Target
				GetProductivityTarget(WorkflowModuleUID);

				var Type = $('#productivitydropdown').val() ? $('#productivitydropdown').val() : '';
				if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
					var Type=Type.split('Chart');console.log(Type[1]);
					var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[0] = GetProductivityChart(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target,inflowType);
				}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
					var Type=Type.split('Table');console.log(Type[1]);
					var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[0] = getReportInflow(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target,inflowType);
				}else{
					fn_Array[0] = GetProductivityChart(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target);
				}
				
			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#Queueprogresscard').offset().top+350) {
			var pending_load = ($('#Queueprogresscard').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#Queueprogresscard').attr('loaded', true);
				
				$("#SuccessQueueChart").select2().select2("val", 'SuccessChartPeriodic');

				fn_Array[1] = Success_Queue_Chart(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID);
				//fn_Array[1] = queueprogress_periodwise(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target);
			}
		}


		if ($(window).scrollTop() + $(window).height() >= $('#Inflowcard').offset().top+350) {
			var pending_load = ($('#Inflowcard').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#Inflowcard').attr('loaded', true);
				var Type = $('#InflowType').val();
				if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
					var Type=Type.split('Chart');console.log(Type[1]);
					var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[2] = ProcessinflowChart(adv_fromDate,adv_toDate,WorkflowModuleUID,inflowType);
				}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
					var Type=Type.split('Table');console.log(Type[1]);
					var inflowType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[2] = ProcessinflowReport(adv_fromDate,adv_toDate,WorkflowModuleUID,inflowType);
				}else{
					fn_Array[2] = ProcessinflowChart(adv_fromDate,adv_toDate,WorkflowModuleUID);
				}
				
			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#Aging_Card').offset().top+350) {
			var pending_load = ($('#Aging_Card').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#Aging_Card').attr('loaded', true);
		
				$("#AgingChartType").select2().select2("val", 'ChartAgingPeriodic');

				fn_Array[3] = Aging_Periodic_Chart_Result(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target);
				//fn_Array[3] = agingReport_periodcounts(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target);

			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#GateKeeping_Card').offset().top+350) {
			var pending_load = ($('#GateKeeping_Card').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#GateKeeping_Card').attr('loaded', true);

				var Type = $('#GatekeepingType').val() ? $('#GatekeepingType').val() : 'TotalReviewedChart';

				if(Type=='AgingTable'){
					fn_Array[5] = (pipeLine_AgingFileWiseRepo(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID,adv_Target));
				}else if(Type=='TotalReviewedTable'){
					fn_Array[5] = (Pipleline_reviewCountReport(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID));
				}else if(Type=='AgingChart'){
					fn_Array[5] = (GetGatekeepingAgingChart(adv_fromDate,adv_toDate,WorkflowModuleUID));
				}else{
					fn_Array[5] = (GetReviewCountChart(adv_fromDate,adv_toDate,WorkflowModuleUID));
				}
				//fn_Array[5] = GetReviewCountChart(adv_fromDate,adv_toDate,WorkflowModuleUID);
				//fn_Array[5] = Pipleline_reviewCountReport(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID);
			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#Quality_Card').offset().top+350) {
			var pending_load = ($('#Quality_Card').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#Quality_Card').attr('loaded', true);
				fn_Array[6] = GetQualityReport(adv_fromDate,adv_toDate,WorkflowModuleUID);
			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#Pending_Card').offset().top+350) {
			var pending_load = ($('#Pending_Card').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#Pending_Card').attr('loaded', true);
				var Type = $('#PendingFileType').val() ? $('#PendingFileType').val() : 'PendingChart';

				if(Type=='PendingTable'){
					fn_Array[7] = fetchPendingCounts(WorkflowModuleUID,adv_fromDate,adv_toDate);
				}else{
					fn_Array[7] = GetPendingChart(WorkflowModuleUID,adv_fromDate,adv_toDate);
				}
				//fn_Array[7] = fetchPendingCounts(WorkflowModuleUID,adv_fromDate,adv_toDate);
			}
		}


		if ($(window).scrollTop() + $(window).height() >= $('#TATAging_Card').offset().top+350) {
			var pending_load = ($('#TATAging_Card').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#TATAging_Card').attr('loaded', true);
				$("#TATAgingDrop").select2().select2("val", 'TATAgingChart');
				//fn_Array[8] = TATAgingCounts(adv_period,adv_fromDate,adv_toDate,WorkflowModuleUID);

				var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
				var adv_period = $('#adv_period').val();
				var adv_fromDate = $('#adv_fromDate').val();
				var adv_toDate = $('#adv_toDate').val();
				
				fn_Array[8] = TAT_AgingChart(WorkflowModuleUID,adv_fromDate,adv_toDate);

			}
		}

		if ($(window).scrollTop() + $(window).height() >= $('#FollowUpcard').offset().top+350) {
			var pending_load = ($('#FollowUpcard').attr('loaded') == 'true');
			if(!pending_load) {
				//not in ajax.success due to multiple sroll events
				$('#FollowUpcard').attr('loaded', true);
				var Type = $('#FollowUpType').val();
				if(Type=='ChartIndividual' || Type=='ChartPeriodic'){
					var Type=Type.split('Chart');console.log(Type[1]);
					var FollowUpType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[2] = ProcessFollowUpChart(adv_fromDate,adv_toDate,WorkflowModuleUID,FollowUpType);
				}else if(Type=='TablePeriodic' || Type=='TableIndividual'){
					var Type=Type.split('Table');console.log(Type[1]);
					var FollowUpType=(Type[1]=='Periodic') ? 'Team' : 'Individual';
					fn_Array[2] = ProcessFollowUpReport(adv_fromDate,adv_toDate,WorkflowModuleUID,FollowUpType);
				}else{
					// fn_Array[2] = ProcessFollowUpChart(adv_fromDate,adv_toDate,WorkflowModuleUID);
					fn_Array[2] = ProcessFollowUpReport(adv_fromDate,adv_toDate,WorkflowModuleUID);
				}
				
			}
		}

		Promise.all(fn_Array).then(function (value) {
			removebodyspinner();
		});
	})

	//init functions
	$(window).scroll();


	$(window).on('load',function () {

		//Filter change event
		$(document).off("dp.change", "#adv_fromDate").on("dp.change", "#adv_fromDate", function (e) {
			//init charts
			var date=$(".DateRange").val();
			var thisDate=$(this).val();
			if(date!=thisDate){
				$("#adv_period option[value=Custom]").show();
				$('#adv_period').val('Custom');
	    		$("#adv_period").select2();
			}
			$(".DateRange").val(thisDate);
			$('.card').removeAttr('loaded');    

			$(window).scroll();
		});

		$(document).off("dp.change", "#adv_toDate").on("dp.change", "#adv_toDate", function (e) {
			//init charts
			var date=$(".DateRangeTo").val();
			var thisDate=$(this).val();
			if(date!=thisDate){
				$("#adv_period option[value=Custom]").show();
				$('#adv_period').val('Custom');
	    		$("#adv_period").select2();
			}
			$('.card').removeAttr('loaded');    
			$(".DateRangeTo").val(thisDate);
			$(window).scroll();
		});

		$(document).off("change", "#adv_WorkflowModuleUID").on("change", "#adv_WorkflowModuleUID", function (e) {
			//init charts
			$('.card').removeAttr('loaded');    

			$(window).scroll();
		});

		//fetch from date and to date
		$(document).off("change", "#adv_period").on("change", "#adv_period", function (e) {
			e.preventDefault();
			//init charts
			$("#adv_period option[value=Custom]").hide();
			$('.card').removeAttr('loaded');    
			var period = $(this).val();
			getDate(period);
		});

	})

	//Pending files count
	function fetchPendingCounts(WorkflowModuleUID,adv_fromDate,adv_toDate,filterlist='',QueueUID='')
	{
		var WorkflowModuleUID = WorkflowModuleUID;
		var filterlist = '#completedorderslist';

		$.ajax({
			url: base_url+'Dashboard/widgetGetPendingOrdersCount',
			type: 'POST',
			dataType: 'json',
			data: {'WorkflowModuleUID':WorkflowModuleUID,'filterlist':filterlist,'QueueUID': QueueUID,'FromDate':adv_fromDate,'ToDate':adv_toDate},
			beforeSend: function(){

			},
		})
		.done(function(response) {
			$('.TablePendingReport').show();
			$('.PendingChart').hide();
			$('.TablePendingReport').html(response);
		})
		.fail(function(jqXHR) {
			console.error("error", jqXHR);
		});
	}


	//change inflow chart periodic and individual
	$(document).on('change','.InflowType',function(){
		var type=($(this).val());
		
		$('#InflowType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#InflowType').val() ? $('#InflowType').val() : 'Team';

		if(Type=='TablePeriodic' || Type=='ChartPeriodic'){
			var Type='Team';
		}else{
			var Type='Individual';
		}

		if(type=='TablePeriodic' || type=='TableIndividual'){
			//$('#InflowChart').val('');
			ProcessinflowReport(FromDate,ToDate,WorkflowModuleUID,Type)
		}else{
			//$('#InflowTable').val('');
			ProcessinflowChart(FromDate,ToDate,WorkflowModuleUID,Type);
		}
		

	});

	//change FollowUp chart periodic and individual
	$(document).on('change','.FollowUpType',function(){
		var type=($(this).val());
		
		$('#FollowUpType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#FollowUpType').val() ? $('#FollowUpType').val() : 'Team';

		if(Type=='TablePeriodic' || Type=='ChartPeriodic'){
			var Type='Team';
		}else{
			var Type='Individual';
		}

		if(type=='TablePeriodic' || type=='TableIndividual'){
			//$('#FollowUpChart').val('');
			ProcessFollowUpReport(FromDate,ToDate,WorkflowModuleUID,Type)
		}else{
			//$('#FollowUpTable').val('');
			ProcessFollowUpChart(FromDate,ToDate,WorkflowModuleUID,Type);
		}
		

	});



//SuccessQueueType
/*
	$(document).on('change','.SuccessQueueType',function(){
		var type=($(this).val());
		
		$('#SuccessQueueType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#SuccessQueueType').val() ? $('#SuccessQueueType').val() : 'SuccessTablePeriodic';

		if(Type=='SuccessTablePeriodic' || Type=='SuccessChartPeriodic')
		{
			var Type='Team';
		}else{
			var Type='Individual';
		}

		if(type=='TablePeriodic' || type=='TableIndividual'){
			//$('#FollowUpChart').val('');
			ProcessFollowUpReport(FromDate,ToDate,WorkflowModuleUID,Type)
		}else{
			//$('#FollowUpTable').val('');
			ProcessFollowUpChart(FromDate,ToDate,WorkflowModuleUID,Type);
		}
		

	});
*/


	//change Success Queue chart
	//$(document).on('click','SuccessQueueChart',function(){
	$('#SuccessQueueChart').click(function () {

		var value = $("#SuccessQueueChart option:selected").val();
		if (value=="SuccessChartPeriodic")
		{
			//alert("Periodic");
			var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
			var Period = $('#adv_period').val();
			var FromDate = $('#adv_fromDate').val();
			var ToDate = $('#adv_toDate').val();
			Success_Queue_Chart(Period,FromDate,ToDate,WorkflowModuleUID);
		}
		else if (value=="SuccessChartIndividual")
		{

			var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
			var Period = $('#adv_period').val();
			var FromDate = $('#adv_fromDate').val();
			var ToDate = $('#adv_toDate').val();
			Success_Queue_Individual_Chart(Period,FromDate,ToDate,WorkflowModuleUID);

		}
		else if (value=="SuccessTablePeriodic")
		{
			var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
			var Period = $('#adv_period').val();
			var FromDate = $('#adv_fromDate').val();
			var ToDate = $('#adv_toDate').val();
			queueprogress_periodwise(Period,FromDate,ToDate,WorkflowModuleUID,adv_Target);

		}
		else if (value=="SuccessTableIndividual")
		{
			var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
			var Period = $('#adv_period').val();
			var FromDate = $('#adv_fromDate').val();
			var ToDate = $('#adv_toDate').val();
			queueprogress_individualwise(Period,FromDate,ToDate,WorkflowModuleUID,adv_Target);
			//queueprogress_periodwise(Period,FromDate,ToDate,WorkflowModuleUID,adv_Target);
		}

	});



/*
	//change Success Queue chart
	$(document).on('click','.SuccessQueueChart',function(){

		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		
		Success_Queue_Chart(Period,FromDate,ToDate,WorkflowModuleUID);

	});

*/
	function Success_Queue_Chart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{

		$.ajax({
			type: "POST",
			url: 'Dashboard/queueProgress_periodcounts_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.TableDiv').hide();
				$('#chart').show();

				if (data)
				{
		            	var chart = c3.generate({
		                data: {
		                    columns: [
		                        //data["inflow"],
		                        data["complete"],
		                        data["pending"],
		                        data["ratio"]
		                              ],
		                    type: 'bar',
		                    types: {
		                        ratio: 'spline',
		                    },
		                    colors: { pending: "#f87c7c", ratio: "#0cc2aa", complete: "#6c89d3" }
		                },
					axis: {
					    x: {
					      type: 'category',
					      categories: data["Date"]
					    }
					  }
		            });

				}
				else
				{

					$('#chart').removeAttr('style');
					$('#chart').append('<div class="" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');


				}




/*
            c3.generate({
                bindto: "#chart",
                data: {
                    columns: [
                        ["SonyVaio", 30, 20, 50, 40, 60, 50],
                        ["iMacs", 200, 130, 90, 240, 130, 220],
                        ["Tablets", 300, 200, 160, 400, 250, 250],
                        ["iPhones", 200, 130, 90, 240, 130, 220],
                        ["Macbooks", 130, 120, 150, 140, 160, 150],
                    ],
                    types: { SonyVaio: "bar", iMacs: "bar", Tablets: "spline", iPhones: "line", Macbooks: "bar" },
                    colors: { SonyVaio: "#6f4343", iMacs: "#ebeff2", Tablets: "#0cc2aa", iPhones: "#6c89d3", Macbooks: "#27ade9" },
                    groups: [["SonyVaio", "iMacs"]],
                },
                axis: { x: { type: "categorized" } },
            });
	
            	var chart = c3.generate({
                data: {
                    columns: [
                        //data["inflow"],
                        data["pending"],
                        data["complete"],
                        data["ratio"]
                              ],
                    //types: { data["pending"]: "bar", data["complete"]: "bar", data["ratio"]: "line" },

                    type: 'bar',
                    types: {
                        ratio: 'line',
                    },
                    colors: { pending: "#f87c7c", ratio: "#ebeff2", complete: "#6c89d3" },
				axis: {
				    x: {
				      type: 'category',
				      categories: [ 'Date' ]
				    }
				  }
				}
            });
*/


			},
			error: function (jqXHR) {
			}
		});
	}

//Success_Queue_Individual_Chart
	function Success_Queue_Individual_Chart(adv_period,adv_fromDate,adv_toDate,WorkFlows,adv_Target)
	{
		$.ajax({
			type: "POST",
			url: 'Dashboard/queueProgress_individualcounts_Chart',
			data: {WorkFlows:WorkFlows, adv_period:adv_period, adv_fromDate:adv_fromDate, adv_toDate:adv_toDate},
			dataType: 'JSON',
			success: function(data)
			{
				console.log(data);
				//alert(data);
				//$('.TableDiv').html(data.Result);
				$('.TableDiv').hide();
				$('#chart').show();

            	var chart = c3.generate({
                data: {
                    columns: [
                        //data["inflow"],
                        data["complete"],
                        data["pending"],
                        data["ratio"]
                              ],
                    type: 'bar',
                    types: {
                        ratio: 'spline',
                    },
                    colors: { pending: "#f87c7c", ratio: "#0cc2aa", complete: "#6c89d3" }
                },
			axis: {
			    x: {
			      type: 'category',
			      tick: {
                	rotate: 75,
                	multiline: false
            		},
            		height:130,
			      	categories: data["UserName"]
			    }
			  }
            });


			},
			error: function (jqXHR) {
			}
		});
	}







	//change inflow chart periodic and individual
	$(document).on('click','.InflowChart',function(){
		var type=($(this).attr('data-type'));
		if(type=='Team'){
			$('#Team').hide();
			$('#Individual').show();
		}else{
			$('#Team').show();
			$('#Individual').hide();
		}
		$('#InflowChart').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#InflowChart').val() ? $('#InflowChart').val() : 'Team';
		
		ProcessinflowChart(FromDate,ToDate,WorkflowModuleUID,Type);

	});

	//change FollowUp chart periodic and individual
	$(document).on('click','.FollowUpChart',function(){
		var type=($(this).attr('data-type'));
		if(type=='Team'){
			$('#Team').hide();
			$('#Individual').show();
		}else{
			$('#Team').show();
			$('#Individual').hide();
		}
		$('#FollowUpChart').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#FollowUpChart').val() ? $('#FollowUpChart').val() : 'Team';
		
		ProcessFollowUpChart(FromDate,ToDate,WorkflowModuleUID,Type);

	});

	//get inflow report 
	function ProcessinflowChart(FromDate,ToDate,WorkflowModuleUID,inflowType='Team')
	{   
		var formData = ({'Type': inflowType,'FromDate' : FromDate,'ToDate':ToDate,'WorkflowModuleUID':WorkflowModuleUID,'Status':'Pending'});
		$.ajax({
			type: "POST",
			url: 'Dashboard/getInflowProcessChart',
			data: formData,
			dataType: 'JSON',
			beforeSend: function()
			{
				//addcardspinner($('#ProcessInflow'));
			},
			success: function(data)
			{
				$('.TableProcessInflow').hide();
				$('.ChartDiv').show();
				$('.ChartDiv').attr('style','height:300px;');
				$('.ChartRecord').remove();
				$('#myChart').remove();
				if(data){

					$('.ChartDiv').append('<canvas id="myChart"></canvas>');
				
					var ctx = document.getElementById("myChart").getContext('2d');

					var gradientGreen = ctx.createLinearGradient(0, 0, 0, 450);
					gradientGreen.addColorStop(0, 'rgba(43,118,8, 10)');
					gradientGreen.addColorStop(1, 'rgba(67,160,71, 0.7)');

					var gradientBlue = ctx.createLinearGradient(0, 0, 0, 450);
					gradientBlue.addColorStop(0, 'rgba(18,124,276, 10)');
					gradientBlue.addColorStop(1, 'rgba(3,169,244, 0.6)');

					var myChart = new Chart(ctx, {
					    type: 'line',
					    data: {
					        labels: data['Label'],
					        datasets: [{
					            label: 'FHA', // Name the series
					            data: data['FHA'], // Specify the data values array
					            fill: true,
					            borderColor: '#0E63A7', // Add custom color border (Line)
					            backgroundColor: gradientBlue, // Add custom color background (Points and Fill)
					            borderWidth: 1 // Specify bar border width
					        },
					        {
					            label: 'VA', // Name the series
					            data: data['VA'], // Specify the data values array
					            fill: true,
					            borderColor: '#096629', // Add custom color border (Line)
					            backgroundColor: gradientGreen, // Add custom color background (Points and Fill)
					            borderWidth: 1 // Specify bar border width
					        }]
					    },
					    options: {
					      responsive: true, // Instruct chart js to respond nicely.
					      maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
					      animation: {
								easing: 'easeInOutQuad',
								duration: 520
							},
							scales: {
	    						yAxes: [{
	    							ticks: {
	    								padding: 20,
	    							}
	    						}],
	    						xAxes: [{ 
	    							ticks: {
	    								beginAtZero: false, 
	    								minRotation: 0
	    							}
	    						}]
	    					}
					    }
					});
				}else{
					$('.ChartDiv').removeAttr('style');
					$('.ChartDiv').append('<div class="ChartRecord" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			}
		});
	}

	//get FollowUp report 
	function ProcessFollowUpChart(FromDate,ToDate,WorkflowModuleUID,FollowUpType='Team')
	{   
		var formData = ({'Type': FollowUpType,'FromDate' : FromDate,'ToDate':ToDate,'WorkflowModuleUID':WorkflowModuleUID,'Status':'Pending'});
		$.ajax({
			type: "POST",
			url: 'Dashboard/getFollowUpProcessChart',
			data: formData,
			dataType: 'JSON',
			beforeSend: function()
			{
				//addcardspinner($('#ProcessFollowUp'));
			},
			success: function(data)
			{
				$('.TableProcessFollowUp').hide();
				$('.FollowUpChartDiv').show();
				$('.FollowUpChartDiv').attr('style','height:300px;');
				$('.ChartRecord').remove();
				$('#FollowUpmyChart').remove();
				if(data){

					$('.FollowUpChartDiv').append('<div id="FollowUpmyChart"  style="height: 300px;"></div>');
				
					// Themes begin
					am4core.useTheme(am4themes_animated);
					// Themes end

					// Create chart instance
					var chart = am4core.create("FollowUpmyChart", am4charts.XYChart);

					// Add data
					chart.data = data;
					// Create axes
					var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
					categoryAxis.dataFields.category = "Label";
					categoryAxis.renderer.grid.template.location = 0;
					categoryAxis.renderer.minGridDistance = 30;
					categoryAxis.renderer.labels.template.horizontalCenter = "right";
					categoryAxis.renderer.labels.template.verticalCenter = "middle";
					categoryAxis.renderer.labels.template.rotation = -45;
					categoryAxis.tooltip.disabled = true;
					categoryAxis.renderer.minHeight = 110;

					var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
					valueAxis.renderer.minWidth = 50;

					// Create series
					var series = chart.series.push(new am4charts.ColumnSeries());
					series.sequencedInterpolation = true;
					series.dataFields.valueY = "Total";
					series.dataFields.categoryX = "Label";
					series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
					series.columns.template.strokeWidth = 0;
					//series.columns.template.propertyFields.fill = "red";

					series.tooltip.pointerOrientation = "vertical";

					series.columns.template.column.cornerRadiusTopLeft = 10;
					series.columns.template.column.cornerRadiusTopRight = 10;
					series.columns.template.column.fillOpacity = 0.8;

					// on hover, make corner radiuses bigger
					var hoverState = series.columns.template.column.states.create("hover");
					hoverState.properties.cornerRadiusTopLeft = 0;
					hoverState.properties.cornerRadiusTopRight = 0;
					hoverState.properties.fillOpacity = 1;

					series.heatRules.push({
					  "target": series.columns.template,
					  "property": "fill",
					  "min": am4core.color("#33A361"),
					  "max": am4core.color("#33A361"),
					  "dataField": "valueY"
					});
					// Cursor
					chart.cursor = new am4charts.XYCursor();
					chart.cursor.lineY.disabled = true;
					chart.cursor.lineX.disabled = true;
					$("g[aria-labelledby]").hide();

				}else{
					$('.FollowUpChartDiv').removeAttr('style');
					$('.FollowUpChartDiv').append('<div class="ChartRecord" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			}
		});
	}

	//Get productivity Chart
	function GetProductivityChart(Period,FromDate,ToDate,WorkflowModuleUID,Target=10,Type='Team'){
		var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Target':Target,'WorkflowModuleUID':WorkflowModuleUID,'Type':Type,'adv_period':Period});
		console.log(formData);
		$.ajax({
			type: "POST",
			url: 'Dashboard/getProductivityChart',
			data: formData,
			dataType: 'JSON',
			beforeSend: function()
			{
				//addcardspinner($('#ProcessInflow'));
			},
			success: function(data)
			{
				console.log(data);
				$('.TableInflow').hide();
				$('#Workflow-Userslist').hide();
				$('.productivityChart').show();
				$('.productivityChart').attr('style','height: 300px;');
				$('.ProductivityChartRecord').remove();
				$('#myChartMixed').remove();
				if(data){
					$('.productivityChart').append('<canvas id="myChartMixed"></canvas>');

					var ctx = document.getElementById("myChartMixed").getContext('2d');

					var mixedChart = new Chart(ctx, {
					    type: 'bar',
					    data: {
					        labels: data['Label'],
					        datasets: [{
								type: 'line',
								label: 'Productivity',
								borderColor: '#136ED4',
								borderWidth: 2,
								fill: false,
								data: data['Productivity']
							}, {
								type: 'bar',
								label: 'Completed',
								backgroundColor: '#3CB371',
								data: data['CompletedOrders'],
								borderColor: 'white',
								borderWidth: 2
							}]
					    },
					    options: {
					      responsive: true, // Instruct chart js to respond nicely.
					      maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
					      animation: {
								easing: 'easeInOutQuad',
								duration: 520
							},
							scales:{
					            xAxes: [{
					              ticks: {autoSkip: false, maxRotation: 20, minRotation: 0, beginAtZero: true}
					            }],
					          yAxes: [{
					            ticks: {autoSkip: false, maxRotation: 20, minRotation: 0, beginAtZero: true}
					          }]
								  }
					    }
					});
				}else{
					$('.productivityChart').removeAttr('style');
					$('.productivityChart').append('<div class="ProductivityChartRecord" style="padding-top: 30px;"><p style="text-align:center;">No Records found</p></div>');
				}

			}
		});

	}


	//Get Pending Files chart
		function GetPendingChart(WorkflowModuleUID,adv_fromDate,adv_toDate,filterlist='',QueueUID=''){
		$.ajax({
			type: "POST",
			url: 'Dashboard/getPendingChart',
			data: {'WorkflowModuleUID':WorkflowModuleUID,'filterlist':'#completedorderslist','QueueUID': '','FromDate':adv_fromDate,'ToDate':adv_toDate},
			success: function(result)
			{ 
				$('.TablePendingReport').hide();
				$('.PendingChart').show();
				$('#chartdiv').remove();
				
				$('.PendingChart').append('<div class="" style="height:320px" id="chartdiv"></div>');

				var chartData=result;console.log(chartData);
				// Themes begin
				am4core.useTheme(am4themes_animated);
				// Themes end

				// Create chart instance
				var chart = am4core.create("chartdiv", am4charts.PieChart);

				// Add data
				chart.data = JSON.parse(chartData);

				// Add and configure Series
				var pieSeries = chart.series.push(new am4charts.PieSeries());
				pieSeries.dataFields.value = "value";
				pieSeries.dataFields.category = "name";
				pieSeries.slices.template.stroke = am4core.color("#fff");
				pieSeries.slices.template.strokeOpacity = 1;

				// This creates initial animation
				pieSeries.hiddenState.properties.opacity = 1;
				pieSeries.hiddenState.properties.endAngle = -90;
				pieSeries.hiddenState.properties.startAngle = -90;

				// Add legend
				chart.legend = new am4charts.Legend();
				chart.hiddenState.properties.radius = am4core.percent(0);
				$("g[aria-labelledby]").hide();
			}
		});
 	
	}


	//change Pending File chart and table
	$(document).on('change','.PendingFileType',function(){
		var type=($(this).val());
		
		$('#PendingFileType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#PendingFileType').val() ? $('#PendingFileType').val() : 'PendingChart';

		if(Type=='PendingTable'){
			fetchPendingCounts(WorkflowModuleUID,FromDate,ToDate);
		}else{
			GetPendingChart(WorkflowModuleUID,FromDate,ToDate);
		}

	});

	//change gatekeeping chart and table
	$(document).on('change','.GatekeepingType',function(){
		var type=($(this).val());
		
		$('#GatekeepingType').val(type);
		var WorkflowModuleUID = $("#adv_WorkflowModuleUID option:selected").val();;
		var Period = $('#adv_period').val();
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Type = $('#GatekeepingType').val() ? $('#GatekeepingType').val() : 'TotalReviewedChart';

		if(Type=='AgingTable'){
			pipeLine_AgingFileWiseRepo(Period,FromDate,ToDate,WorkflowModuleUID,adv_Target=10);
		}else if(Type=='TotalReviewedTable'){
			Pipleline_reviewCountReport(Period,FromDate,ToDate,WorkflowModuleUID);
		}else if(Type=='AgingChart'){
			GetGatekeepingAgingChart(FromDate,ToDate,WorkflowModuleUID);
		}else{
			GetReviewCountChart(FromDate,ToDate,WorkflowModuleUID);
		}

	});

	//Gatekeeping total review chart	GetReviewCountChart();
	function GetReviewCountChart(adv_fromDate,adv_toDate,WorkFlows){
		var formData = ({'adv_fromDate' : adv_fromDate,'adv_toDate':adv_toDate,'WorkFlows':WorkFlows});
		$.ajax({
			type: "POST",
			url: 'Dashboard/Pipeline_reviewCountChart',
			data: formData,
			success: function(data)
			{ 
				$('.PipeLineTableDiv').hide();
				$('.PipeLineAgingChart').hide();
				$('.PipeLineChart').show();
				$('#doughnut-chart').remove();
				
				$('.PipeLineChart').append('<canvas id="doughnut-chart" style="height:320px" ></canvas>');

				var ChartData=(JSON.parse(data));
				new Chart(document.getElementById("doughnut-chart"), {
			    type: 'doughnut',
			    data: {
			      labels: ChartData["label"],
			      datasets: [
			        {
			          label: "Population (millions)",
			          backgroundColor: ["#3e95cd", "#8e5ea2"],
			          data: ChartData["value"]
			        }
			      ]
			    },
			    options: {
			      responsive: true, // Instruct chart js to respond nicely.
			      maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
			      animation: {
						easing: 'easeInOutQuad',
						duration: 520
					},
				    tooltips: {
				      callbacks: {
				        label: function(tooltipItem, data) {
				        	var dataset = data.datasets[tooltipItem.datasetIndex];
				          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
				            return parseInt(previousValue) + parseInt(currentValue);
				          });
				          var currentValue = dataset.data[tooltipItem.index];//alert(currentValue);
				          var percentage = (((currentValue/total) * 100)).toFixed(2);      
				            
				          return currentValue+'( '+percentage + "% )";
				        },
					      title: function(tooltipItem, data) {
					        return data.labels[tooltipItem[0].index];
					      }
				      }
				    }
			    }
			});
			}
		});
	}

	//Get gatekeeping aging chart
	function GetGatekeepingAgingChart(adv_fromDate,adv_toDate,WorkFlows){
		var formData = ({'WorkFlows':WorkFlows, 'adv_fromDate':adv_fromDate, 'adv_toDate':adv_toDate});
		$.ajax({
			type: "POST",
			url: 'Dashboard/PipelineAgingChart',
			data: formData,
			success: function(data)
			{ 
				$('.PipeLineTableDiv').hide();
				$('.PipeLineAgingChart').show();
				$('.PipeLineChart').hide();
				$('#GatekeepingAgingChart').remove();
				
				$('.PipeLineAgingChart').append('<div id="GatekeepingAgingChart" style="height:320px" ></div>');

				// Themes begin
				am4core.useTheme(am4themes_animated);
				// Themes end

				// Create chart instance
				var chart = am4core.create("GatekeepingAgingChart", am4charts.XYChart);


				// Add data
				chart.data = JSON.parse(data);;

				// Create axes
				var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
				categoryAxis.dataFields.category = "category";
				categoryAxis.renderer.grid.template.location = 0;
				categoryAxis.renderer.minGridDistance = 30;

				var label = categoryAxis.renderer.labels.template;
				label.truncate = true;
				label.maxWidth = 120;
				
				var  valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
				valueAxis.min = 0;
				categoryAxis.events.on("sizechanged", function(ev) {
				var axis = ev.target;
				  var cellWidth = axis.pixelWidth / (axis.endIndex - axis.startIndex);
				  if (cellWidth < axis.renderer.labels.template.maxWidth) {
				    axis.renderer.labels.template.rotation = -45;
				    axis.renderer.labels.template.horizontalCenter = "right";
				    axis.renderer.labels.template.verticalCenter = "middle";
				  }
				  else {
				    axis.renderer.labels.template.rotation = 0;
				    axis.renderer.labels.template.horizontalCenter = "middle";
				    axis.renderer.labels.template.verticalCenter = "top";
				  }
				});

				// Create series
				function createSeries(field, name) {
				  
				  // Set up series
				  var series = chart.series.push(new am4charts.ColumnSeries());
				  series.name = name;
				  series.dataFields.valueY = field;
				  series.dataFields.categoryX = "category";
				  series.sequencedInterpolation = true;
				  
				  // Make it stacked
				  series.stacked = true;
				  
				  // Configure columns
				  series.columns.template.width = am4core.percent(60);
				  series.columns.template.tooltipText = "[bold]{name}[/]\n[font-size:14px]{categoryX}: {valueY}";
				  
				  // Add label
				  // var labelBullet = series.bullets.push(new am4charts.LabelBullet());
				  // labelBullet.label.text = "{valueY}";
				  // labelBullet.locationY = 0.5;
				  // labelBullet.label.hideOversized = true;
				  
				  return series;
				}

				createSeries("5-10 Days", "5-10 Days");
				createSeries("15-20 Days", "15-20 Days");
				createSeries("25-30 Days", "25-30 Days");
				createSeries("Followup Past Due", "Followup Past Due");
				createSeries("Due Today", "Due Today");

				// Legend
				chart.legend = new am4charts.Legend();
				$("g[aria-labelledby]").hide();
			}
		});
	}


	//Change from and to date
	//Filter change event
	// $(document).on("click", ".checklistdatepicker", function () {
	// 	//init Custom
	// 	alert();
	// 	$("#adv_period option[value=Custom]").show();
	// 	$('#adv_period').val('Custom');
	// 	$("#adv_period").select2();
	// });
});