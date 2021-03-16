<table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">      <thead>
                  <tr>
                   <th><b>UserName</b></th>
                   <?php if($this->RoleType != $this->config->item('Internal Roles')['Agent']){?>
                    <th class="text-center bold">Assigned Orders</th>
                  <?php }?>
                   <?php
                           foreach ($Aging as $value) 
                           {
                          foreach($QueueNames as $queue){
                           ?>
                                 <th class="text-center bold"><?php echo $queue['QueueName'];?>(<?php echo $this->config->item('AgingHeader')[$value];?>)</th> 
                         <?php } 
                       }
                         ?> 
                 </tr>  
         </thead>
         <tbody>
           <?php
      //print_r($list);exit;
      $i=1;
      if(!empty($list)){
      foreach($list as $row): 
        //print_r($row);exit;
        ?>
           <tr>
       <td style="text-align: left;"><?php /*echo $row->UserUID;*/ echo $row['UserName'];?></td>
          <?php if($this->RoleType != $this->config->item('Internal Roles')['Agent']){?>
       <td class="text-center bold"><a href="javascript:void(0);" class="text-primary listassignedorders" data-workflowmoduleuid="<?php echo $WorkflowModule; ?>" data-uid="<?php echo $row['UserUID']; ?>"  title="Queues Report- Assigned Orders- (<?php echo $row['UserName'].')';?>" ><?php  echo $row['assigned_cnt'];?></a></td>
     <?php }?>
        <?php foreach ($Aging as $value) 
                           {
                              $heading=$this->config->item('AgingHeader')[$value];
                          foreach($QueueNames as $queue){ 
                            if(isset($row[$value][$queue['QueueUID']]))
                            {
                            ?>
                            <td class="text-center bold" >
                              <a href="javascript:void(0);" class="text-primary listorders" data-workflowmoduleuid="<?php echo $WorkflowModule; ?>"  data-queueid="<?php echo $queue['QueueUID']; ?>" title="Queues Report- <?php  echo $queue['QueueName'].'-'.$heading.'('.$row['UserName'].')'; 
             ?>" data-orderid="<?php echo $row[$value]['Orders'][$queue['QueueUID']];?>">
             <?php 
                              echo $row[$value][$queue['QueueUID']];?></a></td>
            <?php }
              else { ?><td class="text-center bold"><a href="javascript:void(0);">0</a>

                  </a></td>
                            <?php }} 
                       }
                         ?> 
          </tr> 
          <?php 
          $i++;
        endforeach; }
        ?>
         </tbody>
</table>
<?php $this->load->view('Reports/orderstable'); ?>

<script type="text/javascript">
   var WorkflowModule=$("#WorkflowModule").val();

$("#MaritalTableList").dataTable({
        "pageLength": 10, // Set Page Length
        "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Queues Aging Report'
            }
            
        ],
              processing: true,
              scrollX:  true,
              paging:true,
        },

        );

</script>