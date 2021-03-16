<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#success-table" role="tablist"><small>
			Imported&nbsp;<i class="fa fa-check-circle"></i></small>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#error-data" role="tablist"><small>
			Not Imported&nbsp;<i class="fa fa-times-circle-o"></i></small>
		</a>
	</li>
</ul>
<div class="tab-content tab-space">
	<div id="success-table" class="tab-pane active cont">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="tblsuccessentries">
					<thead>
						<tr>
							<?php 	
							foreach ($headingsArray as $key => $value) {
								?><th><?php echo $value; ?></th><?php
							} 
							?>
						</tr>
					</thead>
					<tbody>
           <?php 
            foreach ($Success as $key => $value) 
            {
              echo '<tr>';
              echo '<td>'.$value[0].'</td>';
              echo '<td>'.$value[1].'</td>';
              echo '<td>'.$value[2].'</td>';
              echo '<td>'.$value[3].'</td>';
              echo '</tr>';
            }
           ?>
					</tbody>
				</table>
		</div>
	</div>

	<div id="error-data" class="tab-pane cont">
		<div class="text-right">
			<span class="badge badge-pill" style="background-color: #757575;">Invalid</span>
			<span class="badge badge-pill" style="background-color: #168998;">Order Number</span> 
			<span class="badge badge-pill" style="background-color: #ff04eb;">Loan Number</span> 
			<span class="badge badge-pill" style="background-color: #32CD32;">Milestone</span> 
			<?php 
			$workflow = $this->Common_Model->GetCustomerBasedModules();
			foreach ($workflow as $key => $value) 
			{
				?>
 <span class="badge badge-pill" style="background-color: <?php echo $value->ColorCode; ?>"><?php echo $value->SystemName?></span> 
<?php }
  ?>
		</div>
		<div class="">
			<div class="table-responsive defaultfontsize tablescroll">
				<table class="table table-striped table-hover table-format nowrap datatable" id="tblfailedentries">
					<thead>
						<tr>
							<?php 	
							foreach ($headingsArray as $key => $value) {
								?><th><?php echo $value; ?></th><?php
							}
							?>
						</tr>
					</thead>
					<tbody>
           <?php 
            foreach ($Error as $key => $value) 
            {
              echo '<tr style="'.$value['Style'].'">';
              echo '<td>'.$value[0].'</td>';
              echo '<td>'.$value[1].'</td>';
              echo '<td>'.$value[2].'</td>';
              echo '<td>'.$value[3].'</td>';
              echo '</tr>';
            }
           ?> 
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
		$(document).ready(function()
  {
  		//datatable resize function
$(window).resize(function() {
$($.fn.dataTable.tables( true ) ).css('width', '100%');
$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
}
);

$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
}
);
  });

</script>
