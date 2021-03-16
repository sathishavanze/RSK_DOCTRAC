<?php 
$OrderUID = $this->uri->segment(3);
$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID); 
$tOrderPackage = $this->Common_Model->get_row('tOrderPackage', ['PackageUID'=>$OrderDetails->PackageUID]);
$NBSRequired = $this->Common_Model->get_NBSRequired($OrderDetails->PropertyStateCode);
$GetOrderLog = $this->Common_Model->GetOrderLogs($OrderUID);
?>
<style type="text/css">
	#historybar{
		display: block !important;
	}
		#trackingbar{
		display: block !important;
	}
	.mt-5{
		margin-top:5px;
	}
	.mb-0{
		margin-bottom: 0px !important;
	}
	.tabheader p {
		font-size: 11px;
		margin-bottom: 0px !important;
	}
	#headers {
		background: #5a5a5a  !important;
		padding-top: 15px;
		padding-bottom: 3px  !important;
	}
	.badge {
		padding: 4px 12px;
		text-transform: capitalize;
		font-size: 10px;
	}
	.orderinfodiv {
		margin: 0 auto;
		max-width: 170px;
	}
	.icondiv{
		float: left;
		width: 28px;
		height: 28px;
		text-align: center;
		margin: 10px 6px 0px 0px;
		border: 1px solid #4a4a4a;
		border-radius: 5px;
		background: #1f264e;
	}
	.descriptiondiv label{
		font-size: 11px;
	}
	.descriptiondiv{
		margin-top: 10px;
	}

span.package {
    border: 1px solid #000;
    padding:3px 7px;
    background: #5a5a5a;
    border-radius: 5px;
    font-size: 9px !important;
}
#headers .btn{
	padding: 4px 10px;
}
#headers .badge{
	    border-radius: 15px 0px 0px 15px;
}
/*Logs begin*/
.log_dc_col {
	font-weight: bold;
}
.log_dc_val {
    font-weight: bold;
    font-style: italic;
}
/*Logs end*/
</style>

