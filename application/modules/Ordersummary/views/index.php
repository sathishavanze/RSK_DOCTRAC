
<style type="text/css">
	.pd-btm-0{
		padding-bottom: 0px;
	}

	.margin-minus8{
		margin: -8px;
	}

	.mt--15{
		margin-top: -15px;
	}

	.bulk-notes
	{
		list-style-type: none
	}
	.bulk-notes li:before
	{
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}


	.white-box {
		background: #ffffff;
		padding: 25px;
		margin-bottom: 15px;
	}
	.btn-outline {
		color: #fb9678;
		background-color: transparent;
	}
	.btn-danger.disabled {
		background: #fb9678;
		border: 1px solid #fb9678;
	}
	.exception{
		border: 1px dotted black;
		border-radius: 5px;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.table-bordered>thead>tr>th {
		min-width: 0px ! important;
	}
	.productadd_button{
		margin-top: -40px;
		font-size: 23px;
	}
	table{
		width:100% !important;
	}
	table, th, td {
		border: 1px solid #ddd;
	}

</style>
<?php
$HeaderColor = $this->db->select('SideBar_NavColor')->from('mUsers')->where('UserUID',$this->loggedid)->get()->row();
?>
<div class="col-md-12 pd-0" >
	<div class="card mt-0" id="Orderentrycard">
		<div class="card-header tabheader" id="">
			<div class="col-md-12 pd-0">
				<div id="headers" style="color: #ffffff;" class="<?php echo $HeaderColor->SideBar_NavColor ?:'#333' ?>">
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
						<input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderSummary->OrderUID;?>">
						<input type="hidden" name="OrderNumber" id="OrderNumber" value="<?php echo $OrderSummary->OrderNumber;?>">
						<input type="hidden" name="OnHoldUID" id="OnHoldUID"  value="<?php echo $OrderSummary->OnHoldUID;?>">

						<?php $this->load->view('Ordersummary/Orderdetails'); ?>

						<div class="form-group pull-right " style="margin-top: 0px">
							<p class="text-right">
								<?php if ($OrderSummary->StatusUID != $this->config->item('keywords')['Cancelled']) { ?>
									<button type="submit" class="btn btn-space btn-social btn-color btn-update checklist_update pull-right" value="1">Update</button>
								<?php } ?>
								<?php $this->load->view('orderinfoheader/workflowbuttons'); ?>

							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php if (!empty($ExceptionList)) { ?>


		<div class="card mt-10">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="text-danger">Exception List</h4>
					</div>
					<div class="col-md-12 white-box perfectscrollbar" id="exception_list" style="max-height: 330px;overflow-y:scroll;">
						<?php foreach ($ExceptionList as $key => $value) { ?>

							<div class="btn-outline row col-md-12 m-b-10 m-t-30 mb-20 exception"> 
								<div class="col-md-6"> 

									<p align="left" style="margin-bottom: 0px;" class="col-md-12 Exception_label">Exception 
										<span class="Exception_span">
											<span class="fa fa-exclamation-circle"> </span>
										</span> 
									</p>

									<p align="left" style="margin-bottom: 0px;" class="col-md-12">Exception Name: 
										<span class="Exception_Name_span"><?php echo $value->ExceptionName; ?> </span>
									</p>

									<p align="left" style="margin-bottom: 0px;" class="col-md-12">Remarks: 
										<span class="Exception_Remark_span"><?php echo $value->ExceptionRemarks; ?></span>
									</p>
								</div>

								<div class="col-md-6"> 
									<p align="right" style="margin-bottom: 0px;" class="col-md-12">Date: 
										<span class="Exception_Date_span">: <?php echo $value->ExceptionRaisedDateTime; ?></span>                  
									</p>

									<p align="right" style="margin-bottom: 0px;" class="col-md-12">Exception Status: 
										<span class="Exception_Name_span"><?php echo $value->IsExceptionCleared == 1 ? 'Cleared' : 'Pending'; ?> </span>
									</p>
								</div>

							</div>

							<?php 
						} ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

</div>
<?php $this->load->view('orderinfoheader/workflowpopover'); ?>


<script type="text/javascript">

// Global Accessable variable declaration section 
$('.order_expand_div').show();
$('.exception').hover(function(){ $(this).addClass('btn-danger') },function(){ $(this).removeClass('btn-danger') });


</script>








