<style type="text/css">
   .pd-btm-0 {
      padding-bottom: 0px;
   }

   .margin-minus8 {
      margin: -8px;
   }

   .mt--15 {
      margin-top: -15px;
   }

   .bulk-notes {
      list-style-type: none
   }

   .bulk-notes li:before {
      content: "*  ";
      color: red;
      font-size: 15px;
   }

   .nowrap {
      white-space: nowrap
   }

   .table-format>thead>tr>th {
      font-size: 12px;
   }

   .custom_add_icon {
      font-weight: 600;
      text-decoration: underline;
      font-size: 11px;
      text-align: right;
      margin: 0;
      display: block;
      cursor: pointer;
      color: #1A73E8;
   }

   .add_icon .bmd-form-group {
      float: left;
      width: 92%;
   }
</style>
<div class="card mt-40" id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Priority
      </div>
   </div>
   <div class="card-body">
      <form action="#" name="user_form" id="user_form">
         <div class="row">
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <label for="username" class="bmd-label-floating">Name</label>
                  <input type="text" class="form-control" id="PriorityName" name="PriorityName" value="<?php echo $PriorityDetails->PriorityName; ?>" />
                  <input type="hidden" name="PriorityUID" class="PriorityUID" value="<?php echo $PriorityDetails->PriorityUID; ?>">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <label for="CustomerUID" class="bmd-label-floating">Client</label>
                  <select class="select2picker form-control" id="CustomerUID" name="CustomerUID" readonly>
                     <option value=""></option>
                     <?php foreach ($Customer as $key => $value) { ?>
                        <?php if ($PriorityDetails->ClientUID == $value->CustomerUID) { ?>
                           <option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>

                     <?php }
                     } ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group bmd-form-group">
                  <label for="roleuid" class="bmd-label-floating">Help Text</label>
                  <input type="text" class="form-control" name="HelpText" value="<?php echo $PriorityDetails->HelpText; ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12" style="padding-bottom: 15px;">
               <div class="togglebutton">
                  <label class="label-color"> Active
                     <input type="checkbox" id="Active" name="Active" class="Active" <?php if ($PriorityDetails->Active == 1) {
                                                                                          echo "checked";
                                                                                       } ?>>
                     <span class="toggle"></span>
                  </label>
               </div>
            </div>
         </div>
         <?php foreach ($getPriorityData as $key => $value) {
         ?>
            <div class="row removeRow">
               <div class="col-md-4">
                  <div class="form-group bmd-form-group">
                     <label for="username" class="bmd-label-floating">Workflow Module</label>
                     <input type="text" class="form-control" id="WorkflowModuleUID" name="WorkflowModuleUID" value="<?php echo $getWorkflowModuleName[$value['WorkflowModuleUID']]; ?>" />
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group bmd-form-group">
                     <label for="roleuid" class="bmd-label-floating">Workflow Status</label>
                     <select class="select2picker form-control" id="WorkflowStatus" name="WorkflowStatus">
                        <option value="empty">Select Status</option>
                        <option value="Completed" <?php if ($value['WorkflowStatus'] == "Completed") {
                                                      echo "selected";
                                                   } ?>>Completed</option>
                        <option value="Pending" <?php if ($value['WorkflowStatus'] == "Pending") {
                                                   echo "selected";
                                                } ?>>Pending</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-1"><a style="width:8%;float:right;"><i class="fa fa-minus-circle removepriority pull-right" data-delete="<?php echo $value['WorkflowModuleUID']; ?>" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a></div>
            </div>
         <?php } ?>

         <div class="row">
            <div class="col-md-4">
               <label class="bmd-label-floating" for="WorkflowModuleUID">Workflow Module</label>
               <select name="priority[0][WorkflowModuleUID]" class="select2picker form-control" id="WorkflowModuleUID">
                  <option value=""> Select Workflow Module</option>
                  <?php
                  foreach ($getWorkflowModule as $keyWorkflow => $valueWorkflow) { ?>
                     <option value="<?php echo $valueWorkflow['WorkflowModuleUID']; ?>"> <?php echo $valueWorkflow['WorkflowModuleName']; ?> </option>
                  <?php }
                  ?>
               </select>
            </div>
            <div class="col-md-4">
               <label class=" bmd-label-floating" for="WorkflowStatus">Workflow Status</label>
               <select name="priority[0][WorkflowStatus]" class="select2picker form-control" id="WorkflowStatus">
                  <option value=""> Select Workflow Status</option>
                  <option value="Completed">Completed</option>
                  <option value="Pending">Pending</option>
               </select>
            </div>
            <div class="col-md-1" style="padding-top: 30px;">

               <a style="width:8%;float:right;"><i class="fa fa-plus-circle addchecklist pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a>
            </div>
         </div>

         <div class="appendRow"></div>

         <div class="ml-auto text-right">
            <!-- <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
            <a href="<?php echo base_url('PriorityCustomization'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Cancel</a>

            <button type="submit" class="btn btn-fill btn-update btn-wd updateuser" name="updateuser"><i class="icon-floppy-disk pr-10"></i>Update Priority </button>
         </div>
      </form>


   </div>
