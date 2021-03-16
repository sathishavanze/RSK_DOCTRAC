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
</style>

<div class="col-md-12 pd-0" >
  <div class="card mt-0" id="Orderentrycard">
    <div class="card-header tabheader" id="">
      <div class="col-md-12 pd-0">
        <div id="headers" style="color: #ffffff;" class="<?php echo $HeaderColor->SideBar_NavColor ?:'#333' ?>">
           
          <!-- Order Info Header View --> 
          <?php $this->load->view('orderinfoheader/orderinfo'); ?>
        </div>
      </div>
    </div>
    <div class="card-body pd-0">
      <!-- Workflow Header View --> 
      <?php $this->load->view('orderinfoheader/workflowheader'); 
      $OrderUID = $this->uri->segment(3);
      ?>
      <div class="tab-content tab-space">
        <div class="tab-pane active" id="loanfile">
          <div class="card mt-40 customcardbody" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">SFTP Files
    </div>    
  </div>
  <div class="card-body">
    <div class="col-md-12">
  
    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
         <th  class="text-left">S.No</th>
         <th  class="text-left">Loan Number</th>
         <th  class="text-left">Order Number</th>
         <th  class="text-left">DocumentName</th>
         <th  class="text-left">Uploaded Date time</th>
         <th  class="text-left">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php $i=1;foreach($SFTP_Files as $row): ?>
      <tr>
       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $row->LoanNumber; ?></td>
       <td style="text-align: left;"><?php echo $row->OrderNumber; ?></td>
       <td style="text-align: left;"><?php echo $row->DocumentName; ?></td>
       <td style="text-align: left;"><?php echo $row->UploadedDateTime; ?></td>
       <td style="text-align: left"> 
        <a target="_blank" title="" href="<?php echo base_url().$row->DocumentURL; ?>" class="btn btn-sm btn-xs viewFile" style="background-color: #f2f2f2;color: #000;"><span class="mdi mdi-eye"></span> View <div class="ripple-container"></div></a>
      </td>
    </tr>
    <?php 
    $i++;
    endforeach; ?>
  </tbody>
</table>
</div>
</div>

</div>
</div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('orderinfoheader/workflowpopover'); ?>
<script type="text/javascript">

	$(document).ready(function(){

    $("#MaritalTableList").dataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: [
      ],
      processing: true,
      paging:true,



    });


    $(document).off('click','.adduser').on('click','.adduser', function(e) {

      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Users/SaveDocument'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {

        },
        success: function (response) {
          if(response.validation_error == 1)
          {
            $.notify(
            {
              icon:"icon-bell-check",
              message:response.message
            },
            {
              type:"danger",
              delay:1000 
            });
            $.each(response, function(k, v) {

              $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
              $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

            });
          }
          else
          {
            $.notify(
            {
              icon:"icon-bell-check",
              message:response.message
            },
            {
              type:"success",
              delay:1000 
            }); 
          }

        }
      });

    });



			// $(document).off('keyup','#loginid').on('keyup','#loginid', function(e) {

				function log()
				{

          var loginid = $('#loginid').val();

          $.ajax({
            type: "POST",
            url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
            data: {'loginid' : loginid},
            dataType:'json',
            success: function (response) {

              if(response.Status == 1)
              {

                $('#loginexists').show();
              }else{
               $('#loginexists').hide();
             }


           },
           error:function(xhr){

             console.log(xhr);
           }
         });

        }
			// });

   });
 </script>







