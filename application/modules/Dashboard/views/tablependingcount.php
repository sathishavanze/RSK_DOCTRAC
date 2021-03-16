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

<table class="display dataTable table-striped table-hover table-actions-bar PendingReport" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
      <thead>
        <tr>
          <?php foreach($PendingWorklfowsCount as $key => $value){ 
            if($key!='CompletedOrdersCount'){?>
         <th><b><?php echo $key;?></b></th>
        <?php } } ?>
       </tr>	
    </thead>
     <tbody>
     <tr>
      <?php foreach($PendingWorklfowsCount as $key => $value){ 
        if($key!='CompletedOrdersCount'){ 
          if(!empty($value)){ 
          $$WorkflowCount=0;         
            foreach($value as $PendingKey =>$PendingValue){
              $WorkflowCount = count(explode(',', $PendingValue['Count']));
               } ?> 
      <td><a href="javascript:;" title="View Loans" data-heading="Getekeeping - <?php echo $key; ?> Pending" class="listorders" data-orderid="<?php echo $PendingValue['Count']; ?>"><?php echo $WorkflowCount;?></a></td><?php }else{ ?> 
          <td><b>0</b></td>
      <?php } } }?>
     </tr>
   </tbody>  
</table>
