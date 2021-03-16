    <table id="myTable" class="display dataTable table table-striped table-hover table-actions-bar queueprogress-period" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
        <tr>
         <th>Date</th>
         <th>Inflows</th>
         <th>Reviewed</th>
         <th>Percentage </th>
       </tr>
     </thead>

     <tbody>
       <?php 

       $tot_inflow = 0;
       $tot_review = 0;
       $complete_percent = 0;
       foreach ($ReportCounts as $row) 
       { 

            $date = $row["Date"];
            $tot_inflow = $row["InflowCount"];
            $tot_review = $row["ReviewCount"];
            if ($tot_inflow >=0 && $tot_review >=0)
            {
                $complete_percent = $tot_review / $tot_inflow * 100;              
            }
            else
            {
              $complete_percent = 0; 
            }
            $complete_percent = sprintf('%0.2f', $complete_percent);
            echo "<tr><td>" . $date. "</td><td>". $tot_inflow . "</td><td>". $tot_review . "</td><td>" . $complete_percent . "%</td></tr>";
        }

        ?>

    </tbody>  

  </table>

