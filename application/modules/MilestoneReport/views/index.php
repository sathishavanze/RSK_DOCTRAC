<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />


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
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}

	.badge {
		padding: 4px 8px;
		text-transform: capitalize;
	}

	.mt-0{
		margin-bottom: 0px !important;
	}

	.width-100{
		width:100%;
	}

	.excel-btn{
		position: relative;
		float: right;
	}

	.dataTables_scrollBody table {
		margin-left:0px;
	}

	div.scrollmenu {
  
  overflow: auto;
  white-space: nowrap;
}

div.scrollmenu a {
  display: inline-block;
  color: white;
  text-align: center;
  padding: 14px;
  text-decoration: none;
}

div.scrollmenu a:hover {
  background-color: #777;
}

.card .card-header.card-header-icon .card-title,
.card .card-header.card-header-text .card-title {
 margin-top: 1px !important;
 color: #ffffff;
}
</style>
<div class="card mt-10" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
     <h4 class="card-title">Milestone Report</h4>
		</div>
<!-- 		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Milestone Report</h4>
			</div>
		</div> -->
	</div>

      <div class="text-right">
        <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
        <i class="fa fa-file-excel-o exceldownload" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
      </div>

      <div id="advancedMilestone"  style="display: none;">
        <fieldset class="advancedsearchdiv">
          <legend>Advanced Search</legend>
          <form id="advancedsearchdata">
            <div class="col-md-12 pd-0">
              <div class="row " >

                <div class="col-md-2 ">
                 <div class="form-group bmd-form-group">
                  <label for="adv_ProjectUID" class="bmd-label-floating">Project </label>
                  <select class="select2picker form-control" id="adv_ProjectUID"  name="ProjectUID">   
                   <option value="All">All</option>
                   <?php foreach ($Projects as $key => $value) { ?>

                    <option value="<?php echo $value->ProjectUID; ?>" ><?php echo $value->ProjectName; ?></option>
                  <?php } ?>                     
                </select>
              </div>
            </div>

            <div class="col-md-2 ">
             <div class="form-group bmd-form-group">
              <label for="adv_MilestoneUID" class="bmd-label-floating">Milestone </label>
              <select class="select2picker form-control" id="adv_MilestoneUID"  name="WorkflowModuleUID">   
                <option value="All">All</option>
                <?php foreach ($Milestone as $key => $value) { ?>
                  <option value="<?php echo $value->MilestoneUID; ?>" ><?php echo $value->MilestoneName; ?></option>
                <?php } ?>                      
              </select>
            </div>
          </div>

           <div class="col-md-2 ">
           <div class="form-group bmd-form-group">
            <label for="adv_Status" class="bmd-label-floating">Status</label>
            <select class="select2picker form-control" id="adv_Status"  name="Status">   
              <option value="All">All</option>
              <option value="NA">N/A</option>                
              <option value="Completed">Completed</option>
              <option value="Pending">Pending</option>      
            </select>
          </div>
        </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row">
              <div class="col-md-6 pd-0 inputprepand" >
                <p class="mt-5"> Order Entry From Date</p>
              </div>
              <div class=" col-md-6 " style="padding-left: 0px">
                <div class="datediv">
                  <input type="text" id="adv_FromDate" name="FromDate" class="form-control datepicker" value="">
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3 datadiv">
            <div class="bmd-form-group row">
              <div class="col-md-6 pd-0 inputprepand" >
                <p class="mt-5"> Order Entry To Date</p>
              </div>
              <div class=" col-md-6 " style="padding-left: 0px">
                <div class="datediv">
                  <input type="text" id="adv_ToDate" name="ToDate" class="form-control datepicker" value=""/>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="col-md-12  text-right pd-0 mt-10">
        <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
        <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
      </div>

    </form>
  </fieldset>
</div>

<div class="material-datatables">
  <table class="table table-hover table-striped" id="MilestoneTable">
    <thead>
     <tr>
      <th>Order Number</th>
      <th>Loan Number</th>
      <th>MileStone</th>
       <?php foreach ($Milestone as $key => $value) { ?>
        <th><?php echo $value->MilestoneName; ?></th>
      <?php } ?> 
      <th class="no-sort">Actions</th>
    </tr>
  </thead>

  <tbody>

  </tbody>
</table>
</div>

</div>




<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>


<script type="text/javascript">
	$(document).ready(function()
  {
     $(window).resize(function() {
        $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
      }
      );

       $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
      }
      );


		/* --- Dropify initialization starts */

		$('.dropify').dropify();

		// Used events
		var drEvent = $('.dropify').dropify();

		drEvent.on('dropify.beforeClear', function(event, element){
			// return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
		});

		drEvent.on('dropify.afterClear', function(event, element){
			// alert('File deleted');
		});

		drEvent.on('dropify.errors', function(event, element){
			console.log('Has Errors');
		});
      
    $('.fa-filter').click(function(){
      $("#advancedMilestone").slideToggle();
    });

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

     var MilestoneExport = false;

     milestoneinitialize('false');
    function milestoneinitialize(formdata)
    {
      MilestoneExport = $('#MilestoneTable').DataTable( {
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        scrollY: '100vh',
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
            "url": "<?php echo base_url('MilestoneReport/MilestoneReportAjaxList')?>",
            "type": "POST",
            "data" : {'formData':formdata}  
          },
          "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
          } ],
        });
       $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }

    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
              var ProjectUID = $('#adv_ProjectUID option:selected').val();
              var Status = $('#adv_Status option:selected').val();
              var Milestone = $('#adv_MilestoneUID').val();
              var FromDate = $('#adv_FromDate').val();
              var ToDate = $('#adv_ToDate').val();
              if((ProjectUID == '')  && (Milestone == '') && (Status == '') && (FromDate == '')&& (ToDate == ''))
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
               var formData = ({'Milestone':Milestone,'Status':Status,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate}); 
               milestoneinitialize(formData);
             }

             return false;
           });

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_MilestoneUID").val('All');
      $("#adv_ProjectUID").val('All');
      $("#adv_Status").val('All');
      $("#adv_FromDate").val('');
      $("#adv_ToDate").val('');
      milestoneinitialize('false');
      callselect2();

    });


    $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
      var ProjectUID = $('#adv_ProjectUID option:selected').val();
      var Status = $('#adv_Status option:selected').val();
      var Milestone = $('#adv_MilestoneUID option:selected').val();
      var FromDate = $('#adv_FromDate').val();
      var ToDate = $('#adv_ToDate').val();
      if((ProjectUID == '')  && (Milestone == '')&& (Status == '') && (FromDate == '')&& (ToDate == ''))
      {
        var formData = 'All';
      } 
      else 
      {
        var formData = ({'Milestone':Milestone,'Status':Status,'ProjectUID': ProjectUID ,'FromDate':FromDate,'ToDate':ToDate}); 
      }

      $.ajax({
       type: "POST",
       url: '<?php echo base_url();?>MilestoneReport/MilestoneExcelExport',
       xhrFields: {
        responseType: 'blob',
      },
      data: {'formData':formData},
      beforeSend: function(){

      },
      success: function(data)
      {
        var filename = "MileStoneReport.csv";
        if (typeof window.chrome !== 'undefined') {
            //Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "MileStoneReport.csv";
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
});
</script>







