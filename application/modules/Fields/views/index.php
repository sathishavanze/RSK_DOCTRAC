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
<div class="card mt-20 customcardbody" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Fields
		</div>
           <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
              <a href="<?php echo base_url('Fields/AddField'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-user"></i> Add Fields</a>
    
     </div>
   </div>

	</div>
	<div class="card-body">
<div class="col-md-12">

    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-hover order-column">
       <thead>
        <tr>
          <th  class="text-left" style="width: 30px;" >S.No</th>
          <th  class="text-left">Field Name</th>
          <th  class="text-left">Field Type</th>
          <th  class="text-left">Stacking</th>
          <th  class="text-left">Indexing</th>
          <th  class="text-left">Status</th>
          <th  class="text-left">Action</th>
        </tr>

      </tr>
    </thead>
    <tbody>
      <?php $i=1;foreach($Fields as $row): ?>
      <tr>
        <td style="text-align: left;"><?php echo $i; ?></td>
        <td style="text-align: left;"><?php echo $row->FieldName; ?></td>
        <td style="text-align: left;"><?php echo $row->FieldType; ?></td>
        <td style="text-align: left;">
          <div class="form-check">
             <label class="form-check-label">
              <input class="form-check-input" type="checkbox" name="IsStacking" <?php if($row->IsStacking == 1){ echo "checked"; } ?>  disabled > 
              <span class="form-check-sign">
                <span class="check"></span>
              </span>
            </label>
          </div>            
        </td>
        <td style="text-align: left;">
          <div class="form-check">
             <label class="form-check-label">
              <input class="form-check-input" type="checkbox" name="IsIndexing" <?php if($row->IsIndexing == 1){ echo "checked"; } ?>  disabled > 
              <span class="form-check-sign">
                <span class="check"></span>
              </span>
            </label>
          </div>            
        </td>
        <td style="text-align: left;">
          <div class="togglebutton">
            <label class="label-color"> 
              <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?> disabled>
              <span class="toggle"></span>
            </label>
          </div>
        </td>
        <td style="text-align: left"> 
          <span style="text-align: center;width:100%;">
            <a href="<?php echo base_url('Fields/EditField/'.$row->FieldUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs "><i class="icon-pencil"></i></a>
          </span>
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



<script type="text/javascript">

	$(document).ready(function(){



    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  false,
      scrollY:auto,
      paging:true,

      

    });


    $(document).off('click','.adduser').on('click','.adduser', function(e) {

      
      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Users/SaveUser'); ?>",
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

    $('.confirmClick').click(function(e){

      e.preventDefault();
      console.log('Customer', this);
      var CustomerUID=$(this).attr('data-CustomerUID');
      // alert(CustomerUID);

      swal({
        title: 'Are you sure you want to delete this file?',
        text: 'You will not be able to recover Your file!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      }).then(function() {
       
        if(CustomerUID!=''){
          $.ajax({
            url:"<?php echo base_url();?>Customer/DeleteCustomer",
            method:"post",
            data:{'CustomerUID':CustomerUID},
            success:function(data){
              triggerpage('<?php echo base_url();?>Customer');
            }

          });
        }
        

// ajax request


}, function(dismiss) {
        // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
        if (dismiss === 'cancel') {
          swal({
            title: 'Cancelled',
            text: 'Now your file safe :)',
            type: 'error',
            confirmButtonClass: "btn btn-info",
            buttonsStyling: false
          }).catch(swal.noop)
        }
      })
  // var sure = confirm('Are you sure ?');
  // if(sure){
  //   return true;
  // }
  // return false;
})

			// $(document).off('keyup','#loginid').on('keyup','#loginid', function(e) {

     });
   </script>







