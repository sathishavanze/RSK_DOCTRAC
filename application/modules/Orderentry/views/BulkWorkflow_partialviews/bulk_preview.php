
<div class="text-right">
  <span class="badge badge-pill" style="background-color: #757575;">Invalid</span>
  <span class="badge badge-pill" style="background-color: #168998;">Order Number</span>
  <!-- <span class="badge badge-pill" style="background-color: #8123e8;">Client Code</span>
  <span class="badge badge-pill" style="background-color: #67941e;">Project Name</span>
  <span class="badge badge-pill" style="background-color: #BF6105;">Project Code</span> -->
  <span class="badge badge-pill" style="background-color: #ff04ec;">Loan Number</span> <!-- 
  <span class="badge badge-pill" style="background-color: #ff5c33;">Document Type</span> 
  <span class="badge badge-pill" style="background-color: #bd302ac7;">Product Name</span> -->
</div>
<div class="table-responsive tablescroll">
  <table class="table table-striped table-hover table-format nowrap datatable" id="table-bulkorder">
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