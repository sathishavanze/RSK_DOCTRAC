<table class="table table-stripd table-bordered grp-mdl">
 <thead>
  <tr>
    <th width="90" class="text-center">Orders Due</th>
    <th width="110" class="text-center">Due By</th>
    <th class="text-center" id="orders">
        <div class="icon-container">
            <div class="icon1 totalcount">
                <span class="icon-file-text"></span>
            </div>
        </div><br>
        Total Pending Orders
    </th>
    <th class="text-center" id="search">
        <div class="icon-container">
            <div class="icon9"><span class="icon-home8"></span></div>
        </div><br>
        Waiting For Images
    </th>
    <th class="text-center" id="search">
        <div class="icon-container">
            <div class="icon10"><span class="icon-paperplane"></span></div>
        </div><br>
        Stacking
    </th>
    <th class="text-center" id="exceptions">
        <div class="icon-container">
            <div class="icon6"><span class="icon-wall"></span></div>
        </div><br>
        Exceptions
    </th>
    <th class="text-center" id="taxcert">
        <div class="icon-container">
            <div class="icon5"><span class="icon-file-eye"></span></div>
        </div><br>
        Review
    </th>
    <th class="text-center" id="taxcert">
        <div class="icon-container">
            <div class="icon5"><span class="icon-stamp"></span></div>
        </div><br>
        Export
    </th>
</tr>
</thead>
<tbody>
 <!-- PAST DUE ROWS STARTS -->

 <?php

 $total_orders = [];
 if (!empty($PastDueOrders)) { 
    $past_total_orders = [];
    $past_rowspan = 1;
    foreach ($PastDueOrders as $key => $value) { ?>
        <tr>
            <?php if ($past_rowspan == 1) { ?>
                <td class="text-center" rowspan="<?php echo count($PastDueOrders);  ?>"><b>Orders Due</b></td>
                
            <?php } ?>
            <td class="text-center"><b><?php echo $key; ?></b></td>            
            <?php

            foreach ($value as $orderkey => $order) {

                // To add orders by status;
                if (!isset($past_total_orders[$orderkey]['count'])) {
                    $past_total_orders[$orderkey]['count'] = 0;
                    $past_total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $past_total_orders[$orderkey]['count'] += $order['count'];  
                $past_total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];

                // Total Due orders load;
                if (!isset($total_orders[$orderkey]['count'])) {
                    $total_orders[$orderkey]['count'] = 0;
                    $total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $total_orders[$orderkey]['count'] += $order['count'];  
                $total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];
                ?>
                <td class="text-center"><a href="javascript:void(0);"   data-OrderUID="<?php echo $order['OrderUIDs']; ?>" class="click_count"><?php echo $order['count']; ?></a></td>
                <?php
            }
            $past_rowspan++;
        }
        ?>
    </tr>            
    <?php
}
?>
<!-- PAST DUE ROWS ENDS -->

<?php if (!empty($past_total_orders)) { ?>
    <tr>
        <td class="text-center" colspan="2"><b>Total Past Due</b></td>
        <?php 
        foreach ($past_total_orders as $key => $value) { ?>
         <td class="text-center"><a href="javascript:void(0);" data-OrderUID="<?php echo $value['OrderUIDs']; ?>" class="click_count"><?php echo $value['count']; ?></a></td>
     <?php } ?>
 </tr>   
<?php } ?>

