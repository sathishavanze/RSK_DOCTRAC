<style type="text/css">

  h5.pre_screen_head{
    border-bottom: 1px solid #eee;
    padding-bottom: 3px;
    margin-top: 10px;
    margin-bottom: 1px;
    font-size: 15px;
    font-weight:bold;
  }
</style>





<input type="hidden" name="url" id="url" value="<?php echo $this->uri->segment(1);?>">
<div class="col-md-12 pd-0" >
  <div class="card mt-0">
    <div class="card-header tabheader" id="">
      <div class="col-md-12 pd-0">
        <div id="headers" style="color: #ffffff;">
          <!-- Order Info Header View -->  
          <?php $this->load->view('orderinfoheader/orderinfo'); ?>

        </div>
      </div>
    </div>
    <div class="card-body pd-0">
      <!-- Workflow Header View -->  
      <?php $this->load->view('orderinfoheader/workflowheader'); ?>
      
      <div class="tab-content tab-space">
        <div class="tab-pane active" id="summary">
          <form action="#"  name="orderform" id="frmordersummary">
            <div class="row">
              <div class="col-md-12">
                <div class="MilestoneLog_div">
                  <table class="table table-hover table-striped" id="MilestoneLogTable">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Order Number</th>
                        <th>Milestone</th>                    
                        <th>Completed By</th>
                        <th>Completed Date and Time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $sno = 1;
                      foreach ($MilestoneLog_Details as $key => $value) { ?>
                        <tr>
                          <td><?php echo $sno; ?></td>
                          <td><?php echo $value->OrderNumber; ?></td>
                          <td><?php echo $value->MilestoneName; ?></td>                    
                          <td><?php echo $value->UserName; ?></td>
                          <td><?php echo site_datetimeformat($value->CompletedDateTime); ?></td>
                        </tr>

                      <?php $sno++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // $("#MilestoneLogTable").DataTable({
    //   processing: true,
    //   paging:false,
    //   search : false,
    //   "bDestroy": true
    // });
    //  $("#HistoryDocChaseTable").DataTable({
    //   processing: true,
    //   paging:false,
    //   search : false,
    //   "bDestroy": true
    // });
  });
</script>