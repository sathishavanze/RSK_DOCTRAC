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
 .DTFC_RightBodyLiner {
  overflow-y: hidden !important;
}
</style>
<div class="card customcardbody mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
      Processors Info
    </div>
    <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
        <i class="fa fa-file-excel-o ProcessorsInfoExcelSDownload" title="Export Excel" aria-hidden="true" style="font-size:13px;color:#0B781C;cursor: pointer;"></i>
        <a href="ProcessorInfo/addProcessorInfo"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user-plus pr-10"></i>Add Processor</a>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="col-md-12">
    <div class="material-datatables">
      <table id="ProcessorInfo_Table" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left">S.No</th>
         <th  class="text-left">Name</th>
         <th  class="text-left">Team Leader</th>
         <th  class="text-left">Manager</th>
         <th  class="text-left">VP</th>
         <th  class="text-left">Active</th>
         <th  class="text-left">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php
      $i=1;foreach($ProcessorsDetails as $row): ?>
      <tr>

       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->FirstName.', '.$row->LastName; ?></td>
       <td style="text-align: left;"><?php echo $row->TeamLeader; ?></td>
       <td style="text-align: left;"><?php echo $row->Manager; ?></td>
       <td style="text-align: left;"><?php echo $row->VP; ?></td>
       <td style="text-align: left;"> 
        <div class="form-check">
         <label class="form-check-label">
          <input class="form-check-input" type="checkbox" name="Active" <?php if($row->Active == 1){ echo "checked"; } ?>  disabled > 
          <span class="form-check-sign">
            <span class="check"></span>
          </span>
        </label>
      </div>
    </td>
    <td style="text-align: left"> 
      <span style="text-align: left;width:100%;">
        <a href="<?php echo base_url('ProcessorInfo/updateProcessorInfo/'.$row->ProcessorUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
      </span>
    </td>

  <?php 
  $i++;
endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script type="text/javascript">

	$(document).ready(function(){
   
    $("#ProcessorInfo_Table").dataTable({
      processing: true,
      scrollX:  true,
      
      paging:true,
      // fixedColumns:   {
      //   rightColumns: 1
      // }

    });

    $(document).off('click', '.ProcessorsInfoExcelSDownload').on('click', '.ProcessorsInfoExcelSDownload', function(){

      $.ajax({
        type: "POST",
        url: "ProcessorInfo/ExcelDownload", 
        xhrFields: {
          responseType: 'blob',
        },

        success: function(data) {

          var filename = 'Processors.xlsx';
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

  });
</script>







