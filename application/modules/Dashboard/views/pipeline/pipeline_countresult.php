    <table id="myTable" class="display dataTable table table-striped table-hover table-actions-bar queueprogress-period" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
        <tr>
         <th>Total Reviewed</th>
         <th>Completed</th>
         <th>Completion %</th>
         <th>Pending</th>
         <th>Pending %</th>
       </tr>
     </thead>

     <tbody>
       <?php 
       $tot_count = 0;
       $tot_complete = 0;
       $complete_percent = 0;
       $tot_pending = 0;
       $pending_percent =0;
       foreach ($ReportCounts as $row) 
       { 

          $tot_count = $row["ReviewCount"];
          $tot_complete = (!empty($row["CompleteCount"])) ? count(explode(',', $row["CompleteCount"])) : 0;
          $tot_pending = (!empty($row['PendingCount'])) ? count(explode(',', $row['PendingCount'])) : 0;
      $data['value']=array($completed,$Pending);;

        }

        if ($tot_count == 0)
        {

           $tot_count = 0;
           $tot_complete = 0;
           $complete_percent = 0;
           $tot_pending = 0;
           $pending_percent =0;

        }
        else
        {

            if ($tot_count >= 0 && $tot_complete >=0)
            {
                $complete_percent = $tot_complete / $tot_count * 100;
            }
            else
            {
                $complete_percent = 0;
            }


            if ($tot_count >= 0 && $tot_pending >=0)
            {
                $pending_percent = $tot_pending / $tot_count * 100;
            }
            else
            {
                $pending_percent = 0;
            }
        }

        $complete_percent = sprintf('%0.2f', $complete_percent);
        $pending_percent = sprintf('%0.2f', $pending_percent);


        ?>
        <tr>
          <td><a href="javascript:;" title="View Loans" data-heading="Getekeeping - Completed Orders" class="listorders" data-orderid="<?php echo $row["CompleteCount"].','.$row["PendingCount"]; ?>"><?php echo $tot_count; ?></a></td>
          <td><a href="javascript:;" title="View Loans" data-heading="Getekeeping - Completed Orders" class="listorders" data-orderid="<?php echo $row["CompleteCount"]; ?>"><?php echo $tot_complete; ?></a></td>
          <td><?php echo $complete_percent; ?>%</td>
          <td><a href="javascript:;" title="View Loans" data-heading="Getekeeping - Pending Orders" class="listorders" data-orderid="<?php echo $row["PendingCount"]; ?>"><?php echo $tot_pending; ?></a></td>
          <td><?php echo $pending_percent; ?>%</td>
        </tr>        

    </tbody>  


  </table>

