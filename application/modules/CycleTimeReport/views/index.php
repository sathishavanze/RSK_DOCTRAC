
<style>
	th, td { text-align: center; }
	.bold{ font-weight: bold;  }
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-20 cyclereportdiv" id="card_themeid">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-file-check"></i>
		</div>
		<div class="row">
			<div class="col-md-6">
				<h4 class="card-title">CycleTime Report</h4>
			</div>
		</div>
	</div>
	<div class="text-right"> 
		<i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
		<!-- <i class="fa fa-file-excel-o excelorderlist" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i> -->
	</div>

	<!-- HEADER -->
	<?php $this->load->view('cycletimereportheader'); ?>
	<!-- HEADER -->


	<!-- Filter  -->
	<?php $this->load->view('cycletimereportfilter'); ?>
	<!-- Filter  -->
	


	<div class="col-md-12 TableDiv">


	</div>

</div>
<!-- Orders Table -->
            <?php $this->load->view('InflowReport/CycleReportCountListView'); ?>
            <!-- Orders Table -->
            <script src="assets/js/app/cyclereport.js?reload=1.0.1"></script>

<script type="text/javascript" src="assets/lib/fselect/fSelect.js"></script>

<script type="text/javascript">
	var fndatatable;
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
	$(function() {
		$(".select2picker").select2({
			theme: "bootstrap",
		});
		create_fselect();
	});

	$("#advancedFilterForReport").show();
	$('.fa-filter').click(function(){
		$("#advancedFilterForReport").slideToggle();
	});
	
	$(document).off('click','.filterreport').on('click','.filterreport',function()
	{
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Status = $('#adv_Status').val();
		var LoanNo = $('#adv_LoanNo').val();
		var Processor = $('#adv_Process').val();
		var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();


		var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status,'LoanNo':LoanNo,'Processor':Processor,'WorkflowModuleUID':WorkflowModuleUID});
		ProcessReport(formData);


		return false;
	});

	$(document).off('click','.reset').on('click','.reset',function(){
		var period = 'month';
		$("#adv_period").val(period);
		getDate(period);
		$("#adv_Status").val('Pending');
		$("#adv_period").select2();
		$("#adv_Status").select2();
		$('.listtable').hide();
	});



	function ProcessReport(formData)  
	{   
		var reporttype = $('.cycletimetab .active').attr('data-reporttype');

		var url = 'CycleTimeReport/getOverallTable';
		var ReportName = 'Cycle Time Report';
		if(reporttype == "substatus") {
			url = 'CycleTimeReport/getQueueTable';
			ReportName = 'Cycle Time Report - Sub Status';
		}else if(reporttype == "agent") {
			url = 'CycleTimeReport/getAgentTable';          
			ReportName = 'Cycle Time Report - Agent';
		} else if(reporttype == "substatusagent") {
			url = 'CycleTimeReport/getAgentQueueTable';          
			ReportName = 'Cycle Time Report - Sub Status Agent';
		} 
		$.ajax({
			type: "POST",
			url: url,
			data: formData,
			dataType: 'JSON',
			beforeSend: function()
			{
				addcardspinner($('#card_themeid'));
			},
			success: function(data)
			{
				$('.TableDiv').html(data);
				fn_commondatatable('.listtable',ReportName);
				removecardspinner($('#card_themeid'));
			}
		});
	}


	$(document).off('click','.excelorderlist').on('click','.excelorderlist',function(){
		var FromDate = $('#adv_fromDate').val();
		var ToDate = $('#adv_toDate').val();
		var Status = $('#adv_Status').val();
		var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status});
		var filename = 'CycleTimeReport.xlsx';
		$.ajax({
			type: "POST",
			url:'<?php echo base_url();?>CycleTimeReport/WriteOrdersExcel',
			xhrFields: {
				responseType: 'blob',
			},
			data: formData,
			beforeSend: function(){
				addcardspinner($('#card_themeid'));
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
				removecardspinner($('#card_themeid'));
			},
			error: function (jqXHR, textStatus, errorThrown) {

				console.log(jqXHR);


			},
			failure: function (jqXHR, textStatus, errorThrown) {

				console.log(errorThrown);

			},
		});

	});


	$('#adv_period').change(function()
	{
		var period = $(this).val();
		getDate(period);
	});

	function getDate(period)
	{      
		if(period == 'today')
		{
			$('#adv_fromDate').val("<?php echo date('m/d/Y') ?>");
			$('#adv_toDate').val("<?php echo date('m/d/Y') ?>");
		}
		else
		{
			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>CycleTimeReport/getFromToDate',
				data: {'period':period},
				dataType: 'JSON',
				success: function(data)
				{
					$('#adv_fromDate').val(data.fromDate);
					$('#adv_toDate').val(data.toDate);
				}
			});
		}
	}


	$('#adv_fromDate').focusout(function()
	{
		$('#adv_period').val('<option></option>');
		$("#adv_period").select2();
	});

	$('#adv_toDate').focusout(function()
	{
		$('#adv_period').val('<option></option>');
		$("#adv_period").select2();
	});

</script>



