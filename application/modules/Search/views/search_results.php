<!-- 
  * @desc Page to show the list of orders that are matched with search value
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since July 20th 2020
  * @version E-Fax Integration
-->

<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>thead>tr>td {
    padding: 0px !important;
    font-size: 11px !important;
  }
  .tbheading{
    font-weight: bold;
    background-color: #efefef;
    padding: 5px;
  }
</style>

<div class="card-body">
  <?php if(!empty($AllOrders)) { ?>
    <h6 class="panel-heading">Search Results...</h6>
    <?php foreach ($AllOrders as $key => $orders) { ?>
      <?php if (!empty($orders)) { ?>
        <div class="panel panel-default table-responsive panel-table">
          <div class="panel-body">
            <div class="col-md-12">
              <h5 class="panel-heading mt-20 tbheading"><?php echo $key; ?></h5>
              <div class="col-md-12 col-xs-12 pd-0">
                <div class="material-datatables">
                  <table class="table table-hover search-result-datatables nowarp" style="width: 100%">
                    <thead>
                      <tr>
                        <th class="no-sort">Action</th>
                        <th>Order#</th>
                        <th>Loan#</th>
                        <th>Status</th>
                        <th>City</th>
                        <th>State</th>
                        <th>ZipCode</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($orders as $keys => $row) { ?>
                        <tr data-id="<?php echo $row->OrderUID; ?>">
                          <td style="text-align: center;">
                            <div class="be-radio be-radio-color inline">
                              <input type="radio" class="copy_select_radio" value="<?php echo $row->OrderUID; ?>" name="PDF_Radio" >
                            </div>
                          </td>
                          <td>
                            <?php                       
                            switch ($key) {
                              case 'PreScreen Orders':
                              echo '<a href="'.base_url('PreScreen/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Welcome Call':
                              echo '<a href="'.base_url('WelcomeCall/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Title Team Orders':
                              echo '<a href="'.base_url('TitleTeam/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'FHA/VA Orders':
                              echo '<a href="'.base_url('FHA_VA_CaseTeam/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Third Party Orders':
                              echo '<a href="'.base_url('ThirdPartyTeam/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'HOI Orders':
                              echo '<a href="'.base_url('HOI/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Borrower Doc Orders':
                              echo '<a href="'.base_url('BorrowerDoc/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'PayOff Orders':
                              echo '<a href="'.base_url('PayOff/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'PE Orders':
                              echo '<a href="'.base_url('PE/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'CD Orders':
                              echo '<a href="'.base_url('CD/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Final Approval Orders':
                              echo '<a href="'.base_url('FinalApproval/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Work Up Orders':
                              echo '<a href="'.base_url('Workup/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Doc Chase Orders':
                              echo '<a href="'.base_url('DocChase/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'UnderWriter Orders':
                              echo '<a href="'.base_url('UnderWriter/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Scheduling Orders':
                              echo '<a href="'.base_url('Scheduling/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              case 'Closing Orders':
                              echo '<a href="'.base_url('Closing/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>';
                              break;

                              default:
                              echo '<a href="'.base_url('Ordersummary/index/'.$row->OrderUID).'" class="ajaxload" target="_blank">'.$row->OrderNumber.'</a>'; 
                              break;
                            }
                            ?> 
                          </td>
                          <td> <?php echo $row->LoanNumber; ?> </td>
                          <td> <?php echo '<a  href="javascript:void(0)" style=" background: '.$row->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$row->StatusName.'</a>'; ?> </td>
                          <td> <?php echo $row->PropertyCityName; ?> </td>
                          <td> <?php echo $row->PropertyStateCode; ?> </td>
                          <td> <?php echo $row->PropertyZipCode; ?> </td>                    
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php } ?>
  <?php } else { ?>
    <div class="panel panel-default table-responsive panel-table">
      <div class="panel-body">
        <div class="col-md-12">
          <h5 class="panel-heading mt-20 tbheading">No Record Found</h5>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<script type="text/javascript">
    $(function () {  
      var searchdatatables = $('.search-result-datatables').DataTable( {
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        scrollY: '100vh',
        paging:  true,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true, //Feature control the processing indicator.
        "order": [], //Initial no order.
        "pageLength": 10, // Set Page Length
        "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
          sLengthMenu: "Show _MENU_ Orders",
          emptyTable:     "No Orders Found",
          info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
          infoEmpty:      "Showing 0 to 0 of 0 Orders",
          infoFiltered:   "(filtered from _MAX_ total Orders)",
          zeroRecords:    "No matching Orders found",
          processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>'
        },
        "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
        } ]

      });
    });
  </script>