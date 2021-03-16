    <table id="myTable" class="display dataTable table table-striped table-hover table-actions-bar queueprogress-individual" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
        <tr>
         <th>User Name</th>
         <th>Total Inflows</th>
         <th>Completed</th>
         <th>Pending</th>
         <th>Success Ratio</th>
       </tr>
     </thead>

     <tbody>
       <?php 
       $tot_flow = 0;
       $tot_pend = 0;
       $tot_complete = 0;
       $tot_floworderuid = '';
       $tot_pendorderuid = '';
       $tot_completeorderuid = '';
       $tot_rati = 0;
       foreach ($ReportCounts as $row) 
       { 

        $row['InflowCount'] = ($row['InflowOrders'] != '') ? count(explode(',', $row['InflowOrders'])) : NULL;
        $row['PendingCount'] = ($row['PendingOrders'] != '') ? count(explode(',', $row['PendingOrders'])) : NULL;
        $row['CompleteCount'] = ($row['CompletedOrders'] != '') ? count(explode(',', $row['CompletedOrders'])) : NULL;

        $ratio = !empty($row['CompleteCount']) && !empty($row['InflowCount']) ? percent($row['CompleteCount'],$row['InflowCount']): 0;

        ?>
        <tr>
          <td><b><?php echo $row["UserName"]; ?></b></td>
          <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress <?php echo $row["UserName"]; ?> - Inflow" class="listorders" data-orderid="<?php echo $row['InflowOrders']; ?>"><?php echo $row["InflowCount"]; ?></a></td>
          <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress <?php echo $row["UserName"]; ?> - Completed" class="listorders" data-orderid="<?php echo $row['CompletedOrders']; ?>"><?php echo $row["CompleteCount"]; ?></a></td>
          <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress <?php echo $row["UserName"]; ?> - Pending" class="listorders" data-orderid="<?php echo $row['PendingOrders']; ?>"><?php echo $row["PendingCount"]; ?></a></td>
          <td><b><?php echo $ratio; ?>%</b></td>
        </tr>        
        <?php

        $tot_flow = $tot_flow + $row["InflowCount"];
        $tot_complete = $tot_complete + $row["CompleteCount"];
        $tot_pend = $tot_pend + $row["PendingCount"];
        $tot_floworderuid =  ($row["InflowOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$tot_floworderuid),explode(",", $row["InflowOrders"]))))) : NULL;
        $tot_completeorderuid =  ($row["CompletedOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$tot_completeorderuid),explode(",", $row["CompletedOrders"]))))) : NULL;
        $tot_pendorderuid =  ($row["PendingOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$tot_pendorderuid),explode(",", $row["PendingOrders"]))))) : NULL;
        $tot_rati = $tot_rati + $ratio;

      }
      ?>

      <tr>
        <td><span class="text-bold">Total</span></td>
        <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress - Total Inflow" class="listorders text-bold" data-orderid="<?php echo $tot_floworderuid; ?>"><?php echo $tot_flow; ?></a></td>
        <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress - Total Completed" class="listorders text-bold" data-orderid="<?php echo $tot_completeorderuid; ?>"><?php echo $tot_complete; ?></a></td>
        <td><a href="javascript:;" title="View Loans" data-heading="Queue Progress - Total Pending" class="listorders text-bold" data-orderid="<?php echo $tot_pendorderuid; ?>"><?php echo $tot_pend; ?></a></td>
        <td><span class="text-bold"><?php echo !empty($tot_complete) && !empty($tot_flow) ? percent($tot_complete,$tot_flow) : 0; ?>%</span></td>

      </tr>        


    </tbody>  


  </table>



