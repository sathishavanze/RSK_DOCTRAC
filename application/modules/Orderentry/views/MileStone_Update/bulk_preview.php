
<div class="text-right">
  <span class="badge badge-pill" style="background-color: #757575;">Invalid</span>
  <span class="badge badge-pill" style="background-color: #756af1;">Order Number</span>
  <span class="badge badge-pill" style="background-color: #ff04eb;">Loan Number</span> 
  <span class="badge badge-pill" style="background-color: #32CD32;">Milestone</span> 
  <?php 
$workflow = $this->Common_Model->GetCustomerBasedModules();
foreach ($workflow as $key => $value) 
{
 ?>
 <span class="badge badge-pill" style="background-color: <?php echo $value->ColorCode; ?>"><?php echo $value->SystemName?></span> 
<?php }
  ?>
</div>
<div class="table-responsive tablescroll">
  <table class="table table-striped table-hover table-format nowrap datatable" id="table-bulkmilestone">
    <thead>
      <tr>
        <?php   
        foreach ($headingsArray as $key => $value) {
          ?><th><?php echo $value; ?></th><?php
        }
        ?>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>