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
                <div class="withdrawal_div">
                  <h5 class="pre_screen_head">Withdrawal</h5>
                  <table class="table table-hover table-striped" id="HistoryWithdrawalTable">
                    <thead>
                      <tr>
                        <th>Reason</th>
                        <th>Remarks</th>
                        <th>RaisedBy</th>                    
                        <th>Raised On</th>                    
                        <th>Cleared Reason</th>
                        <th>Cleared Remarks</th>
                        <th>ClearedBy</th>
                        <th>ClearedOn</th>
                        <th>Cleared</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($Withdraw_Details as $key => $value) { ?>
                        <tr>
                          <td><?php echo $value->RaisedReasonName; ?></td>
                          <td><?php echo $value->Remarks; ?></td>
                          <td><?php echo $value->RaisedUserName; ?></td>                    
                          <td><?php if($value->RaisedDateTime != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->RaisedDateTime));}else{echo '-';}?></td>                    
                          <td><?php echo $value->ClearedReasonName; ?></td>
                          <td><?php echo $value->ClearedRemarks; ?></td>
                          <td><?php echo $value->ClearedUserName; ?></td>
                          <td><?php if($value->ClearedDateTime != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->ClearedDateTime));}else{echo '-';} ?></td>
                          <td><?php if($value->IsCleared){echo '<span class="badge badge-success">Cleared</span>';}else{echo '<span class="badge badge-warning">Pending</span>';}; ?></td>
                        </tr>

                      <?php } ?>
                    </tbody>
                  </table>
                </div>

                 <div class="docChase_div">
                  <h5 class="pre_screen_head" style="margin-top:50px;">DocChase</h5>
                  <table class="table table-hover table-striped" id="HistoryDocChaseTable">
                    <thead>
                      <tr>
                        <th>Workflow</th>
                        <th>Findings</th>
                        <th>Reason</th>
                        <th>Remarks</th>
                        <th>RaisedBy</th>                    
                        <th>Raised On</th>                    
                        <th>Cleared Reason</th>
                        <th>Cleared Remarks</th>
                        <th>ClearedBy</th>
                        <th>ClearedOn</th>
                        <th>Cleared</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($DocChase_Details as $key => $value) { ?>
                        <tr>
                          <td><?php 
                          $redirect = 'Ordersummary';
                          if ($value->SystemName == 'PreScreen') {
                            $redirect = 'PreScreen'; 
                          }else if ($value->SystemName == 'WelcomeCall') {
                            $redirect = 'WelcomeCall';
                          }else if ($value->SystemName == 'Title') {
                            $redirect = 'TitleTeam';
                          }else if ($value->SystemName == 'FHA') {
                            $redirect = 'FHA_VA_CaseTeam';
                          }else if ($value->SystemName == 'ThirdParty') {
                            $redirect = 'ThirdPartyTeam';
                          }
                          echo '<a class="btn btn-scondary btn-xs" href="'.base_url($redirect.'/index/'.$OrderUID).'" class="ajaxload">'.$value->WorkflowModuleName.'</a>'; ?></td>
                          <td><?php echo '<a style="background-color:#4E8657;font-size:11px" class="btn  btn-xs" href="'.base_url($redirect.'/index/'.$OrderUID).'" class="ajaxload">'.$value->QuestionCount.'</a>'; ?></td>
                          <td><?php echo $value->RaisedReasonName; ?></td>
                          <td><?php echo $value->Remarks; ?></td>
                          <td><?php echo $value->RaisedUserName; ?></td>                    
                          <td><?php if($value->RaisedDateTime != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->RaisedDateTime));}else{echo '-';} ?></td>                    
                          <td><?php echo $value->ClearedReasonName; ?></td>
                          <td><?php echo $value->ClearedRemarks; ?></td>
                          <td><?php echo $value->ClearedUserName; ?></td>
                          <td><?php if($value->ClearedDateTime != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->ClearedDateTime));}else{echo '-';} ?></td>
                          <td><?php if($value->IsCleared){echo '<span class="badge badge-success">Cleared</span>';}else{echo '<span class="badge badge-warning">Pending</span>';}; ?></td>
                        </tr>

                      <?php } ?>
                    </tbody>
                  </table>
                </div>

                <h5 class="pre_screen_head mt-40">FollowUp</h5>
                <div class="FollowUp_div table-responsive">
                  <table class="table table-hover table-striped" id="HistoryFollowUpTable">
                    <thead>
                      <tr>
                        <th>Workflow</th>
                        <th>Reason</th>
                        <th>Remarks</th>
                        <th>Remind</th>
                        <th>RaisedBy</th>                    
                        <th>Raised On</th>                    
                        <th>Cleared Reason</th>
                        <th>Cleared Remarks</th>
                        <th>ClearedBy</th>
                        <th>ClearedOn</th>
                        <th>Cleared</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($FollowUp_Details as $key => $value) { ?>
                        <tr>
                          <td><?php 
                          $redirect = 'Ordersummary';
                          if ($value->SystemName == 'PreScreen') {
                            $redirect = 'PreScreen'; 
                          }else if ($value->SystemName == 'WelcomeCall') {
                            $redirect = 'WelcomeCall';
                          }else if ($value->SystemName == 'Title') {
                            $redirect = 'TitleTeam';
                          }else if ($value->SystemName == 'FHA') {
                            $redirect = 'FHA_VA_CaseTeam';
                          }else if ($value->SystemName == 'ThirdParty') {
                            $redirect = 'ThirdPartyTeam';
                          }
                          echo '<a class="btn btn-primary btn-xs" href="'.base_url($redirect.'/index/'.$OrderUID).'" class="ajaxload">'.$value->WorkflowModuleName.' ('.$value->QueueName.')</a>'; ?></td>
                          <td><?php echo $value->RaisedReasonName; ?></td>
                          <td><?php echo $value->Remarks; ?></td>
                          <td><?php echo site_datetimeformat($value->Remainder); ?></td>
                          <td><?php echo $value->RaisedUserName; ?></td>                    
                          <td><?php if($value->RaisedDateTime != '0000-00-00 00:00:00'){ echo site_datetimeformat($value->RaisedDateTime);}else{echo '-';} ?></td>                    
                          <td><?php echo $value->ClearedReasonName; ?></td>
                          <td><?php echo $value->ClearedRemarks; ?></td>
                          <td><?php echo $value->ClearedUserName; ?></td>
                          <td><?php if($value->ClearedDateTime != '0000-00-00 00:00:00'){ echo site_datetimeformat($value->ClearedDateTime);}else{echo '-';} ?></td>
                          <td><?php if($value->IsCleared){echo '<span class="badge badge-success">Cleared</span>';}else{echo '<span class="badge badge-warning">Pending</span>';}; ?></td>
                        </tr>

                      <?php } ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>

            <div class="row">
              <div class="col-sm-12 form-group pull-right mt-20">
                <p class="text-right">

                  <?php $this->load->view('orderinfoheader/workflowbuttons'); ?>

                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
</div>

<?php $this->load->view('orderinfoheader/workflowpopover'); ?>



<script type="text/javascript">
  $(document).ready(function(){
    // $("#HistoryWithdrawalTable").DataTable({
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