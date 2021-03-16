
<link href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/amchart/export.css" type="text/css" media="all" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/css/bootstrap-multiselect.css"   type="text/css" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/css/awesome-bootstrap-checkbox.css"   type="text/css" />
<link  rel="stylesheet" href="<?php echo base_url();?>assets/css/dashboard.css"   type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/css/tooltipster.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/js/jquery.tooltipster.min.js"></script>
<style>
.downloadbtndiv
{
	position: absolute;
	 z-index: 99;
    margin-left: 120px;
}
.downloadbtndiv1
{
	position: absolute;
	 z-index: 99;
    margin-left: 120px;
}
.pad-tb20
{
	    padding-top: 20px;
    padding-bottom: 20px;

}
.btn-download
{
    background-color: #f44336;
}
.btn-download:active,.btn-download:focus,.btn-download:active:focus {
    color: #fff;
    background-color: #919191;
    border-color: #595959;
}
.btn-download:hover
{
	background-color: #f44336;
}
.btn-download.focus, .btn-download:focus {
    color: #fff;
    background-color: #f44336;
    border-color: #f44336;
}
.dashboardview {
    margin-top: 15px;
}

.table-title-heading{
	padding: 10px;
	margin-top: -15px !important;
}
</style>

<div class="col-md-12 dashboardview">
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="card card-stats hover-widget">
				<div class="card-header card-header-warning card-header-icon">
					<div class="card-icon">
						<i class="icon-server"></i>
					</div>
					<p class="card-category">TOTAL ORDERS </p>
					<h3 class="card-title totalcount count" data-file="Total" onclick="dashboardCounts('totalorders')" style="cursor: pointer;">0</h3>
				</div>
				<div class="card-footer">
					<div class="stats">			
						<!-- <i class="icon-lab text-danger"></i> -->
						Total Orders
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="card card-stats hover-widget">
				<div class="card-header card-header-rose card-header-icon">
					<div class="card-icon">
						<i class="icon-hour-glass2"></i>
					</div>
					<p class="card-category">PENDING </p>
					<h3 class="card-title pendingcount count" data-file="Pending" onclick="dashboardCounts('pendingorders')" style="cursor: pointer;">0</h3>
				</div>
				<div class="card-footer">
					<div class="stats">
						<!-- <i class="icon-lab"></i> -->
						Total Pending Orders
					</div>
				</div>
			</div>
		</div>
		<?php $MenuLinks = $this->Common_Model->getDynamicLeftMenu('Workflow'); $fieldName='';?>
		<?php foreach ($MenuLinks as $key => $value): ?>
			<?php if ($value->FieldName == 'Exception'):
				 $fieldName=$value->FieldName;
			 endif ?>
		<?php endforeach ?>
	
		
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="card card-stats hover-widget">
				<div class="card-header card-header-danger card-header-icon">
					<div class="card-icon">
						<i class="icon-reload-alt"></i>
					</div>
					<p class="card-category">DOC CHASE </p>
					<h3 class="card-title dochcasecount count" data-file="DocChase" onclick="dashboardCounts('docchaseorders')" style="cursor: pointer;">0</h3>
				</div>
				<div class="card-footer">
					<div class="stats">
						<!-- <i class="icon-lab"></i>  -->
						Doc Chase Orders
					</div>
				</div>
			</div>
		</div>

		
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="card card-stats hover-widget">
				<div class="card-header card-header-info card-header-icon">
					<div class="card-icon">
						<i class="icon-file-check"></i>
					</div>
					<p class="card-category">COMPLETED </p>
					<h3 class="card-title completedcount count" data-file="Complete" onclick="dashboardCounts('completedorders')" style="cursor: pointer;">0</h3>
				</div>
				<div class="card-footer">
					<div class="stats">
						<!-- <i class="icon-lab"></i> -->
						Completed
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">   
		<div class="col-md-12">   
			<div class="card mt-10" id="OrderStatistics_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Order Statistics
						<i class="icon-stats-growth"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="col-md-12 text-right"> 
							<button class="btn btn-link btn-warning btn-xs filterbtn" id="orderstatisticsfilter"><i class="icon-filter3"></i></button> 
							<button id="OrderStatistics_refresh" class="btn btn-default btn-xs btn-link refresh-btn" ><i class="icon-sync"></i></button>
						</div>
					</div>
				</div>
				<div class="card-body pd-0 dd">
					<div class="col-md-12 filterdiv mb-20" style="display: none;">
						<?php $this->load->view('filter'); ?>
					</div>


					<div class="col-md-12">
						<div class="row">
							<div class="col-md-4">
								<div class="col-md-12 mt-20 pd-0">
                  <div class="row borderseparator">
										<div class="col-2">
											<p class="orderspan orderyellow"><i class="icon-server"></i></p>
										</div>
										<div class="col-6">
											<p>Total Orders</p>
										</div>
										<div class="col-4">
											<a href="javascript:void(0);" class="totalcount count" onclick="dashboardCounts('totalorders')" data-file="Total">0</a>
										</div>
									</div>     
                  <?php 
                  $class = ['ordergreen','orderblue','orderorange','orderblue','orderred','orderyellow','orderorange','ordergreen','orderblue','orderorange'];
                  $icons = ['icon-display','icon-drawer','icon-stack','icon-briefcase','icon-user-tie','icon-profile','icon-office','icon-pen','icon-calendar','icon-exit'];
                  $prevwrk = 0;
                  foreach ($workflow as $key => $value) 
                  { 
                    echo '<div class="row borderseparator">
                      <div class="col-2">
                        <p class="orderspan '.$class[$key].'"><i class="'.$icons[$key].'"></i></p>
                      </div>
                      <div class="col-6">
                        <p>'.$value->WorkflowModuleName.'</p>
                      </div>
                      <div class="col-4">
                        <a href="javascript:void(0);" class="count WrkCount'.$value->WorkflowModuleUID.'" data-file="'.$value->WorkflowModuleName.'" onclick="dashboardCounts('.$value->WorkflowModuleUID.')">0</a>
                      </div>
                    </div>';   
                  }
                  ?>
                  <div class="row borderseparator">
                    <div class="col-2">
                      <p class="orderspan ordergreen"><i class="icon-checkmark4"></i></p>
                    </div>
                    <div class="col-6">
                      <p>Completed</p>
                    </div>
                    <div class="col-4">
                      <a href="javascript:void(0);" class="completedcount count" data-file="Completed" onclick="dashboardCounts('completedorders')">0</a>
                    </div>
                  </div>
                  <div class="row borderseparator">
                    <div class="col-2">
                      <p class="orderspan orderred"><i class="icon-cancel-circle2"></i></p>
                    </div>
                    <div class="col-6">
                      <p>Cancelled</p>
                    </div>
                    <div class="col-4">
                      <a href="javascript:void(0);" class="count cancelledcount" data-file="Cancelled" onclick="dashboardCounts('cancelledorders')">0</a>
                    </div>
                  </div>
								</div>

							</div>
							<div class="col-md-8 pd-0 mt-20">
								<div id="colouredBarsChart1" class="ct-chart" style="padding: 20px 0px;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">   
			<div class="card mt-10" id="OrdersDue_Card">
				<div class="card-header card-header-icon card-header-info">
					<div class="card-icon">
						Orders Due
						<i class="icon-pie-chart4"></i>
					</div> 
					<div class="row mt-10"> 
						<div class="col-md-12 text-right"> 				
							<button id="OrderDue_refresh" class="btn btn-default btn-xs btn-link refresh-btn"><i class="icon-sync"></i></button>
						</div>
					</div>
				</div>
				<div class="card-body pd-0"> 				
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6 mt-30">
								<div id="chartPreferences" class="ct-chart mb-20"></div>
							</div>
							<div class="col-md-6 mb-20">
								<h4>ORDERS DUE</h4>
								<div class="row mt-20">
									<div class="col-md-3">
										<span>Due Today</span>
									</div>
									<div class="col-md-6">
										<div class="mt-10">
											<div class="progress progress-line-default">
												<div class="progress-bar progress-bar-warning presentduelevel" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="40" style="width: 0%;">
													<span class="sr-only">0% Complete</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<span id="presentdue" class="count" data-file="PresentDue" onclick="dashboardCounts('presentdue')" style="cursor: pointer;"></span>
									</div>
								</div>

								<div class="row mt-10">
									<div class="col-md-3">
										<span>Past Due</span>
									</div>
									<div class="col-md-6">
										<div class="mt-10">
											<div class="progress progress-line-default">
												<div class="progress-bar progress-bar-danger pastduelevel" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="40" style="width: 0%;">
													<span class="sr-only">0% Complete</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<span id="pastdue" class="count" data-file="PastDue" onclick="dashboardCounts('pastdue')" style="cursor: pointer;"></span>
									</div>
								</div>

								<div class="row mt-10">
									<div class="col-md-3">
										<span>Future Due</span>
									</div>
									<div class="col-md-6">
										<div class="mt-10">
											<div class="progress progress-line-default">
												<div class="progress-bar progress-bar-info futureduelevel" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="40" style="width: 0%;">
													<span class="sr-only">0% Complete</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<span id="futuredue" class="count" data-file="FutureDue" onclick="dashboardCounts('futuredue')" style="cursor: pointer;"></span>
									</div>
								</div>	
								<div class="row">
									<div class="col-md-12">
										<h3 class="mb-0"> Total Orders in Due  : <span id="totaldueorders" class="count fweight700" data-file="TotalDue" onclick="dashboardCounts('totaldueorders')" style="cursor: pointer;"></span> </h3>
									</div>
								</div> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view('OrderAging.php'); ?>
 </div>

		<div class="col-md-12 pd-0 orderstable" style="display: none;">
			<input type="hidden" id="orderstablestatus" value="">
			<input type="hidden" id="orderstabledays" value="">

			<div class="row">
				<div class="card">
					<div class="card-header card-header-icon card-header-rose">
						<div class="card-icon title-heading table-title-heading hide" id="title">									

						</div> 					
						<div class="text-right"> 
							<i class="icon-close2 close" onclick="ChangePage();" style="font-size: 25px !important;color: #000;margin-top: 20px;"></i>
						</div>
					</div>

					<div class="col-md-12 pad-tb20">
						<!-- <div class="text-right">
							<i class="icon-close2 close" onclick="ChangePage();" style="font-size: 25px !important;color: #000;margin-top: 10pt;"></i>
						</div> -->
						<div class="downloadbtndiv">
							<button type="button" class="btn btn-download exceldownloadbtn">Excel</button>
						<button type="button" class="btn btn-download CSVdownloadbtn">CSV</button>
						</div>
						<div class="material-datatables" id="myordertable_parent">
								<table class="table table-striped display nowrap" id="myordertable">
									<thead>
										<tr>
											<th>Order No</th>
											<th>Client </th>
											<th>Project</th>
											<th>Current Status</th>
											<th>Property Address</th>
											<th>Property City</th>
											<th>Property State</th>
											<th>Zip Code</th>
											<th>OrderEntryDateTime</th>
											<th>OrderDueDateTime</th>
											<th class="no-sort">Actions</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
					</div>
				</div>
			</div>
		</div>

			<script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/chartist.min.js"></script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/amchart/amcharts.js"></script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/amchart/serial.js"></script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/amchart/export.min.js"></script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/amchart/light.js"></script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/multiselect/js/bootstrap-multiselect.js"></script>
			<script type="text/javascript"  src="<?php echo base_url(); ?>assets/js/Flex-Gauge.js" ></script>
			<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/chartist-plugin-legend.min.js"  type="text/javascript" ></script>
			<script src="<?php echo base_url(); ?>assets/plugins/amchart/ammap.js" type="text/javascript" ></script>
			<script src="<?php echo base_url(); ?>assets/plugins/amchart/usaLow.js"  type="text/javascript" ></script>
			<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/AjaxJS/library.js"  type="text/javascript" ></script>
			<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/dashboard.js?reload=3"  type="text/javascript" ></script>

			<script type="text/javascript">

				/* Global Section */
				var CustomerUID = 'all';
				var ProductUID = 'all';
				var ProjectUID = 'all';
				var DocTypeUID = 'all';
				var fromdate = ''
				var todate = ''
				var groupby = ''

				$(document).ready(function()
        {

          $('.count').click(function(e){
            $('.exceldownloadbtn, .CSVdownloadbtn').attr('data-file',$(this).attr('data-file'));
          });

					$('.exceldownloadbtn').click(function()
          {
						var status = $(this).attr('status-id');
            var filename = $(this).attr('data-file');
						var btntext = $(this).text();
						CustomerUID = $("#DashboardCustomer").val();
						ProductUID = $("#DashboardProduct").val();
						ProjectUID = $("#DashboardProject").val();
						DocTypeUID = $("#DashboardDocType").val();
						FromDate = $("#chartfdate").val();
						ToDate = $("#charttdate").val();
						if (CustomerUID == "") {
							CustomerUID = "all";
						}
						if (ProductUID == "") {
							ProductUID = "all";
						}
						if (ProjectUID == "") {
							ProjectUID = "all";
						}
						if (DocTypeUID == "") {
							DocTypeUID = "all";
						}
						var dategroup = $("#datefilter_type option:selected").attr(
							"data-groupby"
							);

						var data = {};
						data.Customer = CustomerUID;
						data.Product = ProductUID;
						data.Project = ProjectUID;
						data.DocType = DocTypeUID;
						data.from = FromDate;
						data.to = ToDate;
						data.dategroup = dategroup;
						data.status = status;
						var formdata = '';
						/*alert();*/
						$.ajax({
							type: "POST",
							url: '<?php echo base_url();?>Dashboard/WriteExcel',
							xhrFields: {
								responseType: 'blob',
							},
							data: data,
							beforeSend: function(){
								$('.exceldownloadbtn').html('Please wait...');
								$(".exceldownloadbtn").attr('disabled','disabled');
							},
							success:function(data)
							{ 
 								filename = filename+'-Orders.xlsx';
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
								$('.exceldownloadbtn').html('Excel');
								$(".exceldownloadbtn").removeAttr('disabled');
								
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log(jqXHR);
							},
							failure: function (jqXHR, textStatus, errorThrown) {
									console.log(errorThrown);
							},
							});
				});
				
        $('.CSVdownloadbtn').click(function(){
						var status = $(this).attr('status-id');
            var filename = $(this).attr('data-file');
						var btntext = $(this).text();
						CustomerUID = $("#DashboardCustomer").val();
						ProductUID =$("#DashboardProduct").val();
						ProjectUID = $("#DashboardProject").val();
						DocTypeUID = $("#DashboardDocType").val();
						FromDate = $("#chartfdate").val();
						ToDate = $("#charttdate").val();
						if (CustomerUID == "") {
							CustomerUID = "all";
						}
						if (ProjectUID == "") {
							ProjectUID = "all";
						}
						if (DocTypeUID == "") {
							DocTypeUID = "all";
						}
						if (ProductUID =="") {
							ProductUID ="all";
						}
						var dategroup = $("#datefilter_type option:selected").attr(
							"data-groupby"
							);

						var data = {};
						data.Customer = CustomerUID;
						data.Product =ProductUID;
						data.Project = ProjectUID;
						data.DocType = DocTypeUID;
						data.from = FromDate;
						data.to = ToDate;
						data.dategroup = dategroup;
						data.status = status;
						var formdata = '';
						$.ajax({
							type: "POST",
							url: '<?php echo base_url();?>Dashboard/WriteExcelCSVFormate',
							xhrFields: {
								responseType: 'blob',
							},
							data:data,
							beforeSend: function(){
								$('.CSVdownloadbtn').html('Please wait...');
								$(".CSVdownloadbtn").attr('disabled','disabled');
							},
							success:function(data)
							{  
                filename = filename+'-Orders.csv';
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
								$('.CSVdownloadbtn').html('CSV');
								$(".CSVdownloadbtn").removeAttr('disabled');
								
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log(jqXHR);
							},
							failure: function (jqXHR, textStatus, errorThrown) {
									console.log(errorThrown);
							},
							});
				});
				
				// Init Multi Select
				Customer_init_multiselect();
				Product_init_multiselect();
				Project_init_multiselect();
				Checkbox_init_multiselect();

       // Initiate Chart 1
       $("#orderstatisticsfilter").click(function(){
       $(".filterdiv").toggle();
       $("i" , this).toggleClass("icon-x icon-filter3");
       $(this).toggleClass("btn-danger btn-warning");
       $(".filterbtn").css("z-index" , "-1 !important");
 	});
	
	$(".refreshdiv").click(function(){	
		$(this).closest(".card").append(spinner);	
		$(this).closest(".card").append(overlaydiv);
		$(this).closest(".card .btn").addClass("reduceindex");
	});

	$('#OrderStatistics_refresh').off('click').on('click', function (e) 
  { 

		e.preventDefault();
		dashboardaddcardspinner($('#OrderStatistics_Card'));
		
		$("#DashboardCustomer").multiselect("destroy");
		$("#DashboardProduct").multiselect("destroy");
		$("#DashboardProject").multiselect("destroy");
		$("#DashboardDocType").multiselect("destroy");

		$("#DashboardCustomer").val('');

		$("#DashboardProject").html('');
		$("#DashboardProject").val('');

		$("#DashboardProduct").html('');
		$("#DashboardProduct").val('');

		$("#DashboardDocType").html('');
		$("#DashboardDocType").val('');

		/*ReInit Multiselect */
		Customer_init_multiselect();
		Product_init_multiselect();
		Project_init_multiselect();
		
		$('#chartfdate').val('<?php echo date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));?>');
		$('#charttdate').val('<?php echo date('Y-m-d');?>');

		Checkbox_init_multiselect();


		var dategroup = $('#datefilter_type option:selected').attr('data-groupby');
		var fdate = $('#chartfdate').val();
		var tdate = $('#charttdate').val();  
		var data = {};
		data.Customer = 'all';
		data.Product = 'all';
		data.Project = 'all';
		data.PackNo = 'all';
		data.DocType ='all';
		data.from = '<?php echo date("Y-m-d", strtotime("-90 DAYS")); ?>';
		data.to = '<?php echo date("Y-m-d"); ?>';
		data.dategroup = 'MONTH';
		
		var $fn_Array = [];
		$fn_Array.push(dashboard(data));
		$fn_Array.push(DrawChartData(data));
		Promise.all($fn_Array).then(function (resolve) {  
			dashboardremovecardspinner($('#OrderStatistics_Card'));
		});
		$(".datefilter_type").select2("val", "0");
		$("#datefilter_type").select2("destroy");
		$("#datefilter_type").select2({
			//tags: false,
			theme: "bootstrap",
		});
		$('.overlay.d2tspinner-overlay').css("display", "none");
	});

	$('#OrderDue_refresh').off('click').on('click', function (e) 
  {  
		e.preventDefault();
		dashboardaddcardspinner($('#OrdersDue_Card'));
		
		var dategroup = $('#datefilter_type option:selected').attr('data-groupby');
		var fdate = $('#chartfdate').val();
		var tdate = $('#charttdate').val();  
		var data = {};
		data.Customer = 'all';
		data.Product = 'all';
		data.Project = 'all';
		data.PackNo = 'all';
		data.DocType ='all';
		data.from = '<?php echo date("Y-m-d", strtotime("-90 DAYS")); ?>';
		data.to = '<?php echo date("Y-m-d"); ?>';
		data.dategroup = 'MONTH';
		
		DrawPieChart(data).then(function (resolve) {  
			dashboardremovecardspinner($('#OrdersDue_Card'));
		})
	});

	$('#OrderAging_refresh').off('click').on('click', function (e) 
  {  
		e.preventDefault();
		dashboardaddcardspinner($('#OrderAging_Card'));		
		var dategroup = $('#datefilter_type option:selected').attr('data-groupby');
		var fdate = $('#chartfdate').val();
		var tdate = $('#charttdate').val();  
		var data = {};
		data.Customer = 'all';
		data.Product = 'all';
		data.Project = 'all';
		data.PackNo = 'all';
		data.DocType ='all';
		data.from = '<?php echo date("Y-m-d", strtotime("-90 DAYS")); ?>';
		data.to = '<?php echo date("Y-m-d"); ?>';
		data.dategroup = 'MONTH';
		
		OrderAging(data).then(function (resolve) {  
			dashboardremovecardspinner($('#OrderAging_Card'));
		});
	})
});

