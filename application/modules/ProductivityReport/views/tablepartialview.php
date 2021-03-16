<style type="text/css">

  .productivityreport > tbody > tr:last-child > td > a ,.productivityreport > tbody > tr:last-child
  {
   font-weight : bold;
   color : #000 !important;
  }
</style>
<table class="table table-hover table-striped table-bordered productivityreport">
      <thead>
             <tr>
                    <th>Dates</th>
                    <?php
                    foreach ($ProcessUsers as $value) 
                           { ?>
                                  <th colspan="2"><?php echo $value->UserName;?></th>
                          <?php } ?>
                  </tr>
         </thead>

         <tbody>
         <?php foreach ($ProductivityReportCounts as $ProductivityReportrow) 
         { ?>
                <tr>
                  <?php
                  foreach ($ProductivityReportrow as $ReportCounts) 
                  {     ?>
                  <td><b><?php echo $ReportCounts->date; ?></b></td>
                     <?php   foreach ($ProcessUsers as $value) 
                                {
                                  if($Target != 0)
                                  {
                                    $total = round( $ReportCounts->{'process'.$value->UserUID} * 100 / $Target);
                                  }
                                  else
                                  {
                                    $total = 0;
                                  }?>
                                       <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $ReportCounts->{'processOrderUID'.$value->UserUID} ?>" data-title="ProductivityReportList" ><?php echo $ReportCounts->{'process'.$value->UserUID} ?></a></td>
                                       <td><?php echo $total,'%' ?></td>
                               <?php } 
                       }
                       ?>       
               </tr>        
          <?php }?>

       </tbody>  
</table>