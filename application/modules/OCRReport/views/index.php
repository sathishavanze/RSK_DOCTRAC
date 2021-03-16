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
      <h4 class="card-title">OCR Report</h4>
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
                  <label for="adv_ReportUID" class="bmd-label-floating">Status </label>
                  <select class="select2picker form-control" id="adv_ReportStatus"  name="Status"> 
                    <option value="all" selected>All</option>
                    <option value="pending"> Pending </option>                   
                    <option value="success"> Succeed </option>                    
                    <option value="failure"> Failed </option>                    
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
  <table class="table table-hover table-striped" id="OCRReport">
   <thead>      
      <th>OrderNumber</th>
      <th>LoanNumber</th>
      <th>BorrowerName</th>
      <th>OCR Status</th>
      <th>Document Name</th>
      <th>Actions</th>
    </thead>
  <tbody>
  </tbody>
</table>
</div>

</div>
<script type="<?php echo base_url().'assets\js\plugins\jquery.dataTables.min.js';?>"></script>

<script type="text/javascript">
  var OCRReport = false;

   var ReportStatus = $('#adv_ReportStatus option:selected').val();      
   var formData = ({'ReportStatus':ReportStatus}); 
   // OCRReportFunc(formData);
  
  function OCRReportFunc(formData)
  {
    $('#tableDiv').show();
  	OCRReport = $('#OCRReport').DataTable( {
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
  			"url": "<?php echo base_url('OCRReport/FetchReportsDetails')?>",
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
      var ReportStatus = $('#adv_ReportStatus option:selected').val();      
      var formData = ({'ReportStatus':ReportStatus}); 
      OCRReportFunc(formData);
      return false;
    });

    $(document).off('click','.reset').on('click','.reset',function(){
      $("#adv_ReportUID").val('');
      OCRReportFunc('false');
      callselect2();
    });

  });


  $(document).off('click','.exceldownload').on('click','.exceldownload',function(){
    var ReportUID = $('#adv_ReportUID option:selected').val();
    var ReportStatus = $('#adv_ReportStatus option:selected').val(); 
    var formData = ({'ReportUID':ReportUID,'ReportStatus':ReportStatus}); 

    $.ajax({
     "url": '<?php echo base_url();?>OCRReport/WriteXLS',
     "type": "POST",
     xhrFields: {
      responseType: 'blob',
    },
    "data": {'formData':formData},
    beforeSend: function(){


    },
    success: function(data)
    {
      var filename = "OCRReport.xlsx";
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







