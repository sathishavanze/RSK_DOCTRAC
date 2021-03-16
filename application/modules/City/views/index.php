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

/*span.select2-container {
    z-index:10050;
}*/
</style>
<div class="card  customcardbody mt-20" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">CITY LIST 
		</div>
        <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
              <button type="button" class="btn btn-fill  btn-success btn-wd addcity cardaddinfobtn" data-toggle="modal" data-target="#AddCityModal">
      <i class="pr-10 icon-plus22"></i>Add City</button>



      </div>
    </div>


	</div>
	<div class="card-body">
    <div class="col-md-12">


    <div class="material-datatables">
      <table id="MaritalTableList"  style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column" >
       <thead>
        <tr>
        
         <th  class="text-left">City Name</th>
         <th  class="text-left">State Name</th>
         <th  class="text-left">County Name</th>
         <th  class="text-left">Zip Code</th>
        <!--  <th  class="text-center">Role</th> -->
         <th  class="text-left" style="width:60px">Action</th>
       </tr>
     </thead>
     
</table>
</div>
</div>

</div>
</div>

<div class="modal fade custommodal" id="AddCityModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     <div class="modal-header custommodal">
        <h5 class="modal-title" id="exampleModalLabel">Add City</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mb-0">
        <form action="#"  name="city_form" id="city_form" class="city_form mb-0">
          <div class="form-group bmd-form-group">
            <label for="recipient-name" class="bmd-label-floating">City Name <span class="mandatory"></span></label>
            <input type="text" class="form-control" id="city_name" name="city_name">
          </div>
          <div class="form-group bmd-form-group">
            <label for="message-text" class="bmd-label-floating">State <span class="mandatory"></span></label>
            <select class="select2picker form-control"  id="StateUID" name="StateUID">
           <option value=""></option>
           <?php foreach ( $getstate as $key => $value) { ?>
           <option value="<?php echo $value->StateUID; ?>"><?php echo $value->StateName; ?></option>
           <?php } ?>               
         </select>
          </div>
           <div class="form-group bmd-form-group">
            <label for="message-text" class="bmd-label-floating">County<span class="mandatory"></span></label>
             <select class="select2picker form-control"  id="CountyUID" name="CountyUID">
           <option value=""></option>
         </select>
          </div>
          <div class="form-group bmd-form-group">
            <label for="message-text" class="bmd-label-floating">Zip Code<span class="mandatory"></span></label>
             <input type="text" class="form-control" id="zipcode" name="zipcode">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel pull-right" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-space btn-save btn-color btn-twitter save_city" id="save_city">SAVE</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade custommodal" id="UpdateCityModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update City</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body  mb-0">
        <form action="#"  name="Upcity_form" id="Upcity_form" class="Upcity_form mb-0">
         <!--  <input type="hidden" name = "CityUID" id = "CityUID" > -->
          <div class="form-group bmd-form-group mt-10">
            <label for="recipient-name" class="bmd-label-floating">City Name <span class="mandatory"></span></label>
            <input type="text" class="form-control" id="Upcity_name" name="Upcity_name">
          </div>
          <div class="form-group bmd-form-group mt-10">
            <label for="message-text" class="bmd-label-floating">State <span class="mandatory"></span></label>
            <select class="select2picker form-control"  id="UpStateUID" name="UpStateUID">
           <option value=""></option>
           <?php foreach ( $getstate as $key => $value) { ?>
           <option value="<?php echo $value->StateUID; ?>"><?php echo $value->StateName; ?></option>
           <?php } ?>               
         </select>
          </div>
           <div class="form-group bmd-form-group mt-10">
            <label for="message-text" class="bmd-label-floating">County<span class="mandatory"></span></label>
             <select class="select2picker form-control"  id="UpCountyUID" name="UpCountyUID">
           <option value=""></option>
         </select>
          </div>
          <div class="form-group bmd-form-group mt-10">
            <label for="message-text" class="bmd-label-floating">Zip Code<span class="mandatory"></span></label>
             <input type="text" class="form-control" id="Upzipcode" name="Upzipcode">
          </div>
          <input type="hidden" name="UpCityUID" id="UpCityUID"  value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel pull-right" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-space btn-save btn-color btn-twitter Upsave_city" id="save_city">SAVE</button>
      </div>
    </div>
  </div>
