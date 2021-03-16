			<style type="text/css">
				.bootstrap-select .dropdown-menu{
					z-index: 9999 !important;
				}
				#LoanPrioritizationMetrics .select2-container.select2-container-disabled .select2-choice {
					background-color: #ffffff;
					border:0;
					border-radius:0;
					padding-left:0;
				}
				#LoanPrioritizationMetrics .select2-container.select2-container-disabled .select2-choice .select2-arrow {
					background-color: #ffffff;
				}
				#LoanPrioritizationMetrics .select2-container-multi .select2-choices{
					border: 0;
					border-bottom: 1px solid #c1c1c1;
					background-image: none;
				}
				#LoanPrioritizationMetrics tbody tr td{
					padding: 8px 5px 8px 5px!important;
				}
				.move-handle-icon{
					cursor:grab;
				}
			</style>

		<div class="well clearfix">
		   <a class="btn btn-default pull-right add-record active" data-added="0"><i class="glyphicon glyphicon-plus"></i> Add Row</a>
		</div>

		<div class="material-datatables" >
			<table class="table display nowrap sort_par_div" id="LoanPrioritizationMetrics"  cellspacing="0" width="100%"  style="width:100%">
				<thead>
					<tr>
						<th style="width: 5%">Priority</th>
						<th style="width: 20%">Workflow</th>
						<th style="width: 65%">Dependent Completed Workflows</th>									
						<th style="width: 5%;" class="text-center">Action</th>	
						<th style="width: 5%;" class="text-center"></th>									
					</tr>
				</thead>
				 <tbody id="tbl_posts_body">
				 	<?php
				 	$j=1;
				 	foreach ($CustomerWorkflowMetricsDetails as $key => $value) { ?>
                    <tr id='rec-<?php echo $j; ?>'>
	                  <td>
	                	<span class="sn"><?php echo $j; ?></span>
                    <input type="hidden" name="CustomerWorkflowMetricUID[]" class="CustomerWorkflowMetricUID" value="<?php echo $value['CustomerWorkflowMetricUID']; ?>">
	                  </td>
                      <td>
                      	<div class="form-group">
                      		<select class="select2picker WorkflowModuleUID" name="WorkflowModuleUID" style="width: 100%">                   
                      			<?php foreach ($Customer_Workflow as $key => $workflow) {
                      				if($workflow['WorkflowModuleUID'] == $value['WorkflowModuleUID']){  ?>

                      					<option value="<?php echo $workflow['WorkflowModuleUID']; ?>" selected><?php echo $workflow['SystemName'];?></option>

                      				<?php }else{  ?>

                      					<option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>

                      				<?php } } ?>  
                      			</select>
                      </td>
                      <td style="width: 70%">
                      	<select class="select2picker DependentWorkflowModuleUID" name="DependentWorkflowModuleUID[]" multiple="multiple" style="width: 100%">  
                          <?php                    
                          $CompletedQueues_Arr = explode (",", $value['CompletedQueues_Arr']);
                          foreach ($Customer_Workflow as $key => $workflow) {
                            if($workflow['WorkflowModuleUID'] == $value['WorkflowModuleUID']){  ?>
                              <option value="<?php echo $workflow['WorkflowModuleUID']; ?>" disabled><?php echo $workflow['SystemName'];?></option>
                            <?php }
                            else if (in_array($workflow['WorkflowModuleUID'], $CompletedQueues_Arr)) {   ?>
                              <option value="<?php echo $workflow['WorkflowModuleUID']; ?>" selected><?php echo $workflow['SystemName'];?></option>
                            <?php } else {?>
                              <option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>
                            <?php }
                          } ?> 
                      	</select>
                      </td>                   
	                  <td>
	                  	<a class="delete-record" data-id="<?php echo $j; ?>" title="Delete Workflow">
                        <i class="fa fa-trash pull-right" aria-hidden="true" style="font-size: 20px;"></i>
                      </a>
	                  </td>
                      <td class="text-center" style="width: 5%">
                        <span title="Move" class="icon_action move-handle-icon" style="color: #000;"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                      </td>   
                    </tr>
                <?php 
                $j++;
            }?>
                  </tbody>
			</table>
		</div>

		<div style="display:none;">
		   <table id="sample_table">
                <tr id="">
                	<td>
                    <span class="sn"></span>
                    <input type="hidden" name="CustomerWorkflowMetricUID[]" class="CustomerWorkflowMetricUID" value="">
                  </td>
                  <td>
                  	<div class="form-group">
                  		<select class="select2picker WorkflowModuleUID" name="WorkflowModuleUID" style="width: 100%">                   
              			<?php foreach ($Customer_Workflow as $key => $workflow) { ?>

          					<option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>

              				<?php } ?>  
              			</select>
              		</div>
                  </td>
                  <td style="width: 70%">
                  	<select class="select2picker DependentWorkflowModuleUID" name="DependentWorkflowModuleUID[]" multiple="multiple" style="width: 100%">  
                  		<option value=""></option>
                  		<?php 
                      $i = 0;
                      foreach ($Customer_Workflow as $wkey => $Workflow) { 
                        if ($i == 0) { ?>
                          <option value="<?php echo $Workflow['WorkflowModuleUID']; ?>" disabled><?php echo $Workflow['WorkflowModuleName'];?></option>
                        <?php } else { ?>
                  			<option value="<?php echo $Workflow['WorkflowModuleUID']; ?>" ><?php echo $Workflow['WorkflowModuleName'];?></option>
                  		  <?php } $i++;
                      } ?>
                  	</select>
                  </td>
                  <td>
                  	<a class="delete-record" title="Delete Workflow">
                      <i class="fa fa-trash pull-right" aria-hidden="true" style="font-size: 20px;"></i>
                    </a>
                  </td>
                  <td class="text-center" style="width: 5%">
                    <span title="Move" class="icon_action move-handle-icon" style="color: #000;"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                  </td>
                </tr>
		   </table>
		</div>

		<div class="ml-auto text-right">
			<a href="Customer" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
			<!-- <button type="submit" class="btn btn-fill btn-update btn-wd " name="Update_Productsetup" id="Update_Productsetup"><i class="icon-floppy-disk pr-10"></i>Update</button> -->
		</div>
		<div class="clearfix"></div>
		<script src="assets/js/jquery-ui.min.js"></script>

    <script type = "text/javascript" >

    function callselect2() {
        $("select.select2picker").select2({
            theme: "bootstrap",
        });
    }

    $(function() {
        $("select.select2picker").select2({
            theme: "bootstrap",
        });
    });

    $(document).ready(function() {

        $("div.inner.show").addClass("perfectscrollbar");
        $('.perfectscrollbar').perfectScrollbar();

        $(document).off('change', '.DependentWorkflowModuleUID').on('change', '.DependentWorkflowModuleUID', function(e) {
            var $t = $(this);
            var CustomerUID = $('#CustomerUID').val();
            var WorkflowModuleUID = $t.closest("tr").find('.WorkflowModuleUID :selected').val();
            var DependentWorkflowModuleUID = $t.val();
            var CustomerWorkflowMetricUID = $t.closest("tr").find('.CustomerWorkflowMetricUID').val();
            var Priority = $t.closest("tr").find('.sn').text();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>Customer/UpdateLoanPrioritizationMetrics',
                data: {
                    'CustomerUID': CustomerUID,
                    'WorkflowModuleUID': WorkflowModuleUID,
                    'DependentWorkflowModuleUID': DependentWorkflowModuleUID,
                    'CustomerWorkflowMetricUID': CustomerWorkflowMetricUID,
                    'Priority': Priority
                },
                success: function(data) {
                    console.log(data);
                    $.notify({
                        icon: "icon-bell-check",
                        message: data.msg
                    }, {
                        type: data.type,
                        delay: 1000
                    });
                    $t.closest("tr").find('.CustomerWorkflowMetricUID').val(data.CustomerWorkflowMetricUID);
                    UpdateWorkPriority();
                }
            });
                 
        });

        function UpdateWorkPriority() {
          var CustomerUID = $('#CustomerUID').val();
          var data = $('.sort_par_div .CustomerWorkflowMetricUID').serialize() + '&CustomerUID=' + CustomerUID;
          console.log(data);

          $.ajax({
            type: 'POST',
            dataType: 'JSON',
            global: false,
            url: '<?php echo base_url();?>Customer/PositionLoanPrioritizationMetrics',
            data: data,
            success: function(data) {
              console.log(data);
              // $.notify({
              //     icon: "icon-bell-check",
              //     message: data.msg
              // }, {
              //     type: data.type,
              //     delay: 1000
              // });
            }
          });
        }

        $(document).off('change', '.WorkflowModuleUID').on('change', '.WorkflowModuleUID', function(e) {

            var $t = $(this);
            var WorkflowModuleUID = $t.val();

            $t.closest("tr").find("select.DependentWorkflowModuleUID option").prop("disabled",false);

            $t.closest("tr").find("select.DependentWorkflowModuleUID").find("option[value='" + WorkflowModuleUID + "']").prop("disabled",true).prop('selected', false);

            $t.closest("tr").find("select.DependentWorkflowModuleUID").trigger('change.select2');

            var CustomerUID = $('#CustomerUID').val();
            var CustomerWorkflowMetricUID = $t.closest("tr").find('.CustomerWorkflowMetricUID').val();
            var DependentWorkflowModuleUID = $t.closest("tr").find('select.DependentWorkflowModuleUID').val();

            if (DependentWorkflowModuleUID == "") {
              return false;
            }

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>Customer/UpdateWorkflowModuleUIDLoanPrioritizationMetrics',
                data: {
                    'CustomerUID': CustomerUID,
                    'WorkflowModuleUID': WorkflowModuleUID,
                    'CustomerWorkflowMetricUID': CustomerWorkflowMetricUID
                },
                success: function(data) {
                    console.log(data);
                    $.notify({
                        icon: "icon-bell-check",
                        message: data.msg
                    }, {
                        type: data.type,
                        delay: 1000
                    });
                }
            });
                 
        });

        var fixHelperModified = function(e, tr) {
          var $originals = tr.children();
          var $helper = tr.clone();
          $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width())
          });
          console.log($helper);
          return $helper;
        },
        updateIndex = function(e, ui) {
          $('td span.sn', ui.item.parent()).each(function (i) {
            $(this).html(i+1);
          });
        };

        $(".sort_par_div tbody").sortable({
            axis: "y",
            cursor: "grabbing",
            handle: ".move-handle-icon",
            opacity: 1,
            helper: fixHelperModified,
            stop: updateIndex,
            update: function (event, ui) {
              var CustomerUID = $('#CustomerUID').val();
              var data = $('.sort_par_div .CustomerWorkflowMetricUID').serialize() + '&CustomerUID=' + CustomerUID;
              console.log(data);

              $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>Customer/PositionLoanPrioritizationMetrics',
                data: data,
                success: function(data) {
                  console.log(data);
                  $.notify({
                      icon: "icon-bell-check",
                      message: data.msg
                  }, {
                      type: data.type,
                      delay: 1000
                  });
                }
              });
            }
        });

        $(document).off('click', 'a.add-record').on('click', 'a.add-record', function(e) {
            e.preventDefault();
            $("select.select2picker").select2('destroy');
            var content = $('#sample_table tr'),
                size = $('#LoanPrioritizationMetrics >tbody >tr').length + 1,
                element = null,
                element = content.clone();
            element.attr('id', 'rec-' + size);
            element.find('.delete-record').attr('data-id', size);
            element.appendTo('#tbl_posts_body');
            element.find('.sn').html(size);
            $("select.select2picker").select2({
                theme: "bootstrap",
            });
        });

        $(document).off('click', 'a.delete-record').on('click', 'a.delete-record', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $('#rec-' + id).remove();

            //regnerate index number on table
            $('#tbl_posts_body tr').each(function(index) {
                $(this).find('span.sn').html(index + 1);
            });  

            var CustomerUID = $('#CustomerUID').val();
            var CustomerWorkflowMetricUID = $(this).closest("tr").find('.CustomerWorkflowMetricUID').val();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>Customer/DeleteLoanPrioritizationMetrics',
                data: {
                    'CustomerWorkflowMetricUID': CustomerWorkflowMetricUID,
                    'CustomerUID': CustomerUID
                },
                success: function(data) {
                    console.log(data);
                    $.notify({
                        icon: "icon-bell-check",
                        message: data.msg
                    }, {
                        type: data.type,
                        delay: 1000
                    });
                    UpdateWorkPriority();
                }
            });         

        });

    });

    </script>
