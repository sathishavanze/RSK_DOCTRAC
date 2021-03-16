<table class="table table-bordered table-responsive scrool processinflow">
      <thead>
             <tr>
                    <th></th>
                    <?php
                    if(isset($SubQueues)){
                            foreach ($SubQueues as $value) 
                           { ?>
                                  <th <?php if($post['filter_type'] != 'title'){ echo "colspan=3"; } ?>  ><?php echo $value->QueueName;?></th>
                    <?php }
                    }else{
                          foreach ($ProcessUsers as $value) 
                           { ?>
                                  <th colspan="3"><?php echo $value->UserName;?></th>
                    <?php } } ?>
                  </tr>
                  <?php if($post['filter_type'] != 'title'){ ?>
                  <tr>
                   <th><b>Dates</b></th>
                   <?php
                        $subTh = (isset($SubQueues)) ? $SubQueues : $ProcessUsers;
                          foreach ($subTh as $value) 
                          { ?>
                                 <th><b> Past Due </b></th>
                                 <th><b> Out of Due </b></th>
                                 <th><b> Total </b></th>
                         <?php } ?>	
                 </tr>	
               <?php } ?>


         </thead>

         <tbody>
         <?php 
                  $TotalIn = 0;
                  $TotalOut = 0;
                  $Total = 0;
            foreach ($Followup as $F_count) 
            { ?>
                <tr>
                  <?php
                  foreach ($F_count as $ReportCounts) 
                  {     ?>
                    <td><b><?php echo $ReportCounts->date; ?></b></td>
                    <?php  if(isset($SubQueues)){
                        if($post['filter_type'] != 'title'){  
                          foreach ($SubQueues as $value) 
                          { 
                            ?>
                               <td><?php echo $ReportCounts->{'countIn'.$value->QueueUID} ?></td>
                               <td><?php echo $ReportCounts->{'countOut'.$value->QueueUID} ?></td>
                               <td class="bold"><?php echo ( $ReportCounts->{'countIn'.$value->QueueUID} + $ReportCounts->{'countOut'.$value->QueueUID} ) ?></td>
                          <?php 
                          }
                        }else{
                          foreach ($SubQueues as $value) 
                          {
                            echo "<td>". $ReportCounts->{'missedTat'.$value->QueueUID} ."</td>";
                          }
                        }  
                            // $TotalIn = $TotalIn + $ReportCounts->{'countIn'.$value->UserUID};
                            // $TotalOut = $TotalOut + $ReportCounts->{'countOut'.$value->UserUID};
                            // $Total = $Total + ( $ReportCounts->{'countIn'.$value->UserUID} + $ReportCounts->{'countOut'.$value->UserUID} );
                       
                      
                    }else{
                        foreach ($ProcessUsers as $value) 
                        { ?>
                               <td><?php echo $ReportCounts->{'countIn'.$value->UserUID} ?></td>
                               <td><?php echo $ReportCounts->{'countOut'.$value->UserUID} ?></td>
                               <td class="bold"><?php echo ( $ReportCounts->{'countIn'.$value->UserUID} + $ReportCounts->{'countOut'.$value->UserUID} ) ?></td>
                       <?php }
                    } 
                 }
                 ?>       
               </tr>        
      <?php }?>

          
           

       </tbody>  
</table>
