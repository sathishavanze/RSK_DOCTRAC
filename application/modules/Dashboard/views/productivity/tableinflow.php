<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar productivity-period" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
  <thead>
    <tr>
      <th>Date</th>
      <th>Completed</th>
      <th>Productivity</th>
    </tr>
  </thead>

  <tbody>
    <?php 
    $totalval=0;
    $UserCount=0;
    $tot_completeorderuid = '';
    if(!empty($ReportCounts)){

      foreach ($ReportCounts as $row) 
      { 
        if($row['CompletedOrders']>0) {
          $row['CompleteCount'] = ($row['CompletedOrders'] != '') ? count(explode(',', $row['CompletedOrders'])) : NULL;
          $totalval+=$row['CompleteCount'];
          $UserCount+=$row['UserCount'];
          $tot_completeorderuid =  ($row["CompletedOrders"] != '') ? implode("," , array_unique(array_filter(array_merge(explode(",",$tot_completeorderuid),explode(",", $row["CompletedOrders"]))))) : NULL;

          ?>
          <tr>
            <td><b><?php echo $row['Date']; ?></b></td>
            <td><a href="javascript:;" title="View Loans" data-heading="Productivity - <?php echo $row["Date"]; ?>" class="listorders" data-orderid="<?php echo $row['CompletedOrders']; ?>"><?php echo $row["CompleteCount"]; ?></a></td>
            <td><span class=""><?php echo $row['CompleteCount'] ? round((($row['CompleteCount']/($row['UserCount']*$Target))*100) , 2) : 0; ?>%</span></td>
          </tr>        
          <?php 
        }
      }
      ?>

    <?php }?>
    <tr>
      <td><span class="text-bold"><?php echo 'Total'; ?></span></td>
      <td><a href="javascript:;" title="View Loans" data-heading="Productivity - Periodic Total" class="listorders text-bold" data-orderid="<?php echo $tot_completeorderuid; ?>" class="text-bold"><?php echo $totalval; ?></a></td>
      <td><span class="text-bold"><?php echo ($totalval>0) ? round((($totalval/($UserCount*$Target))*100) , 2) : 0; ?>%</span></td>
    </tr>
  </tbody>  


</table>



