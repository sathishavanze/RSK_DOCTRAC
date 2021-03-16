<style>
  th, td { text-align: center; }

  .card .card-header.card-header-icon .card-title,
.card .card-header.card-header-text .card-title {
 margin-top: 1px !important;
 color: #ffffff;
}
</style>
<div class="card mt-10">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <!-- <i class="icon-file-check"></i> -->
      <h4 class="card-title">Standard Report</h4>
    </div>
    <div class="row">
      <div class="col-md-6">
        <!-- <h4 class="card-title">Standard Report</h4> -->
      </div>
      <div class="col-md-6 text-right"> 
      	<button class="btn btn-default btn-xs btn-link refresh-btn" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color:#900C3F;cursor: pointer;"><i class="fa fa-filter"></i></button>
      	<a class="btn btn-default btn-xs btn-link exceldownload" href="javascript:;"><i class="fa fa-file-excel-o" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
      	</a> 
			</div>
    </div>
  </div>


    <div id="advancedFilterForReport" class="mt-15 mb-20" >
      <fieldset class="col-md-12">
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row">

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_ReportUID" class="bmd-label-floating">Reports </label>
                  <select class="select2picker form-control" id="adv_ReportUID"  name="ReportUID">  
                    <?php foreach ($ClientReports as $key => $value) { ?>
                    <option value="<?php echo $value['ReportUID']; ?>" ><?php echo $value['ReportName']; ?></option>
                    <?php } ?>                     
                  </select>
                </div>
              </div>

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_ReportUID" class="bmd-label-floating">Status </label>
                  <select class="select2picker form-control" id="adv_ReportStatus"  name="Status"> 
                    <option value="All">All</option>                    
                    <option value="Pending" selected>Pending</option>                    
                    <option value="Completed">Completed</option>                    
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
                <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
              </div>
            </div>
          </div>
      </form>
    </fieldset>
  </div>

<div class="col-md-12 material-datatables" id="tableDiv" style="display: none;">
  <table class="table table-hover table-striped" id="ChecklistReport">
    <thead>
  </thead>

  <tbody>

  </tbody>
</table>
</div>

</div>


<script type="text/javascript">
  var ChecklistReport = false;

  function datatableheaderdynamic(formData) {
  	$('#tableDiv').show();
  	$.ajax({
  		"url": "<?php echo base_url('ChecklistReport/FetchReportsHeader')?>",
  		"type": "POST",
  		"data" : {'formData':formData},
  		"success": function(json) {
  			console.log(json);     

  			if (json.validation_error == 1) {
  				$.notify(
  				{
  					icon:"icon-bell-check",
  					message:json.message
  				},
  				{
  					type:'danger',
  					delay:1000 
  				});

  				$('#ChecklistReport').dataTable({
  					responsive: true,
  					"bDestroy": true,
  				});
  				return false;
  			}      

  			$("#tableDiv").empty();

  			var content = '<table class="table table-hover table-striped" id="ChecklistReport">';
  			content += '<thead>';
  			content += '<tr>';
  			//content += '<th rowspan="2">Order Number</th><th rowspan="2">Loan #</th><th rowspan="2">Borrower</th><th rowspan="2">Processor</th><th rowspan="2">Product</th><th rowspan="2">Status For Filtr</th><th rowspan="2">ST</th><th rowspan="2">Processor Offshore</th><th rowspan="2">Reviewed times</th><th rowspan="2">Initial Review Date</th><th rowspan="2">Review completed 1</th><th rowspan="2">Review completed 2</th><th rowspan="2">Review completed 3</th>';

  			$.each(json, function(i, val){
  				content += "<th colspan='"+val.length+"'>"+i+"</th>";
  			});

  			content += '</tr>';  
  			content += '<tr>';

  			$.each(json, function(i, val){
  				$.each(val, function(si, sval){
  					content += "<th>"+sval.HeaderName+"</th>";
  				});
  			});

  			content += '</th>';            
  			content += '</tr>';      
  			content += '</thead>';
  			content += "</table>"

  			$('#tableDiv').append(content);

  			ChecklistReportFunc(formData);
  		},
  		"dataType": "json"
  	});
  }

  // ChecklistReportFunc('false')
  function ChecklistReportFunc(formData)
  {
  	ChecklistReport = $('#ChecklistReport').DataTable( {
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
  			//leftColumns: 1
  			// rightColumns: 1
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
  			"url": "<?php echo base_url('ChecklistReport/FetchReportsDetails')?>",
  			"type": "POST",
  			"data" : {'formData':formData}  
  		},
  		"columnDefs": [ {
  			"targets": 'no-sort',
  			"orderable": false,
  		} ],
  	});
  }


  $(document).ready(function(){

  	//select init
  	$("select.select2picker").select2({
  		//tags: false,
  		theme: "bootstrap",
  	});

  	//datetimepicker init
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

    // /datatableheaderdynamic('false');

    $('.fa-filter').click(function(){
    	$("#advancedFilterForReport").slideToggle();
    });


    $(document).off('click','.filterreport').on('click','.filterreport',function()
    {
      var ReportUID = $('#adv_ReportUID option:selected').val();      
      var ReportStatus = $('#adv_ReportStatus option:selected').val();      
      if(ReportUID == '')
      {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'Please select the report'
        },
        {
          type:'danger',
          delay:1000 
        });
      }
       else 
      {
       var formData = ({'ReportUID':ReportUID,'ReportStatus':ReportStatus}); 
       datatableheaderdynamic(formData);
     }
     return false;
    });

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_ReportUID").val('');
      ChecklistReportFunc('false');
      callselect2();
    });

  });


  $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
    var ReportUID = $('#adv_ReportUID option:selected').val();
    var ReportStatus = $('#adv_ReportStatus option:selected').val(); 
    var formData = ({'ReportUID':ReportUID,'ReportStatus':ReportStatus}); 

    $.ajax({
     "url": '<?php echo base_url();?>ChecklistReport/WriteXLS',
     "type": "POST",
     xhrFields: {
      responseType: 'blob',
    },
    "data": {'formData':formData},
    beforeSend: function(){


    },
    success: function(data)
    {
      var filename = "ChecklistReport.xlsx";
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

</script>







