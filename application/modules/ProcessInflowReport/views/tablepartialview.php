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
<table class="table table-bordered table-responsive scrool processinflow">
      <thead>
             <tr>
                    <th></th>
                    <?php
                    foreach ($ProcessUsers as $value) 
                           { ?>
                                  <th colspan="3"><?php echo $value->UserName;?></th>
                          <?php } ?>
                  </tr>

                  <tr>
                   <th><b>Dates</b></th>
                   <?php
                   foreach ($ProcessUsers as $value) 
                          { ?>
                                 <th><b>FHA</b></th>
                                 <th><b>VA</b></th>
                                 <th><b>Total</b></th>
                         <?php } ?>	
                 </tr>	


         </thead>

         <tbody>
         <?php foreach ($ProcessInflowReportCounts as $ProcessInflowReportrow) 
         { ?>
                <tr>
                  <?php
                  foreach ($ProcessInflowReportrow as $ReportCounts) 
                  {     ?>
                  <td><b><?php echo $ReportCounts->date; ?></b></td>
                     <?php   foreach ($ProcessUsers as $value) 
                                { ?>
                                       <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $ReportCounts->{'FHAOrderUID'.$value->UserUID} ?>" data-title="Process Inflow List" ><?php echo $ReportCounts->{'FHA'.$value->UserUID} ?></a></td>

                                       <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $ReportCounts->{'VAOrderUID'.$value->UserUID} ?>" data-title="Process Inflow List" ><?php echo $ReportCounts->{'VA'.$value->UserUID} ?></a></td>

                                       <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $ReportCounts->{'totalOrderUID'.$value->UserUID} ?>" data-title="Process Inflow List" ><span class="textbold"><?php echo $ReportCounts->{'total'.$value->UserUID} ?></span></a></td>

                               <?php } 
                       }
                       ?>       
               </tr>        
          <?php }?>

          
           

       </tbody>  
</table>