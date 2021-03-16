
<style>
	th, td { text-align: center; }
	.bold{ font-weight: bold;  }
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-40 cyclereportdiv" id="card_themeid">
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
		<i class="fa fa-file-excel-o excelorderlist" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
	</div>

	<!-- HEADER -->
	<?php $this->load->view('cycletimereportheader'); ?>
	<!-- HEADER -->


	
	


	<div class="col-md-12 TableDiv">

		<table class="table table-hover table-striped listtable">
			<thead>
				<tr>
					<!-- total order -->
					<th><b>Total Loans </b></th>
					<th><b>Total Average</b></th>

					<!-- pending order -->
					<th><b>Pending Loans</b></th>
					<th><b>Pending Average</b></th>

					<!-- funded order -->
					<th><b>Funded Loans</b></th>
					<th><b>Funded Average</b></th>
				</tr>	

			</thead>

			<tbody>
				<!-- total order -->
				<?php 
				$PendingAvg = round($cycletimeCount->PendingAvg/$cycletimeCount->PendingOrderCount);
				$FundedAvg = round($cycletimeCount->FundedAvg/$cycletimeCount->FundedOrderCount);
				?>
				<td class="bold"><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->TotalOrderUID; ?>" data-title="CycleTime MTD LIST" ><?php  echo $cycletimeCount->PendingOrderCount+$cycletimeCount->FundedOrderCount;?></a></td>
				<td class="bold"><?php  echo $PendingAvg+$FundedAvg ;?>
				<!-- Pending order -->
				<td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->PendingOrderUID; ?>" data-title="CycleTime MTD LIST" ><?php  echo $cycletimeCount->PendingOrderCount;?></a></td>
				<td><?php  echo $PendingAvg ;?></td>

				<!-- Funded order -->
				<td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $cycletimeCount->FundedOrderUID; ?>" data-title="CycleTime MTD LIST" ><?php  echo $cycletimeCount->FundedOrderCount;?></a></td>
				<td><?php  echo $FundedAvg ;?></td>

			</tbody>  
		</table>
	</div>

</div>
<!-- Orders Table -->
            <?php $this->load->view('InflowReport/CycleReportCountListView'); ?>
            <!-- Orders Table -->
            <script src="assets/js/app/cyclereport.js?reload=1.0.1"></script>

<script type="text/javascript" src="assets/lib/fselect/fSelect.js"></script>
<script src="assets/js/plugins/jquery.dataTables.min.js"  type="text/javascript"></script>

<script type="text/javascript">
	var ReportName = 'CycleTimeMTD';
	 fndatatable = $('.listtable').DataTable( {
    scrollX:        true,
    scrollCollapse: true,
    fixedHeader: false,
    scrollY: '100vh',
    paging:  true,
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
    dom: 'lBfrtip',
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

</script>