<!-- DUE TODAY ROWS STARTS -->
<?php
if (!empty($DueTodayOrders)) { 
    $duetoday_rowspan = 1;
    $duetoday_total_orders = [];

    foreach ($DueTodayOrders as $key => $value) { ?>
        <tr>
            <?php if ($duetoday_rowspan == 1) { ?>
                <td class="text-center"  rowspan="<?php echo count($DueTodayOrders); ?>"><b>Due Today</b></td>
                
            <?php } ?>
            <td class="text-center"><b><?php echo $key; ?></b></td>            
            <?php

            foreach ($value as $orderkey => $order) { 
            // To add orders by status;
                if (!isset($duetoday_total_orders[$orderkey]['count'])) {
                    $duetoday_total_orders[$orderkey]['count'] = 0;
                    $duetoday_total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $duetoday_total_orders[$orderkey]['count'] += $order['count'];
                $duetoday_total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];

            // Total Due orders load;
                if (!isset($total_orders[$orderkey]['count'])) {
                    $total_orders[$orderkey]['count'] = 0;
                    $total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $total_orders[$orderkey]['count'] += $order['count'];
                $total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];

                ?>
                <td class="text-center"><a href="javascript:void(0);"  data-OrderUID="<?php echo $order['OrderUIDs']; ?>"  class="click_count"><?php echo $order['count']; ?></a></td>
                <?php
            }
            $duetoday_rowspan++;
        }
        ?>
    </tr>            
    <?php
}
?>

<?php if (!empty($duetoday_total_orders)) { ?>
    <tr>
        <td class="text-center" colspan="2"><b>Total Due Today</b></td>
        <?php 
        foreach ($duetoday_total_orders as $key => $value) { ?>
         <td class="text-center"><a href="javascript:void(0);"   data-OrderUID="<?php echo $value['OrderUIDs']; ?>" class="click_count"><?php echo $value['count']; ?></a></td>
         <?php 
     } ?>
 </tr>   
 <?php 
} ?>
<!-- DUE TODAY ROWS ENDS -->


<!-- FUTURE DUE ROWS STARTS -->
<?php
if (!empty($FutureDueOrders)) { 
    $futuredue_rowspan = 1;
    $futuredue_total_orders = [];

    foreach ($FutureDueOrders as $key => $value) { ?>
        <tr>
            <?php if ($futuredue_rowspan == 1) { ?>
                <td class="text-center" rowspan="<?php echo count($FutureDueOrders); ?>"><b>Future Due</b></td>
                
            <?php } ?>
            <td class="text-center"><b><?php echo $key; ?></b></td>            
            <?php

            foreach ($value as $orderkey => $order) { 
            // To add orders by status;
                if (!isset($futuredue_total_orders[$orderkey]['count'])) {
                    $futuredue_total_orders[$orderkey]['count'] = 0;
                    $futuredue_total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $futuredue_total_orders[$orderkey]['count'] += $order['count'];
                $futuredue_total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];

                            // Total Due orders load;
                if (!isset($total_orders[$orderkey]['count'])) {
                    $total_orders[$orderkey]['count'] = 0;
                    $total_orders[$orderkey]['OrderUIDs'] = '0';
                }
                $total_orders[$orderkey]['count'] += $order['count'];
                $total_orders[$orderkey]['OrderUIDs'] .= ', ' . $order['OrderUIDs'];

                ?>
                <td class="text-center"><a href="javascript:void(0);" data-OrderUID="<?php echo $order['OrderUIDs']; ?>" class="click_count"><?php echo $order['count']; ?></a></td>
                <?php
            }
            $futuredue_rowspan++;
        }
        ?>
    </tr>            
    <?php
}
?>

<?php if (!empty($futuredue_total_orders)) { ?>
    <tr>
        <td class="text-center" colspan="2"><b>Total Future Due</b></td>
        <?php 
        foreach ($futuredue_total_orders as $key => $value) { ?>
            <td class="text-center"><a href="javascript:void(0);" data-OrderUID="<?php echo $value['OrderUIDs']; ?>" class="click_count"><?php echo $value['count']; ?></a></td>
            <?php 
        } ?>
    </tr>   
    <?php 
} ?>

<!-- FUTURE DUE ROWS ENDS -->

<?php if (!empty($total_orders)) { ?>
    <tr>
        <td class="text-center" colspan="2"><b>Total Orders</b></td>
        <?php 
        foreach ($total_orders as $key => $value) { ?>
            <td class="text-center"><a href="javascript:void(0);"   data-OrderUID="<?php echo $value['OrderUIDs']; ?>" class="click_count"><?php echo $value['count']; ?></a></td>
            <?php 
        } ?>
    </tr>   
    <?php 
} ?>

