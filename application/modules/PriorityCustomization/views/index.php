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
<div class="card mt-40 customcardbody" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">Priority Customization
    </div>
    <!-- Order Info Header View -->
    <?php $this->load->view('orderinfoheader/loginfo'); ?>

    <div class="row">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
        <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i><a href="<?php echo base_url('PriorityCustomization/addpriority'); ?>" class="btn btn-fill  btn-success btn-wd ajaxload cardaddinfobtn"><i class="icon-user-plus pr-10"></i>Add Priority</a>
        <!--div id="headers" style="color: #ffffff;">
          <!-- Order Info Header View -->
        <?php //$this->load->view('orderinfoheader/loginfo'); 
        ?>

        <!--/div-->
      </div>
    </div>
    <div id="advancedFilterForReport" style="display: none;">
      <fieldset class="advancedsearchdiv">
        <legend>Advanced Search</legend>
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row ">
              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
                  <select class="select2picker form-control" id="adv_WorkflowModuleUID" name="WorkflowModuleUID">
                    <option value="All">All</option>
                    <?php foreach ($Modules as $key => $value) { ?>
                      <option value="<?php echo $value->WorkflowModuleUID; ?>"><?php echo $value->SystemName; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_Category" class="bmd-label-floating">Status</label>
                  <select class="select2picker form-control" id="adv_Category" name="Category">
                    <option value="All">All</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12  text-right pd-0 mt-10">
            <button type="button" class="btn btn-fill btn-facebook  filterreport">Submit</button>
            <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
          </div>

        </form>
      </fieldset>
    </div>



  </div>
  <div class="card-body">
    <div class="col-md-12">
      <div class="material-datatables">
        <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
          <thead>
            <tr>
              <th class="text-left">S.No</th>
              <th class="text-left">Name</th>
              <th class="text-left">Client</th>
              <th class="text-left">Active</th>
              <th class="text-left">Action</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    /* Advance fiter */
    $('.fa-filter').click(function() {
      $("#advancedFilterForReport").slideToggle();
    });

    /* reset function */
    $(document).off('click', '.reset').on('click', '.reset', function() {
      $("#adv_WorkflowModuleUID").val('All');
      $("#adv_Category").val('All');
      documentChecListTable('false');
      callselect2();

    });

    documentChecListTable(false);
    /* function for data table */
    function documentChecListTable(formdata) {
      MaritalTableList = $('#MaritalTableList').DataTable({
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: true,
        paging: true,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "pageLength": 10, // Set Page Length
        "lengthMenu": [
          [10, 25, 50, 100],
          [10, 25, 50, 100]
        ],
        fixedColumns: {
          leftColumns: 0,
          rightColumns: 0
        },

        language: {
          sLengthMenu: "Show _MENU_ Orders",
          emptyTable: "No Orders Found",
          info: "Showing _START_ to _END_ of _TOTAL_ Orders",
          infoEmpty: "Showing 0 to 0 of 0 Orders",
          infoFiltered: "(filtered from _MAX_ total Orders)",
          zeroRecords: "No matching Orders found",
          processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

        },

        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": "<?php echo base_url('PriorityCustomization/GetPriority') ?>",
          "type": "POST",
          "data": {
            'formData': formdata
          }
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }],
      });
      $($.fn.dataTable.tables(true)).css('width', '100%');
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }

    /* click function for form submit */
    $(document).off('click', '.adduser').on('click', '.adduser', function(e) {
      var formdata = $('#user_form').serialize();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Users/SaveDocument'); ?>",
        data: formdata,
        dataType: 'json',
        beforeSend: function() {

        },
        success: function(response) {
          /* check validation error from response */
          if (response.validation_error == 1) {
            $.notify({
              icon: "icon-bell-check",
              message: response.message
            }, {
              type: "danger",
              delay: 1000
            });
            /* each start for select class */
            $.each(response, function(k, v) {
              $('#' + k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
              $('#' + k + '.select2picker').next().find('span.select2-selection').addClass('errordisplay');
            });
            /* each end */
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

    /* Sorting position */
    $("#MaritalTableList tbody").sortable({
      axis: "y",
      cursor: "grabbing",
      handle: ".move-handle-icon",
      opacity: 1,
    });

    sortRequest = null;
    $("#MaritalTableList tbody").sortable({
      axis: "y",
      cursor: "grabbing",
      handle: ".move-handle-icon",
      opacity: 1,
      stop: function(event, ui) {
        var current = ui.item.attr("id");
        var wrkprz = ui.item.find("td:nth-child(1)").text();
        var sortData = new Array();
        $('[name="MaritalTableList_length"]').val('100').trigger('change');
        $("#MaritalTableList tbody tr").each(function() {
          sortData.push({
            Type: "Priority",
            ID: $(this).find('.icon_action').attr("data-position"),
          });
        });
        $('[name="MaritalTableList_length"]').val('10').trigger('change');
        console.log(sortData);
        if (sortRequest != null) {
          sortRequest.abort();
          sortRequest = null;
        }

        sortRequest = $.ajax({
          type: "POST",
          dataType: "JSON",
          global: false,
          url: base_url + "PriorityCustomization/priorityPosition",
          data: {
            sortData,
            current: current,
            wPzt: current,
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
          },
        });
      },
    });

    //filtering
    $(document).off('click', '.filterreport').on('click', '.filterreport', function() {
      var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();
      var Category = $('#adv_Category').val();
      if ((WorkflowModuleUID == '') && (Category == '')) {
        $.notify({
          icon: "icon-bell-check",
          message: 'Please Choose Search Keywords'
        }, {
          type: 'danger',
          delay: 1000
        });
      } else {
        var formData = ({
          'WorkflowModuleUID': WorkflowModuleUID,
          'Category': Category
        });
        documentChecListTable(formData);
      }
      return false;
    });



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