</div>


<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

	$(document).ready(function(){

 
    completedinitialize();

     function completedinitialize()
    {
          review = $('#MaritalTableList').DataTable( {
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            scrollY: '100vh',
           
          
             "bDestroy": true,
            "autoWidth": true,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          "pageLength": 10, // Set Page Length
          "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
                  
          
                   

          language: {
            sLengthMenu: "Show _MENU_ Cities",
            emptyTable:     "No Cities Found",
            info:           "Showing _START_ to _END_ of _TOTAL_ Cities",
            infoEmpty:      "Showing 0 to 0 of 0 Cities",
            infoFiltered:   "(filtered from _MAX_ total Cities)",
            zeroRecords:    "No matching Cities found",
            processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

          },

          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>City/city_ajax_list",
            "type": "POST"
              
          },

          
        });
           
    }

     });

 $(document).ready(function(){
   $(document).off('click','.save_city').on('click','.save_city', function(e) {
    var formdata = $('#city_form').serialize();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('City/SaveCity'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading ...');
        button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
        

      },

      success: function (response) {
        if(response.Status == 0)
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
            $('#AddCityModal').modal('hide');
          setTimeout(function(){ 

            triggerpage('<?php echo base_url();?>City');

          }, 3000);
        }
        else if(response.Status == 10){
         $.notify(
         {
          icon:"icon-bell-check",
          message:response.message
        },
        {
          type:"info",
          delay:1000 
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
            type:"danger",
            delay:1000 
          });
          $.each(response, function(k, v) {
            console.log(k);
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
          });
         
        }
        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    });

   });
$(document).off('click','.Upsave_city').on('click','.Upsave_city', function(e) {
    var formdata = $('#Upcity_form').serialize();
    console.log(formdata);
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('City/Updatecitysave'); ?>",
      data: formdata,
      dataType:'json',
      beforeSend: function () {
        button.prop("disabled", true);
        button.html('Loading ...');
        button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
        

      },

      success: function (response) {
        if(response.Status == 0)
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
            $('#UpdateCityModal').modal('hide');
           setTimeout(function(){ 
            triggerpage('<?php echo base_url();?>City');

          }, 3000);
        }
        else
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
            console.log(k);
            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
          });
         
        }
        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    });
    });


   $("#StateUID").change(function(){
      var stateuid = $('#StateUID').val();
      $.ajax({
        type:"POST",
        url:"<?php echo base_url('City/Getcountys'); ?>",
        data: {stateuid:stateuid},
        dataType:'html',
        success: function(response){
          $('#CountyUID').html(response);
          callselect2();
        }
      })
 
    });

   // $("#UpStateUID").change(function(){
   //    var stateuid = $('#UpStateUID').val();
   //    $.ajax({
   //      type:"POST",
   //      url:"<?php echo base_url('City/Getcountys'); ?>",
   //      data: {stateuid:stateuid},
   //      dataType:'html',
   //      success: function(response){
   //        $('#UpCountyUID').html(response);
   //         console.log(response);
   //          callselect2();
   //      }
   //    })
 
   //  });

   $(".addcity").click(function(){
    $('#city_name').val('');
    $('#select2-StateUID-container').empty();
    $('#select2-CountyUID-container').empty();
    $('#zipcode').val('');


   });

   });

 $(document).on('click','.updatecity',function() {
       var cityuid =  $(this).data("idcity");
       $.ajax({
          type:"POST",
          url:"<?php echo base_url('City/Updatecity'); ?>",
          data: {cityuid:cityuid},
          dataType:'json',
          success: function(response){
           
            $('#UpdateCityModal').modal('show');
            $("#Upcity_name").val(response['updateresponse'].CityName).trigger('change');
            $("#UpStateUID").val(response['updateresponse'].StateUID);
            $('#UpCountyUID').html(response['counties']);
            $("#UpCountyUID").val(response['updateresponse'].CountyUID).trigger('change');
            $("#Upzipcode").val(response['updateresponse'].ZipCode).trigger('change');
            $("#UpCityUID").val(response['updateresponse'].CityUID).trigger('change');
            callselect2();
        }
      })
 
    });
 </script>