</tbody>
</table>
<script type="text/javascript">
    $('.click_count').click(function(){
        var OrderUID = $(this).attr('data-OrderUID');
        $('.dashboardview').hide();
        $('.orderstable').show();
        orderage_ajax(OrderUID);
        $('.OrderAgingExceldownload').attr('data-OrderUID',OrderUID);
        $('.OrderAgingCSVdownloadbtn').attr('data-OrderUID',OrderUID);
        $('.downloadbtndiv1').show();
        $('.downloadbtndiv').hide();

    });

    
    function collectdashboardCounts(status)
    {


        $('.dashboardview').hide();
        $('.orderstable').show();
        orderage_ajax(status);


    }
    
    function orderage_ajax(OrderUID)
    {
        $('#myordertable').DataTable().destroy();
        myordertable = $('#myordertable').DataTable( {
            scrollX:        true,
            scrollCollapse: true,
            fixedHeader: false,
            paging:  true,
            fixedColumns:   {
                leftColumns: 1,
                rightColumns: 1
            }, 
            "bDestroy": true,
            "autoWidth": true,
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "pageLength": 25, // Set Page Length
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
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('Dashboard/orders_list')?>",
            "type": "POST",
            "data" : {'OrderUID':OrderUID} 
            
        }

    });
    }
    $(document).ready(function(){
        $('.OrderAgingExceldownload').click(function(event){
            event.stopImmediatePropagation();
            var OrderUID = $(this).attr('data-OrderUID');
            /*alert();*/
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>Dashboard/WriteExcelForOrderAging',
                xhrFields: {
                    responseType: 'blob',
                },
                data: {'OrderUID':OrderUID} ,
                beforeSend: function(){
                    $('.OrderAgingExceldownload').html('Please wait...');
                    $(".OrderAgingExceldownload").attr('disabled','disabled');
                },
                success:function(data)
                { 
                    console.log(data);
                    var filename = "IndexingStackingOrders.xlsx";
                    if (typeof window.chrome !== 'undefined') {
                                //Chrome version
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(data);
                                link.download = "IndexingStackingOrders.xlsx";
                                link.click();
                            } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
                                //IE version
                                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                                window.navigator.msSaveBlob(blob, filename);
                            } else {
                                //Firefox version
                                var file = new File([data], filename, { type: 'application/octet-stream' });
                                window.open(URL.createObjectURL(file));
                            }
                            $('.OrderAgingExceldownload').html('Excel');
                            $(".OrderAgingExceldownload").removeAttr('disabled');

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR);
                        },
                        failure: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        },
                    });
        });
        $('.OrderAgingCSVdownloadbtn').click(function(event){
            event.stopImmediatePropagation();
            var OrderUID = $(this).attr('data-OrderUID');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>Dashboard/WriteExcelForOrderAgingCSVFormate',
                xhrFields: {
                    responseType: 'blob',
                },
                 data: {'OrderUID':OrderUID} ,
                beforeSend: function(){
                    $('.OrderAgingCSVdownloadbtn').html('Please wait...');
                    $(".OrderAgingCSVdownloadbtn").attr('disabled','disabled');
                },
                success:function(data)
                { 
                    console.log(data);
                    var filename = "IndexingStackingOrders.csv";
                    if (typeof window.chrome !== 'undefined') {
                                //Chrome version
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(data);
                                link.download = "IndexingStackingOrders.csv";
                                link.click();
                            } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
                                //IE version
                                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                                window.navigator.msSaveBlob(blob, filename);
                            } else {
                                //Firefox version
                                var file = new File([data], filename, { type: 'application/octet-stream' });
                                window.open(URL.createObjectURL(file));
                            }
                            $('.OrderAgingCSVdownloadbtn').html('CSV');
                            $(".OrderAgingCSVdownloadbtn").removeAttr('disabled');

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR);
                        },
                        failure: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        },
                    });
        });
    });
</script>   