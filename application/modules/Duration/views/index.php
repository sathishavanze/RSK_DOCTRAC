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
<div class="card  customcardbody mt-40" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">WORKFLOW DURATION
		</div>
       <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
         <a href="<?php echo base_url('Duration/AddDuration'); ?>"  class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn" ><i class="icon-grid2"></i>  Add Duration</a>

      </div>
    </div>
	</div>
	<div class="card-body">
  <div class="col-md-12">

    <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
         <th class="text-left" style="width:50px">S.No</th>
         <th class="text-left">WorkFlow</th>
         <th class="text-left">Duration</th>
         <th class="text-left"  style="width:100px">Active</th>
         <th class="text-left"  style="width:60px">Action</th>
       </tr>
     </thead>
     <tbody>
      <?php $i=1;foreach($UserDetails as $row): ?>
      <tr>
       <td style="text-align: left;"><?php echo $i; ?></td>
       <td style="text-align: left;"><?php echo $GetWorkflowUIDName[$row->WorkflowModuleUID]; ?></td>
       <td style="text-align: left;"><?php echo $row->Hours; ?></td>
        <td style="text-align: left;"><div class="togglebutton">
					<label class="label-color"> 
						<input type="checkbox" id="Active" name="Active" class="Active" data-active="<?php echo $row->WorkflowModuleUID.'~'.$row->Hours.'~'.$row->DurationUID; ?>"  <?php if($row->Active == 1){ echo "checked"; } ?>>
						<span class="toggle"></span>
					</label>
				</div></td>
       <td style="text-align: left"> 
        <span style="text-align: left;width:100%;">
          <a href="<?php echo base_url('Duration/Editduration/'.$row->WorkflowModuleUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
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




<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){

    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  true,
    
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

		 //change active and inactive 
    $(document).on('change','#Active',function(){
      var data=($(this).data('active')).split('~');
      
      if($(this).prop("checked") == true){
        var status={'WorkflowModuleUID' : data[0],'WorkFlow' : data[0],'Active':'on','Duration':data[1],'DurationUID':data[2]};
      }else{
        var status={'WorkflowModuleUID' : data[0],'WorkFlow' : data[0],'Duration':data[1],'DurationUID':data[2]};
      }
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Duration/Updateduration'); ?>",
        data: status,
        dataType:'json',
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
        },
        error:function(xhr){

          console.log(xhr);
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







