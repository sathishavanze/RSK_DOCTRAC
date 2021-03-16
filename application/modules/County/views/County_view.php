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
.btnbtn-primary{
  color: red;
}
.modal-backdrop {
    position: fixed;
    z-index: 99;
    background-color: #000;
}
.modal {
    z-index: 999;
}
.col-sm-12 {
    flex: 0 0 100%;
    max-width: 100%;
}
</style>
<div class="card" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">County
    </div>
  </div>
  <div class="card-body">
        <div class="text-right">
                <a href="javascript:void(0);" class="btn btn-fill  btn-danger btn-wd" data-toggle="modal" data-target="#Modal_Add"><span class="fa fa-plus"></span> Add New</a></div>
        </div>

   <div class="material-datatables">
   <div class="col-sm-12">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
         <thead>
            <tr>
            <th  class="text-center">S.No</th>
               <th  class="text-center">CountyCode</th>
               <th  class="text-center">CountyName </th>
               <th  class="text-center">StateName</th>
                 <th  class="text-center">Active</th>
                 <th  class="text-center">Action</th>
                 </tr>
         </thead>
         <tbody>
         <?php $i=1;foreach($DocumentDetails as $row): ?>
      <tr>

       <td style="text-align: center;"><?php echo $i; ?></td>
       <td style="text-align: center;"><?php echo $row->CountyCode; ?></td>
       <td style="text-align: center;"><?php echo $row->CountyName; ?></td>
       <td style="text-align: center;"><?php echo $row->StateName; ?></td>
       <td style="text-align: center;"><div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($row->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div></td>
         <td style="text-align: center"> 
        <span style="text-align: center;width:100%;">
          <a href="javascript:void(0);" class="btn btn-link btn-info btn-just-icon btn-xs edit-btn" data-id="<?php echo $row->CountyUID; ?>"><i class="icon-pencil"></i></a> 
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
  <div class="card-body">
 <div class="tab-content tab-space">
<div class="tab-pane active" id="singleentry">

 <form id="user_form">
            <div class="modal fade" id="Modal_Add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Counties</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  <div class="row">
                  <div class="col-md-3">
                        <div class="form-group bmd-form-group">
                            <label for="statecode" class="bmd-label-floating">County Code</label>
                           <input type="text" name="CountyCode" id="CountyCode" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group bmd-form-group">
                            <label for="statname" class="bmd-label-floating">County Name</label>
                         
                              <input type="text" name="CountyName" id="CountyName" class="form-control">
                            </div>
                        </div>
                             <div class="col-md-3">
         <div class="form-group bmd-form-group">
          <label for="roleuid" class="bmd-label-floating">State Name<span class="mandatory"></span></label>
          <select class="select2picker form-control"  id="StateUID" name="StateUID">
           <option value=""></option>
           <?php foreach ($County as $key => $value) { ?>
           <option value="<?php echo $value->StateUID; ?>"><?php echo $value->StateName; ?></option>
           <?php } ?>               
         </select>
       </div>

     </div>
                 </div>
                 
                
                  <div class="col-sm-65 form-group pull-right">
                   <!--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --><p class="text-right">
                    <button type="button" type="submit"  class="btn btn-space btn-social btn-color btn-twitter btn_save">Save</button>
                    </p>
                   
                  </div>
                </div>
              </div>
            </div>
            </div>
            </form>
               </div>
            </div>
            </div>
            

                <div class="modal fade" id="Modal_Edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit County</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  <form id="user_form_update" class="update">
                       <div>

                  <div class="row">
                        <input type="hidden" name="CountyUID" id="CountyUIDUpdate" value="">
                  <div class="col-md-3">
                     <label for="countycode" class="bmd-label-floating">County Code</label>
                        <div class="form-group bmd-form-group">
                         <input type="text" name="CountyCode" id="CountyCodeUpdate" class="form-control" value="" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label  for="countyname" class="bmd-label-floating">County Name</label>
                        <div class="form-group bmd-form-group">
                         
                              <input type="text" name="CountyName" id="CountyNameUpdate" class="form-control" value="" />
                            </div>
                        </div>
                         <div class="col-md-3">
                         <label for="roleuid" class="bmd-label-floating">StateName</label>
                      <div class="form-group bmd-form-group">
                        <select class="select2picker form-control"  id="StateUIDUpdate" name="StateUID" >
                    
                        <option value=""></option>
                         <?php foreach ($County as $key => $value) { 
                          if($value->StateUID == $DocumentDetails->StateUID)
                            {?>
                          <optio value="<?php echo $value->StateUID; ?>" selected<?php echo $value->StateName; ?></option>
           <?php }else{ ?>
                        <option value="<?php echo $value->StateUID; ?>"><?php echo $value->StateName; ?>
                          <?php }
                      ?>
                    <?php } ?>
                        </option>
                         </select>
                        </div>
                        </div>
                            <div class="col-md-3">
                <div class="togglebutton" style="margin-top: 36pt;">
                  <label class="label-color"> Active
                    <input type="checkbox" id="ActiveUpdate" name="Active" class="">
                    <span class="toggle"></span>
                  </label>
                </div>
              </div> 
              </div> 

                 
                  
                  <div class="col-sm-76 form-group pull-right">
                   <!--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --><p class="text-right">
                    <button type="button" id="btn_update" class="btn btn-space btn-social btn-color btn-twitter btn_update">Update</button>
                    </p>
                   
                
                </div>
                </form>
              </div>
            </div>
            </div>
            </div>
            
         
           
        





<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>



<script type="text/javascript">

  $(document).ready(function(){



    $("#MaritalTableList").dataTable({
      processing: true,
      scrollX:  true,
      scrollY:  "400px",
      paging:true,



    });
    //function show all product
   //  $(document).off('click','.close').on('click','.close', function(e) {
   //  $('.bodyoverlaydiv').css('display','none');
   //     setTimeout(function(){ 

   //          triggerpage('<?php echo base_url();?>County');

   //        }, 500);
   // }); 
   $(document).off('click','.btn_save').on('click','.btn_save', function(e) {


      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('County/Save'); ?>",
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
               $('#Modal_Add').modal('hide');
              setTimeout(function(){ 
             triggerpage('<?php echo base_url();?>County');
           }, 100);
          }

        }
      });

    });

