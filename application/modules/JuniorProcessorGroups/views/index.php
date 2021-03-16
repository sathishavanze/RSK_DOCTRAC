<style type="text/css">
	.pd-btm-0{
		padding-bottom: 0px;
	}

	.margin-minus8{
		margin: -8px;
	}

	.mt--15{
		margin-top: -15px;
	}

	.bulk-notes
	{
		list-style-type: none
	}
	.bulk-notes li:before
	{
		content: "*";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}
	.select2-container--open{
		z-index: 999999999!important;
	}

</style>
<div class="card  customcardbody mt-20" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Junior Processor Groups List
		</div>
		<div class="row">
			<div class="col-md-6">
			</div>
			<div class="col-md-6 text-right">
				<a href="<?php echo base_url(); ?>/JuniorProcessorGroups/addjuniorprocessorgroup" class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn ajaxload"><i class="pr-10 icon-plus22"></i> Add Junior Processor Group</a>
			</div>
		</div>
	</div>

	<div class="card-body">
		<div class="col-md-12">
			<div class="material-datatables">
				<table id="JuniorProcessorGroupTableList"  style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column" >
					<thead>
						<tr>
							<th  class="text-left">S.No</th>
							<th  class="text-left">Junior Processor</th>
							<th  class="text-left">Status</th>
							<th  class="text-left">Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>




<script type="text/javascript">

	function completedinitialize()
	{
		var table = $('#JuniorProcessorGroupTableList').DataTable( {
			scrollX:        true,
			scrollCollapse: true,
			scrollY: '100vh',
			fixedHeader: false,
			"bDestroy": true,
			"autoWidth": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"pageLength": 10, // Set Page Length
			"lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
			searchDelay:1000,
			initComplete: function(settings, json) {
				$($.fn.dataTable.tables( true ) ).css('width', '100%');
			},
			language: {
				sLengthMenu: "Show _MENU_ Groups",
				emptyTable:     "No Junior Processor Groups Found",
				info:           "Showing _START_ to _END_ of _TOTAL_ Groups",
				infoEmpty:      "Showing 0 to 0 of 0 Groups",
				infoFiltered:   "(filtered from _MAX_ total Groups)",
				zeroRecords:    "No matching Junior Processor Groups found",
				processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',
			},
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo base_url(); ?>JuniorProcessorGroups/JuniorProcessorGroup_ajax_list",
				"type": "POST",
				"beforeSend": function() {
					$('.spinnerclass').addClass("be-loading-active");
					if (table) {
						table.settings()[0].jqXHR.abort();
					}
				},
			},
			fnDrawCallback: function( oSettings ) {
				$('.spinnerclass').removeClass("be-loading-active");
			},
			"destroy" : true 

		});

	}

	$(document).ready(function() {
		completedinitialize();
	});

	// Update Active and InActive Status
	$(document).off('change','.Active').on('click', '.Active', function(event) {
        if($(this).prop("checked") == true){
           var Active = 1;
        }else{
           var Active = 0;
        }
        var GroupUID = $(this).data('groupuid');
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            global: false,
            url: '<?php echo base_url();?>JuniorProcessorGroups/UpdateJuniorProcessorGroupStatus',
            data: {
                'GroupUID': GroupUID,
                'Active': Active
            },
            success: function(data) { 
                $.notify({
                    icon: "icon-bell-check",
                    message: data.message
                }, {
                    type: data.type,
                    delay: 2000
                });
            }
        });
    });

</script>







