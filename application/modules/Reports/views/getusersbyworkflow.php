

<table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped  table-hover order-column">
       <thead>
        <tr>
          <th>UserName</th>
          <?php if($this->RoleType != $this->config->item('Internal Roles')['Agent']){?>
          <th class="text-center bold">Assigned Orders</th>
        <?php }?>
          <?php foreach($QueueNames as $queue){ ?>
             <th class="text-center bold"><?php echo $queue['QueueName'];?></th>
          <?php }?>
           
        </tr>
     </thead>
     <tbody>
      <?php
      //print_r($list);
      $i=1;foreach($list as $row): ?>
      <tr>
       <td style="text-align: left;"><?php /*echo $row->UserUID;*/ echo $row['UserName'];?></td>
     <?php if($this->RoleType != $this->config->item('Internal Roles')['Agent']){?>
       <td class="text-center bold"><a href="javascript:void(0);" class="text-primary listassignedorders" data-workflowmoduleuid="<?php echo $WorkflowModule; ?>" data-uid="<?php echo $row['UserUID']; ?>"  title="Queues Report- Assigned Orders- (<?php echo $row['UserName'].')';?>" ><?php  echo $row['assigned_cnt'];?></a></td>
     <?php }?>
        <?php foreach($QueueNames as $queue){ 
          ?>
             <td class="text-center bold" ><?php 
             if(empty($row[$queue['QueueUID']])){
              echo "0";
             }
             else
             {
              ?>
              <a href="javascript:void(0);" class="text-primary listorders" data-workflowmoduleuid="<?php echo $WorkflowModule; ?>"  data-queueid="<?php echo $queue['QueueUID']; ?>" title="Queues Report- <?php  echo $queue['QueueName'].'('.$row['UserName'].')'; 
             ?>" data-orderid="<?php echo $row['Orders'][$queue['QueueUID']];?>"><?php echo $row[$queue['QueueUID']]; ?></a>
              <?php ; 
             }
             ;?><?php //echo $queue['QueueUID'];?></td>
          <?php  }?>
        </tr>

  <?php 
  $i++;
endforeach; ?>
</table>
<?php
$this->load->view('Reports/orderstable'); ?>

<script type="text/javascript">
 var WorkflowModule=$("#WorkflowModule").val();
$("#MaritalTableList").dataTable({
  "pageLength": 10, // Set Page Length
  "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
  buttons: [
    {
      extend: 'excelHtml5',
      title: 'Queues Report'
    }
  ],
  processing: true,
  scrollX:  true,
  paging:true,
});

</script>