function Product_init_multiselect()
{
	$('#DashboardProduct').multiselect({
		includeSelectAllOption: true,
		nonSelectedText: "Select Product(s)",
		enableFiltering: true,
		templates: { 
			li: '<li><div class="checkbox"><label></label></div></li>'
		}
	});
}
 
function Project_init_multiselect()
{
	$('#DashboardProject').multiselect({
		includeSelectAllOption: true,
		nonSelectedText: "Select Project(s)",
		enableFiltering: true,
		templates: { 
			li: '<li><div class="checkbox"><label></label></div></li>'
		}
	});
}

function Customer_init_multiselect()
{
	$('#DashboardCustomer').multiselect({
		includeSelectAllOption: true,
		nonSelectedText: "Select Customer(s)",
		enableFiltering: true,
		templates: { 
			li: '<li><div class="checkbox"><label></label></div></li>'
		}
	});
}

function Checkbox_init_multiselect()
{
	$('.multiselect-container div.checkbox').not('#datefilter_type').each(function (index) { 
		var id = 'multiselect-' + index,
		$input = $(this).find('input');       
		$(this).find('label').attr('for', id);  
		$input.attr('id', id);
		$input.detach();
		$input.prependTo($(this));
		$(this).click(function (e) {      
			e.stopPropagation();
		});
	});
}

