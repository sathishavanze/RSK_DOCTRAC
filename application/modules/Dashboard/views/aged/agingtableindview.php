    <table  class="display dataTable table table-striped table-hover table-actions-bar aging-individual" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
        <tr>
         <th>User Name</th>
         <th>5-10</th>
         <th>10-15</th>
         <th>15-20</th>
         <th>25-30</th>
         <th>Average</th>

       </tr>
     </thead>

     <tbody>
       <?php 

       foreach ($ReportCounts as $row) 
       { 

        ?>
        <tr>
          <td><b><?php echo $row["UserName"]; ?></b></td>
          <td><b><?php echo $row["Result1"]; ?></b></td>
          <td><b><?php echo $row["Result2"]; ?></b></td>
          <td><b><?php echo $row["Result3"]; ?></b></td>
          <td><b><?php echo $row["Result4"]; ?></b></td>
          <td><b><?php echo $row["Average"]; ?></b></td>

        </tr>        
        <?php 
      }
      ?>
    </tbody>  


  </table>