</div>

<script type="text/javascript">
   $(document).ready(function() {
      /* form submit */
      $(document).off('click', '.updateuser').on('click', '.updateuser', function(e) {
         var formdata = $('#user_form').serialize();
         button = $(this);
         button_val = $(this).val();
         button_text = $(this).html();
         $.ajax({
            type: "POST",
            url: "<?php echo base_url('PriorityCustomization/SavePriority'); ?>",
            data: formdata,
            dataType: 'json',
            beforeSend: function() {
               button.prop("disabled", true);
               button.html('Loading ...');
               button.val('<i class="fa fa-spin fa-spinner"></i> update..');

            },
            success: function(response) {
               if (response.Status == 2) {
                  $.notify({
                     icon: "icon-bell-check",
                     message: response.message
                  }, {
                     type: "success",
                     delay: 1000
                  });
                  setTimeout(function() {

                     triggerpage('<?php echo base_url(); ?>PriorityCustomization');

                  }, 3000);
               } else {
                  $.notify({
                     icon: "icon-bell-check",
                     message: response.message
                  }, {
                     type: "danger",
                     delay: 1000
                  });
                  $.each(response, function(k, v) {
                     console.log(k);
                     $('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
                     $('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');

                  });

               }
               button.html(button_text);
               button.val(button_val);
               button.prop('disabled', false);

            }
         });

      });

      /* append child checklist */
      var countChild = 0;
      $(document).on('click', '.addchecklist', function() {
         countChild++;
         $.post('PriorityCustomization/appendWorkflowModule', {
            'count': countChild
         }, function(result) {
            $('.appendRow').append(result);
            $('.appendRow').find('select.select2picker').select2();
         });
      });

      /* remove priority */
      $(document).on("click", ".removepriority", function(e) {
         var deleteData = $(this).data('delete');
         var PriorityUID = $('.PriorityUID').val();
         var PriorityName = $('.PriorityName').val();
         if (deleteData) {
            $.ajax({
               type: "post",
               url: "<?php echo base_url('PriorityCustomization/deleteWorkflow'); ?>",
               data: {
                  workflowUID: deleteData,
                  PriorityUID: PriorityUID,
                  PriorityName: PriorityName
               },
               success: function(data) {
                  console.log(data);
                  if (data) {
                     $(this).closest(".removeRow").remove();
                     $.notify({
                        message: "Deleted Successfully",
                     }, {
                        type: "success",
                        delay: 1000,
                     });
                     console.log(data);
                     setTimeout(function() {
                        location.reload();
                     }, 2000);
                  } else {
                     $.notify({
                        message: "Failed Delete Checklist ",
                     }, {
                        type: "danger",
                        delay: 1000,
                     });
                  }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  console.log(errorThrown);
               },
               failure: function(jqXHR, textStatus, errorThrown) {
                  console.log(errorThrown);
               },
            });
         } else {
            $(this).closest(".removeRow").remove();
         }
      });


      function log() {

         var loginid = $('#loginid').val();

         $.ajax({
            type: "POST",
            url: "<?php echo base_url('Users/CheckLoginUser'); ?>",
            data: {
               'loginid': loginid
            },
            dataType: 'json',
            success: function(response) {

               if (response.Status == 1) {

                  $('#loginexists').show();
               } else {
                  $('#loginexists').hide();
               }


            },
            error: function(xhr) {

               console.log(xhr);
            }
         });

      }


   });
</script>