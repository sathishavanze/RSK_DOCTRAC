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

<div class="card mt-40 customcardbody"  id="Search">

  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Search Result</h4>
      </div>
    </div>
  </div>


  <div class="card-body">

    <?php foreach ($AllOrders as $key => $orders) { 

      if (!empty($orders)) { ?>
        <div class="panel panel-default table-responsive panel-table">
          <div class="panel-body">
            <div class="col-md-12">
              <h5 class="panel-heading mt-20 tbheading"><?php echo $key; ?></h5>
              <div class="col-md-12 col-xs-12 pd-0">
                <div class="material-datatables">
                  <table class="table table-hover search-datatables" style="width: 100%">
                    <thead>
                      <tr>
                        <th>Order No</th>
                        <th>Loan No</th>                  
                        <th>Borrower</th>                  
                        <th>Client</th>
                        <th>Milestone</th>                  
                        <th>Processor</th>                  
                        <th>Loan Type</th>
                        <th>Current Status</th>
                        <th>State</th>
                        <th>Aging</th>
                        <th>Associate</th>
                        <th>Due DateTime</th>
                        <th class="no-sort">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($orders as $myorders) { ?>
                        <tr>
                          <td> <?php echo $myorders->OrderNumber; ?> </td>
                          <td> <?php echo $myorders->LoanNumber; ?> </td>
                          <td> <?php echo $myorders->BorrowerFirstName; ?> </td>
                          <td> <?php echo $myorders->CustomerName; ?> </td>
                          <td> <?php echo $myorders->MilestoneName; ?> </td>
                          <td> <?php echo $myorders->LoanProcessor; ?> </td>
                          <td> <?php echo $myorders->LoanType; ?> </td>
                          <td> <?php echo '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>'; ?> </td>
                          <td> <?php echo $myorders->PropertyStateCode; ?> </td>
                          <td> <?php echo site_datetimeaging($myorders->EntryDatetime); ?> </td>
                          <td> <?php echo $myorders->UserName; ?> </td>
                          <td> <?php echo site_datetimeformat($myorders->DueDateTime); ?> </td>
                          <td> <?php echo '<a href="'.(!empty($myorders->redirectionpage) ? $myorders->redirectionpage.$myorders->OrderUID : base_url('Ordersummary/index/'.$myorders->OrderUID)).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload" target="_new"><i class="icon-pencil"></i></a>'; ?> </td>
                        </tr>
                      <?php } ?> 

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php }  ?>
      <?php }  ?>
    </div>
  </div>
  <script>


    $(function () {  
      var searchdatatables = $('.search-datatables').DataTable( {
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
    })
  </script>