<style type="text/css">
  .timeline {
  list-style: none;
  margin: 25px 0 22px;
  padding: 0;
  position: relative;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline:after {
  border: 6px solid;
  border-top-width: 13px;
  border-color: #00637d transparent transparent transparent;
  content: "";
  display: block;
  position: absolute;
  bottom: -19px;
  left: 15px;
}

.timeline-horizontal:after {
  border-top-width: 6px;
  border-left-width: 13px;
  border-color: transparent transparent transparent #00637d;
  top: 15px;
  right: 0;
  bottom: auto;
  left: auto;
}
.timeline-horizontal .timeline-milestone {
  border-top: 2px solid #00637d;
  display: inline;
  float: left;
  margin: 20px 0 0 0;
  padding: 40px 0 0 0;
}
.timeline-horizontal .timeline-milestone:before {
  top: -17px;
  left: auto;
}
.timeline-horizontal .timeline-milestone.is-completed:after {
  top: -17px;
  left: 0;
}

.timeline-milestone {
  border-left: 2px solid #00637d;
  margin: 0 0 0 20px;
  padding: 0 0 5px 25px;
  position: relative;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone:before {
  border: 7px solid #00637d;
  border-radius: 50%;
  content: "";
  display: block;
  position: absolute;
  left: -17px;
  width: 32px;
  height: 32px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone.is-completed:before {
  background-color: #00637d;
}
.timeline-milestone.is-completed:after {
  color: #FFF;
  content: "\f00c";
  display: block;
  font-family: "FontAwesome";
  line-height: 32px;
  position: absolute;
  top: 0;
  left: -17px;
  text-align: center;
  width: 32px;
  height: 32px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone.is-current:before {
  background-color: #EEE;
}
.timeline-milestone.is-future:before {
  background-color: #8DACB8;
  border: 0;
}
.timeline-milestone.is-future .timeline-action .title {
  color: #8DACB8;
}

.timeline-action {
  background-color: #FFF;
      padding: 7px 8px 3px 0px;
  position: relative;
  top: -15px;
}
.timeline-action.is-expandable .title {
  cursor: pointer;
  position: relative;
}
.timeline-action.is-expandable .title:focus {
  outline: 0;
  text-decoration: underline;
}
.timeline-action.is-expandable .title:after {
  border: 6px solid #666;
  border-color: transparent transparent transparent #666;
  content: "";
  display: block;
  position: absolute;
  top: 6px;
  right: 0;
}
.timeline-action.is-expandable .content {
  display: none;
}
.timeline-action.is-expandable.is-expanded .title:after {
  border-color: #666 transparent transparent transparent;
  top: 10px;
  right: 5px;
}
.timeline-action.is-expandable.is-expanded .content {
  display: block;
}
.timeline-action .title, .timeline-action .content {
  word-wrap: break-word;
}
.timeline-action .title {
  color: #00637d;
  font-size: 14px;
  margin: 0;
}
.date {
 font-size: 11px;
    line-height: 19px;
    text-align: left;
    color: #333;
    font-style: italic;
}
.timeline-action .content {
  font-size: 12px;
}

.file-list {
  line-height: 1.4;
  list-style: none;
  padding-left: 10px;
}



.page {
  /*max-width: 1200px;*/
  margin: 0px 30px;
}

a {
  color: #00637d;
  text-decoration: none;
}
a:hover, a:focus {
  text-decoration: underline;
}

.video-link:before {
  content: "\f03d";
  display: inline-block;
  font-family: "FontAwesome";
  margin-right: 5px;
}

a[href*=".pdf"]:before {
  content: "\f0f6";
  display: inline-block;
  font-family: "FontAwesome";
  margin-right: 8px;
}

.title{
  min-height: 0px;
      font-size: 14px;
      color: #00bcd4!important;
}
</style>
<link href="<?php echo base_url(); ?>assets/css/workflow.css" rel="stylesheet" />
<!-- <script type="text/javascript" src="assets/js/offside.js"></script> -->
<input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderDetails->OrderUID; ?>">
<input type="hidden" name="LoanNumberNamingConv" id="LoanNumberNamingConv" value="<?php echo $OrderDetails->LoanNumber; ?>">

<div class="col-md-12">
	<div class="row">
		<div class="col-md-9">
			<h5 class="text-left mb-0" style="margin-top:4px;"><p style="float: left; margin-top: 2px; font-size: 16px;">Order#&nbsp;<?php echo $OrderDetails->OrderNumber; ?>&nbsp;</p>
			<?php if(!empty($OrderDetails->LoanNumber)){ ?>
				<h5 class="text-left mb-0" style="margin-top:4px;"><p style="float: left; margin-top: 2px; font-size: 16px;"> | Loan#&nbsp;<?php echo $OrderDetails->LoanNumber; ?>&nbsp;</p>
			<?php } ?>
			<?php if(!empty($OrderDetails->NSMServicingLoanNumber)){ ?>
				<h5 class="text-left mb-0" style="margin-top:4px;"><p style="float: left; margin-top: 2px; font-size: 16px;"> | NSM#&nbsp;<?php echo $OrderDetails->NSMServicingLoanNumber; ?>&nbsp;</p>
			<?php } ?>
			<img src="<?php echo base_url() ?>assets/img/star4.png" class="BookMark" data-toggle="bookmark-popover">
			<?php 
			if(!empty($OrderDetails->CustomerName)){
				echo '<span class="package"> Client : '.$OrderDetails->CustomerName.'</span>	';}
			// if(!empty($OrderDetails->LoanNumber)){
			// 		echo '<span class="package"> LoanNumber : '.$OrderDetails->LoanNumber.'</span>	';}
			if (!in_array($OrderDetails->CustomerUID,$this->config->item('Loantypeexcludedclients')) && !empty($OrderDetails->LoanType)) {
				echo '<span class="package" > LoanType : ' . $OrderDetails->LoanType . '</span>	';	
			}
			if (!empty($OrderDetails->PropertyStateCode)) {
				echo '<span class="package"> State : ' . $OrderDetails->PropertyStateCode . '</span>	';	
			}

	



			// if (!empty($OrderDetails->DocTypeName)) {
			// 	echo '<span class="package" style="display: none;"> Doc Type : ' . $OrderDetails->DocTypeName . '</span>	';	
			// }
			// if (!empty($tOrderPackage)) {
			// 	echo '<span class="package"  style="margin-left:10px; display: none;"> Package# ' . $tOrderPackage->PackageNumber . '</span>  ';
			// }
			
			
			
			// if(!empty( $OrderDetails->PropertyZipCode))
			// {echo '<span class="package" style="line-height: 10px;"> Address:'. $OrderDetails->PropertyAddress1.','.$OrderDetails->PropertyCityName.','.$OrderDetails->PropertyStateCode.','.$OrderDetails->PropertyZipCode.','.$OrderDetails->PropertyCountyName .'</span>';}
			?></h5>		
		</div>
		<div class="col-md-3 text-right" style="margin-top:4px;">
			<?php if (isset($NBSRequired)  && !empty($NBSRequired)) {
				if($NBSRequired->NBSRequired == 1) {
					echo '<span class="badge badge-pill badge-HighlightNBSOrder">NBS Required</span>';
				} else {
					echo '<span class="badge badge-pill badge-HighlightNBSOrder">NBS Not Required</span>';
				}
			} ?>
			<span class="badge badge-success"><?php echo $OrderDetails->PriorityName ?> </span>
			<span class="badge badge-info"><?php echo $OrderDetails->StatusName;?></span>
			
			<?php $this->load->view('orderinfoheader/InstructionPopup'); ?>
		</div>
	</div>
</div>


	
		<!-- <div class="col-md-2">
			<div class="icondiv"><i class="icon-file-stats mtop-5"></i></div>
			<div class="descriptiondiv">
				<p style="line-height:10px;">
					<?php 
					if(!empty($OrderDetails->LoanNumber)){
						echo $OrderDetails->LoanNumber;
						
					}else{
						echo '-';
					}
					?>
					</p>					
				<label>Loan Number</label></div>
			</div> -->
			<!-- <div class="col-md-2" style="border-left: 1px solid #5d5e5f;border-right: 1px solid #696a6b;">
				<div class="icondiv"><i class="icon-users mtop-5"></i></div>
				<div class="descriptiondiv">
					<p style="line-height: 10px;"><?php echo $OrderDetails->CustomerName; ?></p>
					<label>Client Name</label>
				</div>
			</div> -->
			<!-- <div class="col-md-6 package">
				
				<div class="descriptiondiv">
					
					<span class="package" style="line-height: 10px;"> Address:<?php echo $OrderDetails->PropertyAddress1; ?>, <?php echo $OrderDetails->PropertyCityName; ?>, <?php echo $OrderDetails->PropertyStateCode; ?>, <?php echo $OrderDetails->PropertyZipCode; ?>  <?php echo $OrderDetails->PropertyCountyName; ?> </span>
					
				</div>	
			</div> -->
		
	

	<div class="fixed-plugin" id="trackingbar" style="display: none;top: 85px;">
		<div class="dropdown show-dropdown pd-10"> 
			<a href="#" class="menubtn-track" ><i class="fa fa-check-square-o" style="color:#fff" title="Order Tracker"></i></a> 
		</div>
		<div class="panel-wrap track">
			<div class="panel">
				<div class="col-md-12 borderbtm">
					<div class="row">
						<div class="col-md-10">
							<h6>Order Tracker</h6>
						</div>
						<div class="col-md-2 text-right">
							<button class="btn btn-default btn-link btn-xs ma-0 btnclose"><i class="icon-cross3"></i></button>
						</div>
					</div>
				</div>
				<div class="circle"> </div>
				<div class="col-md-12" style="padding: 0px 10px;">
					<div id="">
						<div class="row" style="clear: both">
						
                      	<article class="page">
                        	<ul class="timeline">
	                            <?php if ($Workflows): ?>
	                          <?php foreach ($Workflows as $w): ?>
	                            <?php $checked=0;$UserName='';$Date='';?>
	                            <?php foreach ($Status as $s): ?>
	                              <?php if ($s->WorkflowModuleUID == $w->WorkflowModuleUID && $s->WorkflowStatus == 5){ ?>
	                                <?php $checked=1;$UserName=$s->UserName;$Date=date('m/d/Y',strtotime($s->CompleteDateTime)).' - ';?>
	                                <?php } else if($s->WorkflowModuleUID == $w->WorkflowModuleUID && $s->WorkflowStatus == 3){ ?>
	                                  <?php $result= $this->Common_Model->GetOrderWorkflowsAssignedStatus($s->WorkflowModuleUID,$s->OrderUID);?>
	                                  <?php $checked=2;if($s->CompleteDateTime==''){ $UserName=$result->UserName;}?>
	                              <?php } ?>
	                            <?php endforeach ?>
	                            <?php $result= $this->Common_Model->GetOrderWorkflowsAssignedStatus($w->WorkflowModuleUID,$w->OrderUID);?>
	                                  <?php if($checked==2 && $Date==''){ $UserName=$result->UserName;}?>
	                                  <?php if($w->WorkflowModuleName == 'Welcome Call'){ $expand='is-expandable';} else{  $expand=''; }?>
	                            <li class="timeline-milestone <?php if ($checked ==1){ echo "is-completed";?>
	                              
	                            <?php }else if($checked ==2) { echo "is-current";} else{ echo "is-future"; } ?>
	                             timeline-start">
	                              <div class="timeline-action  <?php echo $expand;?>">
	                                <h2 class="title"><?php echo $w->WorkflowModuleName; ?></h2>
	                                <span class="date"><?php echo $Date.''.$UserName;?></span>
	                                <p class="date text-center"> <strong style="font-weight: 700;"><?php if ($checked ==1){ echo "Completed";?>
	                              
	                            <?php }else if($checked ==2) { echo "Work In Progress";} else{ echo "Pending"; } ?></strong></p>
	                                <div class="content">
	                                  
	                                </div>
	                              </div>
	                            </li>
	                          <?php endforeach ?>
	                        <?php endif ?>

                        	</ul>
                      	</article>

						</div>
					</div>
				</div>
				<div class="circle"> </div>
			</div>
		</div>
	</div>

	<div class="fixed-plugin" id="historybar" style="display: none">
		<div class="dropdown show-dropdown pd-10"> 
			<a href="#" class="menubtn" ><i class="icon-history" style="color:#fff"></i></a> 
		</div>
		<div class="panel-wrap">
			<div class="panel">
				<div class="col-md-12 borderbtm">
					<div class="row">
						<div class="col-md-6">
							<h6>Logs</h6>
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-default btn-link btn-xs ma-0 btnclose"><i class="icon-cross3"></i></button>
						</div>
					</div>
				</div>
				<div class="circle"> </div>
				<div class="col-md-12" style="padding: 0px 10px;">
					<div id="timeline">
						<div class="row" style="clear: both">
							<?php 
							foreach ($GetOrderLog as $key => $value) { ?>
								<div class="item">  
									<div>
										<section class="year">    
											<section>
												<p><?php echo $value->LogsDateTime ?></p>
												<ul>
													<li><?php echo $value->Description ?> by <span class="text-info"><?php echo $value->UserName ?></span></li>
												</ul>
											</section>     
										</section>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="circle"> </div>
			</div>
		</div>
	</div>



	<script>
		$(document).ready(function(){
			$("body").on("click" , ".menubtn" , function(e){
				e.preventDefault();
				$(".panel-wrap").css("transform" , "translateX(0%)");   
			});
			$(".btnclose").click(function(){
				$(".panel-wrap").css("transform" , "translateX(100%)");   
			});


			$("body").on("click" , ".menubtn-track" , function(e){
				e.preventDefault();
				$(".track").css("transform" , "translateX(0%)");   
			});
			$(".btnclose").click(function(){
				$(".track").css("transform" , "translateX(100%)");   
			});
		});


	</script>
