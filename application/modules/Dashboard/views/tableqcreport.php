<style type="text/css">
  .textbold
  {
    font-weight: bold;
    color: #000;
  }
  .processinflow > tbody > tr:last-child > td > a ,.processinflow > tbody > tr:last-child
  {
   font-weight : bold;
   color : #000 !important;
  }
</style>
<?php if($Type=='Team'){ ?>
<table id="myTable" class="display dataTable table-striped table-hover table-actions-bar QCReportTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
  <thead>
    <tr>
     <th><b>Date</b></th>
     <th><b>QC Pass</b></th>
     <th><b>QC Fail</b></th>
     <th><b>QC Percent</b></th>
    </tr>	
  </thead>
   <tbody>
     <?php 
     $QCPass=0;
     $QCFail=0;
     foreach ($QualityReportPeriodic as $ReportCounts) 
     { 
      //if($ReportCounts['TotalCount'] > 0){
              $QCPass +=$ReportCounts['QCPass'];
              $QCFail +=$ReportCounts['QCFail'];
              $QCPassPercent =(($ReportCounts['QCPass']-$ReportCounts['QCFail'])/100)*100;
      ?>
        <tr>
          
          <td><b><?php echo $ReportCounts['Date']; ?></b></td>
            
         <td><?php echo $ReportCounts['QCPass']; ?></td>

         <td><?php echo $ReportCounts['QCFail']; ?></td>

         <td><span class="textbold"><?php echo $QCPassPercent; ?>%</span></td>
      
       </tr>        
      <?php //} 
    } 
    $totalQCPass=(($QCPass-$QCFail)/100)*100;
    ?>
         <tr>
           <td><b>Total</b></td>
           <td><?php echo ($QCPass>0) ? $QCPass : 0;?></td>
           <td><?php echo ($QCFail>0) ? $QCFail : 0;?></td>
           <td><?php echo ($totalQCPass>0) ? $totalQCPass : 0;?>%</td>
         </tr> 
  </tbody>  
</table>

<?php }else{ ?>

  <table id="myTable" class="display dataTable table-striped table-hover table-actions-bar QCReportTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
                  <tr>
                   <th><b>UserName</b></th>
                   <th><b>QC Pass</b></th>
                   <th><b>QC Fail</b></th>
                   <th><b>QC Percent</b></th>
                 </tr>  

         </thead>

         <tbody>
              <?php
              $QCPass=0;
              $QCFail=0;
              foreach ($QualityReportIndividual as $ReportCounts) 
              {   
              //   if($ReportCounts['TotalCount'] > 0){
              $QCPass +=$ReportCounts['QCPass'];
              $QCFail +=$ReportCounts['QCFail'];
              $QCPassPercent =(($ReportCounts['QCPass']-$ReportCounts['QCFail'])/100)*100;
                ?>
                <tr>
                  <td><b><?php echo $ReportCounts['UserName']; ?></b></td>
                     
                   <td><?php echo $ReportCounts['QCPass']; ?></td>

                   <td><?php echo $ReportCounts['QCFail']; ?></td>

                   <td><?php echo $QCPassPercent; ?>%</td>

               </tr> 
              <?php  //}
            }  
            $totalQCPass=(($QCPass-$QCFail)/100)*100; ?>       
               <tr>
                 <td><b>Total</b></td>
                 <td><?php echo ($QCPass>0) ? $QCPass : 0;?></td>
                 <td><?php echo ($QCFail>0) ? $QCFail : 0;?></td>
                 <td><?php echo ($totalQCPass>0) ? $totalQCPass : 0;?>%</td>
               </tr>         

       </tbody>  
</table>
<?php } ?>