$(document).ready(function()
{
  var FromDate = $('#chartfdate').val();
	var ToDate = $('#charttdate').val();
	var dategroup = $('#datefilter_type option:selected').attr('data-groupby');
	var data = {};
	data.Customer = 'all';
	data.Product =  'all';
	data.Project = 'all';
	data.PackNo = 'all';
	data.DocType = 'all';
	data.from = FromDate;
	data.to = ToDate;
	data.dategroup = dategroup;
	fn_Ajax_Init(data);
});

function dashboard($object)
{
	var formdata = $object;
	return new Promise(function (resolve, reject) {  

		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>Dashboard/GetDashboardCounts',
			data: formdata,
			dataType:"json",
			success: function(data)
			{
				if(data != 0)
  			{
          $.each(data, function(k, v) {
            $('.'+k).empty();
            $('.'+k).html(v);
          }); 
  			}
			},
			error: function(jqXHR, textStatus, errorThrown){
				reject('Error');
			}
		});	
	})

}

function DrawChartData(data)
{   

	// var dategroup = $('#datefilter_type option:selected').attr('data-groupby');
	
	return new Promise(function (resolve, reject) {  

		$.ajax ({
			type:'POST',
			url:'<?php echo base_url();?>Dashboard/FetchChartData/',
			dataType: 'JSON',
			data: data,
			beforeSend: function() {
				
			},
			success:function(chartdata)
			{    
				
				dataColouredBarsChart = {
					labels: chartdata.label,
					series: [
						chartdata.pending,
						chartdata.received,
						chartdata.completed
						]
					};
					
					optionsColouredBarsChart = {
						lineSmooth: Chartist.Interpolation.simple({
							tension: 10,
						}),
						axisY: {
							showGrid: true,
							offset: 40
						},
						axisX: {
							showGrid: false,
						},
						showPoint: true,
						height: '300px',
						low: 0,
			        legend: {
		            display: true,
		            labels: {
	                fontColor: 'rgb(255, 99, 132)'
		            }
			        }
    
					};
					
				  var colouredBarsChart = new Chartist.Line('#colouredBarsChart1', dataColouredBarsChart, optionsColouredBarsChart);
					
					md.startAnimationForLineChart(colouredBarsChart);
					// dashboardremovecardspinner('#orderschart');
					resolve('success');
				}
				
			});
		});

	}
	
	function DrawPieChart(data)
	{

		return new Promise(function (resolve, reject) {  
			
			
			$.ajax ({
				type:'POST',
				url:'<?php echo base_url();?>Dashboard/FetchPieChartData/',
				dataType: 'JSON',
				data: data,
				beforeSend: function() {
					
				},
				success:function(chartdata)
				{   
					$('.pastduelevel').css({'width':chartdata.pastduepercentage});
					$('.presentduelevel').css({'width':chartdata.presentduepercentage});
					$('.futureduelevel').css({'width':chartdata.futureduepercentage});
					$("#pastdue").empty();
					$("#pastdue").append(chartdata.pastdue);
					$("#presentdue").empty();
					$("#presentdue").append(chartdata.presentdue);
					$("#futuredue").empty();
					$("#futuredue").append(chartdata.futuredue);
					$("#totaldueorders").empty();
					$("#totaldueorders").append(chartdata.TotalOrders);
					var dataPreferences = {
						series: chartdata.Due,
					};
					
					var optionsPreferences = {
						height: chartdata.Height,
						showLabel: false
					};
					
					Chartist.Pie('#chartPreferences', dataPreferences, optionsPreferences).on('draw', function(context) {
						if(context.type === 'slice') {
							var $slice = $(context.element._node);
							$slice.tooltipster({
								content: $slice.parent().attr('ct:series-name') + ' ' + $slice.attr('ct:value')
							});
						}
					});;
					resolve("Success");
				}
				
			});
		});
	} 

	function dashboardinitialize(status)
	{

		CustomerUID = $("#DashboardCustomer").val();
		ProductUID = $("#DashboardProduct").val();
		ProjectUID = $("#DashboardProject").val();
 		FromDate = $("#chartfdate").val();
		ToDate = $("#charttdate").val();
		if (CustomerUID == "") {
			CustomerUID = "all";
		}
		if (ProductUID == "") {
			ProductUID = "all";
		}
		if (ProjectUID == "") {
			ProjectUID = "all";
		}
	 
		var dategroup = $("#datefilter_type option:selected").attr(
			"data-groupby"
			);

		var data = {};
		data.Customer = CustomerUID;
		data.Product = ProductUID;
		data.Project = ProjectUID;
 		data.from = FromDate;
		data.to = ToDate;
		data.dategroup = dategroup;
		data.status = status;
		$('#myordertable').DataTable().destroy();
		myordertable = $('#myordertable').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			fixedHeader: false,
			paging:  true,
		fixedColumns:   {
			leftColumns: 1,
			rightColumns: 1
		}, 
		 "bDestroy": true,
			"autoWidth": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		//"order": [], //Initial no order.
		//"pageLength": 50, // Set Page Length
		//"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
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
			"url": "<?php echo base_url('Dashboard/orders_ajax_list')?>",
			"type": "POST",
			"data" : data 
			
		}

	});
		

		$('.exceldownloadbtn').attr('status-id',status);
		$('.CSVdownloadbtn').attr('status-id',status);
		$('.downloadbtndiv1').hide();
		$('.downloadbtndiv2').hide();
		$('.downloadbtndiv').show();
	}

	function ChangePage()
	{
		$('.dashboardview').show();
		$('.orderstable').hide();
		// $('.refresh-btn').trigger('click');
		$('#charttdate').trigger('dp.change');
    $('#myordertable > tbody').empty();
	}

	function dashboardCounts(status)
	{
		$('#myordertable tbody').html('');
		$('.dashboardview').hide();
		$('.orderstable').show();
		dashboardinitialize(status);
		$('.table-title-heading').addClass('hide');

	}


function dashboard_refresh($object)
{

	var formdata = $object;

  	$.ajax({
	type: "POST",
	url: '<?php echo base_url(); ?>Dashboard/GetDashboardCounts',
	data: formdata,
	dataType:"json",
		success: function(data)
		{
			if(data != 0)
			{ 
        $.each(data, function(k, v) {
          $('.'+k).empty();
          $('.'+k).html(v);
        }); 
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});	
}



</script>