// $(document).off('click','.close').on('click','.close', function(e) {
//     $('.bodyoverlaydiv').css('display','none');
//        setTimeout(function(){ 

//             triggerpage('<?php echo base_url();?>County');

//           }, 100);
//    }); 
  $(document).off('click','.edit-btn').on('click','.edit-btn', function(e) {
    var CountyUID = $(this).attr('data-id');
    //alert(StateUID);
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('County/getdatabyCountyUID'); ?>",
      data: {'CountyUID':CountyUID},
      dataType:'json',
      success: function (response) {
        console.log(response);
          $('.bodyspinner_svg').css('display','none');
          $('.bodyoverlaydiv').css('display','none');
        $('#CountyUIDUpdate').val(response.CountyUID);
        $('#CountyCodeUpdate').val(response.CountyCode);
        $('#CountyNameUpdate').val(response.CountyName);
      /*  $('#StateUIDUpdate').val(response.StateUID);*/
        if(response.StateUID !='')
        {
         $('select[name="StateUID"] option[value="'+response.StateUID+'"]').attr("selected","selected");
         $("select.select2picker").select2({
theme: "bootstrap",
});
          $('#StateUIDUpdate').attr('selected','selected');
        }
 if(response.Active == 1)
        {
          $("#ActiveUpdate").prop("checked", true);
        }
        else
        {
          $("#ActiveUpdate").prop("checked", false);
        }

         $('#Modal_Edit').modal('show');
         $('.bodyspinner_svg').css('display','none');
       
    }
  });
   });

$(document).off('click','.btn_update').on('click','.btn_update', function(e) {
      var formdata = $('#user_form_update').serialize();
      console.log(formdata);
      button=$(this);
      button_val=$(this).val();
      button_text=$(this).html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('County/Update'); ?>",
        data: formdata,
        dataType:'json',
        beforeSend: function () {
          button.prop("disabled", true);
          button.html('Loading ...');
          button.val('<i class="fa fa-spin fa-spinner"></i> update..');
             $('#Modal_Edit').modal('hide');
        },
            success: function (response) {
        if(response.Status == 2)
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
          setTimeout(function(){ 

            triggerpage('<?php echo base_url();?>County');

          }, 500);
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


       
      

        //get data for update record
      

        //get data for delete record
        /*$('#show_data').on('click','.item_delete',function(){
            var product_code = $(this).data('product_code');
            
            $('#Modal_Delete').modal('show');
            $('[name="product_code_delete"]').val(product_code);
        });
*/
  });

</script>
</body>
</html>








