<form id="frmcustomerpriority">
	<div class="col-md-12 borderright dynamicdiv">
		<div class="row" style="background:#f7f7f7;"> 
			<div class="col-md-2 dyndivhead">       
				<strong>Product</strong> 
			</div>

			<?php foreach ($Prioritys as $key => $value) { ?>
				<div class="col-md-3 dyndivhead">       
					<strong><?php echo $value->PriorityName; ?> SLA</strong> 
				</div>

			<?php } ?>

			<div class="col-md-1 dyndivhead">       
				<strong>Action</strong> 
			</div>    
		</div>
		<?php foreach ($details as $key => $value) { ?>

			<div class="row prioritymoduleWrapper"> 
				<div class="col-md-2 dyndiv">       
					<p class="selectedproduct"><?php echo $value['ProductName']  ?></p>
					<input type="hidden" name="ProductUID[<?php echo $key; ?>]" value="<?php echo $value['ProductUID']; ?>">
					<input type="hidden" name="SubProductUID[<?php echo $key; ?>]" value="<?php echo $value['SubProductUID']; ?>">
				</div>
				<?php foreach ($Prioritys as $pkey => $Priority) { ?>
					<?php $Prioritieshours_date = $this->Customer_Model->get_prioritieshours_date($CustomerUID,$Priority->PriorityUID,$value['ProductUID'],$value['SubProductUID']);
					$PriorityTime = !empty($Prioritieshours_date) ? $Prioritieshours_date->PriorityTime : '';
					$SkipOrderOpenDate = !empty($Prioritieshours_date) ? $Prioritieshours_date->SkipOrderOpenDate : ''; ?>
					<div class="col-md-3 dyndiv">
						<div class="row prioritydiv">
							<div class="col-md-5">
								<div class="form-group">
									<label for="<?php echo $Priority->PriorityUID; ?>" class="bmd-label-floating"><?php echo $Priority->PriorityName; ?></label>
									<input class="form-control PriorityTime" type="number" id="<?php echo $Priority->PriorityUID; ?>" data-priorityuid="<?php echo $Priority->PriorityUID; ?>" name="PriorityTime[<?php echo $Priority->PriorityName; ?>][<?php echo $key; ?>]" value="<?php echo $PriorityTime; ?>">
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">

									<select class="select2picker SkipOrderOpenDate" data-priorityuid="<?php echo $Priority->PriorityUID; ?>" id="TATUID" name="TATUID[<?php echo $Priority->PriorityName; ?>][<?php echo $key; ?>]">
										<?php foreach ($mtats as $mkey => $mtat) { 

											if($mtat->TATUID == $SkipOrderOpenDate){  ?>

												<option value="<?php echo $mtat->TATUID; ?>" selected><?php echo $mtat->TATName;?></option>

											<?php }else{  ?>

												<option value="<?php echo $mtat->TATUID; ?>"><?php echo $mtat->TATName;?></option>
											<?php } ?>
										<?php } ?>

									</select>


								</div>
							</div>

						</div>
					</div>
				<?php } ?>

				<div class="col-md-1 dyndiv">
					<div class="mt-10">
						<button type="button" class="btn btn-xs btn-github defaultvalue" style="float:left;"><i class="icon-reset"></i></button> 
						<button type="button" class="btn btn-xs btn-dribbble copyproduct"><i class="icon-copy4"></i></button>
					</div>
				</div>

			</div>
		<?php } ?>
		<div class="updatediv text-right col-md-12 pd-0">
			<button type="submit" class="btn btn-fill btn-dribbble btn-wd" name="UpdateTAT" id="UpdateTAT"> Update</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function() {
		$(".selectpicker").selectpicker();
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});


		/*-----WORKFLOW TAB STARTS*/
		$(document).off('submit', '#frmcustomerpriority').on('submit', '#frmcustomerpriority', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var formdata=new FormData($(this)[0]);
			formdata.append('CustomerUID', $('#CustomerUID').val());
			var button = $('#UpdateTAT');
			var button_text = $('#UpdateTAT').html();

			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>Customer/CustomeTATADD',
				data: formdata, 
				processData:false,
				contentType:false,
				dataType:'json',
				beforeSend: function(){
					button.attr("disabled", true);
					button.html('Please Wait ...'); 
				},
				success: function(data)
				{
					button.html(button_text);
					if(data.validation_error == 0){
						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"success",
							delay:1000 
						})
					}else if(data.validation_error == 1){

						$.notify(
						{
							icon:"icon-bell-check",
							message:data.message
						},
						{
							type:"danger",
							delay:1000 
						})
					}
					button.removeAttr("disabled"); 

				},
				error: function(jqXHR){
					console.log(jqXHR);
				}
			});

		})
		/*-----WORKFLOW TAB ENDS*/

	});



</script>