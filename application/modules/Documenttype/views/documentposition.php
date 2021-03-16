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
</style>
<div class="card mt-20 customcardbody" id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Checklists
      </div>
   </div>
   <div class="card-body">
      <div class="col-md-12">
         <input type="hidden" id="Category" value="<?php echo $this->uri->segment(3); ?>">
         <div class="material-datatables">
            <table id="MaritalTableList" class="table-striped  table-hover order-column">
               <thead>
                  <tr>
                     <th class="text-left" style="width: 40px;">S.No</th>
                     <th class="text-left" style="width: 800px;">Name</th>
                     <th class="text-left">Category</th>
                     <th class="text-left">Loan Type</th>
                     <th class="text-left">Position</th>
                  </tr>
               </thead>
               <tbody class="test1">
               <?php 
                $i = 1;
               foreach($DocumentDetails as $key => $value){
                  ?>
                  <tr class="parent">
                     <td><?php echo $i; ?></td>
                     <td><?php echo $value->DocumentTypeName; ?></td>
                     <td><?php echo $value->CategoryName; ?></td>
                     <td><?php echo $value->Groups; ?></td>
                     <td><span title="Move" class="icon_action move-handle-icon" style="color: #000;" data-position="<?php echo $value->DocumentTypeUID; ?>"><i class="fa fa-arrows" aria-hidden="true"></i></span></td>
                  </tr>
              <?php 
              $childChecklist= $this->Documenttype_model->getDocumentPositionChildChecklist($this->uri->segment(3),$value->DocumentTypeUID);
               if($childChecklist){
                 $child=1;  foreach($childChecklist as $keyChild => $valueChild){ ?>
                  <tr class="child">
                     <td><?php echo $i.'.'.$child; ?></td>
                     <td><?php echo $valueChild->DocumentTypeName; ?></td>
                     <td><?php echo $valueChild->CategoryName; ?></td>
                     <td><?php echo $valueChild->Groups; ?></td>
                     <td><span title="Move" class="icon_action move-handle-icon" style="color: #000;" data-parent="<?php echo $i.'.'.$child; ?>" data-position="<?php echo $valueChild->DocumentTypeUID; ?>"><i class="fa fa-arrows" aria-hidden="true"></i></span></td>
                  </tr>
               <?php  
               $child++; }  } 
              $i++;
              }
               ?>
               </tbody>
            </table>
         </div>
         <div class="ml-auto text-right">
				<!-- <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
				<a href="<?php echo base_url('Documenttype'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
			</div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function() {
 
      /* ajax for add */
      $(document).off('click', '.adduser').on('click', '.adduser', function(e) {
         var formdata = $('#user_form').serialize();
         $.ajax({
            type: "POST",
            url: "<?php echo base_url('Users/SaveDocument'); ?>",
            data: formdata,
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
               if (response.validation_error == 1) {
                  $.notify({
                     icon: "icon-bell-check",
                     message: response.message
                  }, {
                     type: "danger",
                     delay: 1000
                  });
                  $.each(response, function(k, v) {
                     $('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
                     $('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');
                  });
               } else {
                  $.notify({
                     icon: "icon-bell-check",
                     message: response.message
                  }, {
                     type: "success",
                     delay: 1000
                  });
               }
            }
         });
      });

      /* checklist drag and drop start */
      $("#MaritalTableList tbody").sortable({
         axis: "y",
         items: "tr",
         cursor: "grabbing",
         handle: ".move-handle-icon",
         opacity: 1,
      });
  /*     $("#MaritalTableList tbody.test ").sortable({
         axis: "y",
         items: "tr.child",
         cursor: "grabbing",
         handle: ".move-handle-icon",
         opacity: 1,
      }); */
      sortRequest = null;
      $("#MaritalTableList tbody").sortable({
         axis: "y",
         cursor: "grabbing",
         handle: ".move-handle-icon",
         opacity: 1,
         stop: function(event, ui) {
            var current = ui.item.attr("id");
            var dataUID = ui.item.attr("data-delete");
            var OrderUID = $("#OrderUID").val();
            var wrkprz = ui.item.find("td:nth-child(1)").text();
            var sortData = new Array();
            var CustomerUID = $("#CustomerUID").val();
            $("#MaritalTableList tbody tr").each(function() {
               sortData.push({
                  Type: "Parent",
                  ID: $(this).find('.icon_action').attr("data-position"),
               });
            });

            if (sortRequest != null) {
               sortRequest.abort();
               sortRequest = null;
            }

            sortRequest = $.ajax({
               type: "POST",
               dataType: "JSON",
               global: false,
               url: base_url + "Documenttype/documentChecklistPosition",
               data: {
                  sortData,
                  current: current,
                  wPzt: current,
                  OrderUID: OrderUID,
               },
               success: function(data) {
                  console.log(data);
                  $.notify({
                     icon: "icon-bell-check",
                     message: data.msg,
                  }, {
                     type: data.type,
                     delay: 1000,
                  });
                  setTimeout(function() {
                     location.reload();
                  }, 2000); 
                  /* setTimeout(function() {
                     triggerpage('<?php echo base_url(); ?>Documenttype');
                  }, 3000); */
               },
            });
         },
      });

      $(document).ready(function() {
         $('#MaritalTableList').DataTable( {
            "scrollY": "350px",
            "scrollCollapse": true,
            "paging": false
         } );
         $('.dataTables_info').hide();
      } );

      // $(document).off('keyup','#loginid').on('keyup','#loginid', function(e) {

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
      // });

   });